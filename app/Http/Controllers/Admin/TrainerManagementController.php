<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Validation\Rule;

class TrainerManagementController extends Controller
{
    // Menampilkan daftar trainer
    public function index(Request $request)
    {
        $totalTrainers = User::where('role', 'trainer')->count();
        $activeTrainers = User::where('role', 'trainer')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $teachingTrainers = User::where('role', 'trainer')
            ->whereHas('coursesAsTrainer')
            ->orWhereHas('eventsAsTrainer')
            ->count();

        $query = User::where('role', 'trainer')
            ->withCount(['coursesAsTrainer', 'eventsAsTrainer'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // Sorting Logic
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'name_asc': $query->orderBy('name', 'asc'); break;
                case 'name_desc': $query->orderBy('name', 'desc'); break;
                case 'newest': $query->orderBy('created_at', 'desc'); break;
                case 'oldest': $query->orderBy('created_at', 'asc'); break;
            }
        }

        $trainers = $query->paginate(10);

        return view('admin.trainer.index', compact('trainers', 'totalTrainers', 'activeTrainers', 'teachingTrainers'));
    }

    public function create()
    {
        return view('admin.trainer.create');
    }

    // [UPDATED] SIMPAN TRAINER BARU
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'profession' => ['nullable', 'string', 'max:100'], // Field Baru
            'institution' => ['nullable', 'string', 'max:100'], // Field Baru
            'bio' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // Validasi Foto
        ]);

        $data['role'] = 'trainer';
        $data['password'] = Hash::make($data['password']);

        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        User::create($data);

        return redirect()->route('admin.trainer.index')->with('success', 'Trainer berhasil dibuat!');
    }

    public function show(User $trainer)
    {
        if ($trainer->role !== 'trainer') abort(404);
        $trainer->loadCount(['coursesAsTrainer', 'eventsAsTrainer']);
        return view('admin.trainer.show', compact('trainer'));
    }

    public function edit(User $trainer)
    {
        if ($trainer->role !== 'trainer') abort(404);
        return view('admin.trainer.edit', compact('trainer'));
    }

    // [UPDATED] UPDATE TRAINER (YANG ANDA CARI)
    public function update(Request $request, User $trainer)
    {
        if ($trainer->role !== 'trainer') abort(404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($trainer->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'profession' => ['nullable', 'string', 'max:100'], 
            'institution' => ['nullable', 'string', 'max:100'], 
            'bio' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Cek apakah password diisi (jika kosong, jangan update password)
        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($trainer->avatar && !str_starts_with($trainer->avatar, 'http')) {
                Storage::disk('public')->delete($trainer->avatar);
            }
            // Simpan yang baru
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $trainer->update($data);

        return redirect()->route('admin.trainer.index')->with('success', 'Data trainer berhasil diperbarui!');
    }

    public function destroy(User $trainer)
    {
        if ($trainer->role !== 'trainer') abort(404);

        if ($trainer->avatar && !str_starts_with($trainer->avatar, 'http')) {
            Storage::disk('public')->delete($trainer->avatar);
        }

        $name = $trainer->name;
        $trainer->delete();

        return redirect()->route('admin.trainer.index')->with('success', "Trainer {$name} berhasil dihapus!");
    }
}