<?php

namespace App\Http\Controllers\Admin;

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
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FinanceController extends Controller
{
    public function index()
    {
        // 1. Total Omzet (Gross Revenue) - All settled manual payments
        $totalOmzet = ManualPayment::where('status', 'settled')->sum('amount');
        $eventRevenue = ManualPayment::where('status', 'settled')->whereNotNull('event_id')->sum('amount');
        $courseRevenue = ManualPayment::where('status', 'settled')->whereNotNull('course_id')->sum('amount');

        // 2. Pendapatan Bersih (Net Profit)
        // paid reseller commissions
        $paidCommissions = Referral::where('status', 'paid')->sum('amount');
        
        // Approved Expenses
        $totalExpenses = Expense::where(function($q) { $q->where('status', 'approved')->orWhereNull('status'); })->sum('amount');
        $totalTrainerPayments = TrainerPayment::where(function($q) { $q->where('status', 'approved')->orWhereNull('status'); })->sum('amount');
        $totalEventExpenses = \App\Models\EventExpense::where('status', 'approved')->sum('total');
        
        $pendapatanBersih = $totalOmzet - $paidCommissions - $totalExpenses - $totalTrainerPayments - $totalEventExpenses;

        // 3. Status Kas
        // Masih Tertahan (Pending)
        $manualPending = ManualPayment::where('status', 'pending')->sum('amount');
        $danaTertahan = $manualPending;

        // Dana Siap Cair (Paid/Settled)
        $danaSiapCair = $totalOmzet;

        // 3b. Platform Activity Tracking
        $eventSettledCount = ManualPayment::where('status', 'settled')->whereNotNull('event_id')->count();
        $courseSettledCount = ManualPayment::where('status', 'settled')->whereNotNull('course_id')->count();
        $freeCount = ManualPayment::where('status', 'settled')->where('amount', 0)->count();
        $paidCount = ManualPayment::where('status', 'settled')->where('amount', '>', 0)->count();

        // Data for charts or breakdown (Monthly)
        $monthlyRevenue = $this->getMonthlyRevenue();

        // 4. Saldo Kas Saat Ini (Current Balance) = Total Omzet - All Approved Expenses
        $salDoKas = $totalOmzet - $paidCommissions - $totalExpenses - $totalTrainerPayments - $totalEventExpenses;

        // 5. Pengeluaran Bulan Ini (Current Month)
        $thisMonth = now()->month;
        $thisYear  = now()->year;
        $totalExpenseThisMonth = Expense::where(function($q) { $q->where('status','approved')->orWhereNull('status'); })
            ->whereMonth('expense_date', $thisMonth)->whereYear('expense_date', $thisYear)->sum('amount');
        $totalExpenseThisMonth += TrainerPayment::where(function($q) { $q->where('status','approved')->orWhereNull('status'); })
            ->whereMonth('created_at', $thisMonth)->whereYear('created_at', $thisYear)->sum('amount');
        $totalExpenseThisMonth += \App\Models\EventExpense::where('status','approved')
            ->whereMonth('created_at', $thisMonth)->whereYear('created_at', $thisYear)->sum('total');

        // 6. Pending Expenses Count (Pengeluaran menunggu persetujuan)
        $pendingExpensesCount = Expense::where('status', 'pending')->count()
            + TrainerPayment::where('status', 'pending')->count()
            + \App\Models\EventExpense::where('status', 'pending')->count();

        // 7. Pending Withdrawals
        $pendingWithdrawalsCount = \App\Models\Withdrawal::where('status', 'pending')->count();

        // 8. Pendapatan Bulan Ini
        $revenueThisMonth = ManualPayment::where('status', 'settled')
            ->whereMonth('created_at', $thisMonth)->whereYear('created_at', $thisYear)->sum('amount');

        // 9. Top Performers (based on earnings/commissions)
        $topPerformers = \App\Models\User::join('referrals', 'users.id', '=', 'referrals.user_id')
            ->select('users.name', \DB::raw('SUM(referrals.amount) as total_commission'))
            ->where('referrals.status', 'paid')
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_commission', 'desc')
            ->limit(3)
            ->get();

        // 10. Recent Transactions (Pemasukan Terakhir)
        $recentTransactions = ManualPayment::with(['user', 'event', 'course'])
            ->where('status', 'settled')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($m) {
                return [
                    'id'     => $m->order_id,
                    'user'   => $m->user->name ?? 'Guest',
                    'amount' => $m->amount,
                    'date'   => $m->created_at,
                    'type'   => 'Manual',
                    'source' => $m->event_id ? 'Event' : ($m->course_id ? 'Course' : 'Manual'),
                    'url'    => $m->order_id ? route('invoice.manual', $m->order_id) : '#'
                ];
            });

        // 11. Recent All Expenses (combined)
        $recentAllExpenses = collect();
        $recentAllExpenses = $recentAllExpenses->merge(
            Expense::where(function($q) { $q->where('status','approved')->orWhereNull('status'); })
                ->latest('expense_date')->limit(3)->get()->map(fn($e) => [
                    'desc' => $e->description, 'amount' => $e->amount,
                    'date' => $e->expense_date, 'cat' => $e->category ?? 'Operasional'
                ])
        );
        $recentAllExpenses = $recentAllExpenses->merge(
            TrainerPayment::where(function($q) { $q->where('status','approved')->orWhereNull('status'); })
                ->latest()->limit(2)->get()->map(fn($t) => [
                    'desc' => 'Gaji Trainer: '.($t->trainer->name ?? '-'), 'amount' => $t->amount,
                    'date' => $t->created_at, 'cat' => 'Gaji Trainer'
                ])
        );
        $recentAllExpenses = $recentAllExpenses->sortByDesc('date')->take(5)->values();

        // 12. Trainers and their balances
        $trainers = User::where(function($q) {
                $q->where('role', 'trainer')
                  ->orWhere('role', 'Trainer');
            })
            ->orderByDesc('wallet_balance')
            ->get();

        return view('admin.finance.index', compact(
            'totalOmzet',
            'pendapatanBersih',
            'salDoKas',
            'danaTertahan',
            'danaSiapCair',
            'eventRevenue',
            'courseRevenue',
            'eventSettledCount',
            'courseSettledCount',
            'revenueThisMonth',
            'totalExpenseThisMonth',
            'pendingExpensesCount',
            'paidCommissions',
            'monthlyRevenue',
            'pendingWithdrawalsCount',
            'topPerformers',
            'recentTransactions',
            'recentAllExpenses',
            'trainers'
        ));
    }

    public function export(Request $request)
    {
        $period = $request->get('period', 'this_month');
        $format = $request->get('format', 'pdf');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Determine Range
        if ($period == 'this_week') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
            $periodName = "Minggu Ini (" . $start->format('d M') . " - " . $end->format('d M') . ")";
        } elseif ($period == 'this_month') {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
            $periodName = "Bulan Ini (" . $start->format('F Y') . ")";
        } elseif ($period == 'per_6_months') {
            $start = Carbon::now()->subMonths(5)->startOfMonth();
            $end = Carbon::now()->endOfMonth();
            $periodName = "6 Bulan Terakhir (" . $start->format('M Y') . " - " . $end->format('M Y') . ")";
        } elseif ($period == 'per_year') {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now()->endOfYear();
            $periodName = "Tahun Ini (" . $start->format('Y') . ")";
        } elseif ($period == 'custom' && $startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            $periodName = "Periode " . $start->format('d/m/Y') . " - " . $end->format('d/m/Y');
        } else {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
            $periodName = "Bulan Ini (" . $start->format('F Y') . ")";
        }

        // Fetch Data
        // Fetch Data (Manual Only as per user request)

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

        $eventExpenses = \App\Models\EventExpense::with('event')
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'approved')
            ->get();

        // Transform Data for Table
        $transactions = [];


        foreach ($manualIncomes as $m) {
            $source = $m->event_id ? "Event: " . ($m->event->title ?? 'N/A') : ($m->course_id ? "Course: " . ($m->course->name ?? 'N/A') : "Manual");
            $transactions[] = [
                'date' => $m->created_at,
                'description' => $source . " (#" . $m->order_id . " - " . ($m->user->name ?? 'Guest') . ")",
                'method' => 'Transfer Manual',
                'status' => 'Settled',
                'amount' => $m->amount,
                'type' => 'income'
            ];
        }

        foreach ($commissions as $c) {
            $transactions[] = [
                'date' => $c->created_at,
                'description' => "Komisi Reseller: " . $c->description,
                'method' => 'Wallet Deduction',
                'status' => 'Paid',
                'amount' => $c->amount,
                'type' => 'expense'
            ];
        }

        foreach ($manualExpenses as $me) {
            $transactions[] = [
                'date' => $me->expense_date,
                'description' => "Pengeluaran Manual: " . $me->description . ($me->category ? " ({$me->category})" : ""),
                'method' => 'Cash/Transfer',
                'status' => 'Approved',
                'amount' => $me->amount,
                'type' => 'expense'
            ];
        }

        foreach ($trainerPayments as $tp) {
            $transactions[] = [
                'date' => $tp->created_at,
                'description' => "Gaji Trainer: " . ($tp->trainer->name ?? 'Unknown') . " (" . ($tp->title ?? $tp->notes) . ")",
                'method' => 'Transfer',
                'status' => 'Approved',
                'amount' => $tp->amount,
                'type' => 'expense'
            ];
        }

        foreach ($eventExpenses as $ee) {
            $transactions[] = [
                'date' => $ee->created_at,
                'description' => "Cost Event: " . ($ee->event->title ?? 'Unknown') . " - " . $ee->item,
                'method' => 'Transfer',
                'status' => 'Approved',
                'amount' => $ee->total,
                'type' => 'expense'
            ];
        }

        // Sort by date
        usort($transactions, function($a, $b) {
            return $b['date']->timestamp <=> $a['date']->timestamp;
        });

        // Totals
        $totalOmzet = collect($transactions)->where('type', 'income')->sum('amount');
        $eventRevenue = collect($transactions)
            ->where('type', 'income')
            ->filter(fn($t) => str_starts_with($t['description'], 'Event:'))
            ->sum('amount');
        $courseRevenue = collect($transactions)
            ->where('type', 'income')
            ->filter(fn($t) => str_starts_with($t['description'], 'Course:'))
            ->sum('amount');
        $totalCommissions = collect($transactions)->where('type', 'expense')->sum('amount');
        $netProfit = $totalOmzet - $totalCommissions;

        if ($format == 'excel') {
            return $this->exportToExcel($transactions, $periodName, $eventRevenue, $courseRevenue);
        }

        // Export to PDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        
        $html = view('admin.finance.report_pdf', compact(
            'transactions', 'periodName', 'start', 'end', 
            'totalOmzet', 'eventRevenue', 'courseRevenue', 'totalCommissions', 'netProfit'
        ))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="Laporan_Keuangan_' . now()->format('YmdHis') . '.pdf"');
    }

    private function exportToExcel($transactions, $periodName, $eventRevenue = 0, $courseRevenue = 0)
    {
        $filename = "Laporan_Keuangan_" . now()->format('YmdHis') . ".csv";
        $headers = [
            "Content-Type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        
        $callback = function() use ($transactions, $periodName, $eventRevenue, $courseRevenue) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8
            fputs($file, (chr(0xEF) . chr(0xBB) . chr(0xBF)));

            fputcsv($file, ['IDSPORA - LAPORAN KEUANGAN']);
            fputcsv($file, ['Periode:', $periodName]);
            fputcsv($file, []);

            // Summary
            $totalIn = collect($transactions)->where('type', 'income')->sum('amount');
            $totalOut = collect($transactions)->where('type', 'expense')->sum('amount');
            fputcsv($file, ['RINGKASAN']);
            fputcsv($file, ['Total Omzet', $totalIn]);
            fputcsv($file, ['  - Pendapatan Event', $eventRevenue]);
            fputcsv($file, ['  - Pendapatan Course', $courseRevenue]);
            fputcsv($file, ['Total Komisi', $totalOut]);
            fputcsv($file, ['Pendapatan Bersih', $totalIn - $totalOut]);
            fputcsv($file, []);

            // Headers
            fputcsv($file, ['Tanggal', 'Keterangan', 'Metode', 'Status', 'Jumlah', 'Jenis']);

            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t['date']->format('Y-m-d H:i'),
                    $t['description'],
                    $t['method'],
                    $t['status'],
                    $t['amount'],
                    strtoupper($t['type'])
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getMonthlyRevenue()
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('M');
            $year = $date->year;
            $monthNum = $date->month;


            $manual = ManualPayment::where('status', 'settled')
                ->whereMonth('created_at', $monthNum)
                ->whereYear('created_at', $year)
                ->sum('amount');

            $data[] = [
                'month' => $month,
                'revenue' => (float)$manual
            ];
        }
        return $data;
    }

    public function events()
    {
        $pendingWithdrawals = \App\Models\Withdrawal::where('status', 'pending')->count();
        
        $events = \App\Models\Event::withCount(['registrations as total_registrations', 'registrations as active_registrations' => function($q){
            $q->where('status', 'active');
        }])
            ->orderByDesc('event_date')
            ->orderByDesc('created_at')
            ->paginate(10);

        // Add revenue per event from ManualPayment
        foreach($events as $event) {
            $event->revenue = ManualPayment::where('event_id', $event->id)->where('status', 'settled')->sum('amount');
            $event->pending_revenue = ManualPayment::where('event_id', $event->id)->where('status', 'pending')->sum('amount');
        }

        return view('admin.finance.events', compact('events', 'pendingWithdrawals'));
    }

    public function courses()
    {
        $pendingWithdrawals = \App\Models\Withdrawal::where('status', 'pending')->count();
        
        $courses = \App\Models\Course::withCount(['enrollments as total_enrollments', 'enrollments as active_enrollments' => function($q){
            $q->where('status', 'active');
        }])->latest()->paginate(10);

        // Add revenue per course from ManualPayment
        foreach($courses as $course) {
            $course->revenue = ManualPayment::where('course_id', $course->id)->where('status', 'settled')->sum('amount');
            $course->pending_revenue = ManualPayment::where('course_id', $course->id)->where('status', 'pending')->sum('amount');
        }

        return view('admin.finance.courses', compact('courses', 'pendingWithdrawals'));
    }

    public function eventDetail($id)
    {
        $event = \App\Models\Event::with(['expenses'])->findOrFail($id);
        $pendingWithdrawals = \App\Models\Withdrawal::where('status', 'pending')->count();
        
        $transactions = ManualPayment::with('user')
            ->where('event_id', $id)
            ->latest()
            ->paginate(15);
            
        $totalIncome = ManualPayment::where('event_id', $id)->where('status', 'settled')->sum('amount');
        $opExpenses = $event->expenses_total; // from model getter
        
        // Commissions related to this event (if any logic links them, but for now we'll sum manual desc matches)
        $commissions = Referral::where('description', 'LIKE', '%' . $event->title . '%')->where('status', 'paid')->sum('amount');
        
        return view('admin.finance.event_detail', compact('event', 'transactions', 'totalIncome', 'opExpenses', 'commissions', 'pendingWithdrawals'));
    }

    public function courseDetail($id)
    {
        $course = \App\Models\Course::findOrFail($id);
        $pendingWithdrawals = \App\Models\Withdrawal::where('status', 'pending')->count();
        
        $transactions = ManualPayment::with('user')
            ->where('course_id', $id)
            ->latest()
            ->paginate(15);
            
        $totalIncome = ManualPayment::where('course_id', $id)->where('status', 'settled')->sum('amount');
        
        // Similarly for courses
        $commissions = Referral::where('description', 'LIKE', '%' . $course->name . '%')->where('status', 'paid')->sum('amount');
        
        return view('admin.finance.course_detail', compact('course', 'transactions', 'totalIncome', 'commissions', 'pendingWithdrawals'));
    }

    public function storeExpense(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'nullable|string'
        ]);

        Expense::create($request->all());

        return back()->with('success', 'Pengeluaran berhasil dicatat.');
    }

    /* ═══════════════════════════════════════════
     *  TRAINER MANAGEMENT (Finance)
     * ═══════════════════════════════════════════ */

    /**
     * List all trainers with their wallet balance + payout requests.
     */
    public function trainers(Request $request)
    {
        $minDisburse = 200000; // Minimum saldo pencairan course Rp 200.000

        // 1. Ambil semua trainer
        $trainers = User::where(function($q) {
                $q->where('role', 'trainer')
                  ->orWhere('role', 'Trainer');
            })
            ->orderByDesc('wallet_balance')
            ->get();

        // 2. Map data tambahan untuk list trainer (Saldo Course)
        foreach ($trainers as $t) {
            $t->total_paid = 0;
            $t->pending_payout = false;
            $t->can_disburse = ($t->wallet_balance ?? 0) >= $minDisburse;
        }

        // 3. Ambil event yang sudah selesai (untuk tab Fee Event)
        // Kriteria: ada trainer, ada ended_at, ended_at sudah lewat
        $endedEvents = Event::whereNotNull('trainer_id')
            ->whereNotNull('ended_at')
            ->where('ended_at', '<', now())
            ->with('trainer')
            ->get()
            ->filter(function ($event) {
                // Filter event yang BELUM memiliki record TrainerPayment (fee event)
                return !TrainerPayment::where('event_id', $event->id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->where('type', 'event_fee')
                    ->exists();
            });

        // 4. Permintaan fee event yang sedang pending
        $pendingEventFees = TrainerPayment::with(['trainer', 'event'])
            ->where('type', 'event_fee')
            ->where('status', 'pending')
            ->latest()
            ->get();

        // 5. Riwayat payout yang sudah disetujui
        $payoutHistory = TrainerPayment::with(['trainer', 'event', 'course'])
            ->where('status', 'approved')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.finance.trainers.index', compact(
            'trainers', 
            'endedEvents', 
            'pendingEventFees', 
            'payoutHistory', 
            'minDisburse'
        ));
    }

    /**
     * Admin mencairkan saldo course trainer.
     * Minimum pencairan: Rp 500.000
     */
    public function disburseCourseBalance(Request $request, $trainerId)
    {
        $trainer = User::findOrFail($trainerId);
        $minDisburse = 200000;

        if ($trainer->wallet_balance < $minDisburse) {
            return back()->with('error', 'Saldo trainer belum mencapai minimum pencairan Rp ' . number_format($minDisburse, 0, ',', '.'));
        }

        $request->validate([
            'proof_of_payment' => 'required|image|max:5120',
            'notes'            => 'nullable|string|max:500',
        ]);

        $proofPath = $request->file('proof_of_payment')->store('finance/proofs/trainers', 'public');
        $amount    = $trainer->wallet_balance;

        TrainerPayment::create([
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

        return back()->with('success', "Pencairan saldo Rp " . number_format($amount, 0, ',', '.') . " untuk trainer {$trainer->name} berhasil diproses.");
    }

    /**
     * Admin creates an event fee payout request for a completed event.
     */
    public function createEventFeeRequest(Request $request, $eventId)
    {
        $event = \App\Models\Event::with('trainer')->findOrFail($eventId);

        if (!$event->trainer_id) {
            return back()->with('error', 'Event ini tidak memiliki trainer yang terdaftar.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'notes'  => 'nullable|string|max:500',
        ]);

        TrainerPayment::create([
            'user_id'      => $event->trainer_id,
            'type'         => 'event_fee',
            'event_id'     => $event->id,
            'trainer_name' => $event->trainer->name ?? '-',
            'title'        => 'Fee Event: ' . $event->title,
            'amount'       => $request->amount,
            'status'       => 'pending',
            'notes'        => $request->notes,
        ]);

        return back()->with('success', 'Permintaan pencairan fee event "' . $event->title . '" berhasil dibuat dan menunggu proses pembayaran.');
    }

    /**
     * Admin approves an event fee payout (upload proof).
     */
    public function approveEventFeePayment(Request $request, $paymentId)
    {
        $payment = TrainerPayment::with('trainer')->findOrFail($paymentId);

        $request->validate([
            'proof_of_payment' => 'required|image|max:5120',
        ]);

        $proofPath = $request->file('proof_of_payment')->store('finance/proofs/events', 'public');

        $payment->update([
            'status'           => 'approved',
            'payment_date'     => now(),
            'payment_method'   => 'transfer',
            'proof_file'       => $proofPath,
        ]);

        // Notify trainer
        if ($payment->trainer) {
            \App\Models\TrainerNotification::create([
                'trainer_id' => $payment->trainer->id,
                'type'       => 'payout_processed',
                'title'      => 'Fee Event Dicairkan',
                'message'    => 'Fee event Anda sebesar Rp ' . number_format($payment->amount, 0, ',', '.') . ' telah dicairkan.',
                'data'       => ['amount' => $payment->amount, 'url' => route('trainer.finance')],
            ]);
        }

        return back()->with('success', 'Fee event berhasil dicairkan.');
    }

    /**
     * Admin rejects an event fee payout.
     */
    public function rejectEventFeePayment(Request $request, $paymentId)
    {
        $request->validate(['rejected_reason' => 'required|string|max:500']);
        $payment = TrainerPayment::findOrFail($paymentId);
        $payment->update(['status' => 'rejected', 'rejected_reason' => $request->rejected_reason]);

        return back()->with('success', 'Permintaan fee event ditolak.');
    }

    public function incomes(Request $request)
    {
        $query = \App\Models\ManualPayment::with(['user', 'event', 'course'])
            ->where('status', 'settled');

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        $incomes = $query->orderByDesc('created_at')->paginate(15)->appends($request->except('page'));

        // Total pemasukan berdasarkan filter aktif
        $filteredTotal = $query->sum('amount');

        // Saldo Kas Saat Ini (all-time, tidak terpengaruh filter)
        $totalOmzet       = \App\Models\ManualPayment::where('status', 'settled')->sum('amount');
        $paidCommissions  = \App\Models\Withdrawal::where('status', 'approved')->sum('amount');
        $totalExpenses    = \App\Models\Expense::where(function($q) { $q->where('status', 'approved')->orWhereNull('status'); })->sum('amount');
        $totalTrainerPay  = \App\Models\TrainerPayment::where(function($q) { $q->where('status', 'approved')->orWhereNull('status'); })->sum('amount');
        $totalEventExp    = \App\Models\EventExpense::where('status', 'approved')->sum('total');

        $currentBalance = $totalOmzet - $paidCommissions - $totalExpenses - $totalTrainerPay - $totalEventExp;

        // Label periode filter
        $filterLabel = null;
        if ($request->filled('month') || $request->filled('year')) {
            $m = $request->month ? \Carbon\Carbon::create()->month((int)$request->month)->translatedFormat('F') : 'Semua Bulan';
            $y = $request->year ?? date('Y');
            $filterLabel = "$m $y";
        }

        return view('admin.finance.transactions.incomes', compact('incomes', 'currentBalance', 'filteredTotal', 'filterLabel'));
    }

    public function storeIncome(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'received_date' => 'required|date'
        ]);

        \App\Models\ManualPayment::create([
            'amount' => $request->amount,
            'status' => 'settled',
            'method' => 'manual_external',
            'order_id' => 'MANUAL-' . time(),
            'metadata' => [
                'description' => $request->description,
                'received_date' => $request->received_date,
            ],
        ]);

        return back()->with('success', 'Pemasukan manual berhasil ditambahkan.');
    }

    public function expenses(Request $request)
    {
        $wQuery = \App\Models\Withdrawal::with('user');
        $tpQuery = \App\Models\TrainerPayment::with('trainer');
        $eeQuery = \App\Models\EventExpense::with('event');
        $geQuery = \App\Models\Expense::query();

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

        $withdrawals = $wQuery->latest()->paginate(10, ['*'], 'reseller_page')->appends($request->except('reseller_page'));
        $trainerPayments = $tpQuery->latest()->paginate(10, ['*'], 'trainer_page')->appends($request->except('trainer_page'));
        $eventExpenses = $eeQuery->latest()->paginate(10, ['*'], 'event_page')->appends($request->except('event_page'));
        $generalExpenses = $geQuery->latest()->paginate(10, ['*'], 'expense_page')->appends($request->except('expense_page'));

        $pendingWithdrawalsCount = \App\Models\Withdrawal::where('status', 'pending')->count();

        return view('admin.finance.transactions.expenses', compact('withdrawals', 'trainerPayments', 'eventExpenses', 'generalExpenses', 'pendingWithdrawalsCount'));
    }

    // --- APPROVAL LOGIC ---

    public function approveEventExpense(Request $request, $id)
    {
        $request->validate(['proof_of_payment' => 'required|image|max:5120']);
        $expense = \App\Models\EventExpense::findOrFail($id);
        
        $path = $request->file('proof_of_payment')->store('finance/proofs/events', 'public');
        $expense->update(['status' => 'approved', 'proof_of_payment' => $path]);

        return back()->with('success', 'Cost event berhasil disetujui.');
    }

    public function rejectEventExpense(Request $request, $id)
    {
        $request->validate(['rejected_reason' => 'required|string']);
        $expense = \App\Models\EventExpense::findOrFail($id);
        $expense->update(['status' => 'rejected', 'rejected_reason' => $request->rejected_reason]);

        return back()->with('success', 'Cost event ditolak.');
    }


    public function approveExpense(Request $request, $id)
    {
        $request->validate(['proof_of_payment' => 'required|image|max:5120']);
        $exp = \App\Models\Expense::findOrFail($id);
        
        $path = $request->file('proof_of_payment')->store('finance/proofs/manual', 'public');
        $exp->update(['status' => 'approved', 'proof_of_payment' => $path]);

        return back()->with('success', 'Pengeluaran manual berhasil disetujui.');
    }

    public function rejectExpense(Request $request, $id)
    {
        $request->validate(['rejected_reason' => 'required|string']);
        $exp = \App\Models\Expense::findOrFail($id);
        $exp->update(['status' => 'rejected', 'rejected_reason' => $request->rejected_reason]);

        return back()->with('success', 'Pengeluaran manual ditolak.');
    }

    public function downloadPayoutInvoice($id)
    {
        $payment = TrainerPayment::with('trainer', 'event')->findOrFail($id);

        return view('admin.finance.trainers.invoice', compact('payment'));
    }
}
