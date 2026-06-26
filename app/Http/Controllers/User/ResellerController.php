<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Withdrawal;
use App\Models\Referral;
use App\Models\User;
use App\Models\Course;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ResellerController extends Controller
{
    // Fungsi 1: Khusus nampilin Dashboard & Grafik Admin
    public function adminDashboard(Request $request)
    {
        $activeResellersCount = User::where('role', 'user')->whereNotNull('referral_code')->count();
        $totalSalesCount = Referral::where('status', 'paid')->count();
        $totalPendingReferrals = Referral::where('status', 'pending')->count();

        // Ambil top 5 reseller
        $topResellers = User::where('role', 'user')
            ->whereNotNull('referral_code')
            ->withSum(['referrals as total_earned' => function ($q) {
                $q->where('status', 'paid');
            }], 'amount')
            ->orderByDesc('total_earned')
            ->take(5)
            ->get();

        // Chart Data berdasarkan range (7_days, 30_days, 3_months, 1_year)
        $range = $request->query('range', '7_days');
        if (!in_array($range, ['7_days', '30_days', '3_months', '1_year'])) {
            $range = '7_days';
        }

        $chartLabels = [];
        $chartValues = [];

        if ($range === '7_days') {
            for ($i = 6; $i >= 0; $i--) {
                $date = \Carbon\Carbon::now()->subDays($i);
                $count = Referral::where('status', 'paid')
                    ->whereDate('created_at', $date->toDateString())
                    ->count();
                $chartLabels[] = $date->translatedFormat('d M');
                $chartValues[] = $count;
            }
        } elseif ($range === '30_days') {
            for ($i = 29; $i >= 0; $i--) {
                $date = \Carbon\Carbon::now()->subDays($i);
                $count = Referral::where('status', 'paid')
                    ->whereDate('created_at', $date->toDateString())
                    ->count();
                $chartLabels[] = $date->translatedFormat('d M');
                $chartValues[] = $count;
            }
        } elseif ($range === '3_months') {
            for ($i = 11; $i >= 0; $i--) {
                $startOfWeek = \Carbon\Carbon::now()->startOfWeek()->subWeeks($i);
                $endOfWeek = $startOfWeek->copy()->endOfWeek();
                $count = Referral::where('status', 'paid')
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->count();
                $chartLabels[] = $startOfWeek->translatedFormat('d M');
                $chartValues[] = $count;
            }
        } else { // 1_year
            for ($i = 11; $i >= 0; $i--) {
                $date = \Carbon\Carbon::now()->startOfMonth()->subMonths($i);
                $count = Referral::where('status', 'paid')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
                $chartLabels[] = $date->translatedFormat('M Y');
                $chartValues[] = $count;
            }
        }

        return view('admin.reseller.dashboard', compact(
            'activeResellersCount',
            'totalSalesCount',
            'totalPendingReferrals',
            'topResellers',
            'chartLabels',
            'chartValues',
            'range'
        ));
    }

    // Fungsi 2: Khusus nampilin Tabel Data Reseller untuk Admin
    public function adminData()
    {
        $resellers = User::where('role', 'user')
            ->whereNotNull('referral_code')
            ->withCount('referrals')
            ->withSum(['referrals as total_earned' => function ($q) {
                $q->where('status', 'paid');
            }], 'amount')
            ->latest()
            ->get();

        return view('admin.reseller.data', compact('resellers'));
    }

    public function adminKatalog(Request $request)
    {
        $courses = Course::where('status', 'active')
            ->select('id', 'name', 'price', 'is_reseller_course', 'reseller_commission_bronze', 'reseller_commission_silver', 'reseller_commission_gold')
            ->latest()
            ->get();

        $events = Event::where('is_published', 1)
            ->active()
            ->select('id', 'title', 'price', 'is_reseller_event', 'reseller_commission_bronze', 'reseller_commission_silver', 'reseller_commission_gold')
            ->latest()
            ->get();

        return view('admin.reseller.katalog', compact('courses', 'events'));
    }

    public function adminLaporan(Request $request)
    {
        $totalResellers = User::where('role', 'user')->whereNotNull('referral_code')->count();
        $activeResellers = User::where('role', 'user')->whereNotNull('referral_code')->has('referrals')->count();
        $activeResellerProducts = Course::where('is_reseller_course', 1)->count() + Event::where('is_reseller_event', 1)->count();
        
        // Top performer reseller
        $topProducts = Referral::where('status', 'paid')
            ->select('description', \DB::raw('count(*) as sales_count'))
            ->groupBy('description')
            ->orderByDesc('sales_count')
            ->take(5)
            ->get();
            
        // Reseller status distribution count
        $statusActiveCount = User::where('role', 'user')->whereNotNull('referral_code')->where(fn($q) => $q->whereNull('reseller_status')->orWhere('reseller_status', 'active'))->count();
        $statusSuspendedCount = User::where('role', 'user')->whereNotNull('referral_code')->where('reseller_status', 'suspended')->count();
        $statusPendingCount = 0;
        $totalStatus = max(1, $statusActiveCount + $statusSuspendedCount);

        // Count reseller levels dynamically
        $resellersWithCount = User::where('role', 'user')
            ->whereNotNull('referral_code')
            ->withCount('referrals')
            ->get();
        
        $bronzeCount = 0;
        $silverCount = 0;
        $goldCount = 0;

        foreach ($resellersWithCount as $r) {
            $c = $r->referrals_count ?? 0;
            if ($c >= 151) {
                $goldCount++;
            } elseif ($c >= 51) {
                $silverCount++;
            } else {
                $bronzeCount++;
            }
        }

        $bronzePercent = $totalResellers > 0 ? round(($bronzeCount / $totalResellers) * 100) : 0;
        $silverPercent = $totalResellers > 0 ? round(($silverCount / $totalResellers) * 100) : 0;
        $goldPercent = $totalResellers > 0 ? round(($goldCount / $totalResellers) * 100) : 0;

        // Dynamic range filter
        $range = $request->query('range', '6_months');
        if (!in_array($range, ['30_days', '6_months', '1_year'])) {
            $range = '6_months';
        }

        $chartLabels = [];
        $chartValues = [];

        if ($range === '30_days') {
            $startDate = now()->subDays(30);
            $totalKomisiVal = Referral::where('status', 'paid')
                ->where('created_at', '>=', $startDate)
                ->sum('amount');

            $bestPeriod = Referral::where('status', 'paid')
                ->where('created_at', '>=', $startDate)
                ->select(
                    \DB::raw('SUM(amount) as total_amount'),
                    \DB::raw('DATE(created_at) as period')
                )
                ->groupBy('period')
                ->orderByDesc('total_amount')
                ->first();

            $bestMonthVal = $bestPeriod ? \Carbon\Carbon::parse($bestPeriod->period)->translatedFormat('d M Y') : '-';
            $labelTotalKomisi = 'Total Komisi 30 Hari Terakhir';
            $labelBestPeriod = 'Hari Terbaik';

            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $sum = Referral::where('status', 'paid')
                    ->whereDate('created_at', $date->toDateString())
                    ->sum('amount');
                $chartLabels[] = $date->translatedFormat('d M');
                $chartValues[] = (float) $sum;
            }
        } elseif ($range === '1_year') {
            $startDate = now()->subMonths(12);
            $totalKomisiVal = Referral::where('status', 'paid')
                ->where('created_at', '>=', $startDate)
                ->sum('amount');

            $bestPeriod = Referral::where('status', 'paid')
                ->where('created_at', '>=', $startDate)
                ->select(
                    \DB::raw('SUM(amount) as total_amount'),
                    \DB::raw('DATE_FORMAT(created_at, "%Y-%m") as period')
                )
                ->groupBy('period')
                ->orderByDesc('total_amount')
                ->first();

            $bestMonthVal = $bestPeriod ? \Carbon\Carbon::parse($bestPeriod->period . '-01')->translatedFormat('F Y') : '-';
            $labelTotalKomisi = 'Total Komisi 1 Tahun Terakhir';
            $labelBestPeriod = 'Bulan Terbaik';

            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $sum = Referral::where('status', 'paid')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('amount');
                $chartLabels[] = $date->translatedFormat('M Y');
                $chartValues[] = (float) $sum;
            }
        } else { // 6_months (default)
            $startDate = now()->subMonths(6);
            $totalKomisiVal = Referral::where('status', 'paid')
                ->where('created_at', '>=', $startDate)
                ->sum('amount');

            $bestPeriod = Referral::where('status', 'paid')
                ->where('created_at', '>=', $startDate)
                ->select(
                    \DB::raw('SUM(amount) as total_amount'),
                    \DB::raw('DATE_FORMAT(created_at, "%Y-%m") as period')
                )
                ->groupBy('period')
                ->orderByDesc('total_amount')
                ->first();

            $bestMonthVal = $bestPeriod ? \Carbon\Carbon::parse($bestPeriod->period . '-01')->translatedFormat('F Y') : '-';
            $labelTotalKomisi = 'Total Komisi 6 Bulan Terakhir';
            $labelBestPeriod = 'Bulan Terbaik';

            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $sum = Referral::where('status', 'paid')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('amount');
                $chartLabels[] = $date->translatedFormat('M Y');
                $chartValues[] = (float) $sum;
            }
        }

        $averageCommission = Referral::where('status', 'paid')->avg('amount') ?? 0;
        $totalPurchases = Referral::whereIn('status', ['paid', 'pending'])->count();
        $totalClicks = \DB::table('referral_clicks')->count();
        $conversionRate = $totalClicks > 0 ? ($totalPurchases / $totalClicks) * 100 : 0;

        $totalKomisi6BulanVal = $totalKomisiVal;
        $highestKomisiVal = $totalKomisiVal;

        return view('admin.reseller.laporan', compact(
            'totalResellers',
            'activeResellers',
            'activeResellerProducts',
            'topProducts',
            'statusActiveCount',
            'statusSuspendedCount',
            'statusPendingCount',
            'totalStatus',
            'bronzeCount',
            'silverCount',
            'goldCount',
            'bronzePercent',
            'silverPercent',
            'goldPercent',
            'totalKomisi6BulanVal',
            'highestKomisiVal',
            'bestMonthVal',
            'chartLabels',
            'chartValues',
            'range',
            'labelTotalKomisi',
            'labelBestPeriod',
            'averageCommission',
            'conversionRate'
        ));
    }

    public function adminExportExcel(Request $request)
    {
        $range = $request->query('range');
        $query = Referral::where('status', 'paid')->with(['user', 'referredUser']);

        if ($range === '7_days') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($range === '30_days') {
            $query->where('created_at', '>=', now()->subDays(30));
        } elseif ($range === '3_months') {
            $query->where('created_at', '>=', now()->subMonths(3));
        } elseif ($range === '6_months') {
            $query->where('created_at', '>=', now()->subMonths(6));
        } elseif ($range === '1_year') {
            $query->where('created_at', '>=', now()->subMonths(12));
        }

        $referrals = $query->latest()->get();

        $filename = 'laporan-reseller-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() use ($referrals) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            fputcsv($file, [
                'No',
                'Tanggal Transaksi',
                'Nama Reseller',
                'Email Reseller',
                'Nama Pembeli',
                'Email Pembeli',
                'Deskripsi Produk',
                'Komisi (IDR)',
                'Status'
            ], ';');

            foreach ($referrals as $index => $ref) {
                fputcsv($file, [
                    $index + 1,
                    $ref->created_at->format('Y-m-d H:i:s'),
                    $ref->user->name ?? '-',
                    $ref->user->email ?? '-',
                    $ref->referredUser->name ?? '-',
                    $ref->referredUser->email ?? '-',
                    $ref->description ?? '-',
                    (int) $ref->amount,
                    strtoupper($ref->status)
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function adminExportPdf(Request $request)
    {
        $range = $request->query('range');
        $type = $request->query('type', 'referrals');

        if ($range === '7_days') {
            $rangeText = '7 Hari Terakhir';
        } elseif ($range === '30_days') {
            $rangeText = '30 Hari Terakhir';
        } elseif ($range === '3_months') {
            $rangeText = '3 Bulan Terakhir';
        } elseif ($range === '6_months') {
            $rangeText = '6 Bulan Terakhir';
        } elseif ($range === '1_year') {
            $rangeText = '1 Tahun Terakhir';
        } else {
            $rangeText = 'Semua Periode';
        }

        $activeResellersCount = User::where('role', 'user')->whereNotNull('referral_code')->count();

        if ($type === 'withdrawals') {
            $query = Withdrawal::query()->with('user');

            if ($range === '7_days') {
                $query->where('created_at', '>=', now()->subDays(7));
            } elseif ($range === '30_days') {
                $query->where('created_at', '>=', now()->subDays(30));
            } elseif ($range === '3_months') {
                $query->where('created_at', '>=', now()->subMonths(3));
            } elseif ($range === '6_months') {
                $query->where('created_at', '>=', now()->subMonths(6));
            } elseif ($range === '1_year') {
                $query->where('created_at', '>=', now()->subMonths(12));
            }

            $withdrawals = $query->latest()->get();

            $totalWithdrawalsCount = $withdrawals->count();
            $totalApprovedAmount = $withdrawals->where('status', 'approved')->sum('amount');
            $totalPendingAmount = $withdrawals->where('status', 'pending')->sum('amount');

            return view('admin.reseller.print_withdraw_report', compact(
                'withdrawals',
                'totalWithdrawalsCount',
                'totalApprovedAmount',
                'totalPendingAmount',
                'activeResellersCount',
                'rangeText'
            ));
        } else {
            $query = Referral::where('status', 'paid')->with(['user', 'referredUser']);

            if ($range === '7_days') {
                $query->where('created_at', '>=', now()->subDays(7));
            } elseif ($range === '30_days') {
                $query->where('created_at', '>=', now()->subDays(30));
            } elseif ($range === '3_months') {
                $query->where('created_at', '>=', now()->subMonths(3));
            } elseif ($range === '6_months') {
                $query->where('created_at', '>=', now()->subMonths(6));
            } elseif ($range === '1_year') {
                $query->where('created_at', '>=', now()->subMonths(12));
            }

            $referrals = $query->latest()->get();

            $totalSalesCount = $referrals->count();
            $totalKomisi = $referrals->sum('amount');

            return view('admin.reseller.print_report', compact(
                'referrals',
                'totalSalesCount',
                'totalKomisi',
                'activeResellersCount',
                'rangeText'
            ));
        }
    }

    public function toggleResellerStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'type' => 'required|string|in:Course,Event',
            'status' => 'required|boolean'
        ]);

        $id = $request->id;
        $type = $request->type;
        $status = $request->status ? 1 : 0;

        if ($type === 'Course') {
            $product = Course::findOrFail($id);
            $product->is_reseller_course = $status;
            $product->save();
        } else {
            $product = Event::findOrFail($id);
            $product->is_reseller_event = $status;
            $product->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Status reseller berhasil diperbarui.'
        ]);
    }

    public function toggleResellerUserStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:users,id',
            'status' => 'required|string|in:active,suspended'
        ]);

        $user = User::findOrFail($request->id);
        
        if (empty($user->referral_code)) {
            return response()->json([
                'success' => false,
                'message' => 'User tersebut bukan reseller.'
            ], 422);
        }

        $user->reseller_status = $request->status;
        $user->save();

        $statusText = $request->status === 'active' ? 'diaktifkan' : 'ditangguhkan (suspended)';
        return response()->json([
            'success' => true,
            'message' => "Status keaktifan reseller \"{$user->name}\" berhasil {$statusText}."
        ]);
    }

    public function saveCommission(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'type' => 'required|string|in:Course,Event',
            'bronze' => 'required|integer|min:0|max:100',
            'silver' => 'required|integer|min:0|max:100',
            'gold' => 'required|integer|min:0|max:100',
        ]);

        $id = $request->id;
        $type = $request->type;
        $bronze = (int) $request->bronze;
        $silver = (int) $request->silver;
        $gold = (int) $request->gold;

        // Validation rule: Bronze <= Silver <= Gold
        if ($bronze > $silver) {
            return response()->json([
                'success' => false,
                'message' => 'Komisi Bronze tidak boleh lebih besar dari Silver.'
            ], 422);
        }
        if ($silver > $gold) {
            return response()->json([
                'success' => false,
                'message' => 'Komisi Silver tidak boleh lebih besar dari Gold.'
            ], 422);
        }

        if ($type === 'Course') {
            $product = Course::findOrFail($id);
            $product->reseller_commission_bronze = $bronze;
            $product->reseller_commission_silver = $silver;
            $product->reseller_commission_gold = $gold;
            $product->save();
        } else {
            $product = Event::findOrFail($id);
            $product->reseller_commission_bronze = $bronze;
            $product->reseller_commission_silver = $silver;
            $product->reseller_commission_gold = $gold;
            $product->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Komisi khusus berhasil disimpan.'
        ]);
    }

    private function isSuspended()
    {
        $user = Auth::user();
        return $user && $user->reseller_status === 'suspended';
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if ($this->isSuspended()) {
            return response()->view('reseller.suspended', compact('user'));
        }
        if (empty($user->referral_code)) {
            // Kalau belum punya, lempar ke halaman "Join"
            return view('reseller.join');
        }

        // --- 1. Statistik Dasar & Funnel ---
        $allReferrals = $user->referrals()->get();

        $totalEarnings = $allReferrals->where('status', 'paid')->sum('amount');
        $pendingEarnings = $allReferrals->where('status', 'pending')->sum('amount');
        
        // Total Klik Link
        $totalClicks = \DB::table('referral_clicks')->where('user_id', $user->id)->count();
        
        // Pendaftar Baru (Registrasi)
        $totalSignups = User::where('referrer_id', $user->id)->count();
        
        // Pembelian (Paid + Pending referrals)
        $totalPurchases = $allReferrals->whereIn('status', ['paid', 'pending'])->count();
        
        // Total referrals all time (for badge level calculations)
        $totalReferrals = $allReferrals->count();

        $paidReferralsCount = $allReferrals->where('status', 'paid')->count();

        // Earnings & Referral Bulan Ini
        $referralsThisMonth = $allReferrals->where('created_at', '>=', now()->startOfMonth())->count();
        $earningsThisMonth = $allReferrals->where('status', 'paid')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('amount');

        // --- 2. Hitung Conversion Rate ---
        // Rumus: Conversion Rate = (Pembelian / Total Klik) x 100%
        $conversionRate = $totalClicks > 0 ? ($totalPurchases / $totalClicks) * 100 : 0;

        // --- 2.5. Grafik Performa Reseller (6 Bulan Terakhir) ---
        $chartLabels = [];
        $clicksData = [];
        $signupsData = [];
        $purchasesData = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $year = $monthDate->year;
            $monthNum = $monthDate->month;

            $chartLabels[] = $monthDate->translatedFormat('F Y');

            $clicksData[] = \DB::table('referral_clicks')
                ->where('user_id', $user->id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $monthNum)
                ->count();

            $signupsData[] = User::where('referrer_id', $user->id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $monthNum)
                ->count();

            $purchasesData[] = Referral::where('user_id', $user->id)
                ->whereIn('status', ['paid', 'pending'])
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $monthNum)
                ->count();
        }

        // --- 3. Logika Level ---
        if ($totalReferrals >= 151) {
            $level = 'Gold';
            $progress = 100;
            $nextLevelTarget = 0;
        } elseif ($totalReferrals >= 51) {
            $level = 'Silver';
            // Progress dari 50 ke 150 (range 100)
            $progress = (($totalReferrals - 50) / 100) * 100;
            $nextLevelTarget = 151 - $totalReferrals;
        } else {
            $level = 'Bronze';
            // Progress dari 0 ke 50
            $progress = ($totalReferrals / 50) * 100;
            $nextLevelTarget = 51 - $totalReferrals;
        }

        $commissionRate = match ($level) {
            'Gold' => 0.15,
            'Silver' => 0.12,
            default => 0.10,
        };

        $courseProducts = Course::query()
            ->where('status', 'active')
            ->where('is_reseller_course', 1)
            ->with('category:id,name')
            ->select('id', 'name', 'category_id', 'price', 'card_thumbnail', 'reseller_commission_bronze', 'reseller_commission_silver', 'reseller_commission_gold')
            ->latest()
            ->get()
            ->map(function ($course) use ($level, $user) {
                $price = (float) ($course->price ?? 0);
                $courseThumb = (string) ($course->card_thumbnail ?? ''); // Source: courses.card_thumbnail
                $promoImage = $courseThumb !== ''
                    ? (str_starts_with($courseThumb, 'http://') || str_starts_with($courseThumb, 'https://')
                        ? $courseThumb
                        : asset('uploads/' . ltrim(str_replace('storage/', '', $courseThumb), '/')))
                    : asset('aset/poster.png');

                $bronze = $course->reseller_commission_bronze ?? 10;
                $silver = $course->reseller_commission_silver ?? 12;
                $gold = $course->reseller_commission_gold ?? 15;
                $pct = match ($level) {
                    'Gold' => $gold,
                    'Silver' => $silver,
                    default => $bronze,
                };
                $rate = $pct / 100;

                return [
                    'type' => 'Course',
                    'program' => $course->name,
                    'category' => $course->category->name ?? 'Course',
                    'price' => $price,
                    'commission_rate' => $rate,
                    'commission_amount' => $price * $rate,
                    'promo_image' => $promoImage,
                    'promo_filename' => 'promo-course-' . $course->id . '.jpg',
                    'referral_link' => route('courses.show', $course) . '?ref=' . urlencode((string) $user->referral_code),
                    'is_custom' => ($bronze != 10 || $silver != 12 || $gold != 15),
                ];
            });

        $eventProducts = Event::query()
            ->where('is_published', 1)
            ->active()
            ->where('is_reseller_event', 1)
            ->select('id', 'title', 'jenis', 'price', 'image', 'reseller_commission_bronze', 'reseller_commission_silver', 'reseller_commission_gold')
            ->latest()
            ->get()
            ->map(function ($event) use ($level, $user) {
                $price = (float) ($event->price ?? 0);
                $eventImage = (string) ($event->image ?? ''); // Source: events.image
                $promoImage = $eventImage !== ''
                    ? (str_starts_with($eventImage, 'http://') || str_starts_with($eventImage, 'https://')
                        ? $eventImage
                        : asset('uploads/' . ltrim(str_replace('storage/', '', $eventImage), '/')))
                    : asset('aset/poster.png');

                $bronze = $event->reseller_commission_bronze ?? 10;
                $silver = $event->reseller_commission_silver ?? 12;
                $gold = $event->reseller_commission_gold ?? 15;
                $pct = match ($level) {
                    'Gold' => $gold,
                    'Silver' => $silver,
                    default => $bronze,
                };
                $rate = $pct / 100;

                return [
                    'type' => 'Event',
                    'program' => $event->title,
                    'category' => !empty($event->jenis) ? $event->jenis : 'Event',
                    'price' => $price,
                    'commission_rate' => $rate,
                    'commission_amount' => $price * $rate,
                    'promo_image' => $promoImage,
                    'promo_filename' => 'promo-event-' . $event->id . '.jpg',
                    'referral_link' => route('events.show', $event) . '?ref=' . urlencode((string) $user->referral_code),
                    'is_custom' => ($bronze != 10 || $silver != 12 || $gold != 15),
                ];
            });

        $commissionProducts = $courseProducts
            ->concat($eventProducts)
            ->sortByDesc('price')
            ->values();

        $search = $request->input('search');
        if ($search) {
            $commissionProducts = $commissionProducts->filter(function ($item) use ($search) {
                return stripos($item['program'], $search) !== false
                    || stripos($item['category'], $search) !== false
                    || stripos($item['type'], $search) !== false;
            })->values();
        }

        $perPage = 5;
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;
        
        $commissionProducts = new \Illuminate\Pagination\LengthAwarePaginator(
            $commissionProducts->slice($offset, $perPage)->values(),
            $commissionProducts->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // --- 4. Data Tabel Riwayat ---
        // Mengambil data riwayat referral beserta relasi referredUser
        $history = $user->referrals()
            ->with('referredUser')
            ->latest()
            ->take(5)
            ->get();

        // --- 5. Top Resellers ---
        // Ngambil 6 User dengan total komisi terbanyak (Global Leaderboard)
        $topResellers = User::where('role', 'user')
            ->whereNotNull('referral_code')
            ->withCount(['referrals' => function ($q) {
                $q->where('status', 'paid');
            }])
            ->withSum(['referrals' => function ($q) {
                $q->where('status', 'paid');
            }], 'amount')
            ->orderByDesc('referrals_sum_amount') // Urutkan berdasarkan total komisi uang
            ->orderByDesc('referrals_count')      // Jika komisi sama, baru urutkan berdasarkan jumlah referral
            ->take(6)
            ->get();

        // --- 6. Ranking User Saat Ini (Sticky Rank) ---
        // Hitung ada berapa orang yang total komisi paid-nya LEBIH BANYAK dari kita
        $currentUserEarnings = $totalEarnings; 
        $usersAhead = User::where('role', 'user')
            ->whereNotNull('referral_code')
            ->withSum(['referrals' => function ($q) {
                $q->where('status', 'paid');
            }], 'amount')
            ->having(\DB::raw('COALESCE(referrals_sum_amount, 0)'), '>', $currentUserEarnings)
            ->count();

        // Ranking kita = jumlah orang di atas kita + 1
        $userRank = $usersAhead + 1;

        $registrations = \App\Models\EventRegistration::where('user_id', $user->id)
            ->with('event')
            ->latest()
            ->get();

        return view('reseller.index', compact(
            'user',
            'totalEarnings',
            'pendingEarnings',
            'totalClicks',
            'totalSignups',
            'totalPurchases',
            'totalReferrals',
            'referralsThisMonth',
            'earningsThisMonth',
            'conversionRate',
            'level',
            'progress',
            'nextLevelTarget',
            'history',
            'topResellers',
            'userRank',
            'paidReferralsCount',
            'registrations',
            'commissionRate',
            'commissionProducts',
            'search',
            'chartLabels',
            'clicksData',
            'signupsData',
            'purchasesData'
        ));
    }

    public function activate()
    {
        if ($this->isSuspended()) {
            return back()->with('error', 'Akun reseller Anda ditangguhkan.');
        }
        $user = Auth::user();

        // Validasi dan generate kode referral unik jika belum memilikinya
        if (empty($user->referral_code)) {
            $code = strtoupper(Str::random(3) . rand(100, 999));

            while (User::where('referral_code', $code)->exists()) {
                $code = strtoupper(Str::random(3) . rand(100, 999));
            }

            $user->referral_code = $code;
            $user->save();

            try {
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    \App\Models\UserNotification::create([
                        'user_id' => $admin->id,
                        'type' => 'reseller',
                        'title' => 'Reseller baru bergabung!',
                        'message' => "{$user->name} (" . ($user->email) . ") mendaftar sebagai reseller.",
                        'data' => ['url' => route('admin.reseller.data')],
                    ]);
                }
            } catch (\Throwable $e) {
                \Log::error('Reseller registration notification for admin failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('reseller.index')->with('success', 'Selamat! Akun Reseller Anda telah aktif.');
    }

    public function updateReferralCode(Request $request)
    {
        \Log::info('Headers received in updateReferralCode:', $request->headers->all());
        if ($this->isSuspended()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akun reseller Anda ditangguhkan.'], 403);
            }
            return back()->with('error', 'Akun reseller Anda ditangguhkan.');
        }
        $user = Auth::user();
        if (empty($user->referral_code)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Silakan aktifkan akun reseller Anda terlebih dahulu.'], 400);
            }
            return back()->with('error', 'Silakan aktifkan akun reseller Anda terlebih dahulu.');
        }

        // 1. Check if user has at least 5 successful referrals (purchases)
        $totalPurchases = Referral::where('user_id', $user->id)
            ->whereIn('status', ['paid', 'pending'])
            ->count();
        if ($totalPurchases < 5) {
            $msg = 'Fitur kustomisasi kode referral hanya tersedia jika kode Anda telah digunakan minimal 5 kali.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $msg], 400);
            }
            return back()->with('error', $msg);
        }

        // 2. Check if user has updated their code in the last 7 days (cooldown check)
        if ($user->referral_code_updated_at && $user->referral_code_updated_at->gt(now()->subDays(7))) {
            $nextAvailableDate = $user->referral_code_updated_at->copy()->addDays(7);
            $daysLeft = (int) ceil(now()->diffInDays($nextAvailableDate, false));
            $daysText = $daysLeft > 0 ? "dalam {$daysLeft} hari lagi" : "nanti";
            $msg = "Anda hanya dapat mengubah kode referral sekali seminggu. Silakan coba kembali {$daysText}.";
            if ($request->expectsJson()) {
                return response()->json(['message' => $msg], 400);
            }
            return back()->with('error', $msg);
        }

        // 3. Validate code formatting (alphanumeric, 3-10 chars)
        $request->validate([
            'referral_code' => [
                'required',
                'string',
                'min:3',
                'max:10',
                'regex:/^[A-Z0-9]+$/i', // Alphanumeric only, no spaces or symbols
            ]
        ], [
            'referral_code.required' => 'Kode referral baru harus diisi.',
            'referral_code.min' => 'Kode referral minimal terdiri dari 3 karakter.',
            'referral_code.max' => 'Kode referral maksimal terdiri dari 10 karakter.',
            'referral_code.regex' => 'Kode referral hanya boleh berisi huruf dan angka (tanpa spasi atau simbol).',
        ]);

        $newCode = strtoupper(trim((string)$request->input('referral_code')));

        // 4. Ensure it's not the same code
        if ($newCode === $user->referral_code) {
            $msg = 'Kode baru tidak boleh sama dengan kode saat ini.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $msg], 400);
            }
            return back()->with('error', $msg);
        }

        // 5. Ensure the new code is unique across all users
        $codeExists = User::where('referral_code', $newCode)->exists();
        if ($codeExists) {
            $msg = 'Kode referral ini sudah digunakan oleh pengguna lain. Silakan pilih kode lain.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $msg], 400);
            }
            return back()->with('error', $msg);
        }

        // 6. Save changes
        $user->referral_code = $newCode;
        $user->referral_code_updated_at = now();
        $user->save();

        $successMsg = 'Kode referral Anda berhasil diubah menjadi: ' . $newCode;
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg
            ]);
        }

        return back()->with('success', $successMsg);
    }

    public function checkReferral(Request $request)
    {
        $code = $request->input('code');
        $reseller = User::where('referral_code', $code)->first();

        if ($reseller && $reseller->reseller_status === 'suspended') {
            return response()->json([
                'valid' => false,
                'message' => 'Kode referral ini ditangguhkan dan tidak dapat digunakan.'
            ]);
        }

        // Pastikan kode valid dan user tidak pakai kodenya sendiri
        if ($reseller && $reseller->getKey() !== Auth::id()) {
            return response()->json([
                'valid' => true,
                'discount_percentage' => 10, // Ini diskon 10% buat pembeli
                'message' => 'Kode referral valid! Diskon 10% diterapkan.'
            ]);
        }

        return response()->json([
            'valid' => false,
            'message' => 'Kode referral tidak ditemukan atau tidak valid.'
        ]);
    }

    public function storeWithdraw(Request $request)
    {
        if ($this->isSuspended()) {
            return response()->json([
                'message' => 'Akun reseller Anda ditangguhkan. Penarikan dana dinonaktifkan.'
            ], 403);
        }

        // 1. Validasi Input dari Modal
        $request->validate([
            'amount' => 'required|numeric|min:50000', // Minimal 50rb
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_holder' => 'required|string',
        ]);

        $user = Auth::user();
        $accountNumber = str_replace(' ', '', $request->account_number);

        // 2. Cek kecukupan saldo user
        if ($user->wallet_balance < $request->amount) {
            return response()->json([
                'message' => 'Saldo Anda tidak mencukupi untuk penarikan ini.'
            ], 400);
        }

        // 3. Proses Transaksi (Database Transaction)
        DB::transaction(function () use ($request, $user, $accountNumber) {

            // A. Kurangi saldo user (Wallet Balance)
            $user->wallet_balance = $user->wallet_balance - $request->amount;
            $user->save();

            // B. Simpan data ke tabel withdrawals
            Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'bank_name' => $request->bank_name,
                'account_number' => $accountNumber,
                'account_holder' => $request->account_holder,
                'status' => 'pending'
            ]);
        });

        try {
            $admins = User::where('role', 'admin')->get();
            $amountFormatted = 'Rp ' . number_format($request->amount, 0, ',', '.');
            foreach ($admins as $admin) {
                \App\Models\UserNotification::create([
                    'user_id' => $admin->id,
                    'type' => 'reseller',
                    'title' => 'Pengajuan penarikan baru',
                    'message' => "Reseller {$user->name} mengajukan penarikan sebesar {$amountFormatted}.",
                    'data' => ['url' => route('admin.finance.expenses')],
                ]);
            }
        } catch (\Throwable $e) {
            \Log::error('Reseller withdrawal request notification for admin failed: ' . $e->getMessage());
        }

        // 4. Kirim respon sukses ke JavaScript
        return response()->json([
            'message' => 'Permintaan penarikan berhasil diajukan!'
        ], 200);
    }
    public function downloadHistory()
    {
        $user = Auth::user();
        if ($this->isSuspended()) {
            return response()->view('reseller.suspended', compact('user'));
        }

        // Ambil semua data history referral (termasuk yang rejected)
        $history = $user->referrals()
            ->with(['referredUser'])
            ->latest()
            ->get();

        // Hitung total komisi LUNAS (paid) 
        $totalKomisi = $history->filter(function ($item) {
            return trim(strtolower($item->status)) === 'paid';
        })->sum('amount');

        // Hitung total komisi PENDING
        $pendingKomisi = $history->filter(function ($item) {
            return trim(strtolower($item->status)) === 'pending';
        })->sum('amount');

        // Hitung total komisi DITOLAK (rejected)
        $rejectedKomisi = $history->filter(function ($item) {
            return trim(strtolower($item->status)) === 'rejected';
        })->sum('amount');

        // Tampilkan view report
        return view('reseller.report', compact('user', 'history', 'totalKomisi', 'pendingKomisi', 'rejectedKomisi'));
    }

    public function history()
    {
        $user = Auth::user();
        if ($this->isSuspended()) {
            return response()->view('reseller.suspended', compact('user'));
        }

        if (empty($user->referral_code)) {
            return redirect()->route('reseller.index');
        }

        $query = $user->referrals()->with('referredUser')->latest();

        if ($search = request('search')) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->whereHas('referredUser', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                })->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        if ($date_range = request('date_range')) {
            $dates = explode(' - ', $date_range);
            if (count($dates) === 2) {
                try {
                    $start = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $end = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('created_at', [$start, $end]);
                } catch (\Exception $e) {
                    // Ignore invalid format
                }
            }
        }

        $allReferrals = $user->referrals()->with('referredUser')->latest()->get();
        $history = $query->paginate(12)->withQueryString();

        $totalPaid = $allReferrals->where('status', 'paid')->sum('amount');
        $totalPending = $allReferrals->where('status', 'pending')->sum('amount');
        $totalRejected = $allReferrals->where('status', 'rejected')->sum('amount');

        return view('reseller.history', compact('user', 'history', 'totalPaid', 'totalPending', 'totalRejected'));
    }

    public function withdrawHistory()
    {
        $user = Auth::user();
        if ($this->isSuspended()) {
            return response()->view('reseller.suspended', compact('user'));
        }

        if (empty($user->referral_code)) {
            return redirect()->route('reseller.index');
        }

        $query = $user->withdrawals()->latest();

        if ($search = request('search')) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('bank_name', 'like', '%' . $search . '%')
                    ->orWhere('account_number', 'like', '%' . $search . '%')
                    ->orWhere('account_holder', 'like', '%' . $search . '%');
            });
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        if ($date_range = request('date_range')) {
            $dates = explode(' - ', $date_range);
            if (count($dates) === 2) {
                try {
                    $start = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $end = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('created_at', [$start, $end]);
                } catch (\Exception $e) {
                    // Ignore invalid format
                }
            }
        }

        $allWithdrawals = $user->withdrawals()->latest()->get();
        $withdrawals = $query->paginate(12)->withQueryString();

        $totalApproved = $allWithdrawals->where('status', 'approved')->sum('amount');
        $totalPending = $allWithdrawals->where('status', 'pending')->sum('amount');
        $totalRejected = $allWithdrawals->where('status', 'rejected')->sum('amount');

        return view('reseller.withdraw_history', compact('user', 'withdrawals', 'totalApproved', 'totalPending', 'totalRejected'));
    }

    public function downloadWithdrawHistory()
    {
        $user = Auth::user();
        if ($this->isSuspended()) {
            return response()->view('reseller.suspended', compact('user'));
        }

        // Ambil semua data penarikan dana user
        $withdrawals = $user->withdrawals()->latest()->get();

        // Hitung total yang sudah sukses cair (approved)
        $totalApproved = $withdrawals->filter(function ($item) {
            return strtolower($item->status) === 'approved';
        })->sum('amount');

        // Hitung total yang masih diproses (pending)
        $totalPending = $withdrawals->filter(function ($item) {
            return strtolower($item->status) === 'pending';
        })->sum('amount');

        // Tampilkan view cetak khusus withdraw
        return view('reseller.withdraw_report', compact('user', 'withdrawals', 'totalApproved', 'totalPending'));
    }
}
