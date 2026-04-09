<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Http\Resources\EventResource;
use App\Http\Resources\EventRegistrationResource;
use App\Models\ManualPayment;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    private const REFERRAL_DISCOUNT_RATE = 0.10;

    private function jsonSuccess(string $message, $data = null, $pagination = null, int $statusCode = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'pagination' => $pagination,
        ], $statusCode);
    }

    private function jsonError(string $message, int $statusCode = 400, $data = null)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data,
            'pagination' => null,
        ], $statusCode);
    }

    public function index(Request $request)
    {
        $perPage = max(1, min((int) $request->query('per_page', 10), 100));

        $query = Event::query()->with(['scheduleItems']);

        $isAdmin = $request->user() && strtolower(trim((string) ($request->user()->role ?? ''))) === 'admin';
        if (!$isAdmin) {
            $query->where('is_published', true);
        }

        $status = strtolower(trim((string) $request->query('status', 'active')));
        $now = now()->format('Y-m-d H:i:s');
        $startExpr = "TIMESTAMP(event_date, COALESCE(event_time,'00:00:00'))";
        $endExpr = "TIMESTAMP(event_date, COALESCE(event_time_end, COALESCE(event_time,'23:59:59')))";

        if ($status === 'finished') {
            $query->finished();
        } elseif ($status === 'ongoing') {
            $query->whereNotNull('event_date')->whereRaw("$startExpr <= ? AND $endExpr >= ?", [$now, $now]);
        } elseif ($status === 'upcoming') {
            $query->whereNotNull('event_date')->whereRaw("$startExpr > ?", [$now]);
        } elseif ($status === 'all') {
            // no constraint
        } else {
            // default behavior: only active events
            $query->active();
        }

        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('speaker', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('materi', 'like', "%{$search}%")
                    ->orWhere('jenis', 'like', "%{$search}%");
            });
        }

        $location = trim((string) $request->query('location', ''));
        if ($location !== '') {
            $query->where('location', $location);
        }

        // category maps to `jenis`
        $category = trim((string) $request->query('category', ''));
        if ($category !== '') {
            $query->whereRaw('LOWER(jenis) = ?', [mb_strtolower($category)]);
        }

        if ($request->has('free')) {
            $isFree = filter_var($request->query('free'), FILTER_VALIDATE_BOOL);
            if ($isFree) {
                $query->where(function ($q) {
                    $q->where('price', 0)
                        ->orWhere(function ($qq) {
                            $qq->where('discount_percentage', 100)->where('price', '>', 0);
                        });
                });
            }
        }

        $priceSort = strtolower(trim((string) $request->query('price', '')));
        if (in_array($priceSort, ['asc', 'desc'], true)) {
            $query->orderBy('price', $priceSort);
        } else {
            $query->latest();
        }

        $events = $query->paginate($perPage)->appends($request->query());

        return $this->jsonSuccess('List event', EventResource::collection($events), [
            'current_page' => $events->currentPage(),
            'per_page' => $events->perPage(),
            'total' => $events->total(),
            'last_page' => $events->lastPage(),
        ]);
    }

    public function show(Request $request, int $id)
    {
        $event = Event::query()
            ->with(['scheduleItems'])
            ->find($id);

        if (!$event) {
            return $this->jsonError('Event tidak ditemukan', 404);
        }

        $isAdmin = $request->user() && strtolower(trim((string) ($request->user()->role ?? ''))) === 'admin';
        if (!$isAdmin && !(bool) $event->is_published) {
            return $this->jsonError('Event tidak ditemukan', 404);
        }

        return $this->jsonSuccess('Detail Event', new EventResource($event));
    }
    
   public function register(Request $request, $id)
    {
        $user = $request->user();
        $validated = $request->validate([
            'referral_code' => 'nullable|string|max:64',
        ]);
        $event = Event::find($id);

        // 1. Validasi Event
        if (!$event) {
            return $this->jsonError('Event tidak ditemukan', 404);
        }

        $isAdmin = $user && strtolower(trim((string) ($user->role ?? ''))) === 'admin';
        if (!$isAdmin && !(bool) $event->is_published) {
            // Avoid leaking unpublished events
            return $this->jsonError('Event tidak ditemukan', 404);
        }

        // 1b. Cek apakah event sudah selesai
        if (method_exists($event, 'isFinished') && $event->isFinished()) {
            return $this->jsonError('Event sudah selesai, pendaftaran ditutup.', 422);
        }

        // 2. Hitung Harga (jangan treat discounted_price null sebagai gratis)
        $basePrice = $this->resolveFinalEventPrice($event);
        $isFree = (float) $basePrice <= 0;

        // Referral/discount: only for reseller-enabled events and paid events.
        $rawReferralCode = trim((string) ($validated['referral_code'] ?? ''));
        $referrer = (!$isFree && (bool) ($event->is_reseller_event ?? false))
            ? $this->resolveValidReferrer($user, $rawReferralCode)
            : null;
        $referralCode = $referrer ? $rawReferralCode : null;

        $amount = $isFree ? 0 : $this->applyReferralDiscountAmount((float) $basePrice, $referrer !== null);

        // Buat Nomor Order Unik (Must be unique for each registration attempt)
        // Format: REG-{UserID}-{EventID}-{Timestamp}
        $orderId = 'REG-' . $user->id . '-' . $event->id . '-' . time();

        // 3. Cek Apakah User Sudah Terdaftar
        $existing = EventRegistration::query()->where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->first();

        if ($existing) {
            // Kalau sudah daftar tapi belum bayar, kasih link bayar yang lama
            if ($existing->status == 'pending' && $existing->payment_url) {
                return $this->jsonSuccess(
                    'Anda sudah mendaftar, silakan selesaikan pembayaran.',
                    new EventRegistrationResource($existing->load('event'))
                );
            }

            // Jika sebelumnya ditolak, izinkan daftar lagi: reset jadi attempt baru
            if ($existing->status == 'rejected') {
                try {
                    DB::beginTransaction();

                    $existing->status = $isFree ? 'active' : 'pending';
                    $existing->registration_code = $orderId;
                    $existing->total_price = $amount;
                    $existing->payment_url = null;
                    $existing->payment_proof = null;
                    $existing->rejection_reason = null;
                    $existing->payment_verified_at = null;
                    $existing->payment_verified_by = null;
                    $existing->referral_code = $referralCode;
                    $existing->save();

                    $manualPayment = ManualPayment::query()
                        ->where('event_id', $event->id)
                        ->where('event_registration_id', $existing->id)
                        ->where('user_id', $user->id)
                        ->latest('id')
                        ->first();

                    if (!$manualPayment) {
                        $manualPayment = new ManualPayment();
                    }

                    $manualPayment->event_id = $event->id;
                    $manualPayment->event_registration_id = $existing->id;
                    $manualPayment->user_id = $user->id;
                    $manualPayment->order_id = $orderId;
                    $manualPayment->amount = $amount;
                    $manualPayment->currency = 'IDR';
                    $manualPayment->method = $isFree ? 'free' : 'manual_transfer';
                    $manualPayment->status = $isFree ? 'settled' : 'pending';
                    $manualPayment->note = null;
                    $manualPayment->referral_code = $referralCode;
                    $manualPayment->metadata = array_merge((array) ($manualPayment->metadata ?? []), [
                        'source' => 'event',
                        'type' => $isFree ? 'free' : 'paid',
                        'retry_after_reject' => true,
                        'base_amount' => (float) $basePrice,
                        'discount_rate' => $referrer ? self::REFERRAL_DISCOUNT_RATE : 0,
                    ]);
                    $manualPayment->save();

                    DB::commit();

                    return response()->json([
                        'status' => 'success',
                        'message' => $isFree ? 'Pendaftaran Berhasil!' : 'Pendaftaran berhasil. Silakan lakukan pembayaran manual dan upload bukti bayar.',
                        'data' => [
                            'registration' => new EventRegistrationResource($existing->fresh()->load('event')),
                            'payment_url' => null
                        ]
                    ], 201);
                } catch (\Throwable $e) {
                    DB::rollBack();
                    return $this->jsonError('Gagal memproses pendaftaran ulang: ' . $e->getMessage(), 500);
                }
            }

            return $this->jsonError(
                'Kamu sudah terdaftar di event ini!',
                409,
                new EventRegistrationResource($existing->load('event'))
            );
        }

        try {
            DB::beginTransaction();

            // 4. Simpan ke Database (Status Pending)
            $registration = EventRegistration::query()->create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'status' => $isFree ? 'active' : 'pending',
                'registration_code' => $orderId,
                'total_price' => $amount,
                'payment_url' => null, // Nanti diisi
                'referral_code' => $referralCode,
            ]);


            // 5. JIKA BERBAYAR -> Arahkan ke Manual Payment
            // Tidak perlu panggil Midtrans. User akan upload bukti bayar nanti.
           
            // 6. Track in Finance (ManualPayment Trace)
            ManualPayment::query()->create([
                'event_id' => $event->id,
                'event_registration_id' => $registration->id,
                'user_id' => $user->id,
                'order_id' => $orderId,
                'amount' => $amount,
                'currency' => 'IDR',
                'method' => $isFree ? 'free' : 'manual_transfer',
                'status' => $isFree ? 'settled' : 'pending',
                'referral_code' => $referralCode,
                'metadata' => [
                    'source' => 'event',
                    'type' => $isFree ? 'free' : 'paid',
                    'base_amount' => (float) $basePrice,
                    'discount_rate' => $referrer ? self::REFERRAL_DISCOUNT_RATE : 0,
                ]
            ]);

            DB::commit();

            return $this->jsonSuccess(
                $isFree ? 'Pendaftaran Berhasil!' : 'Pendaftaran berhasil. Silakan lakukan pembayaran manual dan upload bukti bayar.',
                [
                    'registration' => new EventRegistrationResource($registration->load('event')),
                    'payment_url' => null,
                ],
                null,
                201
            );

            } catch (\Exception $e) {
                DB::rollBack();
                return $this->jsonError('Gagal memproses pendaftaran: ' . $e->getMessage(), 500);
            }
        }
    
    /**
     * Cek status pendaftaran event untuk user saat ini.
     */
    public function registrationStatus(Request $request, $id)
    {
        $user = $request->user();

        $event = Event::find($id);
        if (!$event) {
            return $this->jsonError('Event tidak ditemukan', 404);
        }

        // Tidak bisa membuat pembayaran jika event sudah selesai
        if (method_exists($event, 'isFinished') && $event->isFinished()) {
            return $this->jsonError('Event sudah selesai, pembayaran tidak tersedia.', 422);
        }

        $registration = \App\Models\EventRegistration::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->latest()
            ->first();

        if (!$registration) {
            return $this->jsonError('Belum terdaftar di event ini', 404);
        }

        return $this->jsonSuccess('Status pendaftaran', new EventRegistrationResource($registration->load('event')));
    }

    /**
     * Daftar pendaftaran event milik user.
     */
    public function listRegistrations(Request $request)
    {
        $user = $request->user();

        $registrations = EventRegistration::query()->with('event')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return $this->jsonSuccess('Daftar pendaftaran event milik user', EventRegistrationResource::collection($registrations), [
            'current_page' => $registrations->currentPage(),
            'per_page' => $registrations->perPage(),
            'total' => $registrations->total(),
            'last_page' => $registrations->lastPage(),
        ]);
    }

    /**
     * Buat/refresh pendaftaran pending untuk pembayaran manual.
     */
    public function createPayment(Request $request, $id)
    {
        $user = $request->user();
        $validated = $request->validate([
            'referral_code' => 'nullable|string|max:64',
        ]);
        $event = Event::find($id);

        if (!$event) {
            return $this->jsonError('Event tidak ditemukan', 404);
        }

        $registration = EventRegistration::query()->where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->latest()
            ->first();

        if (!$registration) {
            return $this->jsonError('Belum mendaftar pada event ini', 409);
        }

        if ($registration->status === 'active') {
            return $this->jsonError('Pendaftaran sudah LUNAS', 409);
        }

        // Recompute base price safely and (optionally) apply referral
        $basePrice = $this->resolveFinalEventPrice($event);
        $rawReferralCode = trim((string) ($validated['referral_code'] ?? ''));
        $referrer = (bool) ($event->is_reseller_event ?? false)
            ? $this->resolveValidReferrer($user, $rawReferralCode)
            : null;
        $referralCode = $referrer ? $rawReferralCode : null;

        $finalAmount = $this->applyReferralDiscountAmount((float) $basePrice, $referrer !== null);
        $amount = (float) max(0, (float) ($registration->total_price ?? 0));
        if ($amount <= 0) {
            $amount = $finalAmount;
        }

        if ($amount <= 0) {
            return $this->jsonError('Event ini gratis, tidak memerlukan pembayaran', 400);
        }

        DB::beginTransaction();
        try {
            // Buat order id baru agar unik (Update registration_code in DB)
            $orderId = 'REG-' . $user->id . '-' . $event->id . '-' . time();
            $registration->registration_code = $orderId;
            $registration->referral_code = $referralCode;
            $registration->total_price = $finalAmount;
            $registration->save();

            $manualPayment = ManualPayment::query()
                ->where('event_id', $event->id)
                ->where('event_registration_id', $registration->id)
                ->where('user_id', $user->id)
                ->whereIn('status', ['pending', 'rejected'])
                ->latest('id')
                ->first();

            if (!$manualPayment) {
                $manualPayment = new ManualPayment();
            }

            $manualPayment->event_id = $event->id;
            $manualPayment->event_registration_id = $registration->id;
            $manualPayment->user_id = $user->id;
            $manualPayment->order_id = $orderId;
            $manualPayment->amount = $finalAmount;
            $manualPayment->currency = 'IDR';
            $manualPayment->method = 'manual_transfer';
            $manualPayment->status = 'pending';
            $manualPayment->rejection_reason = null;
            $manualPayment->referral_code = $referralCode;
            $manualPayment->metadata = array_merge((array) ($manualPayment->metadata ?? []), [
                'source' => 'event',
                'type' => 'paid',
                'base_amount' => (float) $basePrice,
                'discount_rate' => $referrer ? self::REFERRAL_DISCOUNT_RATE : 0,
                'refreshed' => true,
            ]);
            $manualPayment->save();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->jsonError('Gagal membuat pembayaran: ' . $e->getMessage(), 500);
        }

        return $this->jsonSuccess('Silakan lakukan pembayaran manual dan upload bukti bayar.', new EventRegistrationResource($registration->load('event')));
    }

    /**
     * Batalkan pendaftaran pending.
     */
    public function cancelRegistration(Request $request, $id)
    {
        $user = $request->user();
        $event = Event::find($id);

        if (!$event) {
            return $this->jsonError('Event tidak ditemukan', 404);
        }

        $registration = EventRegistration::query()->where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->latest()
            ->first();

        if (!$registration) {
            return $this->jsonError('Data pendaftaran tidak ditemukan', 404);
        }

        if ($registration->status !== 'pending') {
            return $this->jsonError('Hanya pendaftaran dengan status pending yang dapat dibatalkan', 409);
        }

        $registration->update([
            'status' => 'canceled',
            'payment_url' => null,
        ]);

        ManualPayment::query()
            ->where('event_id', $event->id)
            ->where('event_registration_id', $registration->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'rejected'])
            ->latest('id')
            ->limit(1)
            ->update(['status' => 'cancelled']);

        return $this->jsonSuccess('Pendaftaran berhasil dibatalkan', new EventRegistrationResource($registration->load('event')));
    }

    private function resolveFinalEventPrice(Event $event): float
    {
        $price = (float) ($event->price ?? 0);

        $discounted = $event->discounted_price ?? null;
        if ($discounted !== null && $discounted !== '' && is_numeric($discounted)) {
            return max(0, (float) $discounted);
        }

        $discountPercent = $event->discount_percentage ?? null;
        if ($discountPercent !== null && $discountPercent !== '' && is_numeric($discountPercent)) {
            $pct = max(0, min(100, (float) $discountPercent));
            return max(0, round($price * (1 - ($pct / 100)), 2));
        }

        return max(0, $price);
    }

    private function resolveValidReferrer(?User $buyer, ?string $referralCode): ?User
    {
        $code = trim((string) $referralCode);
        if ($code === '') {
            return null;
        }

        $referrer = User::query()->where('referral_code', $code)->first();
        if (!$referrer) {
            return null;
        }

        if ($buyer && (int) $referrer->id === (int) $buyer->id) {
            return null;
        }

        return $referrer;
    }

    private function applyReferralDiscountAmount(float $baseAmount, bool $hasValidReferral): float
    {
        $base = max(0, (float) $baseAmount);
        if (!$hasValidReferral) {
            return $base;
        }

        $discounted = $base * (1 - self::REFERRAL_DISCOUNT_RATE);
        return max(0, round($discounted, 2));
    }
}