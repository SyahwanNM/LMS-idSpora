<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
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
        [$from, $to] = $this->parseDateRange($request);

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
            period: $this->normalizePeriod($request->query('period', 'monthly')),
        );

        return view('admin.report', [
            'courses' => $courses,
            'revenueReport' => $revenueReport,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ]);
    }

    public function revenue(Request $request)
    {
        [$from, $to] = $this->parseDateRange($request);
        $period = $this->normalizePeriod($request->query('period', 'monthly'));

        $revenueReport = $this->buildRevenueReport(from: $from, to: $to, period: $period);

        return response()->json($revenueReport);
    }

    private function parseDateRange(Request $request): array
    {
        $toRaw = trim((string) $request->query('to', ''));
        $fromRaw = trim((string) $request->query('from', ''));

        $to = $toRaw !== '' ? Carbon::parse($toRaw)->endOfDay() : Carbon::now()->endOfDay();
        $from = $fromRaw !== '' ? Carbon::parse($fromRaw)->startOfDay() : $to->copy()->startOfMonth()->startOfDay();

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return [$from, $to];
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

    private function buildRevenueReport(Carbon $from, Carbon $to, string $period = 'monthly'): array
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

        $series = $this->buildRevenueSeries($from, $to, $period);

        return [
            'totals' => $totals,
            'rows' => $rows,
            'revenue_by_level' => $revenueByLevel,
            'series' => $series,
            'period' => $period,
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
