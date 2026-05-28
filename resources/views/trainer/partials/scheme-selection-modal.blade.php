{{-- Scheme Selection & E-Agreement Modal for Event Invitations --}}
<div class="modal fade scheme-modal" id="schemeSelectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <form id="schemeSelectionForm" method="POST" style="display: none;">
                @csrf
                <input type="hidden" name="notification_id" id="notification_id">
            </form>

            <div class="modal-header border-0 scheme-modal-header">
                <div class="flex-grow-1">
                    <h5 class="modal-title mb-0 scheme-modal-title">
                        <i class="bi bi-rocket-takeoff-fill me-2 scheme-modal-title-icon"></i>
                        Konfirmasi Penugasan
                    </h5>
                    <p class="scheme-modal-subtitle">Terima undangan dan tentukan preferensi kerjamu</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 py-4 scheme-modal-body">
                <div class="scheme-modal-invite-box mb-4">
                    <div class="invite-box-icon"><i class="bi bi-envelope-open-fill"></i></div>
                    <div class="invite-box-content">
                        <p class="invite-box-label">DETAIL UNDANGAN</p>
                        <p id="invitationTitle" class="scheme-modal-invite-title"></p>
                        <p id="invitationDesc" class="scheme-modal-invite-desc"></p>
                    </div>
                </div>

                <div class="scheme-modal-step mb-4">
                    <div class="step-header">
                        <span class="step-number">1</span>
                        <div>
                            <p class="scheme-modal-section-title">Pilih Skema Beban Kerja</p>
                            <p class="scheme-modal-section-help">Pilih persentase bagi hasil berdasarkan komitmen material yang bisa kamu penuhi.</p>
                        </div>
                    </div>

                    <div class="scheme-cards-container">
                        <div class="scheme-card mb-3">
                            <input type="radio" name="scheme_type" value="1" id="scheme1" class="scheme-radio" onchange="window.syncSchemeSelectionState && window.syncSchemeSelectionState()">
                            <label for="scheme1" class="scheme-label">
                                <div class="scheme-card-bg"></div>
                                <div class="scheme-select-indicator"><i class="bi bi-check-lg"></i></div>
                                <div class="scheme-label-row">
                                    <div class="scheme-label-main">
                                        <div class="scheme-label-title">Beban Kerja Penuh</div>
                                        <div class="scheme-label-desc">Upload Modul, Video & Kuis</div>
                                    </div>
                                    <div class="scheme-label-percent-wrap">
                                        <div class="scheme-label-percent">35%</div>
                                        <div class="scheme-badge">Best Value</div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="scheme-card mb-3">
                            <input type="radio" name="scheme_type" value="2" id="scheme2" class="scheme-radio" onchange="window.syncSchemeSelectionState && window.syncSchemeSelectionState()">
                            <label for="scheme2" class="scheme-label">
                                <div class="scheme-card-bg"></div>
                                <div class="scheme-select-indicator"><i class="bi bi-check-lg"></i></div>
                                <div class="scheme-label-row">
                                    <div class="scheme-label-main">
                                        <div class="scheme-label-title">Beban Kerja Menengah</div>
                                        <div class="scheme-label-desc">Upload Modul & Video saja</div>
                                    </div>
                                    <div class="scheme-label-percent-wrap">
                                        <div class="scheme-label-percent">25%</div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="scheme-card mb-2">
                            <input type="radio" name="scheme_type" value="3" id="scheme3" class="scheme-radio" onchange="window.syncSchemeSelectionState && window.syncSchemeSelectionState()">
                            <label for="scheme3" class="scheme-label">
                                <div class="scheme-card-bg"></div>
                                <div class="scheme-select-indicator"><i class="bi bi-check-lg"></i></div>
                                <div class="scheme-label-row">
                                    <div class="scheme-label-main">
                                        <div class="scheme-label-title">Beban Kerja Ringan</div>
                                        <div class="scheme-label-desc">Upload Modul saja</div>
                                    </div>
                                    <div class="scheme-label-percent-wrap">
                                        <div class="scheme-label-percent">10%</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="scheme-modal-step">
                    <div class="step-header">
                        <span class="step-number">2</span>
                        <div>
                            <p class="scheme-modal-section-title">Syarat & Ketentuan</p>
                            <p class="scheme-modal-section-help">Harap centang semua kotak untuk melanjutkan.</p>
                        </div>
                    </div>

                    <div class="custom-checkbox-group">
                        <label class="custom-checkbox mb-3" for="agreement1">
                            <input type="checkbox" id="agreement1" class="agreement-check" name="legal_agreement_1" value="1" onchange="window.syncSchemeSelectionState && window.syncSchemeSelectionState()">
                            <span class="checkmark"><i class="bi bi-check"></i></span>
                            <span class="checkbox-text">Saya menyetujui bentuk kerja sama serta skema pembagian hasil (revenue sharing) yang telah saya pilih di atas.</span>
                        </label>

                        <label class="custom-checkbox" for="agreement2">
                            <input type="checkbox" id="agreement2" class="agreement-check" name="legal_agreement_2" value="1" onchange="window.syncSchemeSelectionState && window.syncSchemeSelectionState()">
                            <span class="checkmark"><i class="bi bi-check"></i></span>
                            <span class="checkbox-text">Saya bersedia menyusun/menyediakan materi sesuai kesepakatan, dan menjamin bahwa materi adalah original serta bebas dari pelanggaran hak cipta.</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 px-4 py-4 scheme-modal-footer">
                <button type="button" class="btn scheme-btn-cancel" data-bs-dismiss="modal">Batalkan</button>
                <button type="button" id="confirmSchemeBtn" class="btn scheme-btn-confirm" disabled>
                    Konfirmasi & Mulai Bekerja <i class="bi bi-arrow-right-short fs-5 ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    .scheme-modal {
        font-family: 'Outfit', sans-serif;
    }

    .scheme-modal .modal-dialog {
        max-width: 650px;
        width: calc(100% - 32px);
    }

    .scheme-modal .modal-content {
        border-radius: 24px;
        overflow: hidden;
        background: #ffffff;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .scheme-modal-header {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        padding: 24px 32px;
        color: #ffffff;
        position: relative;
    }

    .scheme-modal-header::after {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        background: url('data:image/svg+xml;utf8,<svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="dots" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="2" cy="2" r="1" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100%" height="100%" fill="url(%23dots)"/></svg>');
        pointer-events: none;
    }

    .scheme-modal-title {
        font-size: 24px;
        font-weight: 800;
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        z-index: 1;
        position: relative;
        color: #ffffff !important;
    }

    .scheme-modal-title-icon {
        color: #fbbf24;
    }

    .scheme-modal-subtitle {
        margin: 6px 0 0;
        font-size: 14px;
        color: #cbd5e1;
        font-weight: 400;
        z-index: 1;
        position: relative;
    }

    .scheme-modal-body {
        max-height: 70vh;
        overflow-y: auto;
        padding: 32px !important;
    }

    /* Scrollbar */
    .scheme-modal-body::-webkit-scrollbar { width: 6px; }
    .scheme-modal-body::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    .scheme-modal-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

    .scheme-modal-invite-box {
        display: flex;
        gap: 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
    }

    .invite-box-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #e0e7ff;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .invite-box-label {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        letter-spacing: 0.1em;
        margin: 0 0 4px;
    }

    .scheme-modal-invite-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 6px;
        line-height: 1.3;
    }

    .scheme-modal-invite-desc {
        font-size: 14px;
        color: #475569;
        margin: 0;
        line-height: 1.5;
    }

    .step-header {
        display: flex;
        gap: 16px;
        align-items: flex-start;
        margin-bottom: 20px;
    }

    .step-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #1e1b4b;
        color: white;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }

    .scheme-modal-section-title {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 4px;
        line-height: 1.2;
    }

    .scheme-modal-section-help {
        font-size: 14px;
        color: #64748b;
        margin: 0;
        line-height: 1.4;
    }

    /* Cards */
    .scheme-card {
        position: relative;
    }

    .scheme-radio {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .scheme-label {
        display: block;
        cursor: pointer;
        padding: 20px 24px;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        background: #ffffff;
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        margin: 0;
    }

    .scheme-card-bg {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.05) 0%, rgba(79, 70, 229, 0) 100%);
        opacity: 0;
        transition: opacity 0.3s;
    }

    .scheme-label:hover {
        border-color: #cbd5e1;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }

    .scheme-label-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        position: relative;
        z-index: 1;
        padding-left: 40px;
    }

    .scheme-select-indicator {
        position: absolute;
        left: 24px;
        top: 50%;
        transform: translateY(-50%);
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: transparent;
        transition: all 0.2s;
        z-index: 1;
    }

    .scheme-select-indicator i {
        font-size: 14px;
        margin-top: 1px;
    }

    .scheme-label-main {
        flex: 1;
    }

    .scheme-label-title {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 4px;
        transition: color 0.2s;
    }

    .scheme-label-desc {
        font-size: 14px;
        color: #64748b;
        line-height: 1.4;
    }

    .scheme-label-percent-wrap {
        text-align: right;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .scheme-label-percent {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }

    .scheme-badge {
        background: #fef3c7;
        color: #d97706;
        font-size: 10px;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 999px;
        margin-top: 6px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .scheme-radio:checked + .scheme-label {
        border-color: #4f46e5;
        box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.15);
    }

    .scheme-radio:checked + .scheme-label .scheme-card-bg {
        opacity: 1;
    }

    .scheme-radio:checked + .scheme-label .scheme-select-indicator {
        border-color: #4f46e5;
        background: #4f46e5;
        color: #ffffff;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    .scheme-radio:checked + .scheme-label .scheme-label-title {
        color: #4f46e5;
    }

    /* Custom Checkboxes */
    .custom-checkbox {
        display: flex;
        align-items: flex-start;
        cursor: pointer;
        position: relative;
        padding: 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.2s;
        margin: 0;
    }

    .custom-checkbox:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .custom-checkbox input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    .checkmark {
        width: 22px;
        height: 22px;
        border: 2px solid #cbd5e1;
        border-radius: 6px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 14px;
        flex-shrink: 0;
        transition: all 0.2s;
        color: transparent;
        margin-top: 2px;
    }

    .checkmark i {
        font-size: 18px;
    }

    .custom-checkbox input:checked ~ .checkmark {
        background: #10b981;
        border-color: #10b981;
        color: #fff;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
    }

    .checkbox-text {
        font-size: 14px;
        color: #334155;
        line-height: 1.5;
        font-weight: 500;
    }

    /* Footer buttons */
    .scheme-modal-footer {
        background: #f8fafc;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        border-top: 1px solid #e2e8f0 !important;
    }

    .scheme-btn-cancel {
        padding: 12px 24px;
        font-weight: 600;
        color: #475569;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        transition: all 0.2s;
    }

    .scheme-btn-cancel:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .scheme-btn-confirm {
        padding: 12px 28px;
        font-weight: 700;
        color: #ffffff;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border: none;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }

    .scheme-btn-confirm:not(:disabled):hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(245, 158, 11, 0.4);
        color: var(--main-navy-clr, #1e1b4b) !important;
    }

    .scheme-btn-confirm:disabled {
        background: #e2e8f0;
        color: #94a3b8;
        box-shadow: none;
        cursor: not-allowed;
    }

    @media (max-width: 640px) {
        .scheme-modal-body {
            padding: 24px !important;
        }
        
        .scheme-label {
            padding: 16px 20px;
        }

        .scheme-label-row {
            padding-left: 36px;
        }

        .scheme-select-indicator {
            left: 16px;
        }

        .scheme-modal-header {
            padding: 20px 24px;
        }
        
        .scheme-modal-footer {
            padding: 20px 24px;
            flex-direction: column;
        }
        
        .scheme-btn-cancel, .scheme-btn-confirm {
            width: 100%;
            justify-content: center;
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

        // Enable/disable confirm button based on form state
        function checkFormValidity() {
            const schemeSelected = Array.from(schemeRadios).some(radio => radio.checked);
            const allAgreementsChecked = Array.from(agreementChecks).every(checkbox => checkbox.checked);
            const isValid = schemeSelected && allAgreementsChecked;

            confirmBtn.disabled = !isValid;
            confirmBtn.toggleAttribute('disabled', !isValid);
            confirmBtn.style.opacity = isValid ? '1' : '0.6';
            confirmBtn.style.pointerEvents = isValid ? 'auto' : 'none';

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

            // Set form action and payload based on invitation entity type
            const notificationId = document.getElementById('notification_id').value;
            const entityType = modal.getAttribute('data-entity-type') || 'event';

            if (entityType === 'event') {
                form.action = acceptWithSchemeUrlTemplate.replace('__NOTIFICATION_ID__', encodeURIComponent(notificationId));
            } else {
                form.action = respondUrlTemplate.replace('__NOTIFICATION_ID__', encodeURIComponent(notificationId));
            }

            // Add scheme and agreement values to form
            const schemeSelected = document.querySelector('.scheme-radio:checked');
            if (!schemeSelected) {
                return;
            }
            const schemeValue = schemeSelected.value;
            const agreement1 = document.getElementById('agreement1').checked ? '1' : '';
            const agreement2 = document.getElementById('agreement2').checked ? '1' : '';

            if (entityType === 'event') {
                // Event flow uses dedicated endpoint
                upsertHiddenInput('scheme_type', schemeValue);
                upsertHiddenInput('legal_agreement_1', agreement1);
                upsertHiddenInput('legal_agreement_2', agreement2);
            } else {
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
                upsertHiddenInput('e_agreement', agreement1 && agreement2 ? '1' : '');
            }

            // Show loading state
            confirmBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Memproses...';
            confirmBtn.disabled = true;

            // Submit form
            form.submit();
        });

        // Window function for opening modal with invitation info
        window.openSchemeSelectionModal = function (notificationId, eventTitle, entityType) {
            modal.setAttribute('data-entity-type', entityType || 'event');
            document.getElementById('notification_id').value = notificationId;
            document.getElementById('invitationTitle').textContent = (entityType === 'course')
                ? 'Anda menerima penugasan untuk course:'
                : 'Anda menerima undangan untuk mengajar kelas:';
            document.getElementById('invitationDesc').textContent = eventTitle;

            // Reset form
            schemeRadios.forEach(r => r.checked = false);
            agreementChecks.forEach(c => c.checked = false);
            checkFormValidity();

            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        };
    });
</script>