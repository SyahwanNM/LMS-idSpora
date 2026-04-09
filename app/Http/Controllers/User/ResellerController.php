<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
    public function adminDashboard()
    {
        $activeResellersCount = User::whereNotNull('referral_code')->count();
        $totalSalesCount = Referral::where('status', 'paid')->count();
        $totalPendingReferrals = Referral::where('status', 'pending')->count();

        // Ambil top 5 reseller
        $topResellers = User::whereNotNull('referral_code')
            ->withSum(['referrals as total_earned' => function ($q) {
                $q->where('status', 'paid');
            }], 'amount')
            ->orderByDesc('total_earned')
            ->take(5)
            ->get();


        // Chart Data (12 Bulan Terakhir) - MENGHITUNG TRANSAKSI REFERRAL SUKSES
        $chartLabels = [];
        $chartValues = [];
        
        for ($i = 11; $i >= 0; $i--) { 
            $date = \Carbon\Carbon::now()->startOfMonth()->subMonths($i);
            
            // menghitung jumlah Transaksi Referral yang PAID
            $count = Referral::where('status', 'paid')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $chartLabels[] = $date->format('M Y'); 
            $chartValues[] = $count;
        }

        return view('admin.reseller.dashboard', compact(
            'activeResellersCount',
            'totalSalesCount',
            'totalPendingReferrals',
            'topResellers',
            'chartLabels',
            'chartValues'
        ));
    }

    // Fungsi 2: Khusus nampilin Tabel Data Reseller untuk Admin
    public function adminData()
    {
        $resellers = User::whereNotNull('referral_code')
            ->withCount('referrals')
            ->withSum(['referrals as total_earned' => function ($q) {
                $q->where('status', 'paid');
            }], 'amount')
            ->latest()
            ->get();

        return view('admin.reseller.data', compact('resellers'));
    }

    public function index()
    {
        $user = Auth::user();
        if (empty($user->referral_code)) {
            // Kalau belum punya, lempar ke halaman "Join"
            return view('reseller.join');
        }

        // --- 1. Statistik Dasar ---
        // Eager load referrals untuk performa
        $allReferrals = $user->referrals()->get();

        $totalEarnings = $allReferrals->where('status', 'paid')->sum('amount');
        $pendingEarnings = $allReferrals->where('status', 'pending')->sum('amount');
        $totalReferrals = $allReferrals->count();

        // Earnings & Referral Bulan Ini
        $referralsThisMonth = $allReferrals->where('created_at', '>=', now()->startOfMonth())->count();
        $earningsThisMonth = $allReferrals->where('status', 'paid')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('amount');

        // --- 2. Hitung Conversion Rate ---
        // Rumus: Conversion Rate = (Lunas / (Lunas + Ditolak)) x 100%

        // Yang SUDAH DICEK ADMIN (Paid & Rejected)
        $processedReferrals = $allReferrals->filter(function ($item) {
            return in_array(strtolower($item->status), ['paid', 'rejected']);
        });

        $totalProcessed = $processedReferrals->count();

        // Hitung yang SUKSES saja (Paid)
        $successfulReferrals = $processedReferrals->filter(function ($item) {
            return strtolower($item->status) === 'paid';
        })->count();

        // Hitung persentase
        $conversionRate = $totalProcessed > 0 ? ($successfulReferrals / $totalProcessed) * 100 : 0;

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

        // Persentase komisi mengikuti level badge reseller.
        $commissionRate = match ($level) {
            'Gold' => 0.15,
            'Silver' => 0.12,
            default => 0.10,
        };

        $courseProducts = Course::query()
            ->with('category:id,name')
            ->select('id', 'name', 'category_id', 'price', 'card_thumbnail')
            ->where('is_reseller_course', true)
            ->where('status', 'active')
            ->latest()
            ->get()
            ->map(function ($course) use ($commissionRate, $user) {
                $price = (float) ($course->price ?? 0);
                $courseThumb = (string) ($course->card_thumbnail ?? ''); // Source: courses.card_thumbnail
                $promoImage = $courseThumb !== ''
                    ? (str_starts_with($courseThumb, 'http://') || str_starts_with($courseThumb, 'https://')
                        ? $courseThumb
                        : asset('uploads/' . ltrim(str_replace('storage/', '', $courseThumb), '/')))
                    : asset('aset/poster.png');

                return [
                    'type' => 'Course',
                    'program' => $course->name,
                    'category' => $course->category->name ?? 'Course',
                    'price' => $price,
                    'commission_rate' => $commissionRate,
                    'commission_amount' => $price * $commissionRate,
                    'promo_image' => $promoImage,
                    'promo_filename' => 'promo-course-' . $course->id . '.jpg',
                    'referral_link' => route('courses.show', $course) . '?ref=' . urlencode((string) $user->referral_code),
                ];
            });

        $eventProducts = Event::query()
            ->select('id', 'title', 'jenis', 'price', 'image')
            ->where('is_reseller_event', true)
            ->where('is_published', true)
            ->latest()
            ->get()
            ->map(function ($event) use ($commissionRate, $user) {
                $price = (float) ($event->price ?? 0);
                $eventImage = (string) ($event->image ?? ''); // Source: events.image
                $promoImage = $eventImage !== ''
                    ? (str_starts_with($eventImage, 'http://') || str_starts_with($eventImage, 'https://')
                        ? $eventImage
                        : asset('uploads/' . ltrim(str_replace('storage/', '', $eventImage), '/')))
                    : asset('aset/poster.png');

                return [
                    'type' => 'Event',
                    'program' => $event->title,
                    'category' => !empty($event->jenis) ? $event->jenis : 'Event',
                    'price' => $price,
                    'commission_rate' => $commissionRate,
                    'commission_amount' => $price * $commissionRate,
                    'promo_image' => $promoImage,
                    'promo_filename' => 'promo-event-' . $event->id . '.jpg',
                    'referral_link' => route('events.show', $event) . '?ref=' . urlencode((string) $user->referral_code),
                ];
            });

        $commissionProducts = $courseProducts
            ->concat($eventProducts)
            ->sortByDesc('price')
            ->values();

        // --- 4. Data Tabel Riwayat ---
        // Ngambil 5 data terakhir beserta nama user yang diajak (referredUser)
        $history = $user->referrals()
            ->with('referredUser') // Eager load biar kenceng
            ->latest()
            ->take(5)
            ->get();

        // --- 5. Top Resellers ---
        // Ngambil 6 User dengan referral terbanyak (Global Leaderboard)
        $topResellers = User::withCount('referrals') // Hitung jumlah referral
            ->withSum(['referrals' => function ($q) { // Hitung total cuan mereka (status paid)
                $q->where('status', 'paid');
            }], 'amount')
            ->orderByDesc('referrals_count')
            ->take(6)
            ->get();

        // --- 6. Ranking User Saat Ini (Sticky Rank) ---
        // Hitung ada berapa orang yang referralnya LEBIH BANYAK dari kita
        $usersAhead = User::withCount('referrals')
            ->having('referrals_count', '>', $totalReferrals)
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
            'registrations',
            'commissionRate',
            'commissionProducts'
        ));
    }

    public function activate()
    {
        $user = Auth::user();

        // Double check biar gak generate ulang kalo udah ada
        if (empty($user->referral_code)) {
            // Bikin kode unik: 3 Huruf Random + 3 Angka Random (Contoh: AXY829)
            $code = strtoupper(Str::random(3) . rand(100, 999));

            // Cek biar gak duplikat di database (opsional tapi bagus)
            while (User::where('referral_code', $code)->exists()) {
                $code = strtoupper(Str::random(3) . rand(100, 999));
            }

            $user->referral_code = $code;
            $user->save();
        }

        // Ngebalikin ke halaman dashboard dengan pesan sukses
        return redirect()->route('reseller.index')->with('success', 'Selamat! Akun Reseller Anda telah aktif.');
    }

    public function checkReferral(Request $request)
    {
        $code = $request->input('code');
        $reseller = User::where('referral_code', $code)->first();

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
        // 1. Validasi Input dari Modal
        $request->validate([
            'amount' => 'required|numeric|min:50000', // Minimal 50rb
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_holder' => 'required|string',
        ]);

        $user = Auth::user();

        // 2. Cek apakah saldo user cukup?
        if ($user->wallet_balance < $request->amount) {
            return response()->json([
                'message' => 'Saldo Anda tidak mencukupi untuk penarikan ini.'
            ], 400);
        }

        // 3. Proses Transaksi (Database Transaction)
        DB::transaction(function () use ($request, $user) {

            // A. Kurangi saldo user (Wallet Balance)
            $user->wallet_balance = $user->wallet_balance - $request->amount;
            $user->save();

            // B. Simpan data ke tabel withdrawals
            Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_holder' => $request->account_holder,
                'status' => 'pending' // Status awalnya pending, nanti admin yang update ke 'paid' atau 'rejected'
            ]);
        });

        // 4. Kirim respon sukses ke JavaScript
        return response()->json([
            'message' => 'Permintaan penarikan berhasil diajukan!'
        ], 200);
    }
    public function downloadHistory()
    {
        $user = Auth::user();

        // 1. Ambil data history KECUALI yang statusnya 'rejected'
        $history = $user->referrals()
            ->where('status', '!=', 'rejected')
            ->with(['referredUser'])
            ->latest()
            ->get();

        // 2. Hitung total komisi LUNAS (paid) 
        $totalKomisi = $history->filter(function ($item) {
            return trim(strtolower($item->status)) === 'paid';
        })->sum('amount');

        // 3. Hitung total komisi PENDING
        $pendingKomisi = $history->filter(function ($item) {
            return trim(strtolower($item->status)) === 'pending';
        })->sum('amount');

        // Tampilkan view report
        return view('reseller.report', compact('user', 'history', 'totalKomisi', 'pendingKomisi'));
    }

    public function history()
    {
        $user = Auth::user();

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