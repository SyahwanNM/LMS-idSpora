<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Withdrawal;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ResellerController extends Controller
{
    public function index()
{
    $user = Auth::user();

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
    // Asumsi: 'paid' berarti orangnya beneran beli course
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
    // Pastikan di Model Referral sudah ada relasi 'referredUser' ya
    $history = $user->referrals()
        ->with('referredUser') // Eager load biar kenceng
        ->latest()
        ->take(5)
        ->get();

    // --- 5. Top Resellers ---
    // Ambil 5 User dengan referral terbanyak (Global Leaderboard)
    $topResellers = User::withCount('referrals') // Hitung jumlah referral
        ->withSum(['referrals' => function($q) { // Hitung total cuan mereka (status paid)
            $q->where('status', 'paid');
        }], 'amount')
        ->orderByDesc('referrals_count')
        ->take(6) // Ambil Top 6
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
        'topResellers'
    ));
}

    public function storeWithdraw(Request $request)
{
    // 1. Validasi Input dari Modal
    $request->validate([
        'amount' => 'required|numeric|min:50000', // Minimal 50rb
        'bank_name' => 'required|string',
        'account_number' => 'required|string', // String karena no rek bisa mulai dari angka 0
        'account_holder' => 'required|string',
    ]);

    $user = Auth::user();

    // 2. Cek apakah saldo user cukup?
    // Kita cek saldo di database, bukan dari tampilan HTML (biar gak dicurangi)
    if ($user->wallet_balance < $request->amount) {
        // Balikin error code 400 kalau saldo kurang
        return response()->json([
            'message' => 'Saldo Anda tidak mencukupi untuk penarikan ini.'
        ], 400); 
    }

    // 3. Proses Transaksi (Database Transaction)
    // Kita pakai 'transaction' supaya kalau gagal simpan history, saldo gak kepotong (harus sukses dua-duanya)
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
            'status' => 'pending' // Status awal pending, nunggu admin transfer
        ]);
    });

    // 4. Kirim respon sukses ke JavaScript
    return response()->json([
        'message' => 'Permintaan penarikan berhasil diajukan!'
    ], 200);
}
}