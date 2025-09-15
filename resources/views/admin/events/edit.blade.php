@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Edit Event</h2>
    <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data" class="card p-4">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Judul Event</label>
            <input type="text" name="title" class="form-control" required value="{{ old('title', $event->title) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Pembicara</label>
            <input type="text" name="speaker" class="form-control" required value="{{ old('speaker', $event->speaker) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="4" required>{{ old('description', $event->description) }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Lokasi</label>
            <input type="text" name="location" class="form-control" required value="{{ old('location', $event->location) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Harga</label>
            <input type="number" name="price" class="form-control" required min="0" value="{{ old('price', $event->price) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Tanggal Event</label>
            <input type="date" name="event_date" class="form-control" required value="{{ old('event_date', $event->event_date) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Waktu Event</label>
            <input type="time" name="event_time" class="form-control" required value="{{ old('event_time', $event->event_time) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Gambar Event</label>
            <input type="file" name="image" class="form-control" id="imageInput" accept="image/*">
            <small class="text-muted">Kosongkan jika tidak ingin mengganti gambar.</small>
            <div class="mt-2">
                <div>
                    <span class="fw-bold">Gambar Sebelumnya:</span><br>
                    <img id="oldImagePreview" src="{{ $event->image ? Storage::url($event->image) : '' }}" alt="event" width="120" style="{{ $event->image ? '' : 'display:none;' }};border-radius:8px;">
                </div>
                <div class="mt-2">
                    <span class="fw-bold">Gambar Baru:</span><br>
                    <img id="newImagePreview" src="#" alt="Preview" width="120" style="display:none;border-radius:8px;">
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('imageInput').addEventListener('change', function(event) {
        const [file] = event.target.files;
        const newPreview = document.getElementById('newImagePreview');
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                newPreview.src = e.target.result;
                newPreview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            newPreview.src = '#';
            newPreview.style.display = 'none';
        }
    });
</script>
@endsection