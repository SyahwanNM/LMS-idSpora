<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

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
            // email is optional on profile edit (account settings handles email changes)
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
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
            'profession' => 'nullable|string|in:Pelajar/Mahasiswa,Karyawan Swasta,ASN/PNS,Wirausaha,Lainnya',
            'institution' => 'nullable|string|max:255',
        ]);

        $user->name = $validated['name'];
        if (array_key_exists('email', $validated)) {
            $user->email = $validated['email'];
        }
        
        // Update password if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        
        \Log::info('Profile Update Request', [
            'has_avatar' => $request->hasFile('avatar'),
            'all_files' => array_keys($request->allFiles()),
            'user_id' => $user->id
        ]);

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
        $user->institution = $validated['institution'] ?? null;
        $user->profession = $validated['profession'] ?? null;
        
        $user->save();
        
        // Auto-deactivate reminder jika profile sudah lengkap
        $reminderService = app(ProfileReminderService::class);
        if ($user->isProfileComplete()) {
            $reminderService->deactivateReminder($user);
        }
        
        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }
    
    public function history()
    {
        $user = Auth::user();
        
        // Get all event registrations with event details
        $registrations = EventRegistration::where('user_id', $user->id)
            ->with(['event', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get all course enrollments
        $enrollments = \App\Models\Enrollment::where('user_id', $user->id)
            ->with(['course', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get all manual payments for this user
        $payments = \App\Models\ManualPayment::where('user_id', $user->id)
            ->where('status', 'settled')
            ->get()
            ->keyBy('id'); // Use ID as key, not event_id to avoid collision
        
        // Count statistics
        $totalEventsCount = $registrations->count();
        $totalCoursesCount = $enrollments->count();
        $certifiedEvents = $registrations->filter(function($reg) {
            return !empty($reg->certificate_issued_at);
        })->count();
        $certifiedCourses = $enrollments->filter(function($enr) {
            return !empty($enr->certificate_issued_at);
        })->count();
        
        $feedbackSubmitted = $registrations->filter(function($reg) {
            return !empty($reg->feedback_submitted_at);
        })->count();
        $reviewsSubmitted = \App\Models\Review::where('user_id', $user->id)->count();
        
        // Fetch saved item IDs to mark them in the UI
        $savedEventIds = \DB::table('user_saved_events')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->pluck('event_id')
            ->all();

        // Build a map of event_id => saved_at for display
        $savedEventAtMap = \DB::table('user_saved_events')
            ->where('user_id', $user->id)
            ->pluck('created_at', 'event_id');

        $savedCourseIds = \DB::table('user_saved_courses')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->pluck('course_id')
            ->all();

        // Build a map of course_id => saved_at for display
        $savedAtMap = \DB::table('user_saved_courses')
            ->where('user_id', $user->id)
            ->pluck('created_at', 'course_id');

        // Also fetch the full saved items for the 'Saved' tab
        $savedEvents = \App\Models\Event::whereIn('id', $savedEventIds)
            ->orderByRaw(empty($savedEventIds) ? '1' : 'FIELD(id, ' . implode(',', array_map('intval', $savedEventIds)) . ')')
            ->get();
        $savedCourses = \App\Models\Course::whereIn('id', $savedCourseIds)
            ->orderByRaw('FIELD(id, ' . implode(',', array_map('intval', $savedCourseIds ?: [0])) . ')')
            ->get();
            
        $activitiesLogs = \App\Models\ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return view('profile.history', compact(
            'registrations', 
            'enrollments',
            'payments', 
            'totalEventsCount',
            'totalCoursesCount',
            'certifiedEvents',
            'certifiedCourses',
            'feedbackSubmitted',
            'reviewsSubmitted',
            'savedEventIds',
            'savedCourseIds',
            'savedEvents',
            'savedCourses',
            'savedAtMap',
            'savedEventAtMap',
            'activitiesLogs'
        ));
    }
    
    public function settings()
    {
        $user = Auth::user();
        // Redirect to edit profile as default
        return redirect()->route('profile.edit');
    }
    
    public function accountSettings()
    {
        $user = Auth::user();
        return view('profile.account-settings', compact('user'));
    }
    
    public function updateAccountSettings(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            // UI uses "Email Baru" (optional). If empty, keep current email.
            'new_email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'required_with:password',
            'password' => 'nullable|min:6|confirmed',
        ]);
        
        // Verify current password if changing password
        if (!empty($validated['password'])) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])->withInput();
            }
            $user->password = Hash::make($validated['password']);
        }
        
        if (!empty($validated['new_email'])) {
            $user->email = $validated['new_email'];
        }
        $user->save();
        
        return redirect()->route('profile.account-settings')->with('success', 'Pengaturan akun berhasil diperbarui!');
    }
}
