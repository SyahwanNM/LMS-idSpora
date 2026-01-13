
@extends('layouts.admin')
@section('title', 'Manage User')
@section('content')
    <div class="box-manage">
        <div class="box-title-add">
            <h3>Manage&nbsp;User</h3>
            <div>
                <input id="searchUser" class="input-search" type="search" placeholder="Cari User..." autocomplete="off">
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
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewUserModalLabel-{{ $user->id }}">Detail Users: {{ $user->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="box-modal-view">
                            <div class="box-view-kiri">
                                <h5>Informasi Pengguna</h5>
                                <img class="profile-biodata" src="{{ $user->avatar_url }}" alt="Avatar">
                                <div class="box-biodata">
                                    <h6>{{ $user->name }}</h6>
                                    <p>{{ $user->email }}</p>
                                    <br>
                                    <p>Profesi: {{ $user->profession ?? '-' }}</p>
                                    <p>Institusi: {{ $user->institution ?? '-' }}</p>
                                    <p>Bergabung Sejak: {{ optional($user->created_at)->translatedFormat('d F Y') }}</p>
                                </div>
                            </div>
                            <div class="box-view-kanan">
                                <div class="box-total">
                                    <h5>Total Partisipasi Acara</h5>
                                    <h6>{{ $user->eventRegistrations->count() }} Acara</h6>
                                </div>
                                <div class="scroll-view-box">
                                    <table class="table-daftar-acara table">
                                        <thead>
                                            <tr>
                                                <th style="background-color: #E4E4E6;" scope="col">Nama Acara</th>
                                                <th style="background-color: #E4E4E6;" scope="col">Tanggal</th>
                                                <th style="background-color: #E4E4E6;" scope="col">Kategori</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse(($user->eventRegistrations ?? collect()) as $reg)
                                                @php($evt = $reg->event)
                                                <tr>
                                                    <td>{{ $evt?->title ?? '-' }}</td>
                                                    <td>{{ ($evt?->start_at?->translatedFormat('d F Y')) ?? '-' }}</td>
                                                    <td>{{ (($evt?->price ?? 0) > 0) ? 'Berbayar' : 'Free' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">Belum ada partisipasi acara.</td>
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
    (function(){
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
