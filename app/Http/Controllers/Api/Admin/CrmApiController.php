<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SupportMessage;
use App\Models\Feedback;
use App\Models\Review;
use App\Models\Broadcast;
use App\Mail\CRMBlastMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CrmApiController extends Controller
{
    /**
     * Get list of customers
     */
    public function getCustomers(Request $request)
    {
        $query = User::where('role', '!=', 'admin')
            ->withCount(['eventRegistrations', 'enrollments']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 20);

        return response()->json([
            'status' => 'success',
            'data' => $customers
        ]);
    }

    /**
     * Get customer details
     */
    public function getCustomerDetail($id)
    {
        $customer = User::with([
            'eventRegistrations.event',
            'enrollments.course'
        ])->where('role', '!=', 'admin')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $customer
        ]);
    }

    /**
     * Update customer profile
     */
    public function updateCustomer(Request $request, $id)
    {
        $customer = User::where('role', '!=', 'admin')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'bio' => 'nullable|string|max:1000',
            'role' => 'sometimes|required|in:user,reseller,trainer',
        ]);

        $customer->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data customer berhasil diperbarui',
            'data' => $customer
        ]);
    }

    /**
     * Get support messages
     */
    public function getSupportMessages(Request $request)
    {
        $query = SupportMessage::query();

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 15);

        return response()->json([
            'status' => 'success',
            'data' => $messages
        ]);
    }

    /**
     * Update support message status
     */
    public function updateSupportStatus(Request $request, $id)
    {
        $message = SupportMessage::findOrFail($id);

        $request->validate([
            'status' => 'required|in:new,processed,resolved,ignored',
        ]);

        $message->update(['status' => $request->status]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status pesan berhasil diperbarui',
            'data' => $message
        ]);
    }

    /**
     * Get feedbacks
     */
    public function getFeedbacks(Request $request)
    {
        $type = $request->get('type', 'event');
        
        if ($type === 'event') {
            $query = Feedback::with(['user', 'event']);
        } else {
            $query = Review::with(['user', 'course']);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $feedbacks = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 15);

        $avgRating = $type === 'event' ? Feedback::avg('rating') : Review::avg('rating');

        return response()->json([
            'status' => 'success',
            'data' => [
                'items' => $feedbacks,
                'average_rating' => round($avgRating, 2)
            ]
        ]);
    }

    /**
     * Get broadcasts
     */
    public function getBroadcasts(Request $request)
    {
        $broadcasts = Broadcast::with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'status' => 'success',
            'data' => $broadcasts
        ]);
    }

    /**
     * Send broadcast
     */
    public function sendBroadcast(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'segment' => 'required|in:all,reseller,trainer,no_event,manual',
            'manual_targets' => 'required_if:segment,manual|string|nullable',
            'platform' => 'required|in:email,whatsapp,both',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240',
        ]);

        if ($request->segment == 'manual') {
            $rawTargets = preg_split('/[\n,]+/', $request->manual_targets);
            $targets = collect();
            foreach ($rawTargets as $rawTarget) {
                $rawTarget = trim($rawTarget);
                if (empty($rawTarget)) {
                    continue;
                }
                if (filter_var($rawTarget, FILTER_VALIDATE_EMAIL)) {
                    $targets->push((object)[
                        'email' => $rawTarget,
                        'phone' => null
                    ]);
                } elseif (preg_match('/^\+?[0-9\-\s]{8,20}$/', $rawTarget)) {
                    $cleanedPhone = preg_replace('/[\-\s]+/', '', $rawTarget);
                    $targets->push((object)[
                        'email' => null,
                        'phone' => $cleanedPhone
                    ]);
                }
            }
            $targetCount = $targets->count();
        } else {
            $query = User::where('role', '!=', 'admin');

            if ($request->segment == 'reseller') {
                $query->where('role', 'reseller');
            } elseif ($request->segment == 'trainer') {
                $query->where('role', 'trainer');
            } elseif ($request->segment == 'no_event') {
                $query->whereDoesntHave('eventRegistrations');
            }

            $targets = $query->get();
            $targetCount = $targets->count();
        }

        if ($targetCount == 0) {
            return response()->json([
                'status' => 'error',
                'message' => $request->segment == 'manual'
                    ? 'Tidak ada target manual (email atau nomor WhatsApp) yang valid ditemukan'
                    : 'Tidak ada pengguna ditemukan untuk segmen ini'
            ], 404);
        }

        // Store attachments if present
        $attachmentPath = null;
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._\-]/', '_', $file->getClientOriginalName());
                $path = $file->storeAs('broadcasts', $filename, 'public');
                $attachments[] = $path;
            }
            $attachmentPath = json_encode($attachments);
        }

        $broadcast = Broadcast::create([
            'title' => $request->title,
            'message' => $request->message,
            'segment' => $request->segment,
            'platform' => $request->platform,
            'attachment' => $attachmentPath,
            'sender_id' => Auth::id() ?? 1,
            'target_count' => $targetCount,
            'status' => 'sent'
        ]);

        foreach ($targets as $user) {
            if (in_array($request->platform, ['email', 'both']) && $user->email) {
                try {
                    Mail::to($user->email)->send(new CRMBlastMail($broadcast));
                } catch (\Exception $e) {}
            }

            if (in_array($request->platform, ['whatsapp', 'both']) && $user->phone) {
                $waMessage = $broadcast->message;
                if ($broadcast->attachment) {
                    $paths = json_decode($broadcast->attachment, true);
                    if (is_array($paths)) {
                        $waMessage .= "\n\n📁 Lampiran Dokumen:";
                        foreach ($paths as $index => $path) {
                            $waMessage .= "\n" . ($index + 1) . ". " . Storage::disk('public')->url($path);
                        }
                    } else {
                        $waMessage .= "\n\n📁 Lampiran Dokumen:\n" . Storage::disk('public')->url($broadcast->attachment);
                    }
                }
                $this->sendWhatsApp($user->phone, $waMessage);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Broadcast berhasil dikirim ke ' . $targetCount . ' target',
            'data' => $broadcast
        ]);
    }

    private function sendWhatsApp($phone, $message)
    {
        $token = env('FONNTE_TOKEN');
        if (!$token) return false;

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
            ]);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
