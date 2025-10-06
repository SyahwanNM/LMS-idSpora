<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket / Kode Registrasi - {{ $event->title }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        body{background:#F8FAFC;font-family:'Poppins',sans-serif;}
        /* Ensure fixed-top navbar stays above ticket content */
        .navbar.fixed-top { z-index: 1050; }
        /* Avoid overlaying content behind navbar */
        body { padding-top: 66px; }
        /* Samakan top spacing dengan detail page (breadcrumb margin-top:80px) */
        .ticket-wrapper{max-width:880px;margin:20px auto 60px;padding:32px 40px;border-radius:24px;background:#fff;box-shadow:0 12px 40px -18px rgba(0,0,0,.2),0 4px 12px -4px rgba(0,0,0,.08);}        
        .code-box{background:#1e293b;color:#f4d24b;font-weight:600;font-size:26px;letter-spacing:3px;padding:16px 24px;border-radius:16px;display:inline-block;box-shadow:0 6px 18px -6px rgba(0,0,0,.35);}        
        .badge-status{background:#16a34a;color:#fff;border-radius:30px;padding:6px 18px;font-size:12px;font-weight:600;letter-spacing:.5px;}
        .meta-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:18px;margin-top:28px;}
        .meta-item{background:#f1f5f9;border:1px solid #e2e8f0;padding:14px 16px;border-radius:14px;}
        .meta-item h6{font-size:11px;text-transform:uppercase;letter-spacing:.8px;color:#64748b;margin-bottom:4px;font-weight:600;}
        .meta-item p{margin:0;font-weight:500;color:#0f172a;}
    .actions{margin-top:34px;display:flex;flex-wrap:wrap;gap:14px;position:relative;z-index:2002;}
        .actions a,.actions button{border:none;border-radius:14px;padding:12px 22px;font-weight:600;display:inline-flex;align-items:center;gap:8px;font-size:14px;text-decoration:none;}
        .btn-back{background:#334155;color:#fff;}
        .btn-back:hover{background:#1e293b;color:#f4d24b;}
        .btn-copy{background:#535088;color:#f4d24b;}
        .btn-copy:hover{background:#474273;color:#fff;}
    /* Ensure print button is clickable above any overlay */
    .actions .btn-print{ position:relative; z-index:2000; pointer-events:auto; cursor:pointer; }
        @media (max-width:768px){
            .ticket-wrapper{padding:28px 22px;}
            .code-box{font-size:22px;}
        }
    </style>
</head>
<body>
    @include('partials.navbar-after-login')
    <!-- Breadcrumb mirip detail.blade -->
    <div class="link-box mb-3">
        <a href="{{ route('dashboard') }}">Home</a>
        <p>/</p>
        <a href="{{ route('events.index') }}">Event</a>
        <p>/</p>
        <a class="active" href="{{ route('events.ticket',$event) }}">Ticket</a>
    </div>
    <div class="ticket-wrapper" style="position:relative;">
        <h2 class="mb-2">Tiket Event</h2>
        <p class="text-muted mb-4">Simpan kode registrasi ini untuk validasi / sertifikat.</p>
        @php
            $certificateReady = false;
            if($event->event_date){
                // Sertifikat siap H+4 setelah tanggal event
                $certificateReady = now()->greaterThanOrEqualTo($event->event_date->copy()->addDays(4));
            }
        @endphp
        <div class="d-flex align-items-center gap-3 flex-wrap mb-4">
            <div class="code-box" id="regCode">{{ $registration->registration_code }}</div>
            <span class="badge-status">Terdaftar</span>
            <button class="btn-copy" id="copyBtn">Salin Kode</button>
        </div>
        <h4 class="mb-1">{{ $event->title }}</h4>
        <p class="text-muted mb-3">Oleh IdSpora</p>
        <div class="meta-grid">
            <div class="meta-item">
                <h6>Tanggal</h6>
                <p>{{ $event->event_date?->format('d F Y') }}</p>
            </div>
            <div class="meta-item">
                <h6>Waktu</h6>
                <p>{{ $event->event_time?->format('H:i') }} WIB</p>
            </div>
            <div class="meta-item">
                <h6>Lokasi</h6>
                <p>{{ $event->location }}</p>
            </div>
            <div class="meta-item">
                <h6>Status</h6>
                <p>{{ ucfirst($registration->status) }}</p>
            </div>
        </div>
        <div class="actions">
            <a href="{{ route('events.show',$event) }}" class="btn-back">Kembali ke Detail</a>
            <a href="{{ route('dashboard') }}" class="btn-back" style="background:#475569;">Dashboard</a>
            @if(Route::has('certificates.show'))
                <a href="{{ route('certificates.show',[$event,$registration]) }}" class="btn-cert" style="background:#535088;color:#f4d24b;">@if($certificateReady) Lihat / Unduh Sertifikat @else Preview Sertifikat @endif</a>
                @if(app()->environment('local'))
                    <a href="{{ route('certificates.download',[$event,$registration]) }}?inline=1&force=1" class="btn-cert" style="background:#334155;color:#f4d24b;">PDF Inline</a>
                @endif
            @endif
            <button type="button" id="printBtn" class="btn-print" style="background:#f4c430;color:#000;">Print Tiket</button>
        </div>
    </div>
    <!-- Print Preview Modal -->
    <div class="modal fade" id="printPreviewModal" tabindex="-1" aria-labelledby="printPreviewLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="printPreviewLabel">Preview Tiket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-white">
                    <div id="printPreviewContainer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" id="confirmPrintBtn" class="btn btn-warning text-dark fw-semibold">Cetak</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        const copyBtn = document.getElementById('copyBtn');
        copyBtn?.addEventListener('click', ()=>{
            const codeEl = document.getElementById('regCode');
            if(!codeEl) return;
            const code = codeEl.innerText.trim();
            navigator.clipboard.writeText(code).then(()=>{
                // Visual feedback: animate check and change text briefly
                const originalHtml = copyBtn.innerHTML;
                const originalDisabled = copyBtn.disabled;
                copyBtn.disabled = true;
                copyBtn.classList.add('copied');
                copyBtn.innerHTML = `
                    <svg class="copy-anim-check" viewBox="0 0 72 72" width="22" height="22" aria-hidden="true">
                        <circle class="circle" cx="36" cy="36" r="28" fill="none" stroke="currentColor" stroke-width="4" />
                        <path class="check" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" d="M24 37 33 46 50 28" />
                    </svg>
                    <span>Kode Disalin</span>
                `;
                setTimeout(()=>{
                    copyBtn.innerHTML = originalHtml;
                    copyBtn.classList.remove('copied');
                    copyBtn.disabled = originalDisabled;
                }, 1400);
            }).catch(()=>{
                // Minimal error feedback
                const originalText = copyBtn.textContent;
                copyBtn.textContent = 'Gagal menyalin';
                setTimeout(()=> copyBtn.textContent = originalText, 1200);
            });
        });
        const printBtn = document.getElementById('printBtn');
        if(printBtn){
            printBtn.addEventListener('click', (e)=>{
                e.preventDefault();
                e.stopPropagation();
                // Clone ticket content into preview
                const source = document.querySelector('.ticket-wrapper');
                const container = document.getElementById('printPreviewContainer');
                if(source && container){
                    container.innerHTML = '';
                    const clone = source.cloneNode(true);
                    // Remove actions and breadcrumbs in preview
                    const acts = clone.querySelector('.actions');
                    if(acts) acts.remove();
                    const breadcrumb = clone.querySelector('.link-box');
                    if(breadcrumb) breadcrumb.remove();
                    clone.classList.add('print-preview-content');
                    container.appendChild(clone);
                }
                // Show modal
                const modalEl = document.getElementById('printPreviewModal');
                if(window.bootstrap && modalEl){
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                }
            });
        }
        // Confirm print from preview
        const confirmPrintBtn = document.getElementById('confirmPrintBtn');
        if(confirmPrintBtn){
            confirmPrintBtn.addEventListener('click', (e)=>{
                e.preventDefault();
                setTimeout(()=> window.print(), 0);
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
    .actions .btn-cert, .actions .btn-print { text-decoration:none; }
        .actions .btn-print:hover { filter:brightness(.9); }
        .actions .btn-cert:hover { filter:brightness(1.1); }
        /* Copy button feedback styles */
        .btn-copy { position: relative; overflow: hidden; }
        .btn-copy.copied { filter: brightness(1.05); }
        .copy-anim-check { display:inline-block; margin-right:6px; }
        .copy-anim-check .circle { stroke-dasharray: 176; stroke-dashoffset:176; animation: copy-draw-circle .45s ease-out forwards; opacity:.9; }
        .copy-anim-check .check { stroke-dasharray: 36; stroke-dashoffset:36; animation: copy-draw-check .35s ease-out .35s forwards; }
        @keyframes copy-draw-circle { to { stroke-dashoffset:0; } }
        @keyframes copy-draw-check { to { stroke-dashoffset:0; } }
        @media print {
            body { background:#fff !important; }
            .link-box, .actions #printBtn, .actions #copyBtn, .actions .btn-back { display:none !important; }
            .ticket-wrapper { box-shadow:none !important; margin:0 !important; max-width:100% !important; padding:20px 10px !important; }
            /* Print only the preview content when modal is open */
            body.modal-open .ticket-wrapper { display:none !important; }
            .modal-header, .modal-footer, .modal-backdrop { display:none !important; }
            .modal .modal-dialog { max-width:100% !important; margin:0 !important; }
            .modal .modal-body { padding:0 !important; }
            .modal .print-preview-content { box-shadow:none !important; margin:0 !important; max-width:100% !important; padding:20px 10px !important; }
        }
        /* Lower footer stacking so it can't intercept clicks on this page */
        .footer-section{ position:relative; z-index:1; }
        .ticket-wrapper{ position:relative; z-index:10; }
        /* Preview container adjustments */
        #printPreviewContainer .code-box{ box-shadow:0 6px 18px -6px rgba(0,0,0,.2); }
        .modal #printPreviewContainer .actions, .modal #printPreviewContainer .link-box { display:none !important; }
    </style>
    @include('partials.footer-before-login')
</body>
</html>