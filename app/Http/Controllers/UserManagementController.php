<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        // Only show admin accounts and allow filtering
        $query = User::where('role', 'admin');
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
            'role' => ['required', Rule::in(['admin'])],
            'avatar' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],
        ]);
        // Force role to admin
        $data['role'] = 'admin';
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
        }
        User::create($data);
        return redirect()->route('admin.users.index')->with('success','User created');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'password' => ['nullable','string','min:6'],
            'role' => ['required', Rule::in(['admin'])],
            'avatar' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],
        ]);
        // Force role to admin
        $data['role'] = 'admin';
        if(!empty($data['password'])){
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        // Handle avatar upload and remove old file if local
        if($request->hasFile('avatar')){
            $file = $request->file('avatar');
            $filename = uniqid('ava_').'.'.$file->getClientOriginalExtension();
            if(!Storage::disk('public')->exists('avatars')){
                Storage::disk('public')->makeDirectory('avatars');
            }
            Storage::disk('public')->putFileAs('avatars', $file, $filename);
            // Delete old local avatar if exists and not an external URL
            if($user->avatar && !str_starts_with($user->avatar, 'http')){
                $oldPath = 'avatars/'.$user->avatar;
                if(Storage::disk('public')->exists($oldPath)){
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $data['avatar'] = $filename;
        }
        $user->update($data);
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
