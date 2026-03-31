@extends('layouts.trainer')

@section('title', 'Riwayat Kegiatan - Trainer')

@php
  $pageTitle = 'Riwayat Kegiatan';
  $breadcrumbs = [
    ['label' => 'Home', 'url' => route('trainer.dashboard')],
    ['label' => 'Riwayat Kegiatan']
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
                <path
                  d="M12 3l1.912 5.813a2 2 0 001.899 1.374h6.098l-4.931 3.582a2 2 0 00-.728 2.236l1.912 5.813-4.931-3.582a2 2 0 00-2.342 0l-4.931 3.582 1.912-5.813a2 2 0 00-.728-2.236L2.091 10.187h6.098a2 2 0 001.899-1.374L12 3z" />
              </svg>
              <span>RIWAYAT KEGIATAN</span>
            </span>
            <h1>Riwayat <span>Kegiatan</span></h1>
            <h5>Semua aktivitas, kelas, dan event yang pernah Anda ikuti atau ajar, lengkap dengan status dan sertifikat.
            </h5>
          </div>
        </div>
      </div>
    </section>

    <section class="card-course" style="grid-template-columns: 1fr;">
      @if(($historyItems ?? collect())->isEmpty())
        <div
          style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: 16px; border: 1px dashed #cbd5e1;">
          <i class="bi bi-inbox text-muted" style="font-size: 2.5rem; margin-bottom: 12px; display:block;"></i>
          <h4 style="color: #1a237e; font-weight: 800; margin-bottom: 8px;">Belum Ada Kegiatan</h4>
          <p style="color: #64748b; font-size: 14px; margin: 0;">Anda belum memiliki riwayat kegiatan yang selesai.</p>
        </div>
      @else
        <div class="table-responsive"
          style="background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; padding: 28px 32px 24px 32px; box-shadow: 0 2px 8px rgba(27,23,99,0.06);">
          <table class="table table-sm align-middle mb-0" style="color: #1a237e;">
            <thead style="background: #f8fafc;">
              <tr>
                <th style="width: 120px; font-weight: 600;">Tipe</th>
                <th style="font-weight: 600;">Tanggal</th>
                <th style="font-weight: 400;">Nama Acara</th>
                <th style="width: 110px; font-weight: 600;">Status</th>
                <th style="width: 200px; font-weight: 600;" class="text-end">Sertifikat</th>
              </tr>
            </thead>
            <tbody>
              @foreach($historyItems as $item)
                @php
                  $cert = $item['certificate'] ?? null;
                  $hasCert = !empty($cert) && !empty($cert->certificate_number) && !empty($cert->file_path);
                  $rowHighlight = !empty($item['highlight']);
                  $dateText = $item['date'] ? \Carbon\Carbon::parse($item['date'])->format('d M Y') : '-';
                @endphp
                <tr style="{{ $rowHighlight ? 'background: #fffbe6;' : '' }}">
                  @php
                    $typeText = strtoupper($item['type'] ?? '-');
                    $typeWeight = ($item['type'] ?? '') === 'course' ? 500 : 600;
                  @endphp
                  <td style="font-weight: {{ $typeWeight }};">{{ $typeText }}</td>
                  <td style="font-weight: 400;">{{ $dateText }}</td>
                  <td style="font-weight: 400;">
                    {{ $item['title'] ?? '-' }}
                    @if($rowHighlight)
                      <div class="small" style="opacity:.85; color: #fbb034;">(baru diberitahukan)</div>
                    @endif
                  </td>
                  <td style="vertical-align: middle; text-align: center;">
                    @if(($item['statusLabel'] ?? '') === 'Selesai')
                      <span class="badge"
                        style="background: rgba(34,197,94,0.13); color: #199a5b; border: none; font-weight:600; padding: 3px 16px; border-radius: 8px; font-size: 14px; letter-spacing: 0.2px; line-height: 1.6; display: inline-block; vertical-align: middle;">Selesai</span>
                    @else
                      <span class="badge bg-secondary"
                        style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a; font-weight:600; line-height: 1.6; display: inline-block; vertical-align: middle;">{{ $item['statusLabel'] ?? '-' }}</span>
                    @endif
                  </td>
                  <td class="text-end">
                    @if($hasCert)
                      <a class="btn btn-sm" style="background: #fbb034; color: #111827; font-weight: 800; border-radius: 12px;"
                        href="{{ $item['downloadUrl'] }}" target="_blank">
                        <i class="bi bi-download"></i> Sertifikat
                      </a>
                      <div class="small text-muted" style="margin-top: 6px;">
                        <code>{{ $cert->certificate_number }}</code>
                      </div>
                    @else
                      <button class="btn btn-sm" disabled
                        style="background: #f1f5f9; color: #b0b3c1; font-weight: 700; border-radius: 12px;">
                        Belum tersedia
                      </button>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </section>
  </div>
@endsection