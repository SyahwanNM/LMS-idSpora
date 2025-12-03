@extends('layouts.admin')
@section('title','Edit User')
@section('content')
<h5 class="mb-3">Edit User</h5>
@if($errors->any())<div class="alert alert-danger py-2"><ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('admin.users.update',$user) }}" class="card p-3" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="mb-3">
        <label class="form-label text-dark">Foto Profil</label>
        <div class="d-flex align-items-center gap-3">
            <div class="avatar-circle" style="width:56px;height:56px;border-radius:50%;overflow:hidden;border:2px solid #EBBC01;background:#6b7280;display:flex;align-items:center;justify-content:center;">
                <img id="avatarEditPreview" src="{{ $user->avatar_url ?? asset('aset/default-avatar.png') }}" alt="avatar" style="width:100%;height:100%;object-fit:cover;display:block;">
            </div>
            <div class="flex-grow-1">
                <input type="file" name="avatar" accept="image/*" class="form-control" id="avatarEditInput">
                <small class="text-muted">Opsional. Format: JPG/PNG, ukuran maks 2MB.</small>
            </div>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label text-dark">Nama</label>
        <input type="text" name="name" value="{{ old('name',$user->name) }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label text-dark">Email</label>
        <input type="email" name="email" value="{{ old('email',$user->email) }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label text-dark">Password (kosongkan jika tidak diubah)</label>
        <input type="password" name="password" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label text-dark">Role</label>
        <select name="role" class="form-select" required>
            <option value="user" @selected(old('role',$user->role)==='user')>User</option>
            <option value="admin" @selected(old('role',$user->role)==='admin')>Admin</option>
        </select>
    </div>
    <div class="d-flex justify-content-between">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Kembali</a>
        <button class="btn btn-primary" type="submit">Update</button>
    </div>
</form>
<script>
document.addEventListener('DOMContentLoaded', function(){
    var input = document.getElementById('avatarEditInput');
    var preview = document.getElementById('avatarEditPreview');
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
});
</script>
@endsection