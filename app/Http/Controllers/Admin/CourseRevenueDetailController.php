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
     * Enrollment statuses that indicate a successful (paid/active) enrollment.
     */
    private const REVENUE_ENROLLMENT_STATUSES = ['active', 'expired'];

    public function show(Request $request)
    {
        $courseId = (int) $request->query('course_id', 0);
        if ($courseId <= 0) {
            abort(404);
        }

        $course = Course::query()
            ->select('id', 'name', 'status', 'price', 'discount_percent', 'discount_start', 'discount_end')
            ->findOrFail($courseId);

        [$from, $to] = $this->parseDateRange($request);

        $base = Enrollment::query()
            ->where('enrollments.course_id', $course->id)
            ->whereIn('enrollments.status', self::REVENUE_ENROLLMENT_STATUSES)
            ->whereBetween('enrollments.enrolled_at', [$from, $to]);

        $transactionsCount = (int) $base->clone()->count();
        $participantsCount = (int) $base->clone()->distinct('enrollments.user_id')->count('enrollments.user_id');
        $lastPaidAt = $base->clone()->max('enrollments.enrolled_at');

        $priceExpr = $this->coursePriceExpr('enrollments.enrolled_at');
        $revenueTotal = (float) ($base->clone()
            ->join('courses', 'courses.id', '=', 'enrollments.course_id')
            ->selectRaw('SUM(' . $priceExpr . ') as total_revenue')
            ->value('total_revenue') ?: 0);

        // Use average realized unit price when transactions exist; fallback to configured course price.
        $unitPrice = $transactionsCount > 0
            ? (float) round($revenueTotal / $transactionsCount)
            : (float) ($course->price ?? 0);

        // Expense breakdown (currently UI uses fixed categories & percentages).
        $expenseHonor = (float) round($revenueTotal * 0.40);
        $expensePlatform = (float) round($revenueTotal * 0.20);
        $expenseMarketing = (float) round($revenueTotal * 0.15);
        $expenseInfra = (float) round($revenueTotal * 0.15);
        $expenseSupport = (float) round($revenueTotal * 0.10);
        $expenseTotal = $expenseHonor + $expensePlatform + $expenseMarketing + $expenseInfra + $expenseSupport;

        $profit = (float) ($revenueTotal - $expenseTotal);
        $profitStatus = $profit >= 0 ? 'Menguntungkan' : 'Rugi';

        return view('admin.view-pendapatan', [
            'course' => $course,
            'period' => [
                'from' => $from,
                'to' => $to,
            ],
            'stats' => [
                'last_paid_at' => $lastPaidAt ? Carbon::parse($lastPaidAt) : null,
                'participants' => $participantsCount,
                'transactions' => $transactionsCount,
                'status' => $course->status ? ucfirst((string) $course->status) : '-',
                'unit_price' => $unitPrice,
                'revenue_total' => $revenueTotal,
                'expense_total' => $expenseTotal,
                'profit' => $profit,
                'profit_status' => $profitStatus,
            ],
            'expenses' => [
                'honor' => $expenseHonor,
                'platform' => $expensePlatform,
                'marketing' => $expenseMarketing,
                'infra' => $expenseInfra,
                'support' => $expenseSupport,
            ],
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
