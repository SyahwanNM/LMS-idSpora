@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Detail Event</h2>
    <div class="card p-4">
        <div class="row">
            <div class="col-md-4">
                <img src="{{ asset('storage/' . $event->image) }}" alt="event" class="img-fluid rounded">
            </div>
            <div class="col-md-8">
                <h4>{{ $event->title }}</h4>
                <p><strong>Pembicara:</strong> {{ $event->speaker }}</p>
                <p><strong>Deskripsi:</strong> {{ $event->description }}</p>
                <p><strong>Lokasi:</strong> {{ $event->location }}</p>
                <p><strong>Tanggal:</strong> {{ $event->event_date }}</p>
                <p><strong>Waktu:</strong> {{ $event->event_time }}</p>
                <p><strong>Harga:</strong> Rp{{ number_format($event->price, 0, ',', '.') }}</p>
                <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-warning">Edit</a>
                <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection