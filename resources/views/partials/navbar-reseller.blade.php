@php
    $user = auth()->user();
@endphp

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-semibold" href="{{ route('admin.dashboard') }}">Admin</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarReseller"
            aria-controls="navbarReseller" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarReseller">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="{{ route('admin.reseller') }}">Reseller</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <span class="text-white-50 small d-none d-lg-inline">{{ $user?->name ?? 'Admin' }}</span>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm rounded-3">Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>
