<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManualPayment;
use App\Models\Referral;
use App\Models\Withdrawal;
use App\Models\Expense;
use App\Models\TrainerPayment;
use App\Models\User;
use App\Models\Event;
use App\Models\Course;
use App\Models\EventExpense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FinanceController extends Controller
{
    /**
     * Get overall financial metrics and dashboard data.
     */
    public function overview()
    {
        // 1. Total Omzet (Gross Revenue) - All settled manual payments
        $totalOmzet = ManualPayment::where('status', 'settled')->sum('amount');
        $eventRevenue = ManualPayment::where('status', 'settled')->whereNotNull('event_id')->sum('amount');
        $courseRevenue = ManualPayment::where('status', 'settled')->whereNotNull('course_id')->sum('amount');

        // 2. Pendapatan Bersih (Net Profit)
        $paidCommissions = Referral::where('status', 'paid')->sum('amount');
        $totalExpenses = Expense::where(function($q) { $q->where('status', 'approved')->orWhereNull('status'); })->sum('amount');
        $totalTrainerPayments = TrainerPayment::where(function($q) { $q->where('status', 'approved')->orWhereNull('status'); })->sum('amount');
        $totalEventExpenses = EventExpense::where('status', 'approved')->sum('total');
        
        $pendapatanBersih = $totalOmzet - $paidCommissions - $totalExpenses - $totalTrainerPayments - $totalEventExpenses;

        // 3. Status Kas
        $manualPending = ManualPayment::where('status', 'pending')->sum('amount');
        $danaTertahan = $manualPending;
        $danaSiapCair = $totalOmzet;

        // Platform Activity Tracking
        $eventSettledCount = ManualPayment::where('status', 'settled')->whereNotNull('event_id')->count();
        $courseSettledCount = ManualPayment::where('status', 'settled')->whereNotNull('course_id')->count();
        $freeCount = ManualPayment::where('status', 'settled')->where('amount', 0)->count();
        $paidCount = ManualPayment::where('status', 'settled')->where('amount', '>', 0)->count();

        // Data for monthly revenue charts
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('M');
            $year = $date->year;
            $monthNum = $date->month;

            $manual = ManualPayment::where('status', 'settled')
                ->whereMonth('created_at', $monthNum)
                ->whereYear('created_at', $year)
                ->sum('amount');

            $monthlyRevenue[] = [
                'month' => $month,
                'revenue' => (float)$manual
            ];
        }

        $salDoKas = $totalOmzet - $paidCommissions - $totalExpenses - $totalTrainerPayments - $totalEventExpenses;

        // Current Month Expenses
        $thisMonth = now()->month;
        $thisYear  = now()->year;
        $totalExpenseThisMonth = Expense::where(function($q) { $q->where('status','approved')->orWhereNull('status'); })
            ->whereMonth('expense_date', $thisMonth)->whereYear('expense_date', $thisYear)->sum('amount');
        $totalExpenseThisMonth += TrainerPayment::where(function($q) { $q->where('status','approved')->orWhereNull('status'); })
            ->whereMonth('created_at', $thisMonth)->whereYear('created_at', $thisYear)->sum('amount');
        $totalExpenseThisMonth += EventExpense::where('status','approved')
            ->whereMonth('created_at', $thisMonth)->whereYear('created_at', $thisYear)->sum('total');

        // Pending Expenses Counts
        $pendingExpensesCount = Expense::where('status', 'pending')->count()
            + TrainerPayment::where('status', 'pending')->count()
            + EventExpense::where('status', 'pending')->count();

        $pendingWithdrawalsCount = Withdrawal::where('status', 'pending')->count();

        $revenueThisMonth = ManualPayment::where('status', 'settled')
            ->whereMonth('created_at', $thisMonth)->whereYear('created_at', $thisYear)->sum('amount');

        // Top Reseller Performers
        $topPerformers = User::join('referrals', 'users.id', '=', 'referrals.user_id')
            ->select('users.name', DB::raw('SUM(referrals.amount) as total_commission'))
            ->where('referrals.status', 'paid')
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_commission', 'desc')
            ->limit(3)
            ->get();

        // Recent Income Transactions
        $recentTransactions = ManualPayment::with(['user', 'event', 'course'])
            ->where('status', 'settled')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($m) {
                return [
                    'id'     => $m->order_id,
                    'user'   => $m->user->name ?? 'Guest',
                    'amount' => (float)$m->amount,
                    'date'   => $m->created_at,
                    'type'   => 'Manual',
                    'source' => $m->event_id ? 'Event' : ($m->course_id ? 'Course' : 'Manual'),
                ];
            });

        // Recent Expenses
        $recentAllExpenses = collect();
        $recentAllExpenses = $recentAllExpenses->merge(
            Expense::where(function($q) { $q->where('status','approved')->orWhereNull('status'); })
                ->latest('expense_date')->limit(3)->get()->map(fn($e) => [
                    'desc' => $e->description, 'amount' => (float)$e->amount,
                    'date' => $e->expense_date, 'cat' => $e->category ?? 'Operasional'
                ])
        );
        $recentAllExpenses = $recentAllExpenses->merge(
            TrainerPayment::where(function($q) { $q->where('status','approved')->orWhereNull('status'); })
                ->latest()->limit(2)->get()->map(fn($t) => [
                    'desc' => 'Gaji Trainer: '.($t->trainer->name ?? '-'), 'amount' => (float)$t->amount,
                    'date' => $t->created_at, 'cat' => 'Gaji Trainer'
                ])
        );
        $recentAllExpenses = $recentAllExpenses->sortByDesc('date')->take(5)->values();

        // Trainers and Balances
        $trainers = User::whereIn('role', ['trainer', 'Trainer'])
            ->orderByDesc('wallet_balance')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_omzet' => (float)$totalOmzet,
                'pendapatan_bersih' => (float)$pendapatanBersih,
                'saldo_kas' => (float)$salDoKas,
                'dana_tertahan' => (float)$danaTertahan,
                'dana_siap_cair' => (float)$danaSiapCair,
                'event_revenue' => (float)$eventRevenue,
                'course_revenue' => (float)$courseRevenue,
                'event_settled_count' => $eventSettledCount,
                'course_settled_count' => $courseSettledCount,
                'free_count' => $freeCount,
                'paid_count' => $paidCount,
                'revenue_this_month' => (float)$revenueThisMonth,
                'total_expense_this_month' => (float)$totalExpenseThisMonth,
                'pending_expenses_count' => $pendingExpensesCount,
                'pending_withdrawals_count' => $pendingWithdrawalsCount,
                'paid_commissions' => (float)$paidCommissions,
                'monthly_revenue_chart' => $monthlyRevenue,
                'top_performers' => $topPerformers,
                'recent_transactions' => $recentTransactions,
                'recent_expenses' => $recentAllExpenses,
                'trainers_summary' => $trainers->map(fn($t) => [
                    'id' => $t->id,
                    'name' => $t->name,
                    'wallet_balance' => (float)$t->wallet_balance
                ])
            ]
        ]);
    }

    /**
     * Get paginated list of events with financial stats.
     */
    public function events()
    {
        $events = Event::withCount([
            'registrations as total_registrations',
            'registrations as active_registrations' => function($q) {
                $q->where('status', 'active');
            }
        ])
        ->orderByDesc('event_date')
        ->orderByDesc('created_at')
        ->paginate(10);

        foreach($events as $event) {
            $event->revenue = (float)ManualPayment::where('event_id', $event->id)->where('status', 'settled')->sum('amount');
            $event->pending_revenue = (float)ManualPayment::where('event_id', $event->id)->where('status', 'pending')->sum('amount');
        }

        return response()->json([
            'status' => 'success',
            'data' => $events
        ]);
    }

    /**
     * Get paginated list of courses with financial stats.
     */
    public function courses()
    {
        $courses = Course::withCount([
            'enrollments as total_enrollments',
            'enrollments as active_enrollments' => function($q) {
                $q->where('status', 'active');
            }
        ])
        ->latest()
        ->paginate(10);

        foreach($courses as $course) {
            $course->revenue = (float)ManualPayment::where('course_id', $course->id)->where('status', 'settled')->sum('amount');
            $course->pending_revenue = (float)ManualPayment::where('course_id', $course->id)->where('status', 'pending')->sum('amount');
        }

        return response()->json([
            'status' => 'success',
            'data' => $courses
        ]);
    }

    /**
     * Get financial details of a specific event.
     */
    public function eventDetail($id)
    {
        $event = Event::with(['expenses'])->find($id);
        if (!$event) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event tidak ditemukan.'
            ], 404);
        }

        $transactions = ManualPayment::with('user')
            ->where('event_id', $id)
            ->latest()
            ->paginate(15);
            
        $totalIncome = (float)ManualPayment::where('event_id', $id)->where('status', 'settled')->sum('amount');
        $opExpenses = (float)$event->expenses_total;
        
        $commissions = (float)Referral::where('description', 'LIKE', '%' . $event->title . '%')->where('status', 'paid')->sum('amount');

        return response()->json([
            'status' => 'success',
            'data' => [
                'event' => $event,
                'total_income' => $totalIncome,
                'operational_expenses' => $opExpenses,
                'commissions' => $commissions,
                'transactions' => $transactions
            ]
        ]);
    }

    /**
     * Get financial details of a specific course.
     */
    public function courseDetail($id)
    {
        $course = Course::find($id);
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course tidak ditemukan.'
            ], 404);
        }

        $transactions = ManualPayment::with('user')
            ->where('course_id', $id)
            ->latest()
            ->paginate(15);
            
        $totalIncome = (float)ManualPayment::where('course_id', $id)->where('status', 'settled')->sum('amount');
        $commissions = (float)Referral::where('description', 'LIKE', '%' . $course->name . '%')->where('status', 'paid')->sum('amount');

        return response()->json([
            'status' => 'success',
            'data' => [
                'course' => $course,
                'total_income' => $totalIncome,
                'commissions' => $commissions,
                'transactions' => $transactions
            ]
        ]);
    }

    /**
     * Get lists of withdrawals, trainer payouts, event expenses, and general expenses.
     */
    public function expenses(Request $request)
    {
        $wQuery = Withdrawal::with('user');
        $tpQuery = TrainerPayment::with('trainer');
        $eeQuery = EventExpense::with('event');
        $geQuery = Expense::query();

        if ($request->filled('month')) {
            $wQuery->whereMonth('created_at', $request->month);
            $tpQuery->whereMonth('created_at', $request->month);
            $eeQuery->whereMonth('created_at', $request->month);
            $geQuery->whereMonth('expense_date', $request->month);
        }
        if ($request->filled('year')) {
            $wQuery->whereYear('created_at', $request->year);
            $tpQuery->whereYear('created_at', $request->year);
            $eeQuery->whereYear('created_at', $request->year);
            $geQuery->whereYear('expense_date', $request->year);
        }

        $withdrawals = $wQuery->latest()->paginate(10, ['*'], 'reseller_page');
        $trainerPayments = $tpQuery->latest()->paginate(10, ['*'], 'trainer_page');
        $eventExpenses = $eeQuery->latest()->paginate(10, ['*'], 'event_page');
        $generalExpenses = $geQuery->latest()->paginate(10, ['*'], 'expense_page');

        return response()->json([
            'status' => 'success',
            'data' => [
                'withdrawals' => $withdrawals,
                'trainer_payments' => $trainerPayments,
                'event_expenses' => $eventExpenses,
                'general_expenses' => $generalExpenses
            ]
        ]);
    }

    /**
     * Record a new operational general expense.
     */
    public function storeExpense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description'      => 'required|string|max:255',
            'amount'           => 'required|numeric|min:0',
            'expense_date'     => 'required|date',
            'category'         => 'nullable|string',
            'proof_of_payment' => 'required|image|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('proof_of_payment');
        if ($request->hasFile('proof_of_payment')) {
            $path = $request->file('proof_of_payment')->store('finance/proofs/manual', 'public');
            $data['proof_of_payment'] = $path;
        }
        $data['status'] = 'approved';

        $expense = Expense::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengeluaran manual berhasil dicatat dengan bukti pembayaran.',
            'data' => $expense
        ], 201);
    }

    /**
     * Approve a general expense.
     */
    public function approveExpense(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'proof_of_payment' => 'required|image|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $expense = Expense::find($id);
        if (!$expense) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengeluaran tidak ditemukan.'
            ], 404);
        }

        $path = $request->file('proof_of_payment')->store('finance/proofs/manual', 'public');
        $expense->update([
            'status' => 'approved',
            'proof_of_payment' => $path
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengeluaran manual berhasil disetujui.',
            'data' => $expense
        ]);
    }

    /**
     * Reject a general expense.
     */
    public function rejectExpense(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rejected_reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $expense = Expense::find($id);
        if (!$expense) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengeluaran tidak ditemukan.'
            ], 404);
        }

        $expense->update([
            'status' => 'rejected',
            'rejected_reason' => $request->rejected_reason
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengeluaran manual ditolak.',
            'data' => $expense
        ]);
    }

    /**
     * Approve an event operational expense.
     */
    public function approveEventExpense(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'proof_of_payment' => 'required|image|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $expense = EventExpense::find($id);
        if (!$expense) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cost event tidak ditemukan.'
            ], 404);
        }

        $path = $request->file('proof_of_payment')->store('finance/proofs/events', 'public');
        $expense->update([
            'status' => 'approved',
            'proof_of_payment' => $path
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Cost event berhasil disetujui.',
            'data' => $expense
        ]);
    }

    /**
     * Reject an event operational expense.
     */
    public function rejectEventExpense(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rejected_reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $expense = EventExpense::find($id);
        if (!$expense) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cost event tidak ditemukan.'
            ], 404);
        }

        $expense->update([
            'status' => 'rejected',
            'rejected_reason' => $request->rejected_reason
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Cost event ditolak.',
            'data' => $expense
        ]);
    }

    /**
     * Get paginated list of incomes (manual payment transactions).
     */
    public function incomes(Request $request)
    {
        $query = ManualPayment::with(['user', 'event', 'course'])
            ->where('status', 'settled');

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        $incomes = $query->orderByDesc('created_at')->paginate(15);
        $filteredTotal = $query->sum('amount');

        return response()->json([
            'status' => 'success',
            'data' => [
                'incomes' => $incomes,
                'filtered_total' => (float)$filteredTotal
            ]
        ]);
    }

    /**
     * Record a manual external income.
     */
    public function storeIncome(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description'   => 'required|string',
            'amount'        => 'required|numeric|min:1',
            'received_date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $payment = ManualPayment::create([
            'amount' => $request->amount,
            'status' => 'settled',
            'method' => 'manual_external',
            'order_id' => 'MANUAL-' . time(),
            'metadata' => [
                'description' => $request->description,
                'received_date' => $request->received_date,
            ],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pemasukan manual berhasil ditambahkan.',
            'data' => $payment
        ], 201);
    }

    /**
     * Get trainers course payout statuses, finished events, pending requests, and payout history.
     */
    public function trainers(Request $request)
    {
        $minDisburse = 200000;

        $trainers = User::whereIn('role', ['trainer', 'Trainer'])
            ->orderByDesc('wallet_balance')
            ->get();

        foreach ($trainers as $t) {
            $t->total_paid = 0;
            $t->pending_payout = false;
            $t->can_disburse = ($t->wallet_balance ?? 0) >= $minDisburse && !empty($t->bank_name) && !empty($t->bank_account_number);
        }

        // Ended events that don't have fee payouts yet
        $endedEvents = Event::withTrashed()
            ->finished()
            ->whereNotNull('trainer_id')
            ->with('trainer')
            ->get()
            ->filter(function ($event) {
                return !TrainerPayment::where('event_id', $event->id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->where('type', 'event_fee')
                    ->exists();
            })
            ->values();

        // Pending event fee requests
        $pendingEventFees = TrainerPayment::with(['trainer', 'event'])
            ->where('type', 'event_fee')
            ->where('status', 'pending')
            ->latest()
            ->get();

        // Approved payout history
        $payoutHistory = TrainerPayment::with(['trainer', 'event', 'course'])
            ->where('status', 'approved')
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'trainers' => $trainers->map(fn($t) => [
                    'id' => $t->id,
                    'name' => $t->name,
                    'email' => $t->email,
                    'wallet_balance' => (float)$t->wallet_balance,
                    'can_disburse' => $t->can_disburse
                ]),
                'ended_events_without_payout' => $endedEvents,
                'pending_event_fees' => $pendingEventFees,
                'payout_history' => $payoutHistory,
                'min_disburse_limit' => $minDisburse
            ]
        ]);
    }

    /**
     * Process course revenue balance disbursement for a trainer.
     */
    public function disburseCourseBalance(Request $request, $trainerId)
    {
        $trainer = User::find($trainerId);
        if (!$trainer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Trainer tidak ditemukan.'
            ], 404);
        }

        $minDisburse = 200000;
        if ($trainer->wallet_balance < $minDisburse) {
            return response()->json([
                'status' => 'error',
                'message' => 'Saldo trainer belum mencapai minimum pencairan Rp ' . number_format($minDisburse, 0, ',', '.')
            ], 400);
        }

        if (empty($trainer->bank_name) || empty($trainer->bank_account_number)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pencairan gagal. Trainer belum mengatur informasi rekening bank.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'proof_of_payment' => 'required|image|max:5120',
            'notes'            => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $proofPath = $request->file('proof_of_payment')->store('finance/proofs/trainers', 'public');
        $amount    = (float)$trainer->wallet_balance;

        $payout = TrainerPayment::create([
            'user_id'          => $trainer->id,
            'type'             => 'course_payout',
            'trainer_name'     => $trainer->name,
            'title'            => 'Pencairan Saldo Course – ' . now()->format('F Y'),
            'amount'           => $amount,
            'status'           => 'approved',
            'payment_date'     => now(),
            'payment_method'   => 'transfer',
            'proof_file'       => $proofPath,
            'notes'            => $request->notes,
        ]);

        // Reset wallet balance
        $trainer->decrement('wallet_balance', $amount);

        // Notify trainer
        \App\Models\TrainerNotification::create([
            'trainer_id' => $trainer->id,
            'type'       => 'payout_processed',
            'title'      => 'Pencairan Saldo Course Berhasil',
            'message'    => 'Pencairan saldo course Anda sebesar Rp ' . number_format($amount, 0, ',', '.') . ' telah diproses.',
            'data'       => ['amount' => $amount, 'url' => route('trainer.finance')],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Pencairan saldo Rp " . number_format($amount, 0, ',', '.') . " untuk trainer {$trainer->name} berhasil diproses.",
            'data' => $payout
        ]);
    }

    /**
     * Submit fee payout request for a finished event.
     */
    public function createEventFeeRequest(Request $request, $eventId)
    {
        $event = Event::withTrashed()->with('trainer')->find($eventId);
        if (!$event) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event tidak ditemukan.'
            ], 404);
        }

        if (!$event->trainer_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event ini tidak memiliki trainer yang terdaftar.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'notes'  => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $payout = TrainerPayment::create([
            'user_id'      => $event->trainer_id,
            'type'         => 'event_fee',
            'event_id'     => $event->id,
            'trainer_name' => $event->trainer->name ?? '-',
            'title'        => 'Fee Event: ' . $event->title,
            'amount'       => $request->amount,
            'status'       => 'pending',
            'notes'        => $request->notes,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Permintaan pencairan fee event "' . $event->title . '" berhasil dibuat dan menunggu proses pembayaran.',
            'data' => $payout
        ], 201);
    }

    /**
     * Approve event fee payout request with proof upload.
     */
    public function approveEventFeePayment(Request $request, $paymentId)
    {
        $payment = TrainerPayment::with('trainer')->find($paymentId);
        if (!$payment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Permintaan pembayaran tidak ditemukan.'
            ], 404);
        }

        if (!$payment->trainer || empty($payment->trainer->bank_name) || empty($payment->trainer->bank_account_number)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pembayaran fee gagal. Trainer belum mengatur informasi rekening bank.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'proof_of_payment' => 'required|image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $proofPath = $request->file('proof_of_payment')->store('finance/proofs/events', 'public');

        $payment->update([
            'status'           => 'approved',
            'payment_date'     => now(),
            'payment_method'   => 'transfer',
            'proof_file'       => $proofPath,
        ]);

        if ($payment->trainer) {
            \App\Models\TrainerNotification::create([
                'trainer_id' => $payment->trainer->id,
                'type'       => 'payout_processed',
                'title'      => 'Fee Event Dicairkan',
                'message'    => 'Fee event Anda sebesar Rp ' . number_format($payment->amount, 0, ',', '.') . ' telah dicairkan.',
                'data'       => ['amount' => $payment->amount, 'url' => route('trainer.finance')],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Fee event berhasil dicairkan.',
            'data' => $payment
        ]);
    }

    /**
     * Reject event fee payout request.
     */
    public function rejectEventFeePayment(Request $request, $paymentId)
    {
        $validator = Validator::make($request->all(), [
            'rejected_reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $payment = TrainerPayment::find($paymentId);
        if (!$payment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Permintaan pembayaran tidak ditemukan.'
            ], 404);
        }

        $payment->update([
            'status' => 'rejected',
            'rejected_reason' => $request->rejected_reason
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Permintaan fee event ditolak.',
            'data' => $payment
        ]);
    }

    /**
     * Export transaction ledger logs.
     */
    public function exportData(Request $request)
    {
        $period = $request->get('period', 'this_month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($period == 'this_week') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
        } elseif ($period == 'this_month') {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        } elseif ($period == 'per_6_months') {
            $start = Carbon::now()->subMonths(5)->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        } elseif ($period == 'per_year') {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now()->endOfYear();
        } elseif ($period == 'custom' && $startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } else {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        }

        $manualIncomes = ManualPayment::with(['user', 'event', 'course'])
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'settled')
            ->get();

        $commissions = Referral::with('referredUser')
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'paid')
            ->get();

        $manualExpenses = Expense::whereBetween('expense_date', [$start, $end])
            ->where(function($q) {
                $q->where('status', 'approved')->orWhereNull('status');
            })
            ->get();

        $trainerPayments = TrainerPayment::with('trainer')
            ->whereBetween('created_at', [$start, $end])
            ->where(function($q) {
                $q->where('status', 'approved')->orWhereNull('status');
            })
            ->get();

        $eventExpenses = EventExpense::with('event')
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'approved')
            ->get();

        $transactions = [];

        foreach ($manualIncomes as $m) {
            $source = $m->event_id ? "Event: " . ($m->event->title ?? 'N/A') : ($m->course_id ? "Course: " . ($m->course->name ?? 'N/A') : "Manual");
            $transactions[] = [
                'date' => $m->created_at->toIso8601String(),
                'description' => $source . " (#" . $m->order_id . " - " . ($m->user->name ?? 'Guest') . ")",
                'method' => 'Transfer Manual',
                'status' => 'Settled',
                'amount' => (float)$m->amount,
                'type' => 'income'
            ];
        }

        foreach ($commissions as $c) {
            $transactions[] = [
                'date' => $c->created_at->toIso8601String(),
                'description' => "Komisi Reseller: " . $c->description,
                'method' => 'Wallet Deduction',
                'status' => 'Paid',
                'amount' => (float)$c->amount,
                'type' => 'expense'
            ];
        }

        foreach ($manualExpenses as $me) {
            $transactions[] = [
                'date' => Carbon::parse($me->expense_date)->toIso8601String(),
                'description' => "Pengeluaran Manual: " . $me->description . ($me->category ? " ({$me->category})" : ""),
                'method' => 'Cash/Transfer',
                'status' => 'Approved',
                'amount' => (float)$me->amount,
                'type' => 'expense'
            ];
        }

        foreach ($trainerPayments as $tp) {
            $transactions[] = [
                'date' => $tp->created_at->toIso8601String(),
                'description' => "Gaji Trainer: " . ($tp->trainer->name ?? 'Unknown') . " (" . ($tp->title ?? $tp->notes) . ")",
                'method' => 'Transfer',
                'status' => 'Approved',
                'amount' => (float)$tp->amount,
                'type' => 'expense'
            ];
        }

        foreach ($eventExpenses as $ee) {
            $transactions[] = [
                'date' => $ee->created_at->toIso8601String(),
                'description' => "Cost Event: " . ($ee->event->title ?? 'Unknown') . " - " . $ee->item,
                'method' => 'Transfer',
                'status' => 'Approved',
                'amount' => (float)$ee->total,
                'type' => 'expense'
            ];
        }

        // Sort by date descending
        usort($transactions, function($a, $b) {
            return strcmp($b['date'], $a['date']);
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'transactions' => $transactions
            ]
        ]);
    }
}
