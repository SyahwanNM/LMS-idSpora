@extends('layouts.admin')
@section('title','Kelola User')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Kelola Akun</h5>
    <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus"></i> Tambah User</a>
</div>
@if(session('success'))<div class="alert alert-success py-2">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger py-2">{{ session('error') }}</div>@endif
<form class="row g-2 mb-3" method="get" action="{{ route('admin.users.index') }}">
    <div class="col-md-3">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama/email">
    </div>
    <div class="col-md-2">
        <select name="role" class="form-select">
            <option value="">Semua Role</option>
            <option value="admin" @selected(request('role')==='admin')>Admin</option>
            <option value="user" @selected(request('role')==='user')>User</option>
        </select>
    </div>
    <div class="col-md-auto">
        <button class="btn btn-outline-secondary" type="submit">Filter</button>
    </div>
</form>
<div class="table-responsive">
<table class="table table-sm align-middle table-bordered">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Role</th>
            <th>Dibuat</th>
            <th style="width:140px;">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($users as $u)
            <tr>
                <td>{{ $loop->iteration + ($users->currentPage()-1)*$users->perPage() }}</td>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td><span class="badge bg-{{ $u->role==='admin' ? 'danger' : 'secondary' }}">{{ ucfirst($u->role) }}</span></td>
                <td>{{ $u->created_at?->format('d-m-Y') }}</td>
                <td>
                    <a href="{{ route('admin.users.edit',$u) }}" class="btn btn-sm btn-warning">Edit</a>
                    @if(auth()->id() !== $u->id)
                    <form action="{{ route('admin.users.destroy',$u) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted">Tidak ada user</td></tr>
        @endforelse
    </tbody>
</table>
</div>
<div>
    {{ $users->links() }}
</div>
@endsection