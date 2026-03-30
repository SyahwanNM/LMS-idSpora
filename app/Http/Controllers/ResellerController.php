<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Withdrawal;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ResellerController extends Controller
{
    public function admin()
    {
        // 1. Stats
        $activeResellersCount = User::whereNotNull('referral_code')->count();
        $totalSalesCount = Referral::where('status', 'paid')->count();
        $totalPendingReferrals = Referral::where('status', 'pending')->count();
        $pendingRequestsCount = Withdrawal::where('status', 'pending')->count(); // Tetap diambil untuk record tapi mungkin tidak ditampilkan utuh sebagai menu penarikan di sini

        // 2. Resellers List
        $resellers = User::whereNotNull('referral_code')
            ->withCount('referrals')
            ->withSum(['referrals as total_earned' => function($q) {
                $q->where('status', 'paid');
            }], 'amount')
            ->latest()
            ->get();

        // 3. Top Performer
        $topResellers = User::whereNotNull('referral_code')
            ->withSum(['referrals as total_earned' => function($q) {
                $q->where('status', 'paid');
            }], 'amount')
            ->orderByDesc('total_earned')
            ->take(5)
            ->get();

        // 4. Chart Data (7 days)
        $chartLabels = [];
        $chartValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->subDays($i);
            $count = User::whereNotNull('referral_code')
                ->whereDate('created_at', $date->toDateString())
                ->count();
            $chartLabels[] = $date->format('D');
            $chartValues[] = $count;
        }

        return view('admin.reseller', compact(
            'activeResellersCount',
            'totalSalesCount',
            'totalPendingReferrals',
            'resellers',
            'topResellers',
            'chartLabels',
            'chartValues'
        ));
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
        // Rumus: (Referral yang statusnya 'paid' / Total Referral) * 100
        $successfulReferrals = $allReferrals->where('status', 'paid')->count();
        $conversionRate = $totalReferrals > 0 ? ($successfulReferrals / $totalReferrals) * 100 : 0;

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

        // --- 4. Data Tabel Riwayat ---
        // Ambil 5 data terakhir beserta nama user yang diajak (referredUser)
        $history = $user->referrals()
            ->with('referredUser') // Eager load biar kenceng
            ->latest()
            ->take(5)
            ->get();

        // --- 5. Top Resellers ---
        // Ambil 6 User dengan referral terbanyak (Global Leaderboard)
        $topResellers = User::withCount('referrals') // Hitung jumlah referral
            ->withSum(['referrals' => function($q) { // Hitung total cuan mereka (status paid)
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
            'registrations'
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
                'status' => 'pending' // Status awal pending
            ]);
        });

        // 4. Kirim respon sukses ke JavaScript
        return response()->json([
            'message' => 'Permintaan penarikan berhasil diajukan!'
        ], 200);
    }
}