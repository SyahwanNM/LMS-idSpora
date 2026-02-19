<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LearningTimeDaily;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CourseReportController extends Controller
{
    /**
     * Enrollment statuses that indicate a successful (paid/active) enrollment.
     */
    private const REVENUE_ENROLLMENT_STATUSES = ['active', 'expired'];

    public function index(Request $request)
    {
        $period = $this->normalizePeriod($request->query('period', 'monthly'));
        [$from, $to, $hasCustomRange] = $this->parseDateRange($request, $period);

        $courses = Course::query()
            ->select('courses.id', 'courses.name', 'courses.status')
            ->withCount([
                'modules',
                'modules as video_count' => function ($q) {
                    $q->where('type', 'video');
                },
                'modules as pdf_count' => function ($q) {
                    $q->where('type', 'pdf');
                },
                'modules as quiz_count' => function ($q) {
                    $q->where('type', 'quiz');
                },
            ])
            ->latest('courses.updated_at')
            ->get();

        $revenueReport = $this->buildRevenueReport(
            from: $from,
            to: $to,
            period: $period,
            hasCustomRange: $hasCustomRange,
        );

        // Growth tab uses its own defaults (no from/to inputs there).
        $growthPeriod = 'monthly';
        [$growthFrom, $growthTo] = $this->defaultDateRange($growthPeriod);
        $growthReport = $this->buildGrowthReport(from: $growthFrom, to: $growthTo, period: $growthPeriod);

        return view('admin.report', [
            'courses' => $courses,
            'revenueReport' => $revenueReport,
            'growthReport' => $growthReport,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ]);
    }

    public function revenue(Request $request)
    {
        $period = $this->normalizePeriod($request->query('period', 'monthly'));
        [$from, $to, $hasCustomRange] = $this->parseDateRange($request, $period);

        $revenueReport = $this->buildRevenueReport(from: $from, to: $to, period: $period, hasCustomRange: $hasCustomRange);

        return response()->json($revenueReport);
    }

    public function growth(Request $request)
    {
        $period = $this->normalizePeriod($request->query('period', 'monthly'));
        [$from, $to] = $this->defaultDateRange($period);

        $growthReport = $this->buildGrowthReport(from: $from, to: $to, period: $period);

        return response()->json($growthReport);
    }

    private function defaultDateRange(string $period = 'monthly'): array
    {
        $to = Carbon::now()->endOfDay();
        $from = match ($period) {
            'daily' => $to->copy()->startOfDay(),
            'weekly' => $to->copy()->startOfWeek(Carbon::MONDAY)->startOfDay(),
            default => $to->copy()->startOfMonth()->startOfDay(),
        };

        return [$from, $to];
    }

    private function buildGrowthReport(Carbon $from, Carbon $to, string $period = 'monthly'): array
    {
        // Base: paid/valid enrollments in range.
        $courseAgg = Course::query()
            ->leftJoin('enrollments', function ($join) use ($from, $to) {
                $join->on('courses.id', '=', 'enrollments.course_id')
                    ->whereIn('enrollments.status', self::REVENUE_ENROLLMENT_STATUSES)
                    ->whereBetween('enrollments.enrolled_at', [$from, $to]);
            })
            ->select('courses.id', 'courses.name', 'courses.level')
            ->selectRaw('COUNT(enrollments.id) as total_views')
            ->selectRaw('SUM(CASE WHEN enrollments.completed_at IS NOT NULL THEN 1 ELSE 0 END) as completed_count')
            ->groupBy('courses.id', 'courses.name', 'courses.level')
            ->havingRaw('COUNT(enrollments.id) > 0')
            ->orderByDesc(DB::raw('total_views'))
            ->get();

        $timeByCourse = LearningTimeDaily::query()
            ->whereBetween('learned_on', [$from->toDateString(), $to->toDateString()])
            ->groupBy('course_id')
            ->selectRaw('course_id')
            ->selectRaw('SUM(seconds) as total_seconds')
            ->selectRaw('COUNT(DISTINCT user_id) as viewers')
            ->get()
            ->keyBy('course_id');

        $reviewsByCourse = Review::query()
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('course_id')
            ->selectRaw('course_id')
            ->selectRaw('COUNT(*) as comments_count')
            ->selectRaw('AVG(rating) as rating_avg')
            ->get()
            ->keyBy('course_id');

        $rows = $courseAgg->map(function ($r) use ($timeByCourse, $reviewsByCourse) {
            $courseId = (int) $r->id;
            $totalViews = (int) ($r->total_views ?? 0);
            $completedCount = (int) ($r->completed_count ?? 0);
            $completionRate = $totalViews > 0 ? (float) round(($completedCount / $totalViews) * 100, 0) : 0.0;

            $timeRow = $timeByCourse->get($courseId);
            $totalSeconds = (int) ($timeRow->total_seconds ?? 0);
            $viewers = (int) ($timeRow->viewers ?? 0);
            $avgSeconds = $viewers > 0 ? (int) floor($totalSeconds / $viewers) : 0;
            $avgMinutes = (int) round($avgSeconds / 60);

            $reviewRow = $reviewsByCourse->get($courseId);
            $commentsCount = (int) ($reviewRow->comments_count ?? 0);

            return [
                'course_id' => $courseId,
                'course_name' => (string) $r->name,
                'course_level' => $r->level,
                'total_views' => $totalViews,
                'total_views_compact' => $this->formatCompactNumber($totalViews),
                'avg_watch_minutes' => $avgMinutes,
                'avg_watch_time_label' => $avgMinutes > 0 ? ($avgMinutes . ' min') : '0 min',
                'completion_rate' => $completionRate,
                'comments_count' => $commentsCount,
            ];
        })->values();

        $completedUsers = Enrollment::query()
            ->whereIn('status', self::REVENUE_ENROLLMENT_STATUSES)
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$from, $to])
            ->distinct('user_id')
            ->count('user_id');

        return [
            'period' => $period,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'summary' => [
                'completed_users' => (int) $completedUsers,
            ],
            'rows' => $rows,
        ];
    }

    private function formatCompactNumber(int $n): string
    {
        $n = (int) $n;
        if ($n >= 1000000) {
            $v = round($n / 1000000, 1);
            return rtrim(rtrim((string) $v, '0'), '.') . 'M';
        }
        if ($n >= 1000) {
            $v = round($n / 1000, 1);
            return rtrim(rtrim((string) $v, '0'), '.') . 'K';
        }
        return (string) $n;
    }

    private function parseDateRange(Request $request, string $period = 'monthly'): array
    {
        $toRaw = trim((string) $request->query('to', ''));
        $fromRaw = trim((string) $request->query('from', ''));
        $hasCustomRange = ($toRaw !== '' || $fromRaw !== '');

        $to = $toRaw !== '' ? Carbon::parse($toRaw)->endOfDay() : Carbon::now()->endOfDay();

        if ($fromRaw !== '') {
            $from = Carbon::parse($fromRaw)->startOfDay();
        } else {
            $from = match ($period) {
                'daily' => $to->copy()->startOfDay(),
                // Week-to-date (Monday start) to match common reporting expectations.
                'weekly' => $to->copy()->startOfWeek(Carbon::MONDAY)->startOfDay(),
                default => $to->copy()->startOfMonth()->startOfDay(),
            };
        }

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return [$from, $to, $hasCustomRange];
    }

    private function normalizePeriod(string $period): string
    {
        $period = strtolower(trim($period));
        return match ($period) {
            'daily', 'harian' => 'daily',
            'weekly', 'mingguan' => 'weekly',
            'monthly', 'bulanan' => 'monthly',
            default => 'monthly',
        };
    }

    private function buildRevenueReport(Carbon $from, Carbon $to, string $period = 'monthly', bool $hasCustomRange = false): array
    {
        $priceExpr = $this->coursePriceExpr('enrollments.enrolled_at');

        $baseEnrollments = Enrollment::query()
            ->whereIn('enrollments.status', self::REVENUE_ENROLLMENT_STATUSES)
            ->whereBetween('enrollments.enrolled_at', [$from, $to]);

        $totals = [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'total_revenue' => (float) $baseEnrollments->clone()
                ->join('courses', 'courses.id', '=', 'enrollments.course_id')
                ->selectRaw('SUM(' . $priceExpr . ') as total_revenue')
                ->value('total_revenue') ?: 0,
            'total_transactions' => (int) $baseEnrollments->clone()->count(),
            'unique_buyers' => (int) $baseEnrollments->clone()->distinct('enrollments.user_id')->count('enrollments.user_id'),
        ];

        $rows = Enrollment::query()
            ->join('courses', 'courses.id', '=', 'enrollments.course_id')
            ->whereIn('enrollments.status', self::REVENUE_ENROLLMENT_STATUSES)
            ->whereBetween('enrollments.enrolled_at', [$from, $to])
            ->groupBy('enrollments.course_id', 'courses.name', 'courses.level', 'courses.price')
            ->selectRaw('enrollments.course_id')
            ->selectRaw('courses.name as course_name')
            ->selectRaw('courses.level as course_level')
            ->selectRaw('courses.price as course_price')
            ->selectRaw('COUNT(*) as transactions_count')
            ->selectRaw('COUNT(DISTINCT enrollments.user_id) as participants_count')
            ->selectRaw('SUM(' . $priceExpr . ') as revenue_total')
            ->selectRaw('MAX(enrollments.enrolled_at) as last_paid_at')
            ->orderByDesc(DB::raw('revenue_total'))
            ->get()
            ->map(function ($r) {
                return [
                    'course_id' => (int) $r->course_id,
                    'course_name' => (string) $r->course_name,
                    'course_level' => $r->course_level,
                    'course_price' => (float) $r->course_price,
                    'participants_count' => (int) $r->participants_count,
                    'transactions_count' => (int) $r->transactions_count,
                    'revenue_total' => (float) $r->revenue_total,
                    'expense_total' => 0.0,
                    'last_paid_at' => $r->last_paid_at ? Carbon::parse($r->last_paid_at)->toDateString() : null,
                ];
            })
            ->values();

        $revenueByLevel = Enrollment::query()
            ->join('courses', 'courses.id', '=', 'enrollments.course_id')
            ->whereIn('enrollments.status', self::REVENUE_ENROLLMENT_STATUSES)
            ->whereBetween('enrollments.enrolled_at', [$from, $to])
            ->groupBy('courses.level')
            ->selectRaw('courses.level as level')
            ->selectRaw('SUM(' . $priceExpr . ') as revenue_total')
            ->orderByDesc(DB::raw('revenue_total'))
            ->get()
            ->map(fn($r) => [
                'level' => $r->level,
                'revenue_total' => (float) $r->revenue_total,
            ])
            ->values();

        [$compareFrom, $compareTo, $compareLabel] = $this->comparisonRange($from, $to, $period, $hasCustomRange);

        $compareTotals = null;
        $compareByLevel = collect();
        if ($compareFrom && $compareTo) {
            $compareBase = Enrollment::query()
                ->whereIn('enrollments.status', self::REVENUE_ENROLLMENT_STATUSES)
                ->whereBetween('enrollments.enrolled_at', [$compareFrom, $compareTo]);

            $compareTotals = [
                'total_revenue' => (float) ($compareBase->clone()
                    ->join('courses', 'courses.id', '=', 'enrollments.course_id')
                    ->selectRaw('SUM(' . $priceExpr . ') as total_revenue')
                    ->value('total_revenue') ?: 0),
                'total_transactions' => (int) $compareBase->clone()->count(),
            ];

            $compareByLevel = Enrollment::query()
                ->join('courses', 'courses.id', '=', 'enrollments.course_id')
                ->whereIn('enrollments.status', self::REVENUE_ENROLLMENT_STATUSES)
                ->whereBetween('enrollments.enrolled_at', [$compareFrom, $compareTo])
                ->groupBy('courses.level')
                ->selectRaw('courses.level as level')
                ->selectRaw('SUM(' . $priceExpr . ') as revenue_total')
                ->orderByDesc(DB::raw('revenue_total'))
                ->get()
                ->map(fn($r) => [
                    'level' => $r->level,
                    'revenue_total' => (float) $r->revenue_total,
                ])
                ->values();
        }

        $revenuePerModule = ($totals['total_transactions'] ?? 0) > 0
            ? (float) round(((float) ($totals['total_revenue'] ?? 0)) / ((int) ($totals['total_transactions'] ?? 0)))
            : 0.0;
        $compareRevenuePerModule = ($compareTotals && ($compareTotals['total_transactions'] ?? 0) > 0)
            ? (float) round(((float) ($compareTotals['total_revenue'] ?? 0)) / ((int) ($compareTotals['total_transactions'] ?? 0)))
            : 0.0;

        $topLevelCurrent = (float) (($revenueByLevel[0]['revenue_total'] ?? 0) ?: 0);
        $topLevelCompare = (float) (($compareByLevel[0]['revenue_total'] ?? 0) ?: 0);

        $changes = [
            'label' => $compareLabel,
            'total_revenue' => $this->percentChange((float) ($totals['total_revenue'] ?? 0), (float) ($compareTotals['total_revenue'] ?? 0)),
            'top_level_revenue' => $this->percentChange($topLevelCurrent, $topLevelCompare),
            'revenue_per_module' => $this->percentChange($revenuePerModule, $compareRevenuePerModule),
        ];

        $series = $this->buildRevenueSeries($from, $to, $period);

        return [
            'totals' => $totals,
            'rows' => $rows,
            'revenue_by_level' => $revenueByLevel,
            'series' => $series,
            'period' => $period,
            'changes' => $changes,
        ];
    }

    private function comparisonRange(Carbon $from, Carbon $to, string $period, bool $hasCustomRange): array
    {
        if ($hasCustomRange) {
            $seconds = max(0, $to->diffInSeconds($from));
            $compareTo = $from->copy()->subSecond()->endOfDay();
            $compareFrom = $compareTo->copy()->subSeconds($seconds)->startOfDay();
            return [$compareFrom, $compareTo, 'dari periode sebelumnya'];
        }

        return match ($period) {
            'daily' => [$from->copy()->subDay()->startOfDay(), $from->copy()->subDay()->endOfDay(), 'dari kemarin'],
            'weekly' => [$from->copy()->subWeek()->startOfDay(), $to->copy()->subWeek()->endOfDay(), 'dari minggu lalu'],
            default => [$from->copy()->subMonthNoOverflow()->startOfMonth()->startOfDay(), $to->copy()->subMonthNoOverflow()->endOfDay(), 'dari bulan lalu'],
        };
    }

    private function percentChange(float $current, float $previous): array
    {
        $current = (float) $current;
        $previous = (float) $previous;

        if ($previous == 0.0) {
            $percent = $current == 0.0 ? 0.0 : 100.0;
        } else {
            $percent = (($current - $previous) / $previous) * 100.0;
        }

        return [
            'percent' => (float) round($percent, 0),
            'direction' => $current >= $previous ? 'up' : 'down',
        ];
    }

    private function buildRevenueSeries(Carbon $from, Carbon $to, string $period): array
    {
        $priceExpr = $this->coursePriceExpr('enrollments.enrolled_at');

        $query = Enrollment::query()
            ->join('courses', 'courses.id', '=', 'enrollments.course_id')
            ->whereIn('enrollments.status', self::REVENUE_ENROLLMENT_STATUSES)
            ->whereBetween('enrollments.enrolled_at', [$from, $to]);

        // MySQL-compatible grouping expressions
        [$labelExpr, $orderExpr] = match ($period) {
            'daily' => ['DATE(enrollments.enrolled_at)', 'DATE(enrollments.enrolled_at)'],
            'weekly' => ['DATE_FORMAT(enrollments.enrolled_at, "%x-W%v")', 'MIN(DATE(enrollments.enrolled_at))'],
            default => ['DATE_FORMAT(enrollments.enrolled_at, "%Y-%m")', 'DATE_FORMAT(enrollments.enrolled_at, "%Y-%m")'],
        };

        return $query
            ->selectRaw($labelExpr . ' as label')
            ->selectRaw('SUM(' . $priceExpr . ') as revenue_total')
            ->groupBy('label')
            ->orderByRaw($orderExpr)
            ->get()
            ->map(fn($r) => [
                'label' => (string) $r->label,
                'revenue_total' => (float) $r->revenue_total,
            ])
            ->values()
            ->all();
    }

    private function coursePriceExpr(string $enrolledAtColumn): string
    {
        // Safely support discounted course pricing when those columns exist.
        if (
            Schema::hasColumn('courses', 'discount_percent') &&
            Schema::hasColumn('courses', 'discount_start') &&
            Schema::hasColumn('courses', 'discount_end')
        ) {
            // Apply discount if enrollment date falls within the discount window.
            return 'CASE '
                . 'WHEN courses.discount_percent IS NOT NULL '
                . 'AND courses.discount_start IS NOT NULL '
                . 'AND courses.discount_end IS NOT NULL '
                . 'AND DATE(' . $enrolledAtColumn . ') BETWEEN courses.discount_start AND courses.discount_end '
                . 'THEN ROUND(courses.price * (100 - courses.discount_percent) / 100) '
                . 'ELSE courses.price '
                . 'END';
        }

        return 'courses.price';
    }
}
