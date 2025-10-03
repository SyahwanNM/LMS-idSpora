@extends('layouts.app')

@section('title','Admin Profile')

@section('content')
<div class="max-w-3xl mx-auto py-10 px-4">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Profil Saya</h1>

    @if(session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-700 text-sm">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 rounded bg-red-100 text-red-600 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-6 bg-white rounded-xl shadow p-6 border border-gray-200">
        @csrf
            <div class="flex items-center gap-4 pb-2">
                <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-20 h-20 rounded-full object-cover border border-gray-300">
                <div class="text-xs text-gray-500 leading-snug">
                    <p>Avatar otomatis disinkron dari Google saat login.</p>
                    <p class="mt-1">Fitur upload avatar lokal bisa ditambahkan nanti.</p>
                </div>
            </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500" required>
        </div>
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru (opsional)</label>
                <input type="password" name="password" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="Biarkan kosong jika tidak diganti">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="Ulangi password baru">
            </div>
        </div>
        <div class="flex items-center justify-between pt-4">
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali</a>
            <button type="submit" class="inline-flex items-center bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
