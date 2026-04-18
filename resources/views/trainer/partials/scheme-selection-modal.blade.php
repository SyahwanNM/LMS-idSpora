{{-- Scheme Selection & E-Agreement Modal for Event Invitations --}}
<div class="modal fade scheme-modal" id="schemeSelectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <form id="schemeSelectionForm" method="POST" style="display: none;">
                @csrf
                <input type="hidden" name="notification_id" id="notification_id">
            </form>

            <div class="modal-header border-0 pb-2 pt-4 px-4">
                <div class="flex-grow-1">
                    <h5 class="modal-title fw-bold mb-0 scheme-modal-title">
                        <i class="bi bi-clipboard-check me-2 scheme-modal-title-icon"></i>
                        Konfirmasi Penugasan Event
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 py-3 scheme-modal-body">
                <div class="scheme-modal-step mb-4">
                    <p class="scheme-modal-section-title">
                        Rincian Undangan
                    </p>
                    <div class="scheme-modal-invite-box">
                        <p id="invitationTitle" class="scheme-modal-invite-title"></p>
                        <p id="invitationDesc" class="scheme-modal-invite-desc"></p>
                    </div>
                </div>

                <div class="scheme-modal-step mb-4">
                    <p class="scheme-modal-section-title">
                        Pilih Skema Beban Kerja
                    </p>
                    <p class="scheme-modal-section-help">
                        Pilih skema beban kerja. Persentase bagi hasil dihitung dari pendapatan per peserta yang
                        mendaftar.
                    </p>
                    <p class="scheme-modal-pick-hint mb-2">
                        Klik salah satu kartu di bawah ini untuk memilih skema.
                    </p>

                    <div class="scheme-card mb-3">
                        <input type="radio" name="scheme_type" value="1" id="scheme1" class="scheme-radio"
                            onchange="window.syncSchemeSelectionState && window.syncSchemeSelectionState()">
                        <label for="scheme1" class="scheme-label">
                            <span class="scheme-select-indicator" aria-hidden="true"><span
                                    class="scheme-select-dot"></span></span>
                            <div class="scheme-label-row">
                                <div class="scheme-label-main">
                                    <div class="scheme-label-title">
                                        Beban Kerja Penuh (35%)
                                    </div>
                                    <div class="scheme-label-desc">
                                        Upload Modul, Video, dan Kuis
                                    </div>
                                </div>
                                <div class="scheme-label-percent-wrap">
                                    <div class="scheme-label-percent">35%</div>
                                    <div class="scheme-label-percent-note">dari harga tiket atau per peserta</div>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="scheme-card mb-3">
                        <input type="radio" name="scheme_type" value="2" id="scheme2" class="scheme-radio"
                            onchange="window.syncSchemeSelectionState && window.syncSchemeSelectionState()">
                        <label for="scheme2" class="scheme-label">
                            <span class="scheme-select-indicator" aria-hidden="true"><span
                                    class="scheme-select-dot"></span></span>
                            <div class="scheme-label-row">
                                <div class="scheme-label-main">
                                    <div class="scheme-label-title">
                                        Beban Kerja Menengah (25%)
                                    </div>
                                    <div class="scheme-label-desc">
                                        Upload Modul dan Video
                                    </div>
                                </div>
                                <div class="scheme-label-percent-wrap">
                                    <div class="scheme-label-percent">25%</div>
                                    <div class="scheme-label-percent-note">dari harga tiket atau per peserta</div>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="scheme-card mb-3">
                        <input type="radio" name="scheme_type" value="3" id="scheme3" class="scheme-radio"
                            onchange="window.syncSchemeSelectionState && window.syncSchemeSelectionState()">
                        <label for="scheme3" class="scheme-label">
                            <span class="scheme-select-indicator" aria-hidden="true"><span
                                    class="scheme-select-dot"></span></span>
                            <div class="scheme-label-row">
                                <div class="scheme-label-main">
                                    <div class="scheme-label-title">
                                        Beban Kerja Ringan (10%)
                                    </div>
                                    <div class="scheme-label-desc">
                                        Upload Video saja
                                    </div>
                                </div>
                                <div class="scheme-label-percent-wrap">
                                    <div class="scheme-label-percent">10%</div>
                                    <div class="scheme-label-percent-note">dari harga tiket atau per peserta</div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="scheme-modal-step">
                    <p class="scheme-modal-section-title">
                        Syarat & Ketentuan
                    </p>

                    <div class="scheme-modal-warning">
                        <p>
                            <strong>Perhatian:</strong> Anda harus menyetujui kedua syarat di bawah untuk melanjutkan
                            proses penugasan.
                        </p>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input agreement-check" id="agreement1"
                            name="legal_agreement_1" value="1"
                            onchange="window.syncSchemeSelectionState && window.syncSchemeSelectionState()">
                        <label class="form-check-label scheme-modal-check-label" for="agreement1">
                            <span>Saya menyetujui bentuk kerja sama serta skema pembagian hasil (revenue sharing) yang
                                telah saya pilih di atas.</span>
                        </label>
                    </div>

                    <div class="form-check mb-0">
                        <input type="checkbox" class="form-check-input agreement-check" id="agreement2"
                            name="legal_agreement_2" value="1"
                            onchange="window.syncSchemeSelectionState && window.syncSchemeSelectionState()">
                        <label class="form-check-label scheme-modal-check-label" for="agreement2">
                            <span>Saya bersedia menyusun dan/atau menyediakan materi pembelajaran sesuai kesepakatan,
                                dan menjamin bahwa materi yang diberikan adalah original dan tidak melanggar hak
                                cipta.</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top bg-light px-4 py-3 scheme-modal-footer">
                <button type="button" class="btn btn-secondary btn-sm scheme-modal-cancel" data-bs-dismiss="modal">
                    Batalkan
                </button>
                <button type="button" id="confirmSchemeBtn" class="btn btn-primary btn-sm scheme-modal-confirm"
                    disabled>
                    <i class="bi bi-check-circle me-2"></i>
                    Konfirmasi & Mulai Bekerja
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .scheme-modal .modal-dialog {
        max-width: 700px;
        width: calc(100% - 24px);
    }

    .scheme-modal .modal-content {
        overflow: hidden;
    }

    .scheme-modal-title {
        color: var(--main-navy-clr);
        font-size: 20px;
        line-height: 1.3;
    }

    .scheme-modal-title-icon {
        color: var(--main-navy-clr);
    }

    .scheme-modal-body {
        max-height: 68vh;
        overflow-y: auto;
        padding-top: 10px !important;
    }

    .scheme-modal-step {
        width: 100%;
    }

    .scheme-modal-section-title {
        margin: 0 0 10px;
        color: #64748b;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .scheme-modal-section-help {
        margin: 0 0 14px;
        color: #475569;
        font-size: 13px;
    }

    .scheme-modal-pick-hint {
        margin: -2px 0 14px;
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px dashed rgba(27, 23, 99, 0.18);
        background: rgba(27, 23, 99, 0.05);
        color: #4b5563;
        font-size: 12px;
        line-height: 1.45;
    }

    .scheme-modal-invite-box {
        padding: 14px;
        background: #f8fafc;
        border-radius: 12px;
        border-left: 4px solid var(--main-navy-clr);
    }

    .scheme-modal-invite-title {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: var(--main-navy-clr);
        line-height: 1.4;
        white-space: normal;
        word-break: normal;
    }

    .scheme-modal-invite-desc {
        margin: 8px 0 0;
        font-size: 14px;
        color: #334155;
        line-height: 1.45;
        white-space: normal;
        word-break: break-word;
    }

    .scheme-card {
        position: relative;
        width: 100%;
    }


    .scheme-radio {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .scheme-label {
        cursor: pointer;
        display: block;
        padding: 16px 16px 15px 52px;
        border: 2px solid #d7deea;
        border-radius: 16px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        transition: all .2s;
        margin: 0;
        position: relative;
        min-height: 96px;
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.05);
    }

    .scheme-label:hover {
        border-color: var(--main-navy-clr);
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(27, 23, 99, 0.10);
    }

    .scheme-select-indicator {
        position: absolute;
        left: 16px;
        top: 16px;
        width: 22px;
        height: 22px;
        border-radius: 999px;
        border: 2px solid #bcc7d8;
        background: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all .2s ease;
    }

    .scheme-select-dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        background: transparent;
        transition: all .2s ease;
    }

    .scheme-label-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
    }

    .scheme-label-main {
        flex: 1;
        min-width: 0;
    }

    .scheme-label-title {
        margin-bottom: 6px;
        font-size: 15px;
        color: var(--main-navy-clr);
        font-weight: 800;
        line-height: 1.35;
        white-space: normal;
    }

    .scheme-label-desc {
        font-size: 13px;
        color: #64748b;
        line-height: 1.45;
        white-space: normal;
    }

    .scheme-label-percent-wrap {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        text-align: right;
        padding-left: 12px;
        flex-shrink: 0;
        max-width: 145px;
    }

    .scheme-label-percent {
        font-weight: 900;
        color: var(--main-navy-clr);
        font-size: 16px;
        line-height: 1.1;
    }

    .scheme-label-percent-note {
        margin-top: 3px;
        font-size: 11px;
        line-height: 1.3;
        color: #94a3b8;
    }

    .scheme-radio:checked+.scheme-label {
        border-color: var(--main-navy-clr);
        background: linear-gradient(180deg, rgba(26, 35, 126, 0.08) 0%, rgba(26, 35, 126, 0.03) 100%);
        box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.12), 0 12px 26px rgba(27, 23, 99, 0.12);
    }

    .scheme-radio:checked+.scheme-label .scheme-label-percent-wrap {
        transform: translateY(-1px);
    }

    .scheme-radio:checked+.scheme-label .scheme-label-title {
        color: #14105c;
    }

    .scheme-radio:checked+.scheme-label .scheme-select-indicator {
        border-color: var(--main-navy-clr);
        background: var(--main-navy-clr);
        box-shadow: 0 0 0 4px rgba(27, 23, 99, 0.08);
    }

    .scheme-radio:checked+.scheme-label .scheme-select-dot {
        background: #fff;
    }

    .scheme-modal-warning {
        background: #fef9e7;
        border: 1px solid #fef08a;
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 14px;
    }

    .scheme-modal-warning p {
        margin: 0;
        font-size: 12px;
        line-height: 1.5;
        color: #714d00;
    }

    .scheme-modal-check-label {
        font-size: 13px;
        line-height: 1.55;
        color: #334155;
        font-weight: 500;
        cursor: pointer;
        white-space: normal;
    }

    .scheme-modal-footer {
        gap: 8px;
    }

    .scheme-modal-cancel {
        flex: 0 0 auto;
        min-width: 100px;
        background: #fff !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1 !important;
        font-weight: 600;
    }

    .scheme-modal-cancel:hover {
        background: #f8fafc !important;
        color: #334155 !important;
        border-color: #94a3b8 !important;
    }

    .scheme-modal-confirm {
        flex: 1 1 auto;
        min-height: 38px;
    }

    #confirmSchemeBtn:disabled {
        background-color: #cbd5e1 !important;
        border-color: #cbd5e1 !important;
        color: #94a3b8;
        cursor: not-allowed;
        opacity: 0.6 !important;
    }

    #confirmSchemeBtn:not(:disabled) {
        background-color: #ffc107 !important;
        border-color: #ffc107 !important;
        color: #0f172a !important;
        font-weight: 600;
        opacity: 1 !important;
        cursor: pointer;
    }

    #confirmSchemeBtn:not(:disabled):hover {
        background-color: #ffb800 !important;
        border-color: #ffb800 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(255, 199, 7, 0.3);
    }

    @media (max-width: 576px) {
        .scheme-modal .modal-dialog {
            width: calc(100% - 12px);
            margin: 10px auto;
        }

        .scheme-modal .modal-header,
        .scheme-modal .modal-body,
        .scheme-modal .modal-footer {
            padding-left: 14px !important;
            padding-right: 14px !important;
        }

        .scheme-modal-title {
            font-size: 17px;
        }

        .scheme-label-title {
            font-size: 14px;
        }

        .scheme-label-percent {
            font-size: 15px;
        }

        .scheme-label {
            padding-left: 48px;
        }

        .scheme-select-indicator {
            left: 14px;
            top: 14px;
        }

        .scheme-label-percent-wrap {
            max-width: 115px;
            padding-left: 8px;
        }

        .scheme-label-percent-note {
            font-size: 10.5px;
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