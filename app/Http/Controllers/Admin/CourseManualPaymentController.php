<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\ManualPayment;
use App\Models\PaymentProof;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherRedemption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Models\Referral;
use App\Models\TrainerNotification;
use App\Mail\PaymentInvoiceMail;

class CourseManualPaymentController extends Controller
{
    private const REJECTION_REASONS = [
        'Nominal pembayaran kurang',
        'Nominal pembayaran lebih',
        'Gambar bukti pembayaran blur/buram. Silahkan kirim ulang',
        'Pembayaran dinyatakan tidak valid',
    ];

    public function upload(Request $request, Course $course): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $isFree = (int) ($course->price ?? 0) <= 0;
        if ($isFree) {
            return redirect()->route('course.payment', $course->id)
                ->with('error', 'Course ini gratis. Gunakan tombol Study Now.');
        }

        $paymentMethod = trim((string) $request->input('payment_method', 'midtrans'));
        if ($paymentMethod !== 'transfer') {
            return redirect()->back()->with('error', 'Metode pembayaran tidak valid untuk upload manual.');
        }

        $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,webp|max:1024',
        ], [
            'payment_proof.required' => 'Bukti transfer wajib diupload.',
            'payment_proof.max' => 'Ukuran file maksimal 1 MB.',
            'payment_proof.mimes' => 'Format file harus JPG, JPEG, PNG, atau WebP.',
        ]);

        $baseAmount = (float) ($course->hasDiscount() ? $course->discounted_price : $course->price);
        $rawReferralCode = trim((string) $request->input('referral_code', ''));
        $referralCode = null;
        if ($rawReferralCode !== '' && (bool) ($course->is_reseller_course ?? false)) {
            $referrer = User::where('referral_code', $rawReferralCode)->first();
            if ($referrer && (int) $referrer->id !== (int) $user->id && $referrer->reseller_status !== 'suspended') {
                $referralCode = $rawReferralCode;
                $baseAmount = round($baseAmount * 0.90, 2);
            }
        }

        $voucherCode = trim((string) $request->input('voucher_code', ''));
        $voucherRedemptionId = null;
        $discountAmount = 0.0;
        if ($voucherCode !== '') {
            $redemption = VoucherRedemption::where('user_id', $user->id)
                ->where('code', $voucherCode)
                ->first();

            if (!$redemption || !$redemption->isUsable()) {
                return redirect()->back()->with('error', 'Voucher tidak valid atau sudah tidak dapat digunakan.');
            }

            $voucher = $redemption->voucher;
            if (!$voucher || !$voucher->isValid()) {
                return redirect()->back()->with('error', 'Voucher tidak valid.');
            }

            if ($baseAmount < $voucher->min_purchase) {
                return redirect()->back()->with('error', 'Minimal pembelian untuk menggunakan voucher ini adalah Rp' . number_format($voucher->min_purchase, 0, ',', '.') . '.');
            }

            $discountAmount = $voucher->calculateDiscount($baseAmount);
            $baseAmount = max(0.0, $baseAmount - $discountAmount);
            $voucherRedemptionId = $redemption->id;
        }

        if ($baseAmount <= 0) {
            return redirect()->route('course.payment', $course->id)
                ->with('error', 'Nilai akhir pembayaran terlalu rendah untuk metode transfer. Gunakan metode lain.');
        }

        $profileUpdates = [];
        $fullName = $request->input('name') ?: $user->name;
        $phone = $request->input('whatsapp') ?: $user->phone;

        if ($fullName !== $user->name) {
            $profileUpdates['name'] = $fullName;
        }
        if ($phone !== $user->phone) {
            $profileUpdates['phone'] = $phone;
        }

        if (!empty($profileUpdates)) {
            $user->update($profileUpdates);
            $user->refresh();
        }

        DB::beginTransaction();
        try {
            $enrollment = Enrollment::updateOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                ['status' => 'pending']
            );

            $manualPayment = ManualPayment::where('course_id', $course->id)
                ->where('user_id', $user->id)
                ->whereIn('status', ['pending', 'rejected'])
                ->latest()
                ->first();

            if (!$manualPayment) {
                $manualPayment = new ManualPayment();
                $manualPayment->order_id = 'TRF-CRS-' . $user->id . '-' . $course->id . '-' . time();
            }

            $manualPayment->fill([
                'course_id' => $course->id,
                'enrollment_id' => $enrollment->id,
                'user_id' => $user->id,
                'amount' => $baseAmount,
                'currency' => 'IDR',
                'method' => 'manual_transfer',
                'whatsapp_number' => $phone,
                'referral_code' => $referralCode,
                'status' => 'pending',
                'metadata' => array_merge((array) ($manualPayment->metadata ?? []), [
                    'source' => 'course',
                    'payment_method' => 'transfer',
                    'voucher_code' => $voucherCode ?: null,
                    'voucher_redemption_id' => $voucherRedemptionId,
                    'discount_amount' => $discountAmount,
                ]),
            ]);
            $manualPayment->save();

            $file = $request->file('payment_proof');
            $path = $file->store('payments/transfer', 'public');

            PaymentProof::create([
                'manual_payment_id' => $manualPayment->id,
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => $user->id,
            ]);

            DB::commit();

            return redirect()->route('course.payment', $course->id)
                ->with('success', 'Bukti transfer berhasil dikirim. Menunggu verifikasi admin.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal mengirim bukti transfer: ' . $e->getMessage());
        }
    }

    public function approve(Request $request, Course $course, ManualPayment $manualPayment): RedirectResponse
    {
        $this->assertPaymentBelongsToCourse($course, $manualPayment);

        DB::beginTransaction();
        try {
            if ($manualPayment->status !== 'settled') {
                $manualPayment->status = 'settled';
                $manualPayment->rejection_reason = null;
                $manualPayment->save();

                $this->processReferralCommission($course, $manualPayment);
                $this->processCourseTrainerRevenue($course, $manualPayment);
            }

            $enrollment = $manualPayment->enrollment;
            if (!$enrollment && $manualPayment->enrollment_id) {
                $enrollment = Enrollment::find($manualPayment->enrollment_id);
            }

            if (!$enrollment) {
                $enrollment = Enrollment::firstOrCreate(
                    ['user_id' => $manualPayment->user_id, 'course_id' => $course->id],
                    ['status' => 'active']
                );
            }

            if ($enrollment->status !== 'active') {
                $enrollment->status = 'active';
                $enrollment->save();
            }

            if ((int) ($manualPayment->enrollment_id ?? 0) !== (int) $enrollment->id) {
                $manualPayment->enrollment_id = $enrollment->id;
                $manualPayment->save();
            }

            DB::commit();

            // Send invoice email (best-effort)
            try {
                $invoiceUser = $manualPayment->user;
                if ($invoiceUser && !empty($invoiceUser->email)) {
                    $invoiceNumber = 'INV-CRS-' . strtoupper(substr(md5($manualPayment->id . $course->id . now()), 0, 8));
                    Mail::to($invoiceUser->email)->send(new PaymentInvoiceMail(
                        invoiceNumber: $invoiceNumber,
                        userName:      (string) ($invoiceUser->name ?? 'User'),
                        userEmail:     (string) ($invoiceUser->email),
                        itemType:      'course',
                        itemTitle:     (string) ($course->name ?? 'Course'),
                        amount:        (float)  ($manualPayment->amount ?? 0),
                        paymentMethod: (string) ($manualPayment->method ?? 'manual_transfer'),
                        paidAt:        now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB',
                        orderId:       (string) ($manualPayment->order_id ?? '-'),
                    ));
                }
            } catch (\Throwable $e) { /* ignore invoice mail errors */
            }

            return redirect()->back()->with('success', 'Pembayaran manual course disetujui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyetujui pembayaran: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Course $course, ManualPayment $manualPayment): RedirectResponse
    {
        $this->assertPaymentBelongsToCourse($course, $manualPayment);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'in:' . implode(',', self::REJECTION_REASONS)],
        ]);

        DB::beginTransaction();
        try {
            $manualPayment->status = 'rejected';
            $manualPayment->rejection_reason = trim((string) $validated['reason']);
            $manualPayment->save();

            if ($manualPayment->enrollment) {
                $manualPayment->enrollment->status = 'canceled';
                $manualPayment->enrollment->save();
            }

            DB::commit();

            return redirect()->back()->with('success', 'Pembayaran manual course ditolak.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menolak pembayaran: ' . $e->getMessage());
        }
    }

    public function pending(Request $request, Course $course, ManualPayment $manualPayment): RedirectResponse
    {
        $this->assertPaymentBelongsToCourse($course, $manualPayment);

        DB::beginTransaction();
        try {
            $manualPayment->status = 'pending';
            $manualPayment->rejection_reason = null;
            $manualPayment->save();

            if ($manualPayment->enrollment) {
                $manualPayment->enrollment->status = 'pending';
                $manualPayment->enrollment->save();
            }

            DB::commit();

            return redirect()->back()->with('success', 'Status pembayaran manual course diubah menjadi pending.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengubah status pembayaran: ' . $e->getMessage());
        }
    }

    public function freeEnroll(Request $request, Course $course): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $isFree = (int) ($course->price ?? 0) <= 0;
        if (!$isFree) {
            return redirect()->route('course.payment', $course->id)->with('error', 'Course ini berbayar.');
        }

        $enrollment = Enrollment::updateOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['status' => 'active']
        );

        // Track in Finance (Amount 0)
        ManualPayment::create([
            'course_id' => $course->id,
            'enrollment_id' => $enrollment->id,
            'user_id' => $user->id,
            'order_id' => 'FREE-CRS-' . strtoupper(uniqid()),
            'amount' => 0,
            'currency' => 'IDR',
            'method' => 'free',
            'status' => 'settled',
            'metadata' => ['source' => 'course', 'type' => 'free']
        ]);

        return redirect()->route('course.learn', $course->id)->with('success', 'Enroll Successfully!');
    }

    public function checkReferral(Request $request, Course $course)
    {
        $code = trim((string) $request->get('code'));
        if ($code === '') {
            return response()->json(['valid' => false, 'message' => '']);
        }

        $referrer = \App\Models\User::where('referral_code', $code)->first();
        if (!$referrer) {
            return response()->json(['valid' => false, 'message' => 'Kode referral tidak ditemukan.']);
        }

        if (Auth::check() && $referrer->id === Auth::id()) {
            return response()->json(['valid' => false, 'message' => 'Tidak bisa menggunakan kode sendiri.']);
        }

        $baseAmount = (float) ($course->hasDiscount() ? $course->discounted_price : ($course->price ?? 0));
        $discountedAmount = $baseAmount * 0.9; // 10% discount

        return response()->json([
            'valid' => true,
            'message' => 'Kode referral valid! Anda mendapatkan diskon 10%.',
            'base_amount' => $baseAmount,
            'final_amount' => (int) round($discountedAmount),
        ]);
    }

    private function assertPaymentBelongsToCourse(Course $course, ManualPayment $manualPayment): void
    {
        if ((int) $manualPayment->course_id !== (int) $course->id) {
            abort(404);
        }
    }

    private function processReferralCommission(Course $course, ManualPayment $payment): void
    {
        if (!(bool) ($course->is_reseller_course ?? false)) {
            return;
        }
        if (empty($payment->referral_code)) {
            return;
        }

        $referrer = User::query()->where('referral_code', $payment->referral_code)->first();
        if (!$referrer || (int) $referrer->id === (int) $payment->user_id) {
            return;
        }

        $totalReferrals = Referral::where('user_id', $referrer->id)->count();
        if ($totalReferrals >= 151) {
            $level = 'Gold';
        } elseif ($totalReferrals >= 51) {
            $level = 'Silver';
        } else {
            $level = 'Bronze';
        }

        $bronze = $course->reseller_commission_bronze ?? 10;
        $silver = $course->reseller_commission_silver ?? 12;
        $gold = $course->reseller_commission_gold ?? 15;

        $pct = match ($level) {
            'Gold' => $gold,
            'Silver' => $silver,
            default => $bronze,
        };
        $rate = ((float) $pct) / 100;

        $commissionAmount = ((float) $payment->amount) * $rate;
        if ($commissionAmount <= 0) {
            return;
        }

        $existingReferral = Referral::query()
            ->where('user_id', $referrer->id)
            ->where('referred_user_id', $payment->user_id)
            ->where('description', 'Komisi Course: ' . $course->name)
            ->first();

        if ($existingReferral) {
            return;
        }

        Referral::create([
            'user_id' => $referrer->id,
            'referred_user_id' => $payment->user_id,
            'amount' => $commissionAmount,
            'status' => 'paid',
            'description' => 'Komisi Course: ' . $course->name,
        ]);

        $referrer->increment('wallet_balance', $commissionAmount);

        try {
            $msg = "Komisi Baru Masuk! Anda mendapatkan komisi sebesar Rp " . number_format($commissionAmount, 0, ',', '.') . " dari pembelian kursus '" . $course->name . "'.";
            \App\Models\UserNotification::create([
                'user_id' => $referrer->id,
                'type' => 'reseller',
                'title' => 'Komisi Baru Masuk!',
                'message' => $msg,
                'data' => ['url' => route('reseller.index')],
            ]);
            if ($referrer->phone) {
                \App\Helpers\WhatsAppHelper::send($referrer->phone, $msg);
            }
        } catch (\Throwable $e) {
            \Log::error('Course manual referral commission notification failed: ' . $e->getMessage());
        }
    }

    private function processCourseTrainerRevenue(Course $course, ManualPayment $payment): void
    {
        if ($course->trainer_id && $course->trainer_revenue_percent > 0) {
            $trainer = User::find($course->trainer_id);
            if ($trainer) {
                $trainerShare = ($payment->amount * $course->trainer_revenue_percent) / 100;
                if ($trainerShare > 0) {
                    $trainer->increment('wallet_balance', $trainerShare);

                    TrainerNotification::create([
                        'trainer_id' => $trainer->id,
                        'type' => 'revenue_share',
                        'title' => 'Pendapatan Course Baru',
                        'message' => 'Anda menerima bagi hasil sebesar Rp ' . number_format($trainerShare, 0, ',', '.') . ' dari penjualan course: ' . $course->name,
                        'data' => ['amount' => $trainerShare, 'course_id' => $course->id]
                    ]);
                }
            }
        }
    }
}
