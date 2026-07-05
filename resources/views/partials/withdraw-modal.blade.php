<style>
    .withdraw-modal-dialog {
        max-width: 640px; /* main desktop cap */
        width: 100%;
        margin: 1.5rem auto;
    }

    @media (max-width: 768px) {
        .withdraw-modal-dialog {
            max-width: 540px;
        }
    }

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
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="background: transparent url('data:image/svg+xml,%3csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 16 16%22 fill=%22%23000%22%3e%3cpath d=%22M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 0 1-1.414-1.414L6.586 8 .293 1.707a1 1 0 0 1 0-1.414z%22/%3e%3c/svg%3e') center/1em auto no-repeat !important;"></button>
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

                {{-- Rekening Tersimpan --}}
                @if(auth()->user()->bank_account_number)
                <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light border border-light-subtle rounded-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-credit-card-2-front text-muted fs-5"></i>
                        <div>
                            <span class="d-block text-muted" style="font-size: 0.7rem; font-weight: 500; line-height: 1;">Rekening Tersimpan</span>
                            <span class="fw-semibold text-dark" style="font-size: 0.8rem;">{{ auth()->user()->bank_name }} - {{ auth()->user()->bank_account_number }}</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-warning fw-semibold rounded-pill px-3 py-1" style="font-size: 0.75rem;"
                        onclick="fillSavedAccount('{{ auth()->user()->bank_name }}', '{{ auth()->user()->bank_account_number }}', '{{ auth()->user()->bank_account_holder }}')">
                        Gunakan
                    </button>
                </div>
                @endif

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
                        <input type="text" class="form-control bg-light border-0 py-2" id="account_number"
                            name="account_number" placeholder="Contoh: 1234 5678 90" required>
                    </div>
                    <div class="col-5">
                        <label for="account_holder" class="form-label fw-light small text-muted">Atas Nama</label>
                        <input type="text" class="form-control bg-light border-0 py-2" id="account_holder"
                            name="account_holder" placeholder="Nama Pemilik" required>
                    </div>
                    <div class="col-12 mt-1 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="save_account" name="save_account" value="1">
                            <label class="form-check-label small text-muted fw-light" for="save_account">
                                Simpan rekening ini untuk penarikan berikutnya
                            </label>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label for="withdrawAmount" class="form-label fw-light small text-muted">Jumlah Penarikan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="withdrawAmount" name="amount" placeholder="0">
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted fw-lighter">Minimal Rp 50.000</small>
                            <button type="button" class="btn btn-link btn-sm text-primary p-0 fw-medium text-decoration-none"
                                onclick="setWithdrawAll({{ max(0, (auth()->user()->wallet_balance ?? 0) - 20000) }})">
                                Tarik Semua
                            </button>
                        </div>
                        <div class="alert alert-light border border-light-subtle rounded-3 p-3 mb-3" style="font-size: 0.75rem; color: #475569; background-color: #f8fafc;">
                            <div class="d-flex gap-2">
                                <i class="bi bi-info-circle-fill text-warning fs-6"></i>
                                <ul class="mb-0 ps-3 fw-light" style="line-height: 1.4;">
                                    <li>Biaya admin Rp <strong>3.000</strong> per transaksi.</li>
                                    <li>Wajib menyisakan minimal <strong>Rp 20.000</strong> di saldo Anda.</li>
                                </ul>
                            </div>
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
                            <div class="row mb-2">
                                <div class="col-5 text-muted small">Atas Nama</div>
                                <div class="col-7 fw-semibold text-end" id="confirmName">Unknown</div>
                            </div>
                            <hr class="my-2" style="border-top: 1px dashed #ccc;">
                            <div class="row mb-1">
                                <div class="col-5 text-muted small">Nominal Penarikan</div>
                                <div class="col-7 fw-semibold text-end text-dark" id="confirmGrossAmount">Rp 0</div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-5 text-muted small">Biaya Admin</div>
                                <div class="col-7 fw-semibold text-end">
                                    <span class="text-danger" style="width: auto !important; font-size: inherit;">- Rp 3.000</span>
                                </div>
                            </div>
                            <div class="row border-top border-1">
                                <div class="col-5 text-muted small">Total Diterima</div>
                                <div class="col-7 fw-bold text-end text-success" id="confirmNetAmount">Rp 0</div>
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
    document.addEventListener('DOMContentLoaded', function() {
        const accountInput = document.getElementById('account_number');
        if (accountInput) {
            accountInput.addEventListener('input', function(e) {
                // Remove all non-digits
                let value = e.target.value.replace(/\D/g, '');
                // Group in chunks of 4 digits
                let formatted = value.match(/.{1,4}/g)?.join(' ') || value;
                e.target.value = formatted;
            });
        }
    });

    function fillSavedAccount(bankName, accountNumber, accountHolder) {
        const bankSelect = document.getElementById('bank_name');
        const numberInput = document.getElementById('account_number');
        const holderInput = document.getElementById('account_holder');

        if (bankSelect) {
            bankSelect.value = bankName;
            bankSelect.classList.remove('text-muted');
        }
        if (numberInput) {
            let formatted = accountNumber.replace(/\D/g, '');
            let spaced = formatted.match(/.{1,4}/g)?.join(' ') || formatted;
            numberInput.value = spaced;
        }
        if (holderInput) {
            holderInput.value = accountHolder;
        }
    }

    // Menyimpan saldo user di variabel JS agar mudah diakses
    const maxBalance = {{ auth()->user()->wallet_balance ?? 0 }};

    function setWithdrawAll(amount) {
        const input = document.getElementById('withdrawAmount');
        if (input) {
            input.value = amount;
        }
    }

    function showConfirmation() {
        const amount = document.getElementById('withdrawAmount')?.value;
        const bank = document.getElementById('bank_name')?.value;
        const rekening = document.getElementById('account_number')?.value;
        const name = document.getElementById('account_holder')?.value;

        const adminFee = 3000;
        const minHolding = 20000;
        const maxWithdrawAllowed = maxBalance - minHolding;

        // 1. Validasi form kosong
        if(!amount || !bank || !rekening || !name) {
            alert("Mohon lengkapi semua data penarikan.");
            return;
        }
        
        // 2. Validasi minimal penarikan
        if(parseInt(amount) < 50000) {
            alert("Minimal penarikan adalah Rp 50.000");
            return;
        }

        if(parseInt(amount) > maxWithdrawAllowed) {
            alert("Maaf, penarikan gagal. Anda wajib menyisakan dana minimal Rp " + new Intl.NumberFormat('id-ID').format(minHolding) + " di saldo Anda. Maksimal penarikan: Rp " + new Intl.NumberFormat('id-ID').format(Math.max(0, maxWithdrawAllowed)));
            return;
        }

        const netAmount = Number(amount) - adminFee;

        const formattedGross = new Intl.NumberFormat('id-ID', { 
            style: 'currency', 
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(Number(amount));

        const formattedNet = new Intl.NumberFormat('id-ID', { 
            style: 'currency', 
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(netAmount);

        document.getElementById('confirmAmountDisplay').innerText = formattedGross; // Tetap tampilkan gross di judul utama konfirmasi
        document.getElementById('confirmBank').innerText = bank;
        document.getElementById('confirmRekening').innerText = rekening;
        document.getElementById('confirmName').innerText = name;

        // Breakdown detail biaya
        document.getElementById('confirmGrossAmount').innerText = formattedGross;
        document.getElementById('confirmNetAmount').innerText = formattedNet;

        document.getElementById('stepInput').style.display = 'none';
        document.getElementById('stepConfirm').style.display = 'block';
    }

    function backToInput() {
        document.getElementById('stepConfirm').style.display = 'none';
        document.getElementById('stepInput').style.display = 'block';
    }
    
    const withdrawModal = document.getElementById('withdrawModal');
    if (withdrawModal) {
        withdrawModal.addEventListener('hidden.bs.modal', function () {
            backToInput();
            const form = document.getElementById('withdrawForm');
            if (form) form.reset();
            const stepSuccess = document.getElementById('stepSuccess');
            if (stepSuccess) stepSuccess.style.display = 'none';
        });
    }

    function submitWithdrawal() {
        let formData = new FormData(document.getElementById('withdrawForm'));
        
        // PENTING: Mencegah tombol di-klik 2 kali (Double Submit)
        const submitBtn = document.querySelector('#stepConfirm .btn-success');
        const originalText = submitBtn.innerText;
        submitBtn.disabled = true;
        submitBtn.innerText = "Memproses...";
        
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
            document.getElementById('stepConfirm').style.display = 'none';
            document.getElementById('stepInput').style.display = 'none';
            document.getElementById('stepSuccess').style.display = 'block';
            
            // Kembalikan tombol seperti semula setelah berhasil
            submitBtn.disabled = false;
            submitBtn.innerText = originalText;
        })
        .catch(error => {
            // Tampilkan pesan error dari backend jika saldo tidak cukup (jaga-jaga jika lolos dari frontend)
            alert(error.message || "Terjadi kesalahan sistem saat memproses penarikan.");
            backToInput();
            submitBtn.disabled = false;
            submitBtn.innerText = originalText;
        });
    }

    function closeAndReset() {
        // Refresh halaman agar saldo di dashboard langsung berkurang
        window.location.reload(); 
    }
</script>