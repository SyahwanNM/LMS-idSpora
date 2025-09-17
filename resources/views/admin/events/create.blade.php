@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Tambah Event Baru</h2>
    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" class="card p-4">
        @csrf
        <div class="mb-3">
            <label class="form-label">Judul Event</label>
            <input type="text" name="title" class="form-control" required value="{{ old('title') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Pembicara</label>
            <input type="text" name="speaker" class="form-control" required value="{{ old('speaker') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Lokasi</label>
            <input type="text" name="location" class="form-control" required value="{{ old('location') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Harga</label>
            <input type="number" name="price" class="form-control" required min="0" value="{{ old('price', 0) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Tanggal Event</label>
            <input type="date" name="event_date" class="form-control" required value="{{ old('event_date') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Waktu Event</label>
            <input type="time" name="event_time" class="form-control" required value="{{ old('event_time') }}">
        </div>
        <div class="mb-3">
            <label>Gambar Event</label>
            <input type="file" name="image" class="form-control" id="imageInput" accept="image/*" required>
            <img id="imagePreview" src="#" alt="Preview" style="display:none;max-width:100%;margin-top:10px;border-radius:8px;">
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('imageInput').addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('imagePreview');
                img.src = e.target.result;
                img.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            document.getElementById('imagePreview').style.display = 'none';
        }
    });
</script>
@endsection