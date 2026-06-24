{{-- Scheme Selection & E-Agreement Modal for Event/Course Invitations --}}
<div class="modal fade scheme-modal" id="schemeSelectionModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0">
            <form id="schemeSelectionForm" method="POST" style="display: none;">
                @csrf
                <input type="hidden" name="notification_id" id="notification_id">
            </form>
            <div class="modal-header border-0 scheme-modal-header">
                <div class="header-content-wrap">
                    <div class="header-rocket-orb">
                        <i class="bi bi-rocket-takeoff-fill"></i>
                        <div class="orb-ring-1"></div>
                        <div class="orb-ring-2"></div>
                    </div>
                    <div class="header-text">
                        <h5 class="modal-title scheme-modal-title">Konfirmasi Penugasan</h5>
                        <p class="scheme-modal-subtitle">Anda akan diundang menjadi Trainer Course</p>
                    </div>
                </div>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>


            <div class="modal-body px-4 py-3 scheme-modal-body">
                {{-- Detail Course & Role Cards --}}
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="detail-info-card">
                            <div class="detail-info-icon"><i class="bi bi-journal-bookmark-fill"></i></div>
                            <div class="detail-info-content">
                                <span class="detail-info-label">COURSE</span>
                                <span class="detail-info-value" id="detailCourseName">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="detail-info-card">
                            <div class="detail-info-icon"><i class="bi bi-person-fill"></i></div>
                            <div class="detail-info-content">
                                <span class="detail-info-label">DIUNDANG SEBAGAI</span>
                                <span class="detail-info-value" id="detailRoleName">Trainer Course</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 1 --}}
                <div class="scheme-modal-step mb-3">
                    <div class="step-header-new mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="step-number">1</span>
                            <p class="scheme-modal-section-title mb-0">Pilih Skema Beban Kerja</p>
                        </div>
                        <p class="scheme-modal-section-help">Tentukan skema berdasarkan materi yang akan Anda unggah.</p>
                    </div>

                    <div class="row g-2 scheme-cards-container">
                        {{-- Card Penuh --}}
                        <div class="col-4">
                            <div class="scheme-card-vertical-wrapper">
                                <input type="radio" name="scheme_type" value="1" id="scheme1" class="scheme-radio" onchange="window.syncSchemeSelectionState && window.syncSchemeSelectionState()">
                                <label for="scheme1" class="scheme-label-vertical">
                                    <div class="scheme-radio-circle"></div>
                                    <div class="scheme-vertical-icon-wrapper text-plum">
                                        <i class="bi bi-trophy-fill"></i>
                                    </div>
                                    <div class="scheme-vertical-title">Beban Kerja Penuh</div>
                                    <div class="scheme-vertical-desc">Upload Modul,<br>Video & Kuis</div>
                                    <div class="scheme-vertical-divider"></div>
                                    <div class="scheme-vertical-rev-label">Revenue Sharing</div>
                                    <div class="scheme-vertical-percent text-plum">35%</div>
                                    <span class="scheme-vertical-badge">BEST VALUE</span>
                                </label>
                            </div>
                        </div>

                        {{-- Card Menengah --}}
                        <div class="col-4">
                            <div class="scheme-card-vertical-wrapper">
                                <input type="radio" name="scheme_type" value="2" id="scheme2" class="scheme-radio" onchange="window.syncSchemeSelectionState && window.syncSchemeSelectionState()">
                                <label for="scheme2" class="scheme-label-vertical">
                                    <div class="scheme-radio-circle"></div>
                                    <div class="scheme-vertical-icon-wrapper text-blue">
                                        <i class="bi bi-book-half"></i>
                                    </div>
                                    <div class="scheme-vertical-title">Beban Kerja Menengah</div>
                                    <div class="scheme-vertical-desc">Upload Modul<br>& Video saja</div>
                                    <div class="scheme-vertical-divider"></div>
                                    <div class="scheme-vertical-rev-label">Revenue Sharing</div>
                                    <div class="scheme-vertical-percent text-blue">25%</div>
                                </label>
                            </div>
                        </div>

                        {{-- Card Ringan --}}
                        <div class="col-4">
                            <div class="scheme-card-vertical-wrapper">
                                <input type="radio" name="scheme_type" value="3" id="scheme3" class="scheme-radio" onchange="window.syncSchemeSelectionState && window.syncSchemeSelectionState()">
                                <label for="scheme3" class="scheme-label-vertical">
                                    <div class="scheme-radio-circle"></div>
                                    <div class="scheme-vertical-icon-wrapper text-green">
                                        <i class="bi bi-camera-video"></i>
                                    </div>
                                    <div class="scheme-vertical-title">Beban Kerja Ringan</div>
                                    <div class="scheme-vertical-desc">Upload Video<br>saja</div>
                                    <div class="scheme-vertical-divider"></div>
                                    <div class="scheme-vertical-rev-label">Revenue Sharing</div>
                                    <div class="scheme-vertical-percent text-green">10%</div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="scheme-modal-step mb-3">
                    <div class="step-header-new mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="step-number">2</span>
                            <p class="scheme-modal-section-title mb-0">Persetujuan & Ketentuan</p>
                        </div>
                        <p class="scheme-modal-section-help">Harap centang semua poin persetujuan di bawah ini.</p>
                    </div>

                    <div class="custom-checkbox-group">
                        <label class="custom-checkbox mb-1.5" for="agreement1">
                            <input type="checkbox" id="agreement1" class="agreement-check" name="legal_agreement_1" value="1" onchange="window.syncSchemeSelectionState && window.syncSchemeSelectionState()">
                            <span class="checkmark"><i class="bi bi-check"></i></span>
                            <span class="checkbox-text">Saya menyetujui skema revenue sharing yang telah saya pilih.</span>
                        </label>

                        <label class="custom-checkbox mb-1.5" for="agreement2">
                            <input type="checkbox" id="agreement2" class="agreement-check" name="legal_agreement_2" value="1" onchange="window.syncSchemeSelectionState && window.syncSchemeSelectionState()">
                            <span class="checkmark"><i class="bi bi-check"></i></span>
                            <span class="checkbox-text">Saya bersedia menyusun/menyediakan materi sesuai kesepakatan.</span>
                        </label>

                        <label class="custom-checkbox mb-2" for="agreement3">
                            <input type="checkbox" id="agreement3" class="agreement-check" name="legal_agreement_3" value="1" onchange="window.syncSchemeSelectionState && window.syncSchemeSelectionState()">
                            <span class="checkmark"><i class="bi bi-check"></i></span>
                            <span class="checkbox-text">Materi yang saya unggah adalah original dan bebas dari hak cipta.</span>
                        </label>
                    </div>

                    <button type="button" class="terms-link-btn" onclick="openTermsAndConditionsModal()">
                        <i class="bi bi-file-earmark-text-fill me-1"></i> Lihat syarat & ketentuan lengkap <i class="bi bi-chevron-right ms-1"></i>
                    </button>
                </div>

                {{-- Summary Section --}}
                <div class="summary-box mb-2" id="schemeSummaryBox">
                    <div class="summary-icon"><i class="bi bi-clipboard-check-fill"></i></div>
                    <div class="summary-content">
                        <p class="summary-title">Ringkasan Penugasan</p>
                        <div class="summary-grid">
                            <div class="summary-col">
                                <span class="summary-col-label">Course</span>
                                <span class="summary-col-val" id="summaryCourseName">-</span>
                            </div>
                            <div class="summary-col">
                                <span class="summary-col-label">Skema</span>
                                <span class="summary-col-val" id="summarySchemeName">-</span>
                            </div>
                            <div class="summary-col">
                                <span class="summary-col-label">Revenue Sharing</span>
                                <span class="summary-col-val text-brand-plum" id="summaryRevenueSharing">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 px-4 py-3 scheme-modal-footer">
                <div class="row w-100 g-2">
                    <div class="col-3">
                        <button type="button" class="btn scheme-btn-cancel w-100" data-bs-dismiss="modal">Batal</button>
                    </div>
                    <div class="col-9">
                        <button type="button" id="confirmSchemeBtn" class="btn scheme-btn-confirm w-100" disabled>
                            <i class="bi bi-rocket-takeoff-fill me-2"></i> Konfirmasi & Mulai Bekerja
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Secondary Terms & Conditions Modal --}}
<div class="modal fade terms-modal" id="termsAndConditionsModal" tabindex="-1" aria-hidden="true" style="display: none; z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0">
            <div class="modal-header border-0 terms-modal-header">
                <div class="header-content-wrap">
                    <div class="header-doc-badge">
                        <i class="bi bi-file-earmark-text-fill"></i>
                    </div>
                    <div class="header-text">
                        <h5 class="modal-title terms-modal-title">Syarat & Ketentuan Penugasan Trainer</h5>
                    </div>
                </div>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="modal-body px-4 py-3 terms-modal-body">
                {{-- Point 1 --}}
                <div class="terms-section mb-3">
                    <div class="terms-step-title">
                        <span class="terms-step-number">1</span>
                        <span>1. Ruang Lingkup Penugasan</span>
                    </div>
                    <p class="terms-text">Trainer bertanggung jawab menyusun materi pembelajaran sesuai skema yang dipilih.</p>
                    <div class="terms-scope-box mb-3">
                        <div class="scope-item">
                            <div class="scope-badge bg-plum-light text-plum"><i class="bi bi-trophy-fill"></i></div>
                            <div class="scope-text">
                                <span class="scope-name">Beban Kerja Penuh</span>
                                <span class="scope-desc">• Menyusun modul pembelajaran &nbsp;&nbsp; • Membuat video pembelajaran &nbsp;&nbsp; • Menyusun kuis/evaluasi</span>
                            </div>
                        </div>
                        <div class="scope-item mt-2">
                            <div class="scope-badge bg-blue-light text-blue"><i class="bi bi-book-half"></i></div>
                            <div class="scope-text">
                                <span class="scope-name">Beban Kerja Menengah</span>
                                <span class="scope-desc">• Menyusun modul pembelajaran &nbsp;&nbsp; • Membuat video pembelajaran</span>
                            </div>
                        </div>
                        <div class="scope-item mt-2">
                            <div class="scope-badge bg-green-light text-green"><i class="bi bi-camera-video"></i></div>
                            <div class="scope-text">
                                <span class="scope-name">Beban Kerja Ringan</span>
                                <span class="scope-desc">• Membuat video pembelajaran</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Point 2 --}}
                <div class="terms-section mb-3">
                    <div class="terms-step-title">
                        <span class="terms-step-number">2</span>
                        <span>2. Revenue Sharing</span>
                    </div>
                    <p class="terms-text">Trainer akan memperoleh bagi hasil sesuai skema yang dipilih.</p>
                    <div class="row g-2 mb-2 align-items-center">
                        <div class="col-6">
                            <table class="table table-sm table-bordered terms-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Skema</th>
                                        <th>Bagi Hasil</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Beban Kerja Penuh</td>
                                        <td class="text-plum font-bold">35%</td>
                                    </tr>
                                    <tr>
                                        <td>Beban Kerja Menengah</td>
                                        <td class="text-blue font-bold">25%</td>
                                    </tr>
                                    <tr>
                                        <td>Beban Kerja Ringan</td>
                                        <td class="text-green font-bold">10%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-6">
                            <div class="terms-card-info">
                                <div class="card-info-icon"><i class="bi bi-cash-coin"></i></div>
                                <p class="card-info-text">Bagi hasil dihitung berdasarkan pendapatan course setelah course dipublikasikan dan menghasilkan transaksi.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Point 3 --}}
                <div class="terms-section mb-3">
                    <div class="terms-step-title">
                        <span class="terms-step-number">3</span>
                        <span>3. Ketentuan Materi</span>
                    </div>
                    <p class="terms-text">Trainer wajib memastikan bahwa:</p>
                    <div class="row g-2 align-items-center">
                        <div class="col-8">
                            <ul class="terms-check-list list-unstyled m-0">
                                <li><i class="bi bi-check-circle-fill text-green me-2"></i> Materi yang diunggah merupakan karya asli.</li>
                                <li><i class="bi bi-check-circle-fill text-green me-2"></i> Tidak melanggar hak cipta pihak lain.</li>
                                <li><i class="bi bi-check-circle-fill text-green me-2"></i> Tidak mengandung unsur plagiarisme.</li>
                                <li><i class="bi bi-check-circle-fill text-green me-2"></i> Tidak mengandung konten yang melanggar hukum.</li>
                            </ul>
                        </div>
                        <div class="col-4 text-center">
                            <div class="terms-illustration text-plum">
                                <i class="bi bi-shield-fill-check"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Point 4 --}}
                <div class="terms-section mb-3">
                    <div class="terms-step-title">
                        <span class="terms-step-number">4</span>
                        <span>4. Proses Review</span>
                    </div>
                    <p class="terms-text">Setiap materi yang diunggah akan melalui proses review oleh Admin Trainer. Admin berhak:</p>
                    <div class="terms-badges-row mb-3">
                        <span class="badge-status bg-green-light text-green"><i class="bi bi-check-circle-fill me-1"></i> Menyetujui materi</span>
                        <span class="badge-status bg-orange-light text-orange"><i class="bi bi-pencil-fill me-1"></i> Meminta revisi</span>
                        <span class="badge-status bg-red-light text-red"><i class="bi bi-x-circle-fill me-1"></i> Menolak materi</span>
                    </div>
                </div>

                {{-- Point 5 --}}
                <div class="terms-section mb-3">
                    <div class="terms-step-title">
                        <span class="terms-step-number">5</span>
                        <span>5. Publikasi Course</span>
                    </div>
                    <p class="terms-text">Course hanya dapat dipublikasikan setelah:</p>
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="terms-card-mini">
                                <i class="bi bi-cloud-arrow-up-fill mb-1 text-blue"></i>
                                <span>Seluruh materi selesai diunggah</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="terms-card-mini">
                                <i class="bi bi-send-fill mb-1 text-plum"></i>
                                <span>Trainer melakukan submit final</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="terms-card-mini">
                                <i class="bi bi-patch-check-fill mb-1 text-green"></i>
                                <span>Materi dinyatakan lolos review</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Point 6 --}}
                <div class="terms-section mb-3">
                    <div class="terms-step-title">
                        <span class="terms-step-number">6</span>
                        <span>6. Pembayaran Revenue Sharing</span>
                    </div>
                    <div class="row g-2 align-items-center">
                        <div class="col-8">
                            <p class="terms-text m-0">Pembayaran bagi hasil dilakukan sesuai kebijakan yang berlaku pada platform dan akan diinformasikan melalui dashboard trainer.</p>
                        </div>
                        <div class="col-4 text-center">
                            <div class="terms-illustration text-plum">
                                <i class="bi bi-wallet2"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Point 7 --}}
                <div class="terms-section">
                    <div class="terms-step-title">
                        <span class="terms-step-number">7</span>
                        <span>7. Pengakhiran Penugasan</span>
                    </div>
                    <div class="row g-2 align-items-center">
                        <div class="col-8">
                            <p class="terms-text mb-1">Penugasan dapat dihentikan apabila:</p>
                            <ul class="terms-bullet-list m-0">
                                <li>Trainer tidak menyelesaikan materi sesuai kesepakatan.</li>
                                <li>Ditemukan pelanggaran hak cipta.</li>
                                <li>Trainer melanggar kebijakan platform.</li>
                            </ul>
                        </div>
                        <div class="col-4 text-center">
                            <div class="terms-illustration text-red">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 px-4 py-3 terms-modal-footer">
                <button type="button" class="btn scheme-btn-confirm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    .scheme-modal, .terms-modal {
        font-family: 'Outfit', sans-serif;
    }

    .scheme-modal.show, .terms-modal.show {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .scheme-modal .modal-dialog {
        max-width: 680px;
        width: calc(100% - 32px);
        margin: 0 auto !important;
    }

    .terms-modal .modal-dialog {
        max-width: 600px;
        width: calc(100% - 32px);
        margin: 0 auto !important;
    }

    .scheme-modal .modal-content, .terms-modal .modal-content {
        border-radius: 20px;
        overflow: hidden;
        background: #ffffff;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    /* Modal Header Redesign to Plum */
    .scheme-modal-header {
        background: linear-gradient(135deg, #70227e 0%, #4a0e4e 100%);
        padding: 24px 28px;
        color: #ffffff;
        position: relative;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .header-content-wrap {
        display: flex;
        align-items: center;
        gap: 16px;
        z-index: 1;
        position: relative;
    }

    .header-rocket-orb {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.05) 100%);
        border: 1px solid rgba(255, 255, 255, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #ffffff;
        position: relative;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .orb-ring-1 {
        position: absolute;
        inset: -4px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: spin 8s linear infinite;
    }

    .orb-ring-2 {
        position: absolute;
        inset: -8px;
        border: 1px dashed rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        animation: spin-reverse 12s linear infinite;
    }

    @keyframes spin {
        100% { transform: rotate(360deg); }
    }

    @keyframes spin-reverse {
        100% { transform: rotate(-360deg); }
    }

    .btn-close-custom {
        position: absolute;
        top: 14px;
        right: 14px;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.12);
        border: none;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        z-index: 10;
    }

    .btn-close-custom:hover {
        background: rgba(255, 255, 255, 0.22);
        transform: scale(1.05);
    }

    .scheme-modal-title {
        font-size: 18px;
        font-weight: 700;
        letter-spacing: -0.01em;
        margin: 0;
        color: #ffffff !important;
    }

    .scheme-modal-subtitle {
        margin: 4px 0 0;
        font-size: 12.5px;
        color: rgba(255, 255, 255, 0.8);
        font-weight: 400;
    }

    .scheme-modal-body {
        padding: 24px 28px !important;
    }

    /* Detail Info Cards */
    .detail-info-card {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        padding: 14px 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }

    .detail-info-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: rgba(112, 34, 126, 0.06);
        color: #70227e;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        flex-shrink: 0;
    }

    .detail-info-content {
        display: flex;
        flex-direction: column;
    }

    .detail-info-label {
        font-size: 9px;
        font-weight: 700;
        color: #94a3b8;
        letter-spacing: 0.05em;
        margin-bottom: 2px;
    }

    .detail-info-value {
        font-size: 13px;
        font-weight: 700;
        color: #1e1b4b;
    }

    /* New Inline Step Header Styling */
    .step-header-new {
        display: flex;
        flex-direction: column;
        margin-bottom: 12px;
    }

    .step-number {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: linear-gradient(135deg, #70227e 0%, #4a0e4e 100%);
        color: #ffffff;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10.5px;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(112, 34, 126, 0.15);
    }

    .scheme-modal-section-title {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        line-height: 1.2;
    }

    .scheme-modal-section-help {
        font-size: 11px;
        color: #64748b;
        margin: 2px 0 0 28px !important;
        line-height: 1.3;
    }

    /* Vertical Selectable Cards */
    .scheme-card-vertical-wrapper {
        position: relative;
        height: 100%;
    }

    .scheme-label-vertical {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        cursor: pointer;
        padding: 20px 12px 16px 12px;
        border: 1.5px solid #f1f5f9;
        border-radius: 12px;
        background: #ffffff;
        position: relative;
        transition: all 0.25s ease-in-out;
        margin: 0;
        height: 100%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }

    .scheme-radio-circle {
        position: absolute;
        top: 12px;
        left: 12px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 1.5px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        transition: all 0.2s;
    }

    .scheme-vertical-icon-wrapper {
        color:#70227e;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin: 0 auto 10px auto;
        flex-shrink: 0;
    }

    .percent-plum {
        color: #70227e !important;
    }

    .percent-blue {
        color: #3b82f6 !important;
    }

    .percent-green {
        color: #10b981 !important;
    }

    .icon-wrapper-plum {
        color: #70227e !important;
        background: rgba(112, 34, 126, 0.06) !important;
    }

    .icon-wrapper-blue {
        color: #3b82f6 !important;
        background: rgba(59, 130, 246, 0.06) !important;
    }

    .icon-wrapper-green {
        color: #10b981 !important;
        background: rgba(16, 185, 129, 0.06) !important;
    }

    .scheme-vertical-title {
        font-size: 12.5px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 6px;
        line-height: 1.2;
    }

    .scheme-vertical-desc {
        font-size: 10.5px;
        color: #64748b;
        line-height: 1.4;
        margin-bottom: auto;
    }

    .scheme-vertical-divider {
        width: 100%;
        height: 1px;
        background: #f1f5f9;
        margin: 12px 0;
    }

    .scheme-vertical-rev-label {
        font-size: 8.5px;
        color: #94a3b8;
        text-transform: uppercase;
        margin-bottom: 2px;
        letter-spacing: 0.02em;
    }

    .scheme-vertical-percent {
        font-size: 22px;
        font-weight: 800;
        line-height: 1;
        color: #70227e;
    }

    .scheme-vertical-badge {
        position: absolute;
        bottom: -8px;
        left: 50%;
        transform: translateX(-50%);
        background: #70227e;
        color: #ffffff;
        font-size: 7.5px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 100px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
        box-shadow: 0 2px 6px rgba(112, 34, 126, 0.2);
        z-index: 2;
    }

    /* Hide standard inputs securely across all custom sheets by moving them off-screen */
    .scheme-modal .modal-body input[type="radio"].scheme-radio,
    .scheme-modal input[type="radio"].scheme-radio,
    .scheme-modal .modal-body input[type="checkbox"].agreement-check,
    .scheme-modal input[type="checkbox"].agreement-check {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        position: absolute !important;
        left: -9999px !important;
    }

    /* Hover & Active States */
    .scheme-label-vertical:hover {
        border-color: #ddd6fe;
        transform: translateY(-1.5px);
        background: #ffffff;
        box-shadow: 0 6px 16px rgba(109, 40, 217, 0.04);
    }

    .scheme-radio:checked + .scheme-label-vertical {
        border-color: #70227e;
        background: #faf8ff;
        box-shadow: 0 6px 16px rgba(112, 34, 126, 0.06);
    }

    .scheme-radio:checked + .scheme-label-vertical .scheme-radio-circle {
        border-color: #70227e;
        background: #70227e;
    }

    .scheme-radio:checked + .scheme-label-vertical .scheme-radio-circle::after {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #ffffff;
    }

    .scheme-radio:checked + .scheme-label-vertical .scheme-vertical-title {
        color: #70227e;
    }

    /* Custom Checkboxes Plum Purple */
    .custom-checkbox {
        display: flex;
        align-items: flex-start;
        cursor: pointer;
        position: relative;
        padding: 5px 0;
        background: transparent;
        border: none;
        border-radius: 0;
        transition: all 0.2s;
        margin: 0;
    }

    .custom-checkbox:hover {
        background: transparent;
    }

    .checkmark {
        width: 18px;
        height: 18px;
        border: 1.5px solid #cbd5e1;
        border-radius: 5px;
        background: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        flex-shrink: 0;
        transition: all 0.2s;
        color: transparent;
        margin-top: 2px;
    }

    .checkmark i {
        font-size: 13px;
        color: #ffffff;
        opacity: 0;
        transition: all 0.1s;
    }

    .custom-checkbox input:checked ~ .checkmark {
        background: #70227e !important;
        border-color: #70227e !important;
        color: #fff !important;
        box-shadow: 0 0 0 2.5px rgba(112, 34, 126, 0.15) !important;
    }

    .custom-checkbox input:checked ~ .checkmark i {
        opacity: 1 !important;
    }

    .checkbox-text {
        font-size: 12.5px;
        color: #475569;
        line-height: 1.4;
        font-weight: 500;
    }

    /* Terms & Conditions outline link button */
    .terms-link-btn {
        display: inline-flex !important;
        align-items: center !important;
        padding: 6px 14px !important;
        border: 1.5px solid rgba(112, 34, 126, 0.2) !important;
        border-radius: 8px !important;
        background: rgba(112, 34, 126, 0.03) !important;
        color: #70227e !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        margin-top: 10px !important;
        margin-bottom: 12px !important;
        transition: all 0.2s !important;
        cursor: pointer !important;
        box-shadow: none !important;
    }

    .terms-link-btn:hover {
        background: rgba(112, 34, 126, 0.06) !important;
        border-color: #70227e !important;
    }

    /* Summary Box with separators */
    .summary-box {
        display: flex;
        align-items: center;
        gap: 16px;
        background: #fcfaff;
        border: 1px solid #f3e8ff;
        border-radius: 12px;
        padding: 14px 18px;
        box-shadow: 0 2px 10px rgba(112, 34, 126, 0.02);
    }

    .summary-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: rgba(112, 34, 126, 0.06);
        color: #70227e;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .summary-content {
        flex: 1;
    }

    .summary-title {
        font-size: 10px;
        font-weight: 700;
        color: #70227e;
        margin: 0 0 8px 0;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .summary-grid {
        display: flex;
        align-items: center;
        gap: 0;
    }

    .summary-col {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 0 16px;
        border-right: 1.5px solid #e2e8f0;
    }

    .summary-col:last-child {
        border-right: none;
        padding-right: 0;
    }

    .summary-col:first-child {
        padding-left: 0;
    }

    .summary-col-label {
        font-size: 9px;
        color: #94a3b8;
        margin-bottom: 2px;
    }

    .summary-col-val {
        font-size: 12px;
        font-weight: 700;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .summary-col-val.text-brand-plum {
        color: #70227e !important;
    }

    /* Terms Modal Specific Styles */
    .terms-modal-header {
        background: #fcfaff;
        padding: 20px 24px;
        color: #1e1b4b;
        border-bottom: 1px solid #f3e8ff;
        position: relative;
    }

    .header-doc-badge {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: rgba(112, 34, 126, 0.06);
        color: #70227e;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .terms-modal-title {
        font-size: 16px;
        font-weight: 700;
        color: #1e1b4b !important;
        margin: 0;
    }

    .terms-modal .btn-close-custom {
        background: #f1f5f9;
        color: #64748b;
    }

    .terms-modal .btn-close-custom:hover {
        background: #e2e8f0;
        color: #1e1b4b;
    }

    .terms-modal-body {
        padding: 24px !important;
    }

    .terms-step-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .terms-step-number {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: linear-gradient(135deg, #70227e 0%, #4a0e4e 100%);
        color: #ffffff;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(112, 34, 126, 0.15);
    }

    .terms-text {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 10px;
        line-height: 1.4;
    }

    /* Point 1 Scope box */
    .terms-scope-box {
        background: #fdfcff;
        border: 1px solid #f3e8ff;
        border-radius: 10px;
        padding: 12px 16px;
    }

    .scope-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .scope-badge {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .bg-plum-light {
        background: rgba(112, 34, 126, 0.06);
    }

    .bg-blue-light {
        background: rgba(59, 130, 246, 0.06);
    }

    .bg-green-light {
        background: rgba(16, 185, 129, 0.06);
    }

    .bg-orange-light {
        background: rgba(249, 115, 22, 0.06);
    }

    .bg-red-light {
        background: rgba(239, 68, 68, 0.06);
    }

    .scope-text {
        display: flex;
        flex-direction: column;
    }

    .scope-name {
        font-size: 12px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 2px;
    }

    .scope-desc {
        font-size: 10.5px;
        color: #64748b;
    }

    /* Point 2 table */
    .terms-table {
        font-size: 11px;
    }

    .terms-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        padding: 6px 10px;
    }

    .terms-table td {
        padding: 6px 10px;
        color: #334155;
    }

    .font-bold {
        font-weight: 700;
    }

    .terms-card-info {
        background: #fdfcff;
        border: 1px solid #f3e8ff;
        border-radius: 10px;
        padding: 12px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .card-info-icon {
        font-size: 16px;
        color: #70227e;
        margin-top: 1px;
    }

    .card-info-text {
        font-size: 10.5px;
        color: #64748b;
        line-height: 1.4;
        margin: 0;
    }

    /* Point 3 checklist */
    .terms-check-list li {
        font-size: 12px;
        color: #475569;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
    }

    .terms-check-list li i {
        font-size: 14px;
    }

    .terms-illustration {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 10px;
        padding: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        min-height: 70px;
    }

    .terms-illustration i {
        font-size: 28px;
    }

    /* Point 4 status badges */
    .terms-badges-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
    }

    /* Point 5 mini cards */
    .terms-card-mini {
        background: #fdfcff;
        border: 1px solid #f1f5f9;
        border-radius: 10px;
        padding: 12px 10px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 100%;
        box-shadow: 0 2px 6px rgba(0,0,0,0.01);
    }

    .terms-card-mini i {
        font-size: 20px;
    }

    .terms-card-mini span {
        font-size: 10px;
        color: #475569;
        line-height: 1.3;
        margin-top: 4px;
    }

    .terms-bullet-list {
        padding-left: 18px;
    }

    .terms-bullet-list li {
        font-size: 12px;
        color: #475569;
        margin-bottom: 3px;
    }

    /* Footer buttons redesign to Plum */
    .scheme-modal-footer, .terms-modal-footer {
        background: #f8fafc;
        display: flex;
        align-items: center;
        padding: 16px 28px !important;
        border-top: 1px solid #f1f5f9 !important;
    }

    .scheme-btn-cancel {
        padding: 10px 20px !important;
        font-weight: 600 !important;
        color: #64748b !important;
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 10px !important;
        font-size: 13.5px !important;
        transition: all 0.2s !important;
        box-shadow: none !important;
    }

    .scheme-btn-cancel:hover {
        background: #f8fafc !important;
        color: #334155 !important;
        border-color: #94a3b8 !important;
    }

    .scheme-btn-confirm {
        padding: 10px 24px !important;
        font-weight: 700 !important;
        color: #ffffff !important;
        background: linear-gradient(135deg, #70227e 0%, #4a0e4e 100%) !important;
        border: 1px solid transparent !important;
        border-radius: 10px !important;
        font-size: 13.5px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: 0 4px 12px rgba(112, 34, 126, 0.25) !important;
    }

    .scheme-btn-confirm:not(:disabled):hover {
        transform: translateY(-1.5px) !important;
        box-shadow: 0 8px 20px rgba(112, 34, 126, 0.35) !important;
        color: #ffffff !important;
    }

    .scheme-btn-confirm:disabled {
        background: #e2e8f0 !important;
        color: #94a3b8 !important;
        box-shadow: none !important;
        cursor: not-allowed !important;
        border: 1px solid transparent !important;
        opacity: 0.6 !important;
    }

    @media (max-width: 640px) {
        .scheme-modal-body, .terms-modal-body {
            padding: 16px 16px !important;
        }
        
        .scheme-label-vertical {
            padding: 14px 8px;
        }

        .scheme-vertical-percent {
            font-size: 18px;
        }

        .scheme-modal-header, .terms-modal-header {
            padding: 16px 16px;
        }
        
        .scheme-modal-footer, .terms-modal-footer {
            padding: 12px 16px !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('schemeSelectionModal');
        const schemeRadios = modal ? modal.querySelectorAll('.scheme-radio') : [];
        const agreementChecks = modal ? modal.querySelectorAll('.agreement-check') : [];
        const confirmBtn = document.getElementById('confirmSchemeBtn');
        const form = document.getElementById('schemeSelectionForm');
        const acceptWithSchemeUrlTemplate = @json(route('trainer.notifications.accept-with-scheme', ['notification' => '__NOTIFICATION_ID__']));
        const respondUrlTemplate = @json(route('trainer.notifications.respond', ['notification' => '__NOTIFICATION_ID__']));

        if (!modal || !confirmBtn || !form) {
            return;
        }

        // Enable/disable confirm button based on form state and update Summary Box
        function checkFormValidity() {
            const schemeSelected = Array.from(schemeRadios).some(radio => radio.checked);
            const allAgreementsChecked = Array.from(agreementChecks).every(checkbox => checkbox.checked);
            const isValid = schemeSelected && allAgreementsChecked;

            confirmBtn.disabled = !isValid;
            confirmBtn.toggleAttribute('disabled', !isValid);
            confirmBtn.style.opacity = isValid ? '1' : '0.6';
            confirmBtn.style.pointerEvents = isValid ? 'auto' : 'none';

            // Update Ringkasan Penugasan
            const selectedRadio = document.querySelector('.scheme-radio:checked');
            if (selectedRadio) {
                const schemeValue = selectedRadio.value;
                let name = '';
                let percent = '';
                if (schemeValue === '1') {
                    name = 'Beban Kerja Penuh';
                    percent = '35%';
                } else if (schemeValue === '2') {
                    name = 'Beban Kerja Menengah';
                    percent = '25%';
                } else if (schemeValue === '3') {
                    name = 'Beban Kerja Ringan';
                    percent = '10%';
                }
                document.getElementById('summarySchemeName').textContent = name;
                document.getElementById('summaryRevenueSharing').textContent = percent;
            } else {
                document.getElementById('summarySchemeName').textContent = '-';
                document.getElementById('summaryRevenueSharing').textContent = '-';
            }

            return isValid;
        }

        window.syncSchemeSelectionState = checkFormValidity;

        // Delegate changes inside modal so the state is always refreshed
        modal.addEventListener('change', function (event) {
            if (event.target && (event.target.matches('.scheme-radio') || event.target.matches('.agreement-check'))) {
                checkFormValidity();
            }
        });

        modal.addEventListener('shown.bs.modal', function () {
            requestAnimationFrame(checkFormValidity);
            checkFormValidity();
        });

        function upsertHiddenInput(name, value) {
            let input = form.querySelector(`input[name="${name}"]`);
            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                form.appendChild(input);
            }
            input.value = value;
        }

        // Handle confirm button click
        confirmBtn.addEventListener('click', function (e) {
            e.preventDefault();

            if (confirmBtn.disabled) return;

            // Set form action and payload
            const notificationId = document.getElementById('notification_id').value;
            const entityType = modal.getAttribute('data-entity-type');
            const schemeSelected = document.querySelector('.scheme-radio:checked');
            if (!schemeSelected) {
                return;
            }
            const schemeValue = schemeSelected.value;
            const agreement1 = document.getElementById('agreement1').checked ? '1' : '';
            const agreement2 = document.getElementById('agreement2').checked ? '1' : '';
            const agreement3 = document.getElementById('agreement3').checked ? '1' : '';

            if (entityType === 'event') {
                form.action = acceptWithSchemeUrlTemplate.replace('__NOTIFICATION_ID__', encodeURIComponent(notificationId));
                upsertHiddenInput('scheme_type', schemeValue);
                upsertHiddenInput('legal_agreement_1', agreement1 ? '1' : '');
                upsertHiddenInput('legal_agreement_2', agreement2 ? '1' : '');
            } else {
                form.action = respondUrlTemplate.replace('__NOTIFICATION_ID__', encodeURIComponent(notificationId));
                // Course flow uses standard respond endpoint
                upsertHiddenInput('decision', 'accept');
                upsertHiddenInput('scheme_type', schemeValue);
                // Backend expects string keys from config('trainer_schemes'):
                // e2e (35%), module_video (25%), video_only (10%)
                const courseSchemeKey = (function () {
                    if (schemeValue === '1') return 'e2e';
                    if (schemeValue === '2') return 'module_video';
                    if (schemeValue === '3') return 'video_only';
                    return schemeValue;
                })();
                upsertHiddenInput('contribution_scheme', courseSchemeKey);
                upsertHiddenInput('e_agreement', agreement1 && agreement2 && agreement3 ? '1' : '');
            }

            // Show loading state
            confirmBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Memproses...';
            confirmBtn.disabled = true;

            // Submit form
            form.submit();
        });

        // Window function for opening modal with invitation info
        window.openSchemeSelectionModal = function (notificationId, eventTitle, entityType) {
            modal.setAttribute('data-entity-type', entityType || 'course');
            document.getElementById('notification_id').value = notificationId;
            
            // Populate details & summary course name
            document.getElementById('detailCourseName').textContent = eventTitle;
            document.getElementById('summaryCourseName').textContent = eventTitle;
            
            // Populate diundang sebagai based on entityType
            const roleName = entityType === 'event' ? 'Trainer Event' : 'Trainer Course';
            document.getElementById('detailRoleName').textContent = roleName;

            // Reset form
            schemeRadios.forEach(r => r.checked = false);
            agreementChecks.forEach(c => c.checked = false);
            checkFormValidity();

            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        };

        // Window function to open secondary Terms & Conditions Modal
        window.openTermsAndConditionsModal = function () {
            const termsModal = document.getElementById('termsAndConditionsModal');
            if (termsModal) {
                const bsTermsModal = new bootstrap.Modal(termsModal);
                bsTermsModal.show();
            }
        };
    });
</script>