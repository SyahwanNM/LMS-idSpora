@extends('layouts.admin')
@section('title','Edit User')
@section('content')
<h5 class="mb-3">Edit User</h5>
@if($errors->any())<div class="alert alert-danger py-2"><ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('admin.users.update',$user) }}" class="card p-3">
    @csrf @method('PUT')
    <div class="mb-3">
        <label class="form-label">Nama</label>
        <input type="text" name="name" value="{{ old('name',$user->name) }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" value="{{ old('email',$user->email) }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Password (kosongkan jika tidak diubah)</label>
        <input type="password" name="password" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label">Role</label>
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
@endsection