<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CourseRevenueDetailController extends Controller
{
    /**
     * Enrollment statuses that indicate a successful (paid/active/completed) enrollment.
     */
    private const REVENUE_ENROLLMENT_STATUSES = ['active', 'expired', 'completed'];

    public function show(Request $request)
    {
        $courseId = (int) $request->query('course_id', 0);
        if ($courseId <= 0) {
            abort(404);
        }

        $course = Course::query()
            ->select('id', 'name', 'status', 'price', 'discount_percent', 'discount_start', 'discount_end', 'expenses_json', 'created_at')
            ->findOrFail($courseId);

        [$from, $to] = $this->parseDateRange($request);

        $base = Enrollment::query()
            ->join('courses', 'courses.id', '=', 'enrollments.course_id')
            ->leftJoin('manual_payments', function ($join) {
                $join->on('enrollments.id', '=', 'manual_payments.enrollment_id')
                     ->whereIn('manual_payments.status', ['paid', 'verified', 'settled']);
            })
            ->where('enrollments.course_id', $course->id)
            ->whereIn('enrollments.status', self::REVENUE_ENROLLMENT_STATUSES)
            ->whereBetween('enrollments.enrolled_at', [$from, $to]);

        $transactionsCount = (int) $base->clone()->count('enrollments.id');
        $participantsCount = (int) $base->clone()->distinct('enrollments.user_id')->count('enrollments.user_id');

        $calcPrice = $this->coursePriceExpr('enrollments.enrolled_at');
        $priceExpr = 'COALESCE(manual_payments.amount, ' . $calcPrice . ')';

        $revenueTotal = (float) ($base->clone()
            ->selectRaw('SUM(' . $priceExpr . ') as total_revenue')
            ->value('total_revenue') ?: 0);

        // Use average realized unit price when transactions exist; fallback to configured course price.
        $unitPrice = $transactionsCount > 0
            ? (float) round($revenueTotal / $transactionsCount)
            : (float) ($course->price ?? 0);

        // Expense breakdown: use course "Pengeluaran" column (expenses_json) as the source of truth.
        $expenseRows = [];
        $rawExpenses = $course->expenses_json;
        if (is_array($rawExpenses)) {
            foreach ($rawExpenses as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $item = trim((string) ($row['item'] ?? ''));

                $total = $row['total'] ?? null;
                if ($total === null) {
                    $qty = (int) ($row['quantity'] ?? 0);
                    $unit = (int) ($row['unit_price'] ?? 0);
                    $total = max(0, $qty) * max(0, $unit);
                }

                $total = (float) $total;
                $total = $total < 0 ? 0.0 : $total;

                if ($item === '' && $total <= 0) {
                    continue;
                }

                $expenseRows[] = [
                    'item' => $item !== '' ? $item : 'Pengeluaran',
                    'total' => $total,
                ];
            }
        }

        // Sort by total desc for nicer breakdown display.
        usort($expenseRows, function ($a, $b) {
            return ($b['total'] ?? 0) <=> ($a['total'] ?? 0);
        });

        $expenseTotal = (float) array_sum(array_map(fn ($r) => (float) ($r['total'] ?? 0), $expenseRows));

        $profit = (float) ($revenueTotal - $expenseTotal);
        $profitStatus = $profit >= 0 ? 'Menguntungkan' : 'Rugi';

        return view('admin.view-pendapatan', [
            'course' => $course,
            'period' => [
                'from' => $from,
                'to' => $to,
            ],
            'stats' => [
                'created_at' => $course->created_at ? Carbon::parse($course->created_at) : null,
                'participants' => $participantsCount,
                'transactions' => $transactionsCount,
                'status' => $course->status ? ucfirst((string) $course->status) : '-',
                'unit_price' => $unitPrice,
                'revenue_total' => $revenueTotal,
                'expense_total' => $expenseTotal,
                'profit' => $profit,
                'profit_status' => $profitStatus,
            ],
            'expense_rows' => $expenseRows,
            'chart' => [
                'revenue' => $revenueTotal,
                'expense' => $expenseTotal,
                'profit' => $profit,
            ],
        ]);
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
