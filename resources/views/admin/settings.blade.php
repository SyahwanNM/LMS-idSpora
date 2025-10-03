@extends('layouts.app')

@section('title','Admin Settings')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Pengaturan Sistem</h1>

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

    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8 bg-white rounded-xl shadow p-6 border border-gray-200">
        @csrf
        <div class="grid md:grid-cols-2 gap-8">
            <div class="space-y-4">
                <h2 class="font-semibold text-gray-700 flex items-center gap-2 text-sm uppercase tracking-wide">Mode & Akses</h2>
                <div class="flex items-start gap-3">
                    <input type="checkbox" name="maintenance_mode" id="maintenance_mode" value="1" class="mt-1 h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500" {{ old('maintenance_mode', $settings['maintenance_mode'] ?? false) ? 'checked' : '' }}>
                    <label for="maintenance_mode" class="text-sm text-gray-600 leading-relaxed">
                        Aktifkan Maintenance Mode<br>
                        <span class="text-xs text-gray-400">Menampilkan pesan pemeliharaan kepada pengguna non-admin.</span>
                    </label>
                </div>
                <div class="flex items-start gap-3">
                    <input type="checkbox" name="allow_registration" id="allow_registration" value="1" class="mt-1 h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500" {{ old('allow_registration', $settings['allow_registration'] ?? true) ? 'checked' : '' }}>
                    <label for="allow_registration" class="text-sm text-gray-600 leading-relaxed">
                        Izinkan Registrasi Baru<br>
                        <span class="text-xs text-gray-400">Matikan untuk menghentikan pendaftaran user baru.</span>
                    </label>
                </div>
            </div>
            <div class="space-y-4">
                <h2 class="font-semibold text-gray-700 flex items-center gap-2 text-sm uppercase tracking-wide">Brand & Tema</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Warna Utama (Hex)</label>
                    <input type="text" name="primary_color" value="{{ old('primary_color', $settings['primary_color'] ?? '#f59e0b') }}" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="#f59e0b">
                    <p class="text-xs text-gray-400 mt-1">Digunakan untuk aksen tombol & highlight.</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-xs text-gray-500">Preview:</div>
                    <div id="colorPreview" class="w-10 h-10 rounded-full border shadow-inner" style="background: {{ old('primary_color', $settings['primary_color'] ?? '#f59e0b') }}"></div>
                </div>
            </div>
        </div>

        <div class="pt-4 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali</a>
            <button type="submit" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition">
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const colorInput = document.querySelector('input[name=primary_color]');
if(colorInput){
  const prev = document.getElementById('colorPreview');
  colorInput.addEventListener('input', ()=>{ prev.style.background = colorInput.value; });
}
</script>
@endpush
