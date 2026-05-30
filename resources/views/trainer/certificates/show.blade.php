@extends('layouts.trainer')

@section('title', 'Preview Sertifikat - Trainer')

@php
  $pageTitle = 'Preview Sertifikat';
  $breadcrumbs = [
    ['label' => 'Home', 'url' => route('trainer.dashboard')],
    ['label' => 'Sertifikat', 'url' => route('trainer.certificates.index')],
    ['label' => 'Preview']
  ];

  $titleText = $context === 'event'
    ? ($event->title ?? 'Event')
    : ($course->name ?? 'Course');

  $downloadUrl = $context === 'event'
    ? route('trainer.certificates.events.download', $event)
    : route('trainer.certificates.courses.download', $course);
@endphp

@push('styles')
<style>
  .cert-preview-container {
    width: 100%;
    margin: 0;
    padding: 0 0 40px;
  }

  .cert-hero {
    background: linear-gradient(135deg, #1b1763 0%, #2e269e 100%);
    border-radius: 20px;
    padding: 36px 40px;
    color: white;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(27, 23, 99, 0.15);
  }

  .cert-hero::after {
    content: '';
    position: absolute;
    right: -20px;
    top: -40px;
    width: 250px;
    height: 250px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
  }

  .cert-hero-content {
    position: relative;
    z-index: 2;
  }

  .cert-eyebrow {
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 1.5px;
    color: rgba(255,255,255,0.8);
    text-transform: uppercase;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  
  .cert-eyebrow::before {
    content: '';
    width: 24px;
    height: 2px;
    background: #fff;
  }

  .cert-title {
    font-size: 26px;
    font-weight: 800;
    margin: 0 0 8px;
    line-height: 1.3;
  }

  .cert-meta {
    display: flex;
    gap: 20px;
    margin-top: 20px;
    flex-wrap: wrap;
  }

  .meta-item {
    background: rgba(255,255,255,0.08);
    padding: 12px 20px;
    border-radius: 12px;
    backdrop-filter: blur(4px);
    border: 1px solid rgba(255,255,255,0.05);
  }

  .meta-label {
    font-size: 11px;
    color: rgba(255,255,255,0.6);
    text-transform: uppercase;
    margin-bottom: 4px;
    font-weight: 600;
  }

  .meta-value {
    font-size: 15px;
    font-weight: 600;
    color: #fff;
  }

  .cert-actions {
    display: flex;
    gap: 12px;
    margin-top: 28px;
  }

  .btn-download {
    background: #fff;
    color: #1b1763;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    border: none;
  }

  .btn-download:hover {
    background: #f8fafc;
    transform: translateY(-2px);
  }

  .btn-back {
    background: rgba(255,255,255,0.1);
    color: white;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    border: 1px solid rgba(255,255,255,0.2);
  }

  .btn-back:hover {
    background: rgba(255,255,255,0.2);
  }

  .cert-preview-box {
    background: white;
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
  }
  
  .cert-preview-ratio-box {
    width: 100%;
    aspect-ratio: 1.414 / 1;
    position: relative;
    overflow: hidden;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
  }

  .cert-preview-scaled {
    width: 29.7cm;
    height: 21cm;
    position: absolute;
    top: 0;
    left: 0;
    transform-origin: top left;
  }
</style>
@endpush

@section('content')
<div class="cert-preview-container">
  
  <div class="cert-hero">
    <div class="cert-hero-content">
      <div class="cert-eyebrow">DETAIL SERTIFIKAT</div>
      <h1 class="cert-title">{{ $titleText }}</h1>
      
      <div class="cert-meta">
        <div class="meta-item">
          <div class="meta-label">Penerima</div>
          <div class="meta-value">{{ $user->name ?? 'Trainer' }}</div>
        </div>
        <div class="meta-item">
          <div class="meta-label">Peran</div>
          <div class="meta-value">{{ $roleLabel ?? 'Narasumber' }}</div>
        </div>
        <div class="meta-item">
          <div class="meta-label">Nomor Sertifikat</div>
          <div class="meta-value" style="font-family: monospace; letter-spacing: 0.5px;">{{ $certificateNumber }}</div>
        </div>
      </div>

      <div class="cert-actions">
        <a href="{{ $downloadUrl }}" target="_blank" class="btn-download">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          Download PDF
        </a>
        <a href="{{ route('trainer.certificates.index') }}" class="btn-back">
          Kembali
        </a>
      </div>
    </div>
  </div>

  <div class="cert-preview-box">
    <div class="cert-preview-ratio-box">
      <div class="cert-preview-scaled">
        @include('trainer.certificates.certificate-pdf', ['is_preview' => true])
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
  (function() {
    function resizeShowCertificate() {
      const container = document.querySelector('.cert-preview-ratio-box');
      if (!container) return;
      const rect = container.getBoundingClientRect();
      if (rect.width === 0) return;
      const scale = rect.width / 1122.5; // A4 standard width is 1122.5px
      const scaled = container.querySelector('.cert-preview-scaled');
      if (scaled) {
        scaled.style.transform = `scale(${scale})`;
      }
    }
    window.addEventListener('load', resizeShowCertificate);
    window.addEventListener('resize', resizeShowCertificate);
    setTimeout(resizeShowCertificate, 50);
  })();
</script>
@endpush
