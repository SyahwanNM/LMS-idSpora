<div class="modal fade" id="withdrawModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">

            {{-- Header --}}
            <div class="modal-header border-0">
                <h6 class="modal-title fw-semibold d-flex align-items-center gap-2">
                    <i class="bi bi-wallet2"></i>
                    Tarik Komisi
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body px-4">

                {{-- Saldo --}}
                <div class="d-flex justify-content-between align-items-center bg-light rounded-3 px-3 py-2 mb-3">
                    <small class="text-muted">Saldo Tersedia</small>
                    <span class="fw-semibold text-primary">
                        Rp {{ number_format($availableBalance ?? 1200000, 0, ',', '.') }}
                    </span>
                </div>

                {{-- Metode --}}
                <div class="mb-3">
                    <label class="form-label small">Metode Penarikan</label>
                    <select class="form-select">
                        <option selected disabled>Pilih Metode</option>
                        <option value="bank">Transfer Bank</option>
                        <option value="ewallet">E-Wallet</option>
                    </select>
                </div>

                {{-- Rekening --}}
                <div class="mb-3">
                    <label class="form-label small">Nomor Rekening</label>
                    <input type="text" class="form-control" placeholder="Contoh: 1234 5678 90">
                </div>

                {{-- Jumlah --}}
                <div class="mb-2">
                    <label class="form-label small">Jumlah Penarikan</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="withdrawAmount" placeholder="0">
                    </div>
                </div>

                {{-- Info bawah --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <small class="text-muted">Minimal Rp 50.000</small>
                    <button type="button"
                        class="btn btn-link btn-sm text-primary p-0"
                        onclick="setWithdrawAll({{ $availableBalance ?? 1200000 }})">
                        Tarik Semua
                    </button>
                </div>

                {{-- CTA --}}
                <button class="btn btn-warning w-100 rounded-pill fw-semibold">
                    Lanjut ke Konfirmasi
                </button>

            </div>
        </div>
    </div>
</div>
