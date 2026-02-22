@extends('layouts.admin')
@section('title','Kelola Akun Admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0">Kelola Akun Admin</h5>
        <small class="text-muted">Kelola akun administrator sistem. Untuk mengelola customer, gunakan menu CRM.</small>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus"></i> Tambah Admin</a>
</div>
@if(session('success'))<div class="alert alert-success py-2">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger py-2">{{ session('error') }}</div>@endif
<form class="row g-2 mb-3" method="get" action="{{ route('admin.users.index') }}">
    <div class="col-md-4">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama/email admin">
    </div>
    <div class="col-md-auto">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search me-1"></i> Cari</button>
        @if(request('q'))
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle me-1"></i> Reset</a>
        @endif
    </div>
</form>
<div class="row g-3">
    @forelse($users as $u)
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    @php
                        $avatar = method_exists($u, 'getAvatarUrlAttribute') || isset($u->avatar_url) ? ($u->avatar_url ?? null) : null;
                        $initials = collect(explode(' ', $u->name))
                            ->map(fn($p) => mb_substr($p, 0, 1))
                            ->take(2)
                            ->implode('');
                    @endphp
                    <div class="me-3">
                        @if($avatar)
                            <img src="{{ $avatar }}" alt="{{ $u->name }}" class="rounded-circle" style="width:56px;height:56px;object-fit:cover;">
                        @else
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width:56px;height:56px;">
                                <span class="fw-semibold">{{ $initials }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="fw-semibold">{{ $u->name }}</div>
                                <div class="text-muted small">{{ $u->email }}</div>
                            </div>
                            <span class="badge bg-{{ $u->role==='admin' ? 'danger' : 'secondary' }}">{{ ucfirst($u->role) }}</span>
                        </div>
                        <div class="text-muted small mt-2">Dibuat: {{ $u->created_at?->format('d-m-Y') }}</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.users.edit',$u) }}" class="btn btn-sm btn-warning">Edit</a>
                    @if(auth()->id() !== $u->id)
                        <button type="button" class="btn btn-sm btn-danger btn-open-delete-modal"
                                data-user-id="{{ $u->id }}"
                                data-user-name="{{ $u->name }}"
                                data-user-email="{{ $u->email }}"
                                data-delete-url="{{ route('admin.users.destroy',$u) }}">
                            Hapus
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center text-muted py-4">
                <i class="bi bi-person-x" style="font-size: 3rem; color: #ccc;"></i>
                <p class="mt-3">Tidak ada akun admin ditemukan</p>
            </div>
        </div>
    @endforelse
</div>
<div class="mt-3">
    {{ $users->links() }}
</div>

<!-- Centered Delete Confirmation Modal (modern) -->
<div class="modal fade" id="confirmDeleteUserModal" tabindex="-1" aria-labelledby="confirmDeleteUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-modern position-relative">
            <span class="gradient-ring" aria-hidden="true"></span>
            <div class="modal-header border-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-pill" style="color:#dc2626"><i class="bi bi-trash fs-4"></i></div>
                    <div>
                        <h5 class="modal-title mb-0" id="confirmDeleteUserLabel">Konfirmasi Hapus</h5>
                        <small class="text-muted">Tindakan ini tidak dapat dibatalkan</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <p class="mb-3">Anda yakin ingin menghapus user berikut?</p>
                <div class="p-3 rounded bg-light">
                    <div class="small text-muted">Nama</div>
                    <div class="fw-semibold" id="confirmDeleteUserName">-</div>
                    <div class="small text-muted mt-2">Email</div>
                    <div class="fw-semibold" id="confirmDeleteUserEmail">-</div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="confirmDeleteUserForm" action="#" method="POST" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger confirm-danger-btn">
                                <i class="bi bi-trash me-1"></i> Ya, hapus
                        </button>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
/* Modern modal look (shared) */
.modal-modern{border:0;border-radius:18px;background:rgba(255,255,255,0.92);backdrop-filter:saturate(180%) blur(10px);-webkit-backdrop-filter:saturate(180%) blur(10px);box-shadow:0 20px 40px rgba(0,0,0,.18),0 8px 18px rgba(0,0,0,.08);overflow:hidden}
.gradient-ring{position:absolute;inset:-2px;border-radius:20px;padding:2px;background:linear-gradient(135deg,#6366f1,#ef4444,#f59e0b,#10b981);background-size:300% 300%;animation:hue-shift 6s ease infinite;-webkit-mask:linear-gradient(#fff 0 0) content-box,linear-gradient(#fff 0 0);-webkit-mask-composite:xor;mask-composite:exclude;pointer-events:none}
@keyframes hue-shift{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
.icon-pill{width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#fee2e2,#fff7ed);box-shadow:inset 0 0 0 1px rgba(220,38,38,.25)}
.confirm-danger-btn{background:#dc2626;border-color:#dc2626}
.confirm-danger-btn:hover{background:#b91c1c;border-color:#b91c1c}
</style>

<script>
    // Wait for DOM and Bootstrap to be available before wiring
    document.addEventListener('DOMContentLoaded', function(){
        const modalEl = document.getElementById('confirmDeleteUserModal');
        const nameEl = document.getElementById('confirmDeleteUserName');
        const emailEl = document.getElementById('confirmDeleteUserEmail');
        const formEl = document.getElementById('confirmDeleteUserForm');
        const spinnerEl = document.getElementById('confirmDeleteSpinner');
        let modal = null;
        try {
            if (window.bootstrap && modalEl) {
                modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            }
        } catch(e) {}

        document.querySelectorAll('.btn-open-delete-modal').forEach(function(btn){
            btn.addEventListener('click', function(){
                const name = btn.getAttribute('data-user-name') || '-';
                const email = btn.getAttribute('data-user-email') || '-';
                const url = btn.getAttribute('data-delete-url');
                if (nameEl) nameEl.textContent = name;
                if (emailEl) emailEl.textContent = email;
                if (formEl && url) formEl.setAttribute('action', url);
                if (modal) { try { modal.show(); } catch(e){} }
                else {
                    // If Bootstrap is not yet loaded, try showing after a short delay
                    setTimeout(function(){
                        try {
                            if (window.bootstrap && modalEl) {
                                modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                                modal.show();
                                return;
                            }
                        } catch(e){}
                        // Last resort: do nothing (avoid native confirm to keep modern UX)
                    }, 120);
                }
            });
        });

        if (formEl) {
            formEl.addEventListener('submit', function(ev){
                // Show success check animation before submitting for modern feedback
                ev.preventDefault();
                try {
                    const body = modalEl?.querySelector('.modal-body');
                    const footer = modalEl?.querySelector('.modal-footer');
                    if (footer) footer.style.display = 'none';
                    if (body) {
                        body.classList.add('d-flex','flex-column','align-items-center','justify-content-center');
                        body.innerHTML = `
                            <div class="text-center">
                                <svg class="check-anim" viewBox="0 0 72 72" aria-hidden="true" style="width:88px;height:88px;display:block;margin:0 auto;">
                                    <circle class="circle" cx="36" cy="36" r="32" fill="none" stroke="#10b981" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="201" stroke-dashoffset="201"></circle>
                                    <path class="check" d="M22 36.5 32 46 50 27" fill="none" stroke="#10b981" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="60" stroke-dashoffset="60"></path>
                                </svg>
                                <p class="mb-0 fw-semibold">Berhasil dihapus</p>
                                <small class="text-muted">Mengirim permintaan...</small>
                            </div>`;
                        // animate strokes via CSS
                        const style = document.createElement('style');
                        style.textContent = `@keyframes drawCircle{to{stroke-dashoffset:0}}@keyframes drawCheck{to{stroke-dashoffset:0}}.circle{animation:drawCircle .6s ease forwards}.check{animation:drawCheck .5s .45s ease forwards}`;
                        document.head.appendChild(style);
                    }
                } catch(e) {}
                setTimeout(function(){ formEl.submit(); }, 750);
            });
        }
    });
</script>
@endsection