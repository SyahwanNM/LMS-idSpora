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

        // 1. Hitung Statistik Utama
        $totalEarnings = $user->referrals()->where('status', 'paid')->sum('amount');
        $pendingEarnings = $user->referrals()->where('status', 'pending')->sum('amount');
        
        // Hitung total referral (jumlah orang yang diajak)
        // hitung dari tabel referrals
        $totalReferrals = $user->referrals()->count();
        
        // Referral bulan ini
        $referralsThisMonth = $user->referrals()
            ->whereMonth('created_at', now()->month)
            ->count();

        // Earnings bulan ini
        $earningsThisMonth = $user->referrals()
            ->where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        // 2. Tentukan Level (Bronze, Silver, Gold)
        // Logika menghitung berdasarkan jumlah referral
        if ($totalReferrals > 150) {
            $level = 'Gold';
            $progress = 100;
            $nextLevelTarget = 0; // Max level
        } elseif ($totalReferrals > 50) {
            $level = 'Silver';
            // Hitung progress bar (scale 0-100 untuk range 50-150)
            $progress = (($totalReferrals - 50) / 100) * 100;
            $nextLevelTarget = 150 - $totalReferrals;
        } else {
            $level = 'Bronze';
            $progress = ($totalReferrals / 50) * 100;
            $nextLevelTarget = 50 - $totalReferrals;
        }

        // 3. Ambil Riwayat Referral (untuk tabel Riwayat)
        $history = $user->referrals()->latest()->take(5)->get();

        // 4. Top Resellers (Dummy atau Real Query)
        // Ambil user dengan referral terbanyak
        $topResellers = User::withCount('referrals')
            ->orderBy('referrals_count', 'desc')
            ->take(5)
            ->get();

        return view('reseller.index', [
            'user' => $user,
            'totalEarnings' => $totalEarnings,
            'pendingEarnings' => $pendingEarnings,
            'totalReferrals' => $totalReferrals,
            'referralsThisMonth' => $referralsThisMonth,
            'earningsThisMonth' => $earningsThisMonth,
            'level' => $level,
            'progress' => $progress,
            'nextLevelTarget' => $nextLevelTarget,
            'history' => $history,
            'topResellers' => $topResellers,
            // Variable khusus untuk modal withdraw
            'availableBalance' => $user->wallet_balance 
        ]);
    }

    public function storeWithdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:50000',
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_holder' => 'required|string',
        ]);

        $user = Auth::user();

        // Cek saldo cukup gak?
        if ($user->wallet_balance < $request->amount) {
            return response()->json(['message' => 'Saldo tidak mencukupi'], 400);
        }

        DB::transaction(function () use ($request, $user) {
            // 1. Kurangi Saldo User
            $user->wallet_balance -= $request->amount;
            $user->save();

            // 2. Catat Withdrawal
            Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_holder' => $request->account_holder,
                'status' => 'pending'
            ]);
        });

        return response()->json(['message' => 'Penarikan berhasil diajukan!']);
    }
}