@extends('layouts.admin')

@section('title', 'Kirim Sertifikat untuk Trainer')

@section('content')
    <div class="container">
        <h1>Kirim Sertifikat untuk {{ $trainer->name ?? 'Trainer' }}</h1>
        <form method="POST" action="{{ route('admin.trainer.certificates.send', $trainer) }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="recipient" class="form-label">Nama Penerima / Email</label>
                <input type="text" class="form-control" id="recipient" name="recipient" required>
            </div>
            <div class="mb-3">
                <label for="certificate_file" class="form-label">File Sertifikat (PDF)</label>
                <input type="file" class="form-control" id="certificate_file" name="certificate_file"
                    accept="application/pdf" required>
            </div>
            <button type="submit" class="btn btn-primary">Kirim Sertifikat</button>
        </form>
    </div>
@endsection