<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ManualPayment;
use App\Models\EventRegistration;
use App\Models\Enrollment;

class FixStaleMidtransTokens extends Command
{
    protected $signature = 'midtrans:fix-stale';
    protected $description = 'Clear stale snap tokens from pending Midtrans payments and sync expired status';

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

    private function mapStatus(?string $ts, ?string $fs = null): string
    {
        $ts = strtolower((string) $ts);
        $fs = strtolower((string) $fs);
        if ($ts === 'capture')    return $fs === 'challenge' ? 'pending' : 'settled';
        if ($ts === 'settlement') return 'settled';
        if ($ts === 'pending')    return 'pending';
        if ($ts === 'expire')     return 'expired';
        return 'rejected';
    }

    public function handle(): int
    {
        try {
            $this->configureMidtrans();
        } catch (\Throwable $e) {
            $this->error('Midtrans tidak terkonfigurasi: ' . $e->getMessage());
            return self::FAILURE;
        }

        $payments = ManualPayment::where('method', 'midtrans')
            ->where('status', 'pending')
            ->whereNotNull('order_id')
            ->get();

        if ($payments->isEmpty()) {
            $this->info('Tidak ada pending Midtrans payment.');
            return self::SUCCESS;
        }

        $this->info("Memproses {$payments->count()} pending payment(s)...");

        foreach ($payments as $payment) {
            // 1. Clear stale snap_token so new token with correct finish URL is generated
            $meta = (array) ($payment->metadata ?? []);
            $hadToken = isset($meta['snap_token']);
            unset($meta['snap_token'], $meta['snap_token_created_at']);
            $payment->metadata = $meta;

            // 2. Check actual status from Midtrans
            try {
                $status    = (array) \Midtrans\Transaction::status($payment->order_id);
                $newStatus = $this->mapStatus(
                    $status['transaction_status'] ?? null,
                    $status['fraud_status'] ?? null
                );

                if ($newStatus !== 'pending') {
                    $payment->status = $newStatus;
                    $payment->save();

                    // Update related entities
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

                    $this->line("  [{$newStatus}] {$payment->order_id}");
                } else {
                    // Still pending — just clear the stale token
                    $payment->save();
                    $this->line("  [token cleared] {$payment->order_id}" . ($hadToken ? ' (had stale token)' : ''));
                }
            } catch (\Throwable $e) {
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
                        $this->line("  [expired/token-stale] {$payment->order_id} (token age: {$tokenAgeHours}h)");
                    } else {
                        // Token masih valid, user belum buka popup → biarkan pending, hanya clear token
                        $payment->save(); // simpan perubahan metadata (token sudah di-unset di atas)
                        $this->line("  [skip/not-yet-opened] {$payment->order_id} (token age: {$tokenAgeHours}h)");
                    }
                } else {
                    // Error lain → tetap clear token tapi jangan ubah status
                    $payment->save();
                    $this->warn("  [token cleared, status unknown] {$payment->order_id}: {$e->getMessage()}");
                }
            }
        }

        $this->info('Selesai.');
        return self::SUCCESS;
    }
}
