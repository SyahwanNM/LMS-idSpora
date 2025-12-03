@extends('layouts.admin')
@section('title','Tambah User')
@section('content')
<h5 class="mb-3">Tambah User</h5>
@if($errors->any())<div class="alert alert-danger py-2"><ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('admin.users.store') }}" class="card p-3" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label class="form-label text-dark">Foto Profil</label>
        <div class="d-flex align-items-center gap-3">
            <div class="avatar-circle" style="width:56px;height:56px;border-radius:50%;overflow:hidden;border:2px solid #EBBC01;background:#6b7280;display:flex;align-items:center;justify-content:center;">
                <img id="avatarPreview" src="{{ asset('aset/default-avatar.png') }}" alt="avatar" style="width:100%;height:100%;object-fit:cover;display:block;">
            </div>
            <div class="flex-grow-1">
                <input type="file" name="avatar" accept="image/*" class="form-control" id="avatarInput">
                <small class="text-muted">Opsional. Format: JPG/PNG, ukuran maks 2MB.</small>
            </div>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label text-dark">Nama</label>
        <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label text-dark">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label text-dark">Password</label>
        <div class="input-group">
            <input type="password" name="password" id="passwordInput" class="form-control" required>
            <button class="btn btn-outline-secondary" type="button" id="togglePassword"><i class="bi bi-eye"></i></button>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label text-dark">Konfirmasi Password</label>
        <div class="input-group">
            <input type="password" name="password_confirmation" id="passwordConfirmInput" class="form-control" required>
            <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm"><i class="bi bi-eye"></i></button>
        </div>
        <div class="invalid-feedback">Konfirmasi password tidak sama dengan password.</div>
        <small id="passwordMatchHint" class="text-muted d-none">Password cocok.</small>
    </div>
    <div class="mb-3">
        <label class="form-label text-dark">Role</label>
        <select name="role" class="form-select" required>
            <option value="user" @selected(old('role')==='user')>User</option>
            <option value="admin" @selected(old('role')==='admin')>Admin</option>
        </select>
    </div>
    <div class="d-flex justify-content-between">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Kembali</a>
        <button class="btn btn-primary" type="submit">Simpan</button>
    </div>
</form>
<script>
document.addEventListener('DOMContentLoaded', function(){
    var input = document.getElementById('avatarInput');
    var preview = document.getElementById('avatarPreview');
    if(input && preview){
        input.addEventListener('change', function(){
            var file = input.files && input.files[0];
            if(!file) return;
            if(!file.type.startsWith('image/')) return;
            var reader = new FileReader();
            reader.onload = function(e){ preview.src = e.target.result; };
            reader.readAsDataURL(file);
        });
    }

    var pass = document.getElementById('passwordInput');
    var confirm = document.getElementById('passwordConfirmInput');
    var hint = document.getElementById('passwordMatchHint');
    var togglePass = document.getElementById('togglePassword');
    var toggleConfirm = document.getElementById('togglePasswordConfirm');
    function validateMatch(){
        if(!pass || !confirm) return true;
        var ok = pass.value.length > 0 && confirm.value.length > 0 && pass.value === confirm.value;
        confirm.classList.toggle('is-invalid', !ok && confirm.value.length > 0);
        hint?.classList.toggle('d-none', !ok);
        return ok;
    }
    pass?.addEventListener('input', validateMatch);
    confirm?.addEventListener('input', validateMatch);

    function toggleVisibility(input, btn){
        if(!input || !btn) return;
        var icon = btn.querySelector('i');
        var isPassword = input.getAttribute('type') === 'password';
        input.setAttribute('type', isPassword ? 'text' : 'password');
        if(icon){ icon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye'; }
    }
    togglePass?.addEventListener('click', function(){ toggleVisibility(pass, togglePass); });
    toggleConfirm?.addEventListener('click', function(){ toggleVisibility(confirm, toggleConfirm); });

    var form = document.querySelector('form[action="{{ route('admin.users.store') }}"]');
    form?.addEventListener('submit', function(e){
        if(!validateMatch()){
            e.preventDefault();
            confirm?.focus();
        }
    });
});
</script>
@endsection