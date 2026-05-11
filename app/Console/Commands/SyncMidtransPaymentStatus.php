<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ManualPayment;
use App\Models\EventRegistration;
use App\Models\Enrollment;

class SyncMidtransPaymentStatus extends Command
{
    protected $signature = 'midtrans:sync-status
                            {--limit=50 : Max payments to check per run}
                            {--older-than=15 : Only check payments older than N minutes}';

    protected $description = 'Sync pending Midtrans payment statuses from Midtrans API (for local/dev where webhook cannot reach)';

    private function configureMidtrans(): void
    {
        $serverKey = (string) config('midtrans.server_key');
        if (trim($serverKey) === '') {
            throw new \RuntimeException('Midtrans server key belum dikonfigurasi.');
        }
        \Midtrans\Config::$serverKey    = $serverKey;
        \Midtrans\Config::$isProduction = (bool) config('midtrans.is_production', false);
        \Midtrans\Config::$isSanitized  = (bool) config('midtrans.sanitize', true);
        \Midtrans\Config::$is3ds        = (bool) config('midtrans.3ds', true);
    }

    private function mapStatus(?string $transactionStatus, ?string $fraudStatus = null): string
    {
        $ts = strtolower((string) $transactionStatus);
        $fs = strtolower((string) $fraudStatus);

        if ($ts === 'capture')    return $fs === 'challenge' ? 'pending' : 'settled';
        if ($ts === 'settlement') return 'settled';
        if ($ts === 'pending')    return 'pending';
        if ($ts === 'expire')     return 'expired';

        return 'rejected'; // deny/cancel/failure
    }

    public function handle(): int
    {
        try {
            $this->configureMidtrans();
        } catch (\Throwable $e) {
            $this->error('Midtrans tidak terkonfigurasi: ' . $e->getMessage());
            return self::FAILURE;
        }

        $limit      = (int) $this->option('limit');
        $olderThan  = (int) $this->option('older-than');

        // Only check payments that have been pending for at least N minutes
        $payments = ManualPayment::query()
            ->where('method', 'midtrans')
            ->where('status', 'pending')
            ->whereNotNull('order_id')
            ->where('created_at', '<=', now()->subMinutes($olderThan))
            ->orderBy('created_at')
            ->limit($limit)
            ->get();

        if ($payments->isEmpty()) {
            $this->info('Tidak ada pending Midtrans payment yang perlu dicek.');
            return self::SUCCESS;
        }

        $this->info("Mengecek {$payments->count()} pending payment(s)...");

        $updated = 0;
        foreach ($payments as $payment) {
            try {
                $status      = (array) \Midtrans\Transaction::status($payment->order_id);
                $newStatus   = $this->mapStatus(
                    $status['transaction_status'] ?? null,
                    $status['fraud_status'] ?? null
                );

                if ($newStatus === $payment->status) {
                    continue; // no change
                }

                DB::beginTransaction();

                $wasSettled      = $payment->status === 'settled';
                $payment->status = $newStatus;
                $payment->metadata = array_merge((array) ($payment->metadata ?? []), [
                    'synced_at'          => now()->toIso8601String(),
                    'synced_status'      => $newStatus,
                    'midtrans_status'    => $status,
                ]);
                $payment->save();

                // Activate on settled
                if (!$wasSettled && $newStatus === 'settled') {
                    if ($payment->event_registration_id) {
                        $reg = EventRegistration::find($payment->event_registration_id);
                        if ($reg && $reg->status !== 'active') {
                            $reg->status = 'active';
                            $reg->payment_verified_at = now();
                            $reg->save();
                        }
                    }
                    if ($payment->enrollment_id) {
                        $enr = Enrollment::find($payment->enrollment_id);
                        if ($enr && $enr->status !== 'active') {
                            $enr->status = 'active';
                            $enr->save();
                        }
                    }
                }

                // Mark expired/rejected on related entities
                if (in_array($newStatus, ['expired', 'rejected'], true)) {
                    if ($payment->event_registration_id) {
                        $reg = EventRegistration::find($payment->event_registration_id);
                        if ($reg && !in_array($reg->status, ['active', 'canceled'], true)) {
                            $reg->status = $newStatus;
                            $reg->save();
                        }
                    }
                    if ($payment->enrollment_id) {
                        $enr = Enrollment::find($payment->enrollment_id);
                        if ($enr && $enr->status !== 'active') {
                            $enr->status = $newStatus;
                            $enr->save();
                        }
                    }
                }

                DB::commit();
                $updated++;
                $this->line("  [{$newStatus}] order_id={$payment->order_id}");

            } catch (\Throwable $e) {
                DB::rollBack();
                // 404 dari Midtrans = order belum pernah dicharge (user belum buka popup Snap).
                // Ini NORMAL selama snap token masih dalam masa berlaku (< 24 jam).
                // Hanya set expired jika token sudah > 24 jam atau tidak ada token sama sekali.
                if (str_contains($e->getMessage(), '404') || str_contains(strtolower($e->getMessage()), 'not found')) {
                    $tokenCreatedAt = data_get($payment->metadata, 'snap_token_created_at');
                    $tokenAgeHours  = $tokenCreatedAt
                        ? now()->diffInHours(\Carbon\Carbon::parse($tokenCreatedAt))
                        : 25; // tidak ada token → anggap sudah expired

                    if ($tokenAgeHours >= 24) {
                        // Token sudah kedaluwarsa → set expired
                        $payment->status = 'expired';
                        $payment->save();

                        if ($payment->event_registration_id) {
                            $reg = EventRegistration::find($payment->event_registration_id);
                            if ($reg && !in_array($reg->status, ['active', 'canceled'], true)) {
                                $reg->status = 'expired';
                                $reg->save();
                            }
                        }
                        if ($payment->enrollment_id) {
                            $enr = Enrollment::find($payment->enrollment_id);
                            if ($enr && $enr->status !== 'active') {
                                $enr->status = 'expired';
                                $enr->save();
                            }
                        }
                        $updated++;
                        $this->line("  [expired/token-stale] order_id={$payment->order_id} (token age: {$tokenAgeHours}h)");
                    } else {
                        // Token masih valid, user belum buka popup → biarkan pending
                        $this->line("  [skip/not-yet-opened] order_id={$payment->order_id} (token age: {$tokenAgeHours}h)");
                    }
                } else {
                    Log::warning('SyncMidtransPaymentStatus: failed to check order', [
                        'order_id' => $payment->order_id,
                        'error'    => $e->getMessage(),
                    ]);
                }
            }
        }

        $this->info("Selesai. {$updated} payment(s) diupdate.");
        return self::SUCCESS;
    }
}
