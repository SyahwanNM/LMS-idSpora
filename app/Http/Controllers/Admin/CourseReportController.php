<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LearningTimeDaily;
use App\Models\Review;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CourseReportController extends Controller
{
    /**
     * Enrollment statuses that indicate a successful (paid/active/completed) enrollment.
     */
    private const REVENUE_ENROLLMENT_STATUSES = ['active', 'expired', 'completed'];

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
            q: $request->query('q', '')
        );

        // Growth tab uses its own filter (month + search).
        $growthPeriod = 'monthly';
        $growthMonth = trim((string) $request->query('month', $request->query('growth_month', Carbon::now()->format('Y-m')))); // YYYY-MM
        $growthQuery = trim((string) $request->query('q', ''));
        [$growthFrom, $growthTo, $growthMonthResolved] = $this->parseGrowthRangeFromMonth($growthMonth, $growthPeriod);
        $growthReport = $this->buildGrowthReport(from: $growthFrom, to: $growthTo, period: $growthPeriod, q: $growthQuery);

        // Chart (Jan–Dec) should be DB-backed but keep the same UI/labels.
        // The year should follow the selected growth month.
        $growthChartYear = $growthMonthResolved instanceof Carbon ? (int) $growthMonthResolved->year : (int) Carbon::now()->year;
        $growthChart = $this->buildGrowthChartYearSeries($growthChartYear);

        return view('admin.report', [
            'courses' => $courses,
            'revenueReport' => $revenueReport,
            'growthReport' => $growthReport,
            'growthChart' => $growthChart,
            'growthChartYear' => $growthChartYear,
            'growthMonth' => $growthMonth,
            'growthQuery' => $growthQuery,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ]);
    }

    public function revenue(Request $request)
    {
        $period = $this->normalizePeriod($request->query('period', 'monthly'));
        [$from, $to, $hasCustomRange] = $this->parseDateRange($request, $period);

        $q = trim((string) $request->query('q', ''));
        $revenueReport = $this->buildRevenueReport(from: $from, to: $to, period: $period, hasCustomRange: $hasCustomRange, q: $q);

        return response()->json($revenueReport);
    }

    public function growth(Request $request)
    {
        $period = $this->normalizePeriod($request->query('period', 'monthly'));
        $month = trim((string) $request->query('month', '')); // YYYY-MM
        $q = trim((string) $request->query('q', ''));

        [$from, $to, $monthResolved] = $this->parseGrowthRangeFromMonth($month, $period);

        // Fallback to default if month is empty/invalid.
        if (!$monthResolved) {
            [$from, $to] = $this->defaultDateRange($period);
        }

        $growthReport = $this->buildGrowthReport(from: $from, to: $to, period: $period, q: $q);

        // Also include chart series for the selected year (based on selected month when provided).
        $chartYear = $monthResolved instanceof Carbon ? (int) $monthResolved->year : (int) Carbon::now()->year;
        $growthReport['chart'] = $this->buildGrowthChartYearSeries($chartYear);

        return response()->json($growthReport);
    }

    /**
     * Determine a [from,to] range from a YYYY-MM month picker.
     *
     * - monthly: whole selected month
     * - weekly: last week of the selected month (clamped within the month)
     * - daily: last day of the selected month
     */
    private function parseGrowthRangeFromMonth(string $month, string $period = 'monthly'): array
    {
        $month = trim($month);
        if ($month === '') {
            return [Carbon::now()->startOfMonth()->startOfDay(), Carbon::now()->endOfDay(), null];
        }

        try {
            // Parse "YYYY-MM".
            $base = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Throwable $e) {
            return [Carbon::now()->startOfMonth()->startOfDay(), Carbon::now()->endOfDay(), null];
        }

        $monthStart = $base->copy()->startOfMonth()->startOfDay();
        $monthEnd = $base->copy()->endOfMonth()->endOfDay();

        if ($period === 'daily') {
            $to = $monthEnd->copy()->endOfDay();
            $from = $to->copy()->startOfDay();
            return [$from, $to, $base];
        }

        if ($period === 'weekly') {
            $to = $monthEnd->copy()->endOfDay();
            $from = $to->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
            if ($from->lessThan($monthStart)) {
                $from = $monthStart->copy();
            }
            return [$from, $to, $base];
        }

        return [$monthStart, $monthEnd, $base];
    }

    private function buildGrowthChartYearSeries(int $year): array
    {
        $year = $year > 0 ? $year : (int) Carbon::now()->year;
        $start = Carbon::create($year, 1, 1, 0, 0, 0)->startOfDay();
        $end = Carbon::create($year, 12, 31, 23, 59, 59)->endOfDay();

        $viewsByMonth = Enrollment::query()
            ->whereIn('status', self::REVENUE_ENROLLMENT_STATUSES)
            ->whereBetween('enrolled_at', [$start, $end])
            ->selectRaw('MONTH(enrolled_at) as m')
            ->selectRaw('COUNT(*) as total_views')
            ->groupBy('m')
            ->pluck('total_views', 'm');

        $participantsByMonth = Enrollment::query()
            ->whereIn('status', self::REVENUE_ENROLLMENT_STATUSES)
            ->whereBetween('enrolled_at', [$start, $end])
            ->selectRaw('MONTH(enrolled_at) as m')
            ->selectRaw('COUNT(DISTINCT user_id) as participants')
            ->groupBy('m')
            ->pluck('participants', 'm');

        // Total watch time in minutes (sum seconds / 60) from LearningTimeDaily.
        $watchByMonth = LearningTimeDaily::query()
            ->whereBetween('learned_on', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('MONTH(learned_on) as m')
            ->selectRaw('SUM(seconds) as total_seconds')
            ->groupBy('m')
            ->pluck('total_seconds', 'm');

        $ratingByMonth = Review::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('MONTH(created_at) as m')
            ->selectRaw('AVG(rating) as rating_avg')
            ->groupBy('m')
            ->pluck('rating_avg', 'm');

        $views = [];
        $participants = [];
        $watchMinutes = [];
        $rating = [];
        for ($m = 1; $m <= 12; $m++) {
            $v = (int) ($viewsByMonth[$m] ?? 0);
            $p = (int) ($participantsByMonth[$m] ?? 0);
            $sec = (int) ($watchByMonth[$m] ?? 0);
            $min = (int) round($sec / 60);
            $r = (float) ($ratingByMonth[$m] ?? 0);

            $views[] = $v;
            $participants[] = $p;
            $watchMinutes[] = $min;
            // Keep numeric values; one decimal is enough.
            $rating[] = $r > 0 ? (float) round($r, 1) : 0;
        }

        return [
            'year' => $year,
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'series' => [
                'views' => $views,
                'participants' => $participants,
                'watch_minutes' => $watchMinutes,
                'rating' => $rating,
            ],
        ];
    }

    public function exportPdf(Request $request)
    {
        $tab = strtolower(trim((string) $request->query('tab', 'pendapatan')));
        if (!in_array($tab, ['pendapatan', 'pertumbuhan'], true)) {
            $tab = 'pendapatan';
        }

        $period = $this->normalizePeriod((string) $request->query('period', 'monthly'));

        $title = $tab === 'pertumbuhan' ? 'Pertumbuhan' : 'Pendapatan';
        $subtitle = '';
        $rows = [];
        $from = '';
        $to = '';
        $periodLabel = match ($period) {
            'daily' => 'Harian',
            'weekly' => 'Mingguan',
            default => 'Bulanan',
        };

        if ($tab === 'pertumbuhan') {
            $month = trim((string) $request->query('month', '')); // YYYY-MM
            $q = trim((string) $request->query('q', ''));
            [$fromDt, $toDt] = $this->defaultDateRange($period);
            if ($month !== '') {
                [$fromDt, $toDt, $resolved] = $this->parseGrowthRangeFromMonth($month, $period);
                if (!$resolved) {
                    [$fromDt, $toDt] = $this->defaultDateRange($period);
                }
            }

            $growth = $this->buildGrowthReport(from: $fromDt, to: $toDt, period: $period, q: $q);
            $rows = $growth['rows'] ?? [];
            $from = (string) ($growth['from'] ?? $fromDt->toDateString());
            $to = (string) ($growth['to'] ?? $toDt->toDateString());
            $subtitle = 'Periode: ' . $periodLabel . ' | Rentang: ' . $from . ' s/d ' . $to;
        } else {
            [$fromDt, $toDt, $hasCustomRange] = $this->parseDateRange($request, $period);
            $q = trim((string) $request->query('q', ''));
            $revenue = $this->buildRevenueReport(from: $fromDt, to: $toDt, period: $period, hasCustomRange: $hasCustomRange, q: $q);
            $rows = $revenue['rows'] ?? [];
            $from = (string) ($revenue['totals']['from'] ?? $fromDt->toDateString());
            $to = (string) ($revenue['totals']['to'] ?? $toDt->toDateString());
            $subtitle = 'Periode: ' . $periodLabel . ' | Rentang: ' . $from . ' s/d ' . $to;
        }

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $dompdf = new Dompdf($options);

        $html = view('admin.report_export_pdf', [
            'tab' => $tab,
            'title' => 'Laporan Course - ' . $title,
            'subtitle' => $subtitle,
            'periodLabel' => $periodLabel,
            'from' => $from,
            'to' => $to,
            'rows' => $rows,
            'generatedAt' => now(),
        ])->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'report-course-' . $tab . '-' . Str::slug($periodLabel) . '-' . now()->format('YmdHis') . '.pdf';
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
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

    private function buildGrowthReport(Carbon $from, Carbon $to, string $period = 'monthly', string $q = ''): array
    {
        $q = trim($q);
        $courseIdFilter = null;
        if ($q !== '') {
            $courseIdFilter = Course::query()
                ->where('name', 'like', '%' . $q . '%')
                ->select('id');
        }

        // Base: all courses, with optional enrollments in range.
        $courseAgg = Course::query()
            ->whereIn('courses.status', ['active', 'approved', 'published', 'completed'])
            ->leftJoin('enrollments', function ($join) use ($from, $to) {
                $join->on('courses.id', '=', 'enrollments.course_id')
                    ->whereIn('enrollments.status', self::REVENUE_ENROLLMENT_STATUSES)
                    ->whereBetween('enrollments.enrolled_at', [$from, $to]);
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where('courses.name', 'like', '%' . $q . '%');
            })
            ->select('courses.id', 'courses.name', 'courses.level', 'courses.created_at')
            ->selectRaw('COUNT(enrollments.id) as total_views')
            ->selectRaw('COUNT(DISTINCT enrollments.user_id) as participants_count')
            ->selectRaw('SUM(CASE WHEN enrollments.completed_at IS NOT NULL THEN 1 ELSE 0 END) as completed_count')
            ->groupBy('courses.id', 'courses.name', 'courses.level', 'courses.created_at')
            ->orderByDesc('courses.created_at')
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
            $participantsCount = (int) ($r->participants_count ?? 0);
            $completedCount = (int) ($r->completed_count ?? 0);
            $completionRate = $totalViews > 0 ? (float) round(($completedCount / $totalViews) * 100, 0) : 0.0;

            $timeRow = $timeByCourse->get($courseId);
            $totalSeconds = (int) ($timeRow->total_seconds ?? 0);
            $viewers = (int) ($timeRow->viewers ?? 0);
            $avgSeconds = $viewers > 0 ? (int) floor($totalSeconds / $viewers) : 0;
            $avgMinutes = (int) round($avgSeconds / 60);

            $reviewRow = $reviewsByCourse->get($courseId);
            $commentsCount = (int) ($reviewRow->comments_count ?? 0);
            
            // Return cumulative rating for the course instead of just the filtered period rating
            $course = Course::find($courseId);
            $ratingAvg = $course ? (float) $course->reviews()->avg('rating') : 0.0;

            return [
                'course_id' => $courseId,
                'course_name' => (string) $r->name,
                'course_level' => $r->level,
                'total_views' => $totalViews,
                'total_views_compact' => $this->formatCompactNumber($totalViews),
                'participants_count' => $participantsCount,
                'avg_watch_minutes' => $avgMinutes,
                'avg_watch_time_label' => $avgMinutes > 0 ? ($avgMinutes . ' min') : '0 min',
                'completion_rate' => $completionRate,
                'comments_count' => $commentsCount,
                'rating_avg' => $ratingAvg > 0 ? (float) round($ratingAvg, 1) : 0,
            ];
        });

        if ($q === '') {
            // Pre-fetch created_at to avoid N+1
            $courseCreatedMap = \App\Models\Course::whereIn('id', $courseAgg->pluck('id'))
                ->pluck('created_at', 'id');

            $rows = $rows->filter(function($row) use ($from, $to, $courseCreatedMap) {
                if ($row['total_views'] > 0 || $row['avg_watch_minutes'] > 0 || $row['comments_count'] > 0) return true;
                $createdAt = $courseCreatedMap->get($row['course_id']);
                if ($createdAt) {
                    $created = $createdAt instanceof \Carbon\Carbon ? $createdAt : \Carbon\Carbon::parse($createdAt);
                    return $created->between($from, $to);
                }
                return false;
            });
        }
        $rows = $rows->values();

        $baseEnrollments = Enrollment::query()
            ->whereIn('status', self::REVENUE_ENROLLMENT_STATUSES)
            ->whereBetween('enrolled_at', [$from, $to]);

        if ($courseIdFilter) {
            $baseEnrollments->whereIn('course_id', $courseIdFilter);
        }

        $summaryTotalViews = (int) $baseEnrollments->clone()->count();
        $summaryParticipants = (int) $rows->sum('participants_count');

        $timeAggAllQuery = LearningTimeDaily::query()
            ->whereBetween('learned_on', [$from->toDateString(), $to->toDateString()]);
        if ($courseIdFilter) {
            $timeAggAllQuery->whereIn('course_id', $courseIdFilter);
        }
        $timeAggAll = $timeAggAllQuery
            ->selectRaw('SUM(seconds) as total_seconds')
            ->selectRaw('COUNT(DISTINCT user_id) as viewers')
            ->first();
        // Waktu tonton rata-rata dijumlah aja sesuai request user
        $avgAllMinutes = (int) $rows->sum('avg_watch_minutes');

        // Platform-wide rating (average of all reviews, respecting search filter AND date range)
        $ratingAllQuery = Review::query()
            ->whereBetween('created_at', [$from, $to]);
        if ($courseIdFilter) {
            $ratingAllQuery->whereIn('course_id', $courseIdFilter);
        }
        $ratingAll = (float) ($ratingAllQuery->avg('rating') ?: 0);

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
                'total_views' => $summaryTotalViews,
                'participants' => $summaryParticipants,
                'avg_watch_minutes' => $avgAllMinutes,
                'rating_avg' => $ratingAll > 0 ? (float) round($ratingAll, 1) : 0,
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
        $monthRaw = trim((string) $request->query('month', ''));
        $hasCustomRange = ($toRaw !== '' || $fromRaw !== '' || $monthRaw !== '');

        if ($monthRaw !== '' && $fromRaw === '' && $toRaw === '') {
            try {
                $base = Carbon::createFromFormat('Y-m', $monthRaw)->startOfMonth();
                return [$base->copy()->startOfMonth()->startOfDay(), $base->copy()->endOfMonth()->endOfDay(), true];
            } catch (\Throwable $e) { /* ignore */ }
        }

        $to = $toRaw !== '' ? Carbon::parse($toRaw)->endOfDay() : Carbon::now()->endOfDay();

        if ($fromRaw !== '') {
            $from = Carbon::parse($fromRaw)->startOfDay();
        } else {
            $from = match ($period) {
                'daily' => $to->copy()->startOfDay(),
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

    private function revenueEnrollmentQuery()
    {
        return Enrollment::query()
            ->join('courses', 'courses.id', '=', 'enrollments.course_id')
            ->leftJoin('manual_payments', function ($join) {
                $join->on('enrollments.id', '=', 'manual_payments.enrollment_id')
                     ->whereIn('manual_payments.status', ['paid', 'verified', 'settled']);
            });
    }

    private function getRevenuePriceExpr(): string
    {
        // Try getting exact paid amount, fallback to calculated generic course price.
        $calcPrice = $this->coursePriceExpr('enrollments.enrolled_at');
        return 'COALESCE(manual_payments.amount, ' . $calcPrice . ')';
    }

    private function buildRevenueReport(Carbon $from, Carbon $to, string $period = 'monthly', bool $hasCustomRange = false, string $q = ''): array
    {
        $priceExpr = $this->getRevenuePriceExpr();

        $baseEnrollments = $this->revenueEnrollmentQuery()
            ->whereIn('enrollments.status', self::REVENUE_ENROLLMENT_STATUSES)
            ->whereBetween('enrollments.enrolled_at', [$from, $to]);

        $totals = [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'total_revenue' => (float) $baseEnrollments->clone()
                ->selectRaw('SUM(' . $priceExpr . ') as total_revenue')
                ->value('total_revenue') ?: 0,
            'total_transactions' => (int) $baseEnrollments->clone()->count(),
            'unique_buyers' => (int) $baseEnrollments->clone()->distinct('enrollments.user_id')->count('enrollments.user_id'),
        ];

        $stats = $baseEnrollments->clone()
            ->groupBy('enrollments.course_id')
            ->selectRaw('enrollments.course_id')
            ->selectRaw('COUNT(enrollments.id) as transactions_count')
            ->selectRaw('COUNT(DISTINCT enrollments.user_id) as participants_count')
            ->selectRaw('SUM(' . $priceExpr . ') as revenue_total')
            ->selectRaw('MAX(enrollments.enrolled_at) as last_paid_at')
            ->get()
            ->keyBy('course_id');

        $courses = \App\Models\Course::query()
            ->whereIn('status', ['active', 'approved', 'published', 'completed'])
            ->select('id', 'name', 'level', 'price', 'expenses_json', 'created_at')
            ->orderByDesc('created_at')
            ->get();

        $rows = $courses->map(function ($c) use ($stats) {
            $stat = $stats->get($c->id);

            $expense = 0.0;
            $expensesArr = $c->expenses_json;
            if (is_array($expensesArr)) {
                foreach ($expensesArr as $e) {
                    if (is_array($e) && isset($e['total'])) {
                        $expense += (float) $e['total'];
                    }
                }
            }

            return [
                'course_id' => (int) $c->id,
                'course_name' => (string) $c->name,
                'course_level' => $c->level,
                'course_price' => (float) $c->price,
                'participants_count' => $stat ? (int) $stat->participants_count : 0,
                'transactions_count' => $stat ? (int) $stat->transactions_count : 0,
                'revenue_total' => $stat ? (float) $stat->revenue_total : 0.0,
                'expense_total' => $expense,
                'last_paid_at' => $stat && $stat->last_paid_at
                    ? \Carbon\Carbon::parse($stat->last_paid_at)->toDateString()
                    : $c->created_at?->toDateString(),
                'created_at' => $c->created_at,
            ];
        });

        // Show courses that: have transactions in period OR were created in period
        if (!isset($q) || $q === '') {
            $rows = $rows->filter(function($row) use ($from, $to) {
                if ($row['transactions_count'] > 0) return true;
                // Also show courses created within the selected period
                $createdAt = $row['created_at'];
                if ($createdAt) {
                    $created = $createdAt instanceof \Carbon\Carbon ? $createdAt : \Carbon\Carbon::parse($createdAt);
                    return $created->between($from, $to);
                }
                return false;
            });
        } elseif (isset($q) && $q !== '') {
            $rows = $rows->filter(function($row) use ($q) {
                return stripos($row['course_name'], $q) !== false;
            });
        }
        
        $rows = $rows->values();

        $revenueByLevel = $baseEnrollments->clone()
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
            $compareBase = $this->revenueEnrollmentQuery()
                ->whereIn('enrollments.status', self::REVENUE_ENROLLMENT_STATUSES)
                ->whereBetween('enrollments.enrolled_at', [$compareFrom, $compareTo]);

            $compareTotals = [
                'total_revenue' => (float) ($compareBase->clone()
                    ->selectRaw('SUM(' . $priceExpr . ') as total_revenue')
                    ->value('total_revenue') ?: 0),
                'total_transactions' => (int) $compareBase->clone()->count(),
            ];

            $compareByLevel = $compareBase->clone()
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
        $priceExpr = $this->getRevenuePriceExpr();

        $query = $this->revenueEnrollmentQuery()
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
