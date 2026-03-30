@extends('layouts.trainer')

@section('title', 'Sertifikat - Trainer')

@php
  $pageTitle = 'Sertifikat';
  $breadcrumbs = [
    ['label' => 'Home', 'url' => route('trainer.dashboard')],
    ['label' => 'Sertifikat']
  ];
@endphp

@section('content')
  <div class="detail-layout" style="padding-top: 24px;">
    <section class="vsa-section">
      <p class="vsa-title">RIWAYAT SERTIFIKAT</p>
      <div class="vsa-context" style="margin-top: 12px;">
        <div class="vsa-context-title">
          <span class="context-dot"></span>
          <span>INFO</span>
        </div>
        <p style="margin: 0;">
          Sistem menampilkan kelas-kelas yang pernah kamu ajar, lengkap dengan tanggal dan status <b>Selesai</b>.
          Tombol download hanya aktif jika admin sudah menerbitkan sertifikat.
        </p>
      </div>

      <p class="vsa-subtitle" style="margin-top: 18px;">DAFTAR KELAS (SELESAI)</p>
      <section class="rundown-list" style="margin-top: 14px;">
        @if(($historyItems ?? collect())->isEmpty())
          <p style="opacity: .8; margin: 0;">Belum ada riwayat kelas yang selesai.</p>
        @else
          <div class="table-responsive" style="background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.10); border-radius: 16px; padding: 12px;">
            <table class="table table-sm align-middle mb-0" style="color: rgba(255,255,255,.92);">
              <thead>
                <tr>
                  <th style="width: 120px;">Tipe</th>
                  <th>Tanggal</th>
                  <th>Nama Acara</th>
                  <th style="width: 110px;">Status</th>
                  <th style="width: 200px;" class="text-end">Sertifikat</th>
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
                  <tr style="{{ $rowHighlight ? 'background: rgba(251,191,36,.14);' : '' }}">
                    <td style="font-weight: 800;">{{ strtoupper($item['type'] ?? '-') }}</td>
                    <td>{{ $dateText }}</td>
                    <td>
                      {{ $item['title'] ?? '-' }}
                      @if($rowHighlight)
                        <div class="small" style="opacity:.85;">(baru diberitahukan)</div>
                      @endif
                    </td>
                    <td>
                      <span class="badge bg-success" style="background: rgba(34,197,94,.25) !important; border: 1px solid rgba(34,197,94,.35);">
                        {{ $item['statusLabel'] ?? 'Selesai' }}
                      </span>
                    </td>
                    <td class="text-end">
                      @if($hasCert)
                        <a class="btn btn-sm" style="background: rgba(251,191,36,.95); color: #111827; font-weight: 800; border-radius: 12px;" href="{{ $item['downloadUrl'] }}" target="_blank">
                          Download Sertifikat PDF
                        </a>
                        <div class="small text-muted" style="margin-top: 6px;">
                          <code>{{ $cert->certificate_number }}</code>
                        </div>
                      @else
                        <button class="btn btn-sm" disabled style="background: rgba(148,163,184,.18); color: rgba(255,255,255,.75); font-weight: 700; border-radius: 12px;">
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
    </section>

    <aside class="hub-card">
      <p class="hub-title">FORMAT NOMOR</p>
      <div class="hub-section">
        <p class="hub-section-title">ATURAN</p>
        <div class="hub-alert" style="margin-top: 0;">
          <p style="margin: 0;">
            <strong>IDSP/[KODE_KEGIATAN]/[KODE_JENIS]/[NOMOR_URUT]/[BULAN_ROMAWI]/[TAHUN]</strong>
          </p>
        </div>
      </div>
    </aside>
  </div>
@endsection

