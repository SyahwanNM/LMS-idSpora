<div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            
            <!-- FORM START -->
            <form action="" method="POST" id="withdrawForm">
                @csrf
                
                <!-- STEP 1: INPUT DETAILS -->
                <div id="stepInput">
                    <div class="modal-header border-0 pb-0">
                        <h1 class="modal-title fs-5 fw-bold" id="withdrawModalLabel">
                            <i class="bi bi-wallet2 text-warning me-2"></i>Tarik Komisi
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body pt-2">
                        <!-- Saldo Display -->
                        <div class="text-center mb-4 p-3 bg-light rounded-4 border border-light-subtle">
                            <span class="text-muted d-block small mb-1 fw-medium">Saldo Tersedia</span>
                            <h2 class="fw-bold text-success mb-0">
                                Rp {{ number_format(auth()->user()->wallet_balance ?? 0, 0, ',', '.') }}
                            </h2>
                        </div>

                        <!-- Input Amount -->
                        <div class="mb-3">
                            <label for="amount" class="form-label fw-semibold small text-muted">Jumlah Penarikan (Min. Rp 50.000)</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-light fw-bold text-muted">Rp</span>
                                <input type="number" 
                                       class="form-control bg-light border-0 py-2" 
                                       id="amount" 
                                       name="amount" 
                                       min="50000" 
                                       max="{{ auth()->user()->wallet_balance ?? 0 }}"
                                       placeholder="0" 
                                       required>
                            </div>
                            <div class="form-text text-end small" id="amountHelp">
                                <a href="#" class="text-decoration-none text-warning" onclick="setMaxAmount()">Tarik Semua</a>
                            </div>
                        </div>

                        <!-- Bank Details -->
                        <div class="mb-3">
                            <label for="bank_name" class="form-label fw-semibold small text-muted">Bank Tujuan</label>
                            <select class="form-select bg-light border-0 py-2" id="bank_name" name="bank_name" required>
                                <option value="" disabled selected>Pilih Bank / E-Wallet</option>
                                <option value="BCA">BCA</option>
                                <option value="Mandiri">Mandiri</option>
                                <option value="BNI">BNI</option>
                                <option value="BRI">BRI</option>
                                <option value="BSI">BSI</option>
                                <option value="Jenius">Jenius / BTPN</option>
                                <option value="Jago">Bank Jago</option>
                                <option value="SeaBank">SeaBank</option>
                                <option value="OVO">OVO</option>
                                <option value="GoPay">GoPay</option>
                                <option value="Dana">DANA</option>
                                <option value="ShopeePay">ShopeePay</option>
                            </select>
                        </div>

                        <div class="row g-3">
                            <div class="col-7">
                                <label for="account_number" class="form-label fw-semibold small text-muted">Nomor Rekening</label>
                                <input type="number" class="form-control bg-light border-0 py-2" id="account_number" name="account_number" placeholder="Contoh: 1234567890" required>
                            </div>
                            <div class="col-5">
                                <label for="account_holder" class="form-label fw-semibold small text-muted">Atas Nama</label>
                                <input type="text" class="form-control bg-light border-0 py-2" id="account_holder" name="account_holder" placeholder="Nama Pemilik" required>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0 pb-4 justify-content-center gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-warning rounded-pill px-5 fw-bold text-white shadow-sm" onclick="showConfirmation()" style="background: linear-gradient(135deg, #ffc107 0%, #ffca2c 100%); border:none;">
                            Lanjut Konfirmasi
                        </button>
                    </div>
                </div>
                <!-- END STEP 1 -->

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
                        <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm">
                            Ya, Tarik Dana
                        </button>
                    </div>
                </div>
                <!-- END STEP 2 -->

            </form>
        </div>
    </div>
</div>

<script>
    function setMaxAmount() {
        const maxAmount = document.getElementById('amount').getAttribute('max');
        document.getElementById('amount').value = maxAmount;
    }

    function showConfirmation() {
        // Ambil value dari input form
        const amount = document.getElementById('amount').value;
        const bank = document.getElementById('bank_name').value;
        const rekening = document.getElementById('account_number').value;
        const name = document.getElementById('account_holder').value;

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
        }).format(amount);

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
            document.getElementById('withdrawForm').reset();
        });
    }
</script>