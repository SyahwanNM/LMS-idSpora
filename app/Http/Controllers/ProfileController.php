<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\EventRegistration;
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
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
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
        $user->phone = $validated['phone'] ?? null;
        $user->website = $validated['website'] ?? null;
        $user->bio = $validated['bio'] ?? null;
        
        $user->save();
        
        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }
    
    public function events()
    {
        $user = Auth::user();
        
        // Get all event registrations with event details
        $registrations = EventRegistration::where('user_id', $user->id)
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('profile.events', compact('registrations'));
    }
}

