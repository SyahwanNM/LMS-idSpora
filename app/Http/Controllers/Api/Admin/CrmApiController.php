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
            'segment' => 'required|in:all,reseller,trainer,no_event',
            'platform' => 'required|in:email,whatsapp,both',
        ]);

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

        if ($targetCount == 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada pengguna ditemukan untuk segmen ini'
            ], 404);
        }

        $broadcast = Broadcast::create([
            'title' => $request->title,
            'message' => $request->message,
            'segment' => $request->segment,
            'platform' => $request->platform,
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
                $this->sendWhatsApp($user->phone, $broadcast->message);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Broadcast berhasil dikirim ke ' . $targetCount . ' pengguna',
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
