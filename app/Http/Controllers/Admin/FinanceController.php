<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ManualPayment;
use App\Models\Referral;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index()
    {
        // 1. Total Omzet (Gross Revenue) - Manual payments only as per user request
        $eventRevenue = ManualPayment::where('status', 'settled')->whereNotNull('event_id')->sum('amount');
        $courseRevenue = ManualPayment::where('status', 'settled')->whereNotNull('course_id')->sum('amount');
        $totalOmzet = $eventRevenue + $courseRevenue;

        // 2. Pendapatan Bersih (Net Profit)
        // paid reseller commissions
        $paidCommissions = Referral::where('status', 'paid')->sum('amount');
        $pendapatanBersih = $totalOmzet - $paidCommissions;

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

        // 4. Pending Withdrawals (for the "3 Request" urgent card)
        $pendingWithdrawals = \App\Models\Withdrawal::where('status', 'pending')->count();

        // 5. Top Performers (based on earnings/commissions)
        $topPerformers = \App\Models\User::join('referrals', 'users.id', '=', 'referrals.user_id')
            ->select('users.name', \DB::raw('SUM(referrals.amount) as total_commission'))
            ->where('referrals.status', 'paid')
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_commission', 'desc')
            ->limit(3)
            ->get();

        // 6. Recent Transactions
        $recentTransactions = ManualPayment::with(['user', 'event', 'course'])
            ->where('status', 'settled')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($m) {
                return [
                    'id' => $m->order_id,
                    'user' => $m->user->name ?? 'Guest',
                    'amount' => $m->amount,
                    'date' => $m->created_at,
                    'type' => 'Manual',
                    'source' => $m->event_id ? 'Event' : ($m->course_id ? 'Course' : 'Manual'),
                    'url' => $m->order_id ? route('invoice.manual', $m->order_id) : '#'
                ];
            });

        return view('admin.finance.index', compact(
            'totalOmzet',
            'pendapatanBersih',
            'danaTertahan',
            'danaSiapCair',
            'eventRevenue',
            'courseRevenue',
            'eventSettledCount',
            'courseSettledCount',
            'freeCount',
            'paidCount',
            'paidCommissions',
            'monthlyRevenue',
            'pendingWithdrawals',
            'topPerformers',
            'recentTransactions'
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

        $manual = ManualPayment::with('user')
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'settled')
            ->get();

        $commissions = Referral::with('referredUser')
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'paid')
            ->get();

        // Transform Data for Table
        $transactions = [];


        foreach ($manual as $m) {
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
        }])->latest()->paginate(10);

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
}
