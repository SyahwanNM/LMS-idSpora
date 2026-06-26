<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Reseller - Panel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #4c1d95;
            --primary-light: #8b5cf6;
            --primary-subtle: #f3e8ff;
            --bg-surface: #ffffff;
            --text-main: #1e1b4b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8FAFC;
        }

        .text-primary-dark {
            color: var(--primary-dark) !important;
        }

        .btn-primary-custom {
            background-color: var(--primary-dark) !important;
            border-color: var(--primary-dark) !important;
            color: #ffffff !important;
            transition: all 0.2s ease;
        }

        .btn-primary-custom:hover {
            background-color: #3b1673 !important;
            border-color: #3b1673 !important;
        }

        .btn-outline-primary-custom {
            color: var(--primary-dark) !important;
            border: 1px solid var(--primary-dark) !important;
            background-color: transparent !important;
            transition: all 0.2s ease;
        }

        .btn-outline-primary-custom:hover {
            color: #ffffff !important;
            background-color: var(--primary-dark) !important;
        }
    </style>
</head>
<body>
    @include('partials.navbar-reseller')

    <main class="main-content min-vh-100">
        <div class="p-4 p-md-5">
            
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="fw-bold mb-1 text-dark fs-2">Katalog Reseller</h2>
                    <p class="text-secondary mb-0">Kelola ketersediaan produk kursus & event untuk program kemitraan.</p>
                </div>
            </div>

            <!-- Search and Filters Bar -->
            <div class="row g-3 mb-4 align-items-center bg-white p-3 rounded-4 shadow-sm mx-0">
                <div class="col-md-8">
                    <div class="position-relative">
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" id="katalogSearchInput" class="form-control ps-5 rounded-pill" placeholder="Cari event atau course..." style="height: 44px;">
                    </div>
                </div>
                <div class="col-md-4">
                    <select id="katalogCategorySelect" class="form-select rounded-pill" style="height: 44px;">
                        <option value="">Semua Kategori</option>
                        <option value="Course">Course</option>
                        <option value="Event">Event</option>
                    </select>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-secondary small">
                                    <th class="ps-4 py-3">NAMA PRODUK</th>
                                    <th class="text-center py-3">KATEGORI</th>
                                    <th class="text-center py-3">KOMISI</th>
                                    <th class="text-center py-3">STATUS RESELLER</th>
                                    <th class="text-center py-3">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Courses -->
                                @foreach($courses as $course)
                                    <tr class="katalog-row" id="courseRow{{ $course->id }}" data-name="{{ strtolower($course->name) }}" data-category="Course" data-bronze="{{ $course->reseller_commission_bronze ?? 10 }}" data-silver="{{ $course->reseller_commission_silver ?? 12 }}" data-gold="{{ $course->reseller_commission_gold ?? 15 }}">
                                        <td class="ps-4 py-3">
                                            <div class="fw-bold text-dark mb-1">{{ $course->name }}</div>
                                            <div class="text-muted small">Rp {{ number_format($course->price, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="text-center py-3">
                                            <span class="badge bg-primary bg-opacity-10 text-primary-dark rounded-pill px-3 py-1.5 fw-semibold small">Course</span>
                                        </td>
                                        <td class="text-center py-3">
                                            @php
                                                $sorted = [
                                                    $course->reseller_commission_bronze ?? 10,
                                                    $course->reseller_commission_silver ?? 12,
                                                    $course->reseller_commission_gold ?? 15
                                                ];
                                                sort($sorted);
                                            @endphp
                                            <div class="fw-bold text-success commission-display">{{ $sorted[0] }}% - {{ $sorted[2] }}%</div>
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex justify-content-center">
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="courseSwitch{{ $course->id }}" @checked($course->is_reseller_course == 1) onchange="toggleResellerStatus('{{ $course->id }}', 'Course', this)" style="cursor: pointer;">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center py-3">
                                            <button class="btn btn-sm btn-outline-primary-custom px-3 shadow-sm" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#editCommissionModal" onclick="prepareCommissionModal('{{ addslashes($course->name) }}', 'Course', '{{ $course->id }}')">
                                                <i class="bi bi-sliders me-1"></i> Komisi Khusus
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach

                                <!-- Events -->
                                @foreach($events as $event)
                                    <tr class="katalog-row" id="eventRow{{ $event->id }}" data-name="{{ strtolower($event->title) }}" data-category="Event" data-bronze="{{ $event->reseller_commission_bronze ?? 10 }}" data-silver="{{ $event->reseller_commission_silver ?? 12 }}" data-gold="{{ $event->reseller_commission_gold ?? 15 }}">
                                        <td class="ps-4 py-3">
                                            <div class="fw-bold text-dark mb-1">{{ $event->title }}</div>
                                            <div class="text-muted small">Rp {{ number_format($event->price, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="text-center py-3">
                                            <span class="badge bg-warning bg-opacity-10 text-warning-emphasis rounded-pill px-3 py-1.5 fw-semibold small">Event</span>
                                        </td>
                                        <td class="text-center py-3">
                                            @php
                                                $sorted = [
                                                    $event->reseller_commission_bronze ?? 10,
                                                    $event->reseller_commission_silver ?? 12,
                                                    $event->reseller_commission_gold ?? 15
                                                ];
                                                sort($sorted);
                                            @endphp
                                            <div class="fw-bold text-success commission-display">{{ $sorted[0] }}% - {{ $sorted[2] }}%</div>
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex justify-content-center">
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="eventSwitch{{ $event->id }}" @checked($event->is_reseller_event == 1) onchange="toggleResellerStatus('{{ $event->id }}', 'Event', this)" style="cursor: pointer;">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center py-3">
                                            <button class="btn btn-sm btn-outline-primary-custom px-3 shadow-sm" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#editCommissionModal" onclick="prepareCommissionModal('{{ addslashes($event->title) }}', 'Event', '{{ $event->id }}')">
                                                <i class="bi bi-sliders me-1"></i> Komisi Khusus
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                
                                <tr id="noResultsRow" style="display: none;">
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <div class="py-4">
                                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                                <i class="bi bi-search text-muted fs-1"></i>
                                            </div>
                                            <h5 class="fw-bold mb-1">Produk Tidak Ditemukan</h5>
                                            <p class="text-secondary small mb-0">Tidak ada produk yang cocok dengan pencarian Anda.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Edit Commission Modal -->
    <div class="modal fade" id="editCommissionModal" tabindex="-1" aria-hidden="true" style="z-index: 1090;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark">Atur Komisi Khusus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Atur persentase komisi khusus per level reseller untuk produk: <br><strong class="text-dark fs-6" id="modalProductName">-</strong></p>
                    <input type="hidden" id="modalProductType">
                    <input type="hidden" id="modalProductId">
                    
                    <!-- Bronze Level -->
                    <div class="mb-3">
                        <label for="modalCommBronze" class="form-label small fw-semibold text-secondary mb-1">
                            <i class="bi bi-shield-fill text-warning-emphasis me-1" style="color: #b45309 !important;"></i> Level Bronze
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="modalCommBronze" placeholder="10" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <!-- Silver Level -->
                    <div class="mb-3">
                        <label for="modalCommSilver" class="form-label small fw-semibold text-secondary mb-1">
                            <i class="bi bi-shield-fill text-secondary me-1"></i> Level Silver
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="modalCommSilver" placeholder="12" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <!-- Gold Level -->
                    <div class="mb-3">
                        <label for="modalCommGold" class="form-label small fw-semibold text-secondary mb-1">
                            <i class="bi bi-shield-fill text-warning me-1" style="color: #d97706 !important;"></i> Level Gold
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="modalCommGold" placeholder="15" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light px-3" style="border-radius: 8px;" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary-custom px-4" style="border-radius: 8px;" onclick="saveCommissionSetting()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION CONTAINER -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
        <div id="actionToast" class="toast align-items-center text-white border-0 bg-dark" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 12px;">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i id="toastIcon" class="bi bi-check-circle-fill text-success fs-5"></i>
                    <span id="toastMessage">Tindakan berhasil diselesaikan!</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toast Helper
        function showToast(msg, isSuccess = true) {
            document.getElementById('toastMessage').innerText = msg;
            const iconEl = document.getElementById('toastIcon');
            if (iconEl) {
                if (isSuccess) {
                    iconEl.className = 'bi bi-check-circle-fill text-success fs-5';
                } else {
                    iconEl.className = 'bi bi-exclamation-circle-fill text-danger fs-5';
                }
            }
            const toastEl = document.getElementById('actionToast');
            const toast = bootstrap.Toast.getOrCreateInstance(toastEl);
            toast.show();
        }

        // Prepare commission modal text and values
        function prepareCommissionModal(productName, type, id) {
            document.getElementById('modalProductName').innerText = productName;
            document.getElementById('modalProductType').value = type;
            document.getElementById('modalProductId').value = id;
            
            // Find row to load current values
            const rowId = type.toLowerCase() + 'Row' + id;
            const row = document.getElementById(rowId);
            if (row) {
                document.getElementById('modalCommBronze').value = row.getAttribute('data-bronze') || '10';
                document.getElementById('modalCommSilver').value = row.getAttribute('data-silver') || '12';
                document.getElementById('modalCommGold').value = row.getAttribute('data-gold') || '15';
            } else {
                document.getElementById('modalCommBronze').value = '10';
                document.getElementById('modalCommSilver').value = '12';
                document.getElementById('modalCommGold').value = '15';
            }
        }

        function saveCommissionSetting() {
            const prodName = document.getElementById('modalProductName').innerText;
            const type = document.getElementById('modalProductType').value;
            const id = document.getElementById('modalProductId').value;
            
            const bronze = document.getElementById('modalCommBronze').value || '10';
            const silver = document.getElementById('modalCommSilver').value || '12';
            const gold = document.getElementById('modalCommGold').value || '15';
            
            const bVal = parseInt(bronze, 10);
            const sVal = parseInt(silver, 10);
            const gVal = parseInt(gold, 10);
            
            if (bVal > sVal) {
                showToast("Gagal: Komisi Bronze tidak boleh lebih besar dari Silver.", false);
                return;
            }
            if (sVal > gVal) {
                showToast("Gagal: Komisi Silver tidak boleh lebih besar dari Gold.", false);
                return;
            }
            
            fetch("{{ route('api.admin.reseller.save-commission') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ id: id, type: type, bronze: bronze, silver: silver, gold: gold })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Close modal
                    const modalEl = document.getElementById('editCommissionModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                    
                    // Update row data attributes
                    const rowId = type.toLowerCase() + 'Row' + id;
                    const row = document.getElementById(rowId);
                    if (row) {
                        row.setAttribute('data-bronze', bronze);
                        row.setAttribute('data-silver', silver);
                        row.setAttribute('data-gold', gold);
                        
                        // Update the "Komisi" display in the row
                        const commDisplay = row.querySelector('.commission-display');
                        if (commDisplay) {
                            const sorted = [parseFloat(bronze), parseFloat(silver), parseFloat(gold)].sort((a, b) => a - b);
                            commDisplay.innerText = `${sorted[0]}% - ${sorted[2]}%`;
                        }
                    }
                    showToast(`Komisi khusus untuk "${prodName}" berhasil disimpan! (Bronze: ${bronze}%, Silver: ${silver}%, Gold: ${gold}%)`, true);
                } else {
                    showToast(`Gagal menyimpan komisi: ${data.message || 'Error'}`, false);
                }
            })
            .catch(err => {
                showToast(`Terjadi kesalahan jaringan.`, false);
            });
        }
 
        // Toggle reseller status via API
        function toggleResellerStatus(id, type, el) {
            const status = el.checked ? 1 : 0;
            const label = type === 'Course' ? 'Course' : 'Event';
            
            fetch("{{ route('api.admin.reseller.toggle-status') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ id: id, type: type, status: status })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(`Status reseller ${label} berhasil diperbarui!`, true);
                } else {
                    el.checked = !el.checked;
                    showToast(`Gagal memperbarui status reseller: ${data.message || 'Error'}`, false);
                }
            })
            .catch(err => {
                el.checked = !el.checked;
                showToast(`Terjadi kesalahan jaringan.`, false);
            });
        }

        // Search & Filter for Katalog Reseller
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('katalogSearchInput');
            const categorySelect = document.getElementById('katalogCategorySelect');

            if (searchInput && categorySelect) {
                function filterKatalog() {
                    const query = searchInput.value.toLowerCase().trim();
                    const category = categorySelect.value;
                    const rows = document.querySelectorAll('.katalog-row');
                    let visibleCount = 0;

                    rows.forEach(row => {
                        const name = row.getAttribute('data-name');
                        const cat = row.getAttribute('data-category');
                        
                        const matchesSearch = !query || name.includes(query);
                        const matchesCategory = !category || cat === category;

                        if (matchesSearch && matchesCategory) {
                            row.style.setProperty('display', '', 'important');
                            visibleCount++;
                        } else {
                            row.style.setProperty('display', 'none', 'important');
                        }
                    });

                    const noResults = document.getElementById('noResultsRow');
                    if (noResults) {
                        noResults.style.display = visibleCount === 0 ? '' : 'none';
                    }
                }

                searchInput.addEventListener('input', filterKatalog);
                categorySelect.addEventListener('change', filterKatalog);
            }
        });
    </script>
</body>
</html>
