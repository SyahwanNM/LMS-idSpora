@extends('layouts.trainer')

@section('title', 'Preview Sertifikat - Trainer')

@php
  $pageTitle = 'Preview Sertifikat';
  $breadcrumbs = [
    ['label' => 'Home', 'url' => route('trainer.dashboard')],
    ['label' => 'Sertifikat', 'url' => route('trainer.certificates.index')],
    ['label' => 'Preview']
  ];

  $title = $context === 'event'
    ? ($event->title ?? 'Event')
    : ($course->name ?? 'Course');

  $downloadUrl = $context === 'event'
    ? route('trainer.certificates.events.download', $event)
    : route('trainer.certificates.courses.download', $course);
@endphp

@section('content')
  <div class="detail-layout" style="padding-top: 24px;">
    <section class="vsa-section">
      <p class="vsa-title">SERTIFIKAT TRAINER</p>

      <div class="vsa-context" style="margin-top: 12px;">
        <div class="vsa-context-title">
          <span class="context-dot"></span>
          <span>DETAIL</span>
        </div>
        <p style="margin: 0;">
          <strong>{{ $title }}</strong><br>
          Penerima: <strong>{{ $user->name ?? 'Trainer' }}</strong><br>
          Peran: <strong>{{ $roleLabel ?? 'Narasumber' }}</strong><br>
          Nomor: <code>{{ $certificateNumber }}</code>
        </p>
      </div>

      <div style="display:flex; gap: 10px; flex-wrap: wrap; margin-top: 14px;">
        <a class="vsa-btn vsa-btn-amber" href="{{ $downloadUrl }}" target="_blank">DOWNLOAD PDF</a>
        <a class="vsa-btn vsa-btn-primary" href="{{ route('trainer.certificates.index') }}">KEMBALI</a>
      </div>

      <div style="margin-top: 16px; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12); border-radius: 16px; padding: 14px; overflow: auto;">
        <div style="width: 1122px; height: 794px; background: white; border-radius: 6px; overflow: hidden;">
          <div style="transform-origin: top left; transform: scale(.9); width: 29.7cm; height: 21cm;">
            @include('trainer.certificates.certificate-pdf', ['is_preview' => true])
          </div>
        </div>
      </div>
    </section>

    <aside class="hub-card">
      <p class="hub-title">KUSTOM</p>
      <div class="hub-section">
        <p class="hub-section-title">UBAH KODE (OPSIONAL)</p>
        <p style="opacity: .85; margin: 0 0 10px;">
          Kamu bisa override kode via query string:
        </p>
        <code style="display:block; padding: 10px 12px; border-radius: 12px; background: rgba(0,0,0,.35); color: #fff;">
          ?activity=WBN&type=TRN&seq=001
        </code>
      </div>
    </aside>
  </div>
@endsection

