<style>
    /* Restrict withdraw modal width and keep it responsive on smaller screens */
    .withdraw-modal-dialog {
        max-width: 640px; /* main desktop cap */
        width: 100%;
        margin: 1.5rem auto;
    }

    /* tablet: slightly narrower */
    @media (max-width: 768px) {
        .withdraw-modal-dialog {
            max-width: 540px;
        }
    }

    /* phones: keep modal inset with small margins (not full screen) */
    @media (max-width: 576px) {
        .withdraw-modal-dialog {
            max-width: 90%;
            margin: 0.75rem auto;
        }
    }
</style>

<div class="modal fade" id="withdrawModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered withdraw-modal-dialog">
        <div class="modal-content border-0 rounded-4 shadow">

            {{-- Header --}}
            <div class="modal-header border-0">
                <h6 class="modal-title d-flex align-items-center gap-2">
                    <i class="bi bi-wallet-fill"></i>
                    Tarik Komisi
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body px-4">
                <form id="withdrawForm">
                    <div id="stepInput">

                {{-- Saldo --}}
                <div class="text-center mb-4 p-4 bg-light rounded-4 border border-light-subtle">
                    <span class="text-muted d-block small mb-2 fw-medium">Saldo Tersedia</span>
                    <h2 class="fw-medium text-success mb-0">
                        Rp {{ number_format(auth()->user()->wallet_balance ?? 0, 0, ',', '.') }}
                    </h2>
                </div>

                {{-- Metode --}}
                {{-- <div class="mb-3">
                    <label class="form-label small">Metode Penarikan</label>
                    <select class="form-select">
                        <option selected disabled>Pilih Metode</option>
                        <option value="bank">Transfer Bank</option>
                        <option value="ewallet">E-Wallet</option>
                    </select>
                </div> --}}

                <div class="mb-3">
                    <label for="bank_name" class="form-label fw-light small text-muted">Bank Tujuan</label>
                    <select class="form-select bg-light border-0 py-2 fw-normal text-muted" id="bank_name" name="bank_name"
                        required>
                        <option value="" disabled selected class="text-muted">Pilih Bank / E-Wallet</option>
                        <option value="BCA">BCA</option>
                        <option value="Mandiri">Mandiri</option>
                        <option value="BNI">BNI</option>
                        <option value="BRI">BRI</option>
                        <option value="SeaBank">SeaBank</option>
                        <option value="OVO">OVO</option>
                        <option value="GoPay">GoPay</option>
                        <option value="Dana">DANA</option>
                        <option value="ShopeePay">ShopeePay</option>
                    </select>
                </div>










                {{-- Rekening --}}
                {{-- <div class="mb-3">
                    <label class="form-label small">Nomor Rekening</label>
                    <input type="text" class="form-control" placeholder="Contoh: 1234 5678 90">
                </div> --}}


                <div class="row g-3">
                    <div class="col-7">
                        <label for="account_number" class="form-label fw-light small text-muted">Nomor Rekening</label>
                        <input type="number" class="form-control bg-light border-0 py-2" id="account_number"
                            name="account_number" placeholder="Contoh: 1234567890" required>
                    </div>
                    <div class="col-5">
                        <label for="account_holder" class="form-label fw-light small text-muted">Atas Nama</label>
                        <input type="text" class="form-control bg-light border-0 py-2" id="account_holder"
                            name="account_holder" placeholder="Nama Pemilik" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-light small text-muted">Jumlah Penarikan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="withdrawAmount" name="amount" placeholder="0">
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <small class="text-muted fw-lighter">Minimal Rp 50.000</small>
                            <button type="button" class="btn btn-link btn-sm text-primary p-0"
        onclick="setWithdrawAll({{ auth()->user()->wallet_balance ?? 0 }})">
        Tarik Semua
    </button>
                        </div>
                    </div>
                </div>

                {{-- Jumlah --}}
                {{-- <div class="mb-2">
                    <label class="form-label fw-medium small text-muted">Jumlah Penarikan</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="withdrawAmount" placeholder="0">
                    </div>
                </div> --}}

                {{-- Info bawah --}}
                {{-- <div class="d-flex justify-content-between align-items-center mb-3">
                    <small class="text-muted">Minimal Rp 50.000</small>
                    <button type="button" class="btn btn-link btn-sm text-primary p-0"
                        onclick="setWithdrawAll({{ $availableBalance ?? 1200000 }})">
                        Tarik Semua
                    </button>
                </div> --}}

                {{-- CTA --}}
                <button type="button" class="btn btn-warning w-100" onclick="showConfirmation()" style="background: linear-gradient(135deg, #ffc107 0%, #ffca2c 100%); border:none;">
                            Lanjut Konfirmasi
                </button>

                    </div> <!-- /#stepInput -->
                <!-- STEP 2: CONFIRMATION -->
        </div>
        
        <!-- STEP 2: CONFIRMATION -->
                <div id="stepConfirm" style="display: none;">
                    <div class="modal-header border-0 pb-0">
                        <h1 class="modal-title fs-5 fw-bold text-center w-100">
                            Konfirmasi Penarikan
                        </h1>
                        <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body text-center pt-3">
                        <div class="mb-4">
                            <div class="avatar-lg bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-shield-check text-success fs-2"></i>
                            </div>
                            <p class="text-muted small mb-1">Anda akan menarik dana sebesar:</p>
                            <h2 class="fw-bold text-dark mb-0" id="confirmAmountDisplay">Rp 0</h2>
                        </div>

                        <div class="card bg-light border-0 rounded-3 text-start p-3 mb-4">
                            <div class="row mb-2">
                                <div class="col-5 text-muted small">Bank Tujuan</div>
                                <div class="col-7 fw-semibold text-end" id="confirmBank">Unknown</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 text-muted small">No. Rekening</div>
                                <div class="col-7 fw-semibold text-end" id="confirmRekening">Unknown</div>
                            </div>
                            <div class="row">
                                <div class="col-5 text-muted small">Atas Nama</div>
                                <div class="col-7 fw-semibold text-end" id="confirmName">Unknown</div>
                            </div>
                        </div>

                        <div class="alert alert-warning border-0 d-flex align-items-start gap-2 text-start p-2 rounded-3">
                            <i class="bi bi-info-circle-fill mt-1"></i>
                            <div class="small lh-sm">
                                Pastikan data rekening benar. Proses transfer membutuhkan waktu 1-3 hari kerja.
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0 pb-4 justify-content-center gap-2 w-100">
                        <button type="button" class="btn btn-light rounded-pill px-4" onclick="backToInput()">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </button>
                        <button type="button" class="btn btn-success rounded-pill px-5 shadow-sm" onclick="submitWithdrawal()">
                            Ya, Tarik Dana
                        </button>
                    </div>
                </div>
                <!-- END STEP 2 -->

                <!-- STEP 3: SUCCESS -->
                <div id="stepSuccess" style="display: none;">
                    <div class="modal-body text-center py-4">
                        <div class="avatar-xl bg-success bg-opacity-10 rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;">
                            <i class="bi bi-check-circle-fill text-success fs-1"></i>
                        </div>
                        <h5 class="fw-bold">Permintaan Diajukan</h5>
                        <p class="text-muted small mb-3">Permintaan penarikan Anda sedang diproses. Mohon tunggu 1-3 hari kerja.</p>
                        <button type="button" class="btn btn-warning px-4" onclick="closeAndReset()">Tutup</button>
                    </div>
                </div>
                <!-- END STEP 3 -->

            </form>
        </div>
    </div>
