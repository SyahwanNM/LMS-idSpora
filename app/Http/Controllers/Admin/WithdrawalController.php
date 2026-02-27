<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of withdrawal requests.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $withdrawals = Withdrawal::with('user')
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.finance.withdrawals.index', compact('withdrawals', 'status'));
    }

    /**
     * Approve a withdrawal request.
     */
    public function approve(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'proof_of_transfer' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('proof_of_transfer')) {
            $file = $request->file('proof_of_transfer');
            $filename = 'proof_' . time() . '_' . $withdrawal->id . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path('uploads/proofs'), $filename);
            $proofPath = 'proofs/' . $filename;
        }

        $withdrawal->update([
            'status' => 'approved',
            'proof_of_transfer' => $proofPath ?? null,
        ]);

        // Send Notification
        $this->notifyUser($withdrawal, 'approved');

        return redirect()->back()->with('success', 'Penarikan berhasil disetujui dan bukti transfer telah diunggah.');
    }

    /**
     * Reject a withdrawal request.
     */
    public function reject(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'rejected_reason' => 'required|string|max:500',
        ]);

        \DB::transaction(function () use ($withdrawal, $request) {
            $withdrawal->update([
                'status' => 'rejected',
                'rejected_reason' => $request->rejected_reason,
            ]);

            // Refund the user's wallet balance
            if ($withdrawal->user) {
                $withdrawal->user->increment('wallet_balance', $withdrawal->amount);
            }
        });

        // Send Notification
        $this->notifyUser($withdrawal, 'rejected');

        return redirect()->back()->with('success', 'Penarikan berhasil ditolak dan saldo telah dikembalikan ke user.');
    }

    /**
     * Internal helper to notify user via WA and Email.
     */
    private function notifyUser(Withdrawal $withdrawal, $status)
    {
        $user = $withdrawal->user;
        if (!$user) return;

        $amountFormatted = 'Rp ' . number_format($withdrawal->amount, 0, ',', '.');
        
        if ($status === 'approved') {
            $message = "Halo {$user->name}, pengajuan penarikan dana Anda sebesar {$amountFormatted} telah DISETUJUI dan berhasil ditransfer ke rekening {$withdrawal->bank_name} ({$withdrawal->account_number}). Silakan cek mutasi rekening Anda. Terima kasih!";
        } else {
            $message = "Halo {$user->name}, pengajuan penarikan dana Anda sebesar {$amountFormatted} telah DITOLAK dengan alasan: {$withdrawal->rejected_reason}. Silakan hubungi admin untuk informasi lebih lanjut.";
        }

        // WhatsApp Notification (Fonnte)
        $token = env('FONNTE_TOKEN');
        if ($token && $user->phone) {
            try {
                Http::withHeaders([
                    'Authorization' => $token,
                ])->post('https://api.fonnte.com/send', [
                    'target' => $user->phone,
                    'message' => $message,
                ]);
            } catch (\Exception $e) {
                \Log::error('Withdrawal WA Notify Error: ' . $e->getMessage());
            }
        }

        // Email Notification
        if ($user->email) {
            try {
                // You might want to create a dedicated Mailable class for this
                // For now, I'll use raw text or generic mailable if available
                Mail::raw($message, function ($mail) use ($user, $status) {
                    $subject = $status === 'approved' ? 'Penarikan Dana Disetujui' : 'Penarikan Dana Ditolak';
                    $mail->to($user->email)->subject("[IdSpora Admin] {$subject}");
                });
            } catch (\Exception $e) {
                \Log::error('Withdrawal Email Notify Error: ' . $e->getMessage());
            }
        }
    }
}
