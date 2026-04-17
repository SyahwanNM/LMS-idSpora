@extends('layouts.admin')
@section('title', 'Manage User')
@section('content')
<div class="box-manage">
    <div class="box-title-add">
        <h3>Manage&nbsp;User</h3>
        <div class="box-serach-users">
            <input id="searchUser" class="input-search" type="search" placeholder="Cari User..." autocomplete="off">
            <div class="box-logo-search">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="white" class="bi bi-search" viewBox="0 0 16 16">
                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                </svg>
            </div>
        </div>
    </div>
    <div class="scroll-add-box">
        <table id="userTable" class="table-daftar table">
            <thead>
                <tr>
                    <th style="background-color: #E4E4E6;" scope="col">Nama</th>
                    <th style="background-color: #E4E4E6;" scope="col">No Telp</th>
                    <th style="background-color: #E4E4E6;" scope="col">Email</th>
                    <th style="background-color: #E4E4E6;" scope="col">Profesi</th>
                    <th style="background-color: #E4E4E6;" scope="col">Institusi</th>
                    <th style="background-color: #E4E4E6;" scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($users ?? []) as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->phone ?? '-' }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->profession ?? '-' }}</td>
                    <td>{{ $user->institution ?? '-' }}</td>
                    <td>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewUserModal-{{ $user->id }}">
                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                        </svg>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada pengguna.</td>
                </tr>
                @endforelse
                @if(isset($users) && count($users) > 0)
                <tr id="noResultsRow" style="display:none;">
                    <td colspan="6" class="text-center">Tidak ada hasil.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    @php(\Carbon\Carbon::setLocale('id'))
    @foreach(($users ?? []) as $user)
    <div class="modal-view modal fade" id="viewUserModal-{{ $user->id }}" tabindex="-1" aria-labelledby="viewUserModalLabel-{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog custom-modal">
            <div class="view-modal-user-event modal-content">
                <div class="modal-header border-bottom pb-3">
                    <h5 class="modal-title fw-bold" id="viewUserModalLabel-{{ $user->id }}">Detail Pengguna: &nbsp;{{ $user->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="box-modal-view">
                        {{-- Kiri: Informasi Pengguna --}}
                        <div class="box-view-kiri text-center">
                            <h5 class="fw-semibold mb-3" style="text-align:left;">Informasi Pengguna</h5>
                            <img class="profile-biodata rounded-circle mb-2"
                                src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=51376c&color=fff&size=120' }}"
                                alt="Avatar" style="width:90px;height:90px;object-fit:cover;">
                            <div class="box-biodata mt-2">
                                <h6 class="fw-bold mb-1">{{ $user->name }}</h6>
                                <p class="mb-1 text-muted" style="font-size:13px;">{{ $user->email }}</p>
                                @if($user->phone)
                                    <p class="mb-1" style="font-size:13px;">{{ $user->phone }}</p>
                                @endif
                                <br>
                                @if($user->institution)
                                    <p class="mb-1" style="font-size:13px;">{{ $user->institution }}</p>
                                @endif
                                @if($user->profession)
                                    <p class="mb-1" style="font-size:13px;">Profesi : {{ $user->profession }}</p>
                                @endif
                                <br>
                                <p class="mb-0" style="font-size:13px;">Bergabung sejak</p>
                                <p class="fw-semibold" style="font-size:13px;">{{ optional($user->created_at)->translatedFormat('d F Y') }}</p>
                            </div>
                        </div>

                        {{-- Kanan: Statistik & Daftar Acara --}}
                        <div class="box-view-kanan" style="display:flex; flex-direction:column; gap:16px;">
                            {{-- Total Partisipasi --}}
                            <div class="d-flex justify-content-between align-items-center p-3 border rounded-3" style="background:#fff;">
                                <div>
                                    <p class="mb-1 text-muted" style="font-size:13px;">Total Partisipasi Acara</p>
                                    <h4 class="fw-bold mb-0">{{ $user->eventRegistrations->count() }}</h4>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#6c757d" class="bi bi-calendar3" viewBox="0 0 16 16">
                                    <path d="M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2M1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857z"/>
                                    <path d="M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                                </svg>
                            </div>

                            {{-- Daftar Acara --}}
                            <div class="border rounded-3 p-3" style="background:#fff;">
                                <h6 class="fw-bold mb-3">Daftar Acara yang Dihadiri</h6>
                                <div class="scroll-view-box">
                                    <table class="table-daftar-acara table table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th style="background-color:#E4E4E6; font-size:13px;">Nama Event</th>
                                                <th style="background-color:#E4E4E6; font-size:13px;">Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse(($user->eventRegistrations ?? collect()) as $reg)
                                            @php($evt = $reg->event)
                                            <tr>
                                                <td style="font-size:13px;">{{ $evt?->title ?? '-' }}</td>
                                                <td style="font-size:13px;">{{ ($evt?->start_at?->translatedFormat('d F Y')) ?? '-' }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="2" class="text-center text-muted" style="font-size:13px;">Belum ada partisipasi acara.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    <div class="modal-add modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" placeholder="Masukkan Nama">
                        </div>
                        <div class="mb-3">
                            <label for="no-telp" class="form-label">No Telp</label>
                            <input type="tel" class="form-control" id="no-telp" placeholder="Masukkan No Telp">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="Masukkan Email">
                        </div>
                        <div class="mb-3">
                            <label for="profesi" class="form-label">Profesi</label>
                            <input type="text" class="form-control" id="profesi" placeholder="Masukkan Profesi">
                        </div>
                        <div class="mb-3">
                            <label for="institusi" class="form-label">Institusi</label>
                            <input type="text" class="form-control" id="institusi" placeholder="Masukkan Institusi">
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    (function() {
        const input = document.getElementById('searchUser');
        const table = document.getElementById('userTable');
        const noResultsRow = document.getElementById('noResultsRow');
        if (!input || !table) return;

        const filterRows = () => {
            const q = (input.value || '').trim().toLowerCase();
            const rows = Array.from(table.querySelectorAll('tbody tr'))
                .filter(r => !noResultsRow || r.id !== 'noResultsRow');

            let anyVisible = false;
            rows.forEach(row => {
                const nameCell = row.querySelector('td:first-child');
                const nameText = (nameCell && nameCell.textContent ? nameCell.textContent : '').toLowerCase();
                const show = !q || nameText.includes(q);
                row.style.display = show ? '' : 'none';
                if (show) anyVisible = true;
            });

            if (noResultsRow) {
                noResultsRow.style.display = anyVisible ? 'none' : '';
            }
        };

        input.addEventListener('input', filterRows);
    })();
</script>
@endsection