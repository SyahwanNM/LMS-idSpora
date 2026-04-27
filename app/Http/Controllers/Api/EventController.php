<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Http\Resources\EventResource;
use App\Http\Resources\EventRegistrationResource;
use App\Models\ManualPayment;
use App\Models\EventRegistration;
use App\Models\User;
use App\Models\Referral;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        // event_type: online | offline | hybrid
        $eventType = strtolower(trim((string) $request->query('event_type', '')));
        if ($eventType === 'online') {
            $query->whereNotNull('zoom_link')->where('zoom_link', '!=', '')
                ->where(fn($q) => $q->whereNull('location')->orWhere('location', '')->orWhereRaw('LOWER(location) = ?', ['online']));
        } elseif ($eventType === 'offline') {
            $query->where(fn($q) => $q->whereNull('zoom_link')->orWhere('zoom_link', ''))
                ->whereNotNull('location')->where('location', '!=', '')->whereRaw('LOWER(location) != ?', ['online']);
        } elseif ($eventType === 'hybrid') {
            $query->whereNotNull('zoom_link')->where('zoom_link', '!=', '')
                ->whereNotNull('location')->where('location', '!=', '')->whereRaw('LOWER(location) != ?', ['online']);
        }

        // day: today | weekdays | weekend
        $day = strtolower(trim((string) $request->query('day', '')));
        if ($day === 'today') {
            $query->whereDate('event_date', now()->toDateString());
        } elseif ($day === 'weekdays') {
            $query->whereRaw('DAYOFWEEK(event_date) BETWEEN 2 AND 6');
        } elseif ($day === 'weekend') {
            $query->whereRaw('DAYOFWEEK(event_date) IN (1,7)');
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
                    ->whereIn('status', ['pending', 'rejected', 'expired'])
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
            ->whereIn('status', ['pending', 'rejected', 'expired'])
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

    /**
     * Submit feedback for a finished event.
     */
    public function submitFeedback(Request $request, $id)
    {
        $user  = $request->user();
        $event = Event::find($id);

        if (!$event) {
            return $this->jsonError('Event tidak ditemukan', 404);
        }

        $registration = EventRegistration::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->where('status', 'active')
            ->first();

        if (!$registration) {
            return $this->jsonError('Anda tidak terdaftar di event ini', 403);
        }

        if ($registration->feedback_submitted_at) {
            return $this->jsonError('Feedback sudah pernah dikirim', 409);
        }

        $validated = $request->validate([
            'rating'         => 'required|integer|min:1|max:5',
            'feedback_text'  => 'nullable|string|max:1000',
            'speaker_rating' => 'nullable|integer|min:1|max:5',
        ]);

        \App\Models\Feedback::create([
            'event_id'       => $event->id,
            'user_id'        => $user->id,
            'rating'         => $validated['rating'],
            'comment'        => $validated['feedback_text'] ?? null,
            'speaker_rating' => $validated['speaker_rating'] ?? null,
        ]);

        $registration->feedback_submitted_at = now();
        if (empty($registration->certificate_issued_at)) {
            $registration->certificate_issued_at = now();
        }
        $registration->save();

        return $this->jsonSuccess('Feedback berhasil dikirim. Sertifikat Anda sudah tersedia.');
    }

    /**
     * Get approved trainer modules for a finished event (for registered users).
     */
    public function materials(Request $request, $id)
    {
        $user  = $request->user();
        $event = Event::find($id);

        if (!$event) {
            return $this->jsonError('Event tidak ditemukan', 404);
        }

        $registration = EventRegistration::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->where('status', 'active')
            ->first();

        if (!$registration) {
            return $this->jsonError('Anda tidak terdaftar di event ini', 403);
        }

        if (!method_exists($event, 'isFinished') || !$event->isFinished()) {
            return $this->jsonError('Materi tersedia setelah event selesai', 403);
        }

        $modules = \App\Models\EventTrainerModule::where('event_id', $event->id)
            ->where('status', 'approved')
            ->with('trainer:id,name')
            ->get()
            ->map(fn($m) => [
                'id'            => $m->id,
                'original_name' => $m->original_name,
                'download_url'  => route('events.modules.download', [$event->id, 'module_id' => $m->id]),
                'trainer'       => $m->trainer ? ['id' => $m->trainer->id, 'name' => $m->trainer->name] : null,
                'uploaded_at'   => $m->created_at?->toISOString(),
            ]);

        return $this->jsonSuccess('Materi event', $modules);
    }

    // =========================================================================
    // MIDTRANS PAYMENT ENDPOINTS
    // =========================================================================

    /**
     * Ambil Midtrans Snap Token untuk event berbayar.
     * Membuat atau menggunakan kembali pending order yang ada.
     *
     * GET /api/events/{id}/midtrans/snap-token
     */
    public function midtransSnapToken(Request $request, $id): JsonResponse
    {
        $user  = $request->user();
        $event = Event::find($id);

        if (!$event) {
            return $this->jsonError('Event tidak ditemukan', 404);
        }

        $isAdmin = $user && strtolower(trim((string) ($user->role ?? ''))) === 'admin';
        if (!$isAdmin && !(bool) $event->is_published) {
            return $this->jsonError('Event tidak ditemukan', 404);
        }

        if (method_exists($event, 'isFinished') && $event->isFinished()) {
            return $this->jsonError('Event sudah selesai, pembayaran tidak tersedia.', 422);
        }

        $this->configureMidtrans();

        $baseAmount = $this->resolveFinalEventPrice($event);
        if ($baseAmount <= 0) {
            return $this->jsonError('Event ini gratis, tidak perlu Midtrans.', 400);
        }

        $forceNew       = $request->boolean('force_new', false);
        $rawReferral    = trim((string) $request->input('referral_code', ''));
        $referrer       = (bool) ($event->is_reseller_event ?? false)
            ? $this->resolveValidReferrer($user, $rawReferral)
            : null;
        $referralCode   = $referrer ? $rawReferral : null;
        $finalAmount    = $this->applyReferralDiscountAmount($baseAmount, $referrer !== null);

        $dial  = trim((string) $request->input('dial_code', ''));
        $wa    = trim((string) $request->input('whatsapp', ''));
        $phone = trim($dial . $wa) ?: (string) ($user->phone ?? '');

        DB::beginTransaction();
        try {
            $registration = EventRegistration::query()
                ->where('user_id', $user->id)
                ->where('event_id', $event->id)
                ->first();

            if ($registration && $registration->status === 'active') {
                DB::rollBack();
                return $this->jsonError('Anda sudah terdaftar di event ini.', 409);
            }

            if (!$registration) {
                $registration = EventRegistration::create([
                    'user_id'           => $user->id,
                    'event_id'          => $event->id,
                    'status'            => 'pending',
                    'registration_code' => 'EVT-' . strtoupper(uniqid()),
                    'total_price'       => $finalAmount,
                ]);
            } else {
                $registration->status      = 'pending';
                $registration->total_price = $finalAmount;
                $registration->save();
            }

            // Reuse existing pending Midtrans order jika ada dan tidak force_new
            $payment = ManualPayment::query()
                ->where('event_registration_id', $registration->id)
                ->where('user_id', $user->id)
                ->where('method', 'midtrans')
                ->where('status', 'pending')
                ->latest('id')
                ->first();

            if ($payment && !$forceNew) {
                $existingToken = data_get($payment->metadata, 'snap_token');
                if (is_string($existingToken) && trim($existingToken) !== '') {
                    $registration->total_price = (float) $payment->amount;
                    $registration->save();
                    DB::commit();

                    return $this->jsonSuccess('Lanjutkan pembayaran yang tertunda.', [
                        'snap_token'    => $existingToken,
                        'order_id'      => $payment->order_id,
                        'amount'        => (int) round((float) $payment->amount),
                        'client_key'    => (string) config('midtrans.client_key'),
                        'is_production' => (bool) config('midtrans.is_production', false),
                        'is_continue'   => true,
                    ]);
                }
            }

            if ($payment && $forceNew) {
                $payment->status           = 'rejected';
                $payment->rejection_reason = 'Diganti karena membuat transaksi Midtrans baru.';
                $payment->save();
                $payment = null;
            }

            $orderId = 'MT-EVT-' . strtoupper(uniqid());
            $payment = ManualPayment::create([
                'event_id'              => $event->id,
                'event_registration_id' => $registration->id,
                'user_id'               => $user->id,
                'order_id'              => $orderId,
                'amount'                => $finalAmount,
                'currency'              => 'IDR',
                'method'                => 'midtrans',
                'status'                => 'pending',
                'whatsapp_number'       => $phone ?: null,
                'referral_code'         => $referralCode,
                'metadata'              => [
                    'source'        => 'event',
                    'base_amount'   => $baseAmount,
                    'discount_rate' => $referrer ? self::REFERRAL_DISCOUNT_RATE : 0,
                    'event_id'      => $event->id,
                    'event_title'   => $event->title,
                ],
            ]);

            $grossAmount = (int) round($finalAmount);
            $snapParams  = [
                'transaction_details' => [
                    'order_id'     => $orderId,
                    'gross_amount' => $grossAmount,
                ],
                'item_details' => [[
                    'id'       => 'event-' . $event->id,
                    'price'    => $grossAmount,
                    'quantity' => 1,
                    'name'     => (string) ($event->title ?? 'Event'),
                ]],
                'customer_details' => [
                    'first_name' => (string) ($user->name ?? 'User'),
                    'email'      => (string) ($user->email ?? ''),
                    'phone'      => $phone,
                ],
                'callbacks' => [
                    'finish' => route('payment.finish'),
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($snapParams);

            $payment->metadata = array_merge((array) ($payment->metadata ?? []), [
                'snap_token'            => $snapToken,
                'snap_token_created_at' => now()->toIso8601String(),
            ]);
            $payment->save();

            DB::commit();

            return $this->jsonSuccess('Snap token berhasil dibuat.', [
                'snap_token'    => $snapToken,
                'order_id'      => $orderId,
                'amount'        => $grossAmount,
                'client_key'    => (string) config('midtrans.client_key'),
                'is_production' => (bool) config('midtrans.is_production', false),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('API midtransSnapToken failed', ['event_id' => $id, 'error' => $e->getMessage()]);
            return $this->jsonError('Gagal membuat pembayaran Midtrans: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Finalisasi status pembayaran Midtrans setelah user selesai di Snap popup.
     * Cek status ke Midtrans dan aktifkan registrasi jika settled.
     *
     * POST /api/events/{id}/midtrans/finalize
     */
    public function midtransFinalize(Request $request, $id): JsonResponse
    {
        $user  = $request->user();
        $event = Event::find($id);

        if (!$event) {
            return $this->jsonError('Event tidak ditemukan', 404);
        }

        $validated = $request->validate([
            'order_id' => 'required|string|max:100',
        ]);
        $orderId = (string) $validated['order_id'];

        $payment = ManualPayment::query()
            ->where('order_id', $orderId)
            ->where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->first();

        if (!$payment) {
            return $this->jsonError('Order tidak ditemukan.', 404);
        }

        try {
            $this->configureMidtrans();
            $midtransStatus = (array) \Midtrans\Transaction::status($orderId);

            $ts             = strtolower((string) ($midtransStatus['transaction_status'] ?? ''));
            $fs             = strtolower((string) ($midtransStatus['fraud_status'] ?? ''));
            $internalStatus = $this->mapMidtransStatus($ts, $fs);

            DB::beginTransaction();

            $wasSettled    = $payment->status === 'settled';
            $payment->status   = $internalStatus;
            $payment->metadata = array_merge((array) ($payment->metadata ?? []), [
                'midtrans_status' => $midtransStatus,
                'finalized_at'    => now()->toIso8601String(),
            ]);
            $payment->save();

            if (!$wasSettled && $internalStatus === 'settled') {
                $registration = $payment->event_registration_id
                    ? EventRegistration::find($payment->event_registration_id)
                    : null;

                if ($registration && $registration->status !== 'active') {
                    $registration->status               = 'active';
                    $registration->payment_verified_at  = now();
                    $registration->payment_verified_by  = null;
                    $registration->save();
                }

                // Komisi referral
                $this->processEventReferralCommission($event, $payment);

                // Notifikasi user
                $this->notifyEventMidtransSuccess($event, $payment);
            }

            if (in_array($internalStatus, ['expired', 'rejected'], true) && $payment->event_registration_id) {
                $registration = EventRegistration::find($payment->event_registration_id);
                if ($registration && !in_array($registration->status, ['active', 'canceled'], true)) {
                    $registration->status = $internalStatus;
                    $registration->save();
                }
            }

            DB::commit();

            return $this->jsonSuccess('Status pembayaran diperbarui.', [
                'order_id' => $orderId,
                'status'   => $internalStatus,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('API midtransFinalize failed', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            return $this->jsonError('Gagal memeriksa status pembayaran.', 500);
        }
    }

    /**
     * Cek pending order Midtrans untuk event ini.
     * Digunakan saat user kembali ke halaman pembayaran.
     *
     * GET /api/events/{id}/midtrans/pending-order
     */
    public function midtransPendingOrder(Request $request, $id): JsonResponse
    {
        $user  = $request->user();
        $event = Event::find($id);

        if (!$event) {
            return $this->jsonError('Event tidak ditemukan', 404);
        }

        $payment = ManualPayment::query()
            ->where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->where('method', 'midtrans')
            ->where('status', 'pending')
            ->latest('id')
            ->first();

        // Sync status real-time dari Midtrans jika ada pending order
        if ($payment && $payment->order_id) {
            try {
                $this->configureMidtrans();
                $midtransStatus = (array) \Midtrans\Transaction::status($payment->order_id);
                $ts             = strtolower((string) ($midtransStatus['transaction_status'] ?? ''));
                $fs             = strtolower((string) ($midtransStatus['fraud_status'] ?? ''));
                $actualStatus   = $this->mapMidtransStatus($ts, $fs);

                if ($actualStatus !== 'pending') {
                    $payment->status = $actualStatus;
                    $payment->save();

                    if (in_array($actualStatus, ['expired', 'rejected'], true)) {
                        $reg = EventRegistration::find($payment->event_registration_id);
                        if ($reg && !in_array($reg->status, ['active', 'canceled'], true)) {
                            $reg->status = $actualStatus;
                            $reg->save();
                        }
                    }

                    $payment = null; // tidak ada pending order aktif
                }
            } catch (\Throwable $e) {
                // 404 dari Midtrans = belum pernah dicharge = expired
                if (str_contains($e->getMessage(), '404') || str_contains(strtolower($e->getMessage()), 'not found')) {
                    $payment->status = 'expired';
                    $payment->save();
                    $reg = EventRegistration::find($payment->event_registration_id);
                    if ($reg && !in_array($reg->status, ['active', 'canceled'], true)) {
                        $reg->status = 'expired';
                        $reg->save();
                    }
                    $payment = null;
                }
                // Error lain: biarkan tetap pending, jangan blokir user
            }
        }

        // Cek apakah registrasi expired/rejected → perlu force_new
        $registration = EventRegistration::query()
            ->where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->latest('id')
            ->first();

        $registrationExpired = $registration && in_array($registration->status, ['expired', 'rejected'], true);
        if ($registrationExpired && $payment) {
            $payment->status = 'expired';
            $payment->save();
            $payment = null;
        }

        $snapToken = null;
        if ($payment) {
            $t = data_get($payment->metadata, 'snap_token');
            $snapToken = (is_string($t) && trim($t) !== '') ? $t : null;
        }

        return $this->jsonSuccess('Status pending order.', [
            'pending'          => (bool) $payment,
            'order_id'         => $payment?->order_id,
            'amount'           => $payment ? (int) round((float) $payment->amount) : null,
            'snap_token'       => $snapToken,
            'whatsapp_number'  => $payment?->whatsapp_number,
            'referral_code'    => $payment?->referral_code,
            'needs_force_new'  => $registrationExpired && !$payment,
        ]);
    }

    // -------------------------------------------------------------------------
    // Private helpers untuk Midtrans
    // -------------------------------------------------------------------------

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

    private function mapMidtransStatus(string $ts, string $fs): string
    {
        if ($ts === 'capture') {
            return $fs === 'challenge' ? 'pending' : 'settled';
        }
        if ($ts === 'settlement') return 'settled';
        if ($ts === 'pending')    return 'pending';
        if ($ts === 'expire')     return 'expired';
        return 'rejected';
    }

    private function processEventReferralCommission(Event $event, ManualPayment $payment): void
    {
        if (!(bool) ($event->is_reseller_event ?? false) || empty($payment->referral_code)) {
            return;
        }

        $referrer = User::query()->where('referral_code', $payment->referral_code)->first();
        if (!$referrer || (int) $referrer->id === (int) $payment->user_id) {
            return;
        }

        $commission = ((float) $payment->amount) * self::REFERRAL_DISCOUNT_RATE;
        if ($commission <= 0) return;

        $exists = Referral::query()
            ->where('user_id', $referrer->id)
            ->where('referred_user_id', $payment->user_id)
            ->where('description', 'Komisi Event: ' . $event->title)
            ->exists();

        if ($exists) return;

        Referral::create([
            'user_id'          => $referrer->id,
            'referred_user_id' => $payment->user_id,
            'amount'           => $commission,
            'status'           => 'paid',
            'description'      => 'Komisi Event: ' . $event->title,
        ]);

        $referrer->increment('wallet_balance', $commission);
    }

    private function notifyEventMidtransSuccess(Event $event, ManualPayment $payment): void
    {
        try {
            $exists = UserNotification::query()
                ->where('user_id', $payment->user_id)
                ->where('type', 'event_registration_midtrans_success')
                ->where('data->order_id', $payment->order_id)
                ->exists();

            if ($exists) return;

            UserNotification::create([
                'user_id'    => $payment->user_id,
                'type'       => 'event_registration_midtrans_success',
                'title'      => 'Pendaftaran Dikonfirmasi',
                'message'    => 'Anda berhasil terdaftar di event "' . ($event->title ?? 'Event') . '".',
                'data'       => [
                    'event_id' => $event->id,
                    'order_id' => $payment->order_id,
                ],
                'expires_at' => now()->addDays(14),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to create midtrans event success notification', [
                'order_id' => $payment->order_id,
                'error'    => $e->getMessage(),
            ]);
        }
    }
}