</div>

<script>
    function setWithdrawAll(amount) {
    const input = document.getElementById('withdrawAmount');
    if (input) {
        // Masukkan nilai saldo ke dalam input
        input.value = amount;
    }
}

    function showConfirmation() {
        // Ambil value dari input form
        const amount = document.getElementById('withdrawAmount')?.value;
        const bank = document.getElementById('bank_name')?.value;
        const rekening = document.getElementById('account_number')?.value;
        const name = document.getElementById('account_holder')?.value;

        // Validasi sederhana
        if(!amount || !bank || !rekening || !name) {
            alert("Mohon lengkapi semua data penarikan.");
            return;
        }
        
        if(parseInt(amount) < 50000) {
            alert("Minimal penarikan adalah Rp 50.000");
            return;
        }

        // Format Rupiah untuk tampilan
        const formattedAmount = new Intl.NumberFormat('id-ID', { 
            style: 'currency', 
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(Number(amount));

        // Isi data ke tampilan konfirmasi
        document.getElementById('confirmAmountDisplay').innerText = formattedAmount;
        document.getElementById('confirmBank').innerText = bank;
        document.getElementById('confirmRekening').innerText = rekening;
        document.getElementById('confirmName').innerText = name;

        // Switch tampilan (hide input, show confirm)
        document.getElementById('stepInput').style.display = 'none';
        document.getElementById('stepConfirm').style.display = 'block';
    }

    function backToInput() {
        // Switch tampilan (show input, hide confirm)
        document.getElementById('stepConfirm').style.display = 'none';
        document.getElementById('stepInput').style.display = 'block';
    }
    
    // Reset modal saat ditutup agar kembali ke step 1
    const withdrawModal = document.getElementById('withdrawModal');
    if (withdrawModal) {
        withdrawModal.addEventListener('hidden.bs.modal', function () {
            backToInput();
            const form = document.getElementById('withdrawForm');
            if (form) form.reset();
            // ensure success step hidden
            const stepSuccess = document.getElementById('stepSuccess');
            if (stepSuccess) stepSuccess.style.display = 'none';
        });
    }

    function submitWithdrawal() {
    // Ambil data
    let formData = new FormData(document.getElementById('withdrawForm'));
    
    // Kirim AJAX ke Laravel
    fetch("{{ route('reseller.withdraw') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        // Sukses
        document.getElementById('stepConfirm').style.display = 'none';
        document.getElementById('stepInput').style.display = 'none';
        document.getElementById('stepSuccess').style.display = 'block';
        
        // Optional: Reload halaman setelah tutup modal agar saldo terupdate
    })
    .catch(error => {
        alert(error.message || "Terjadi kesalahan sistem");
        backToInput();
    });
}

    function closeAndReset() {
        // hide modal via Bootstrap API, then reset
        const modalEl = document.getElementById('withdrawModal');
        if (modalEl) {
            const bsModal = (window.bootstrap && window.bootstrap.Modal) ? (window.bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl)) : null;
            if (bsModal) bsModal.hide();
            else {
                // fallback: trigger close button click
                const closeBtn = modalEl.querySelector('.btn-close');
                if (closeBtn) closeBtn.click();
            }
        }
        backToInput();
        const form = document.getElementById('withdrawForm');
        if (form) form.reset();
    }
</script>