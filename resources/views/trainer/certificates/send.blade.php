@extends('layouts.trainer')

@section('title', 'Kirim Sertifikat')

@php
    $pageTitle = 'Kirim Sertifikat';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('trainer.dashboard')],
        ['label' => 'Kirim Sertifikat']
    ];
@endphp

@section('content')
    <div class="courses-page">
        <section class="top-page">
            <div class="glow-circle glow-circle-1"></div>
            <div class="glow-circle glow-circle-2"></div>
            <div class="top-page-inner">
                <div class="top-page-content">
                    <div class="title-page">
                        <span class="badge-top">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 19V5M5 12l7-7 7 7" />
                            </svg>
                            <span>KIRIM SERTIFIKAT</span>
                        </span>
                        <h1>Kirim Sertifikat</h1>
                        <h5>Upload dan kelola pengiriman sertifikat untuk peserta atau event yang Anda handle.</h5>
                    </div>
                </div>
            </div>
        </section>
        <section class="card-course" style="grid-template-columns: 1fr;">
            <div
                style="background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; padding: 32px; box-shadow: 0 2px 8px rgba(27,23,99,0.06);">
                <h4 style="margin-bottom: 18px;">Form Kirim Sertifikat</h4>
                <form method="POST" action="#" enctype="multipart/form-data">
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
        </section>
    </div>
@endsection