<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\EventRegistration;
use App\Services\ProfileReminderService;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Count events participated
        $eventsCount = $user->eventRegistrations()
            ->whereIn('status', ['active', 'pending'])
            ->count();
        
        // Count courses enrolled
        $coursesCount = DB::table('enrollments')
            ->where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->count();
        
        return view('profile.index', compact('eventsCount', 'coursesCount'));
    }
    
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'phone_country_code' => 'required|string|in:+62,+60,+65,+1,+44,+61,+86,+81,+82,+66,+84,+63,+91',
            'phone_number' => [
                'required',
                'string',
                'max:15',
                function ($attribute, $value, $fail) use ($request) {
                    if (!empty($value)) {
                        // Hapus semua karakter selain angka
                        $cleaned = preg_replace('/[^0-9]/', '', $value);
                        
                        // Hapus leading zero
                        $cleaned = ltrim($cleaned, '0');
                        
                        // Validasi panjang berdasarkan country code
                        $countryCode = $request->input('phone_country_code', '+62');
                        
                        $minLength = 6;
                        $maxLength = 15;
                        
                        // Set panjang minimum/maksimum berdasarkan country code
                        switch ($countryCode) {
                            case '+62': // Indonesia
                                $minLength = 9;
                                $maxLength = 12;
                                break;
                            case '+60': // Malaysia
                            case '+65': // Singapore
                            case '+66': // Thailand
                            case '+84': // Vietnam
                            case '+63': // Philippines
                                $minLength = 8;
                                $maxLength = 10;
                                break;
                            case '+1': // US/Canada
                                $minLength = 10;
                                $maxLength = 10;
                                break;
                            case '+44': // UK
                                $minLength = 10;
                                $maxLength = 10;
                                break;
                            case '+61': // Australia
                                $minLength = 9;
                                $maxLength = 9;
                                break;
                            case '+86': // China
                                $minLength = 11;
                                $maxLength = 11;
                                break;
                            case '+81': // Japan
                                $minLength = 10;
                                $maxLength = 11;
                                break;
                            case '+82': // South Korea
                                $minLength = 9;
                                $maxLength = 10;
                                break;
                            case '+91': // India
                                $minLength = 10;
                                $maxLength = 10;
                                break;
                        }
                        
                        if (strlen($cleaned) < $minLength || strlen($cleaned) > $maxLength) {
                            $fail("Nomor telepon harus {$minLength}-{$maxLength} digit untuk kode negara {$countryCode}");
                        }
                    }
                },
            ],
            'bio' => 'nullable|string|max:1000',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        
        // Update password if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = uniqid('ava_') . '.' . $file->getClientOriginalExtension();
            
            // Ensure directory exists
            if (!Storage::disk('public')->exists('avatars')) {
                Storage::disk('public')->makeDirectory('avatars');
            }
            
            // Delete old avatar if exists and not external URL
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                $oldPath = 'avatars/' . $user->avatar;
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            
            // Store new avatar
            Storage::disk('public')->putFileAs('avatars', $file, $filename);
            $user->avatar = $filename;
        }
        
        // Update additional fields
        // Format phone number: gabungkan country code + number
        if (!empty($validated['phone_number'])) {
            $countryCode = $validated['phone_country_code'];
            $phoneNumber = preg_replace('/[^0-9]/', '', $validated['phone_number']);
            // Hapus leading zero
            $phoneNumber = ltrim($phoneNumber, '0');
            // Gabungkan: +62 + 81234567890 = +6281234567890
            $user->phone = $countryCode . $phoneNumber;
        } else {
            $user->phone = null;
        }
        
        $user->bio = $validated['bio'] ?? null;
        
        $user->save();
        
        // Auto-deactivate reminder jika profile sudah lengkap
        $reminderService = app(ProfileReminderService::class);
        if ($user->isProfileComplete()) {
            $reminderService->deactivateReminder($user);
        }
        
        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }
    
    public function events()
    {
        $user = Auth::user();
        
        // Get all event registrations with event details
        $registrations = EventRegistration::where('user_id', $user->id)
            ->with(['event', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get all payments for this user
        $payments = \App\Models\Payment::where('user_id', $user->id)
            ->whereIn('status', ['capture', 'settlement'])
            ->get()
            ->keyBy('event_id');
        
        // Calculate total spending
        $totalSpending = $payments->sum('gross_amount');
        
        // Count statistics
        $totalEvents = $registrations->count();
        $paidEvents = $registrations->filter(function($reg) {
            return $reg->event && $reg->event->price > 0;
        })->count();
        $freeEvents = $totalEvents - $paidEvents;
        $attendedEvents = $registrations->filter(function($reg) {
            return !empty($reg->attendance_status);
        })->count();
        $certifiedEvents = $registrations->filter(function($reg) {
            return !empty($reg->certificate_issued_at);
        })->count();
        $feedbackSubmitted = $registrations->filter(function($reg) {
            return !empty($reg->feedback_submitted_at);
        })->count();
        
        return view('profile.events', compact(
            'registrations', 
            'payments', 
            'totalSpending',
            'totalEvents',
            'paidEvents',
            'freeEvents',
            'attendedEvents',
            'certifiedEvents',
            'feedbackSubmitted'
        ));
    }
}

