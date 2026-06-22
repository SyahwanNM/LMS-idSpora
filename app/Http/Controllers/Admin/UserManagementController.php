<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        // Show admin, event_admin, and allow filtering
        $query = User::whereIn('role', ['admin', 'event_admin']);
        if($search = $request->get('q')){
            $query->where(function($q) use ($search){
                $q->where('name','like',"%{$search}%")
                  ->orWhere('email','like',"%{$search}%");
            });
        }
        $users = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:6','confirmed'],
            'role' => ['required', Rule::in(['admin', 'event_admin'])],
            'avatar' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],
            'avatar_base64' => ['nullable','string'],
        ]);
        // email_verified_at set now so user can login immediately
        $data['email_verified_at'] = now();
        $data['password'] = Hash::make($data['password']);
        // Handle avatar upload (optional)
        if($request->hasFile('avatar')){
            $file = $request->file('avatar');
            $filename = uniqid('ava_').'.'.$file->getClientOriginalExtension();
            // Ensure directory exists and store via public disk so files appear under public/storage/avatars
            if(!Storage::disk('public')->exists('avatars')){
                Storage::disk('public')->makeDirectory('avatars');
            }
            Storage::disk('public')->putFileAs('avatars', $file, $filename);
            $data['avatar'] = $filename;
        } elseif($request->filled('avatar_base64')) {
            // Accept base64-encoded avatar (data URL)
            $dataUrl = (string) $request->input('avatar_base64');
            if(preg_match('/^data:(image\/(png|jpe?g|webp));base64,(.+)$/i', $dataUrl, $m)){
                $mime = strtolower($m[1]);
                $ext = $m[2] === 'jpeg' || $m[2] === 'jpg' ? 'jpg' : ($m[2] === 'png' ? 'png' : 'webp');
                $payload = base64_decode($m[3]);
                if($payload !== false){
                    $filename = uniqid('ava_').'.'.$ext;
                    if(!Storage::disk('public')->exists('avatars')){
                        Storage::disk('public')->makeDirectory('avatars');
                    }
                    Storage::disk('public')->put('avatars/'.$filename, $payload);
                    $data['avatar'] = $filename;
                }
            }
        }
        User::create($data);
        return redirect()->route('admin.users.index')->with('success','User created');
    }

    public function edit(User $user)
    {
        $events = \App\Models\Event::select('id', 'title', 'event_date')
            ->orderBy('event_date', 'desc')
            ->get();
        $assignedEventIds = \Illuminate\Support\Facades\DB::table('event_admin_assignments')
            ->where('user_id', $user->id)
            ->pluck('event_id')
            ->toArray();
        return view('admin.users.edit', compact('user', 'events', 'assignedEventIds'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'password' => ['nullable','string','min:6'],
            'role' => ['required', Rule::in(['admin', 'event_admin'])],
            'assigned_events' => ['nullable', 'array'],
            'assigned_events.*' => ['integer', 'exists:events,id'],
            'avatar' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],
            'avatar_base64' => ['nullable','string'],
            'remove_avatar' => ['nullable'],
        ]);
        if(!empty($data['password'])){
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        // If explicitly removing avatar, delete old file and nullify
        $removeAvatar = filter_var($request->input('remove_avatar'), FILTER_VALIDATE_BOOLEAN);
        if($removeAvatar){
            if($user->avatar && !str_starts_with($user->avatar, 'http')){
                $oldPath = str_contains($user->avatar, '/') ? ltrim($user->avatar, '/') : ('avatars/'.$user->avatar);
                if(Storage::disk('public')->exists($oldPath)){
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $data['avatar'] = null;
        }

        // Handle avatar upload and remove old file if local (only when not removing)
        if(!$removeAvatar && $request->hasFile('avatar')){
            $file = $request->file('avatar');
            $filename = uniqid('ava_').'.'.$file->getClientOriginalExtension();
            if(!Storage::disk('public')->exists('avatars')){
                Storage::disk('public')->makeDirectory('avatars');
            }
            Storage::disk('public')->putFileAs('avatars', $file, $filename);
            // Delete old local avatar if exists and not an external URL
            if($user->avatar && !str_starts_with($user->avatar, 'http')){
                $oldPath = str_contains($user->avatar, '/') ? ltrim($user->avatar, '/') : ('avatars/'.$user->avatar);
                if(Storage::disk('public')->exists($oldPath)){
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $data['avatar'] = $filename;
        } elseif(!$removeAvatar && $request->filled('avatar_base64')) {
            // Accept base64-encoded avatar (data URL)
            $dataUrl = (string) $request->input('avatar_base64');
            if(preg_match('/^data:(image\/(png|jpe?g|webp));base64,(.+)$/i', $dataUrl, $m)){
                $mime = strtolower($m[1]);
                $ext = $m[2] === 'jpeg' || $m[2] === 'jpg' ? 'jpg' : ($m[2] === 'png' ? 'png' : 'webp');
                $payload = base64_decode($m[3]);
                if($payload !== false){
                    $filename = uniqid('ava_').'.'.$ext;
                    if(!Storage::disk('public')->exists('avatars')){
                        Storage::disk('public')->makeDirectory('avatars');
                    }
                    Storage::disk('public')->put('avatars/'.$filename, $payload);
                    // Delete old local avatar if exists and not an external URL
                    if($user->avatar && !str_starts_with($user->avatar, 'http')){
                        $oldPath = str_contains($user->avatar, '/') ? ltrim($user->avatar, '/') : ('avatars/'.$user->avatar);
                        if(Storage::disk('public')->exists($oldPath)){
                            Storage::disk('public')->delete($oldPath);
                        }
                    }
                    $data['avatar'] = $filename;
                }
            }
        }
        $user->update($data);

        // Sync event_admin_assignments
        if ($data['role'] === 'event_admin') {
            $assignedIds = array_map('intval', $request->input('assigned_events', []));
            \Illuminate\Support\Facades\DB::table('event_admin_assignments')
                ->where('user_id', $user->id)
                ->delete();
            foreach ($assignedIds as $eventId) {
                \Illuminate\Support\Facades\DB::table('event_admin_assignments')->insert([
                    'user_id'    => $user->id,
                    'event_id'   => $eventId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } else {
            // Clear assignments if role changed away from event_admin
            \Illuminate\Support\Facades\DB::table('event_admin_assignments')
                ->where('user_id', $user->id)
                ->delete();
        }

        return redirect()->route('admin.users.index')->with('success','User updated');
    }

    public function destroy(User $user)
    {
        if(auth()->id() === $user->id){
            return redirect()->route('admin.users.index')->with('error','Tidak dapat menghapus akun Anda sendiri');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success','User deleted');
    }
}
