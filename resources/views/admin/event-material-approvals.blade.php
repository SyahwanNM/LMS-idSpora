@extends('layouts.admin')

@section('title', 'Event Material Approvals')

@section('navbar')
    @include('partials.navbar-admin-trainer')
@endsection

@section('styles')
    <style>
        :root {
            --admin-primary: #1e1b4b;
            --admin-secondary: #4338ca;
            --admin-bg: #f8fafc;
            --admin-card-bg: #ffffff;
            --admin-border: #e2e8f0;
            --admin-text-main: #0f172a;
            --admin-text-muted: #64748b;
        }

        body {
            background-color: var(--admin-bg);
        }

        .container-main {
            padding: 32px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .page-header {
            margin-bottom: 32px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--admin-text-main);
            margin-bottom: 8px;
        }

        .page-subtitle {
            font-size: 14px;
            color: var(--admin-text-muted);
        }

        .search-filter-bar {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            font-size: 14px;
            background: white;
        }

        .material-card {
            background: var(--admin-card-bg);
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 20px;
            transition: all 0.2s ease;
        }

        .material-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .material-info {
            flex: 1;
        }

        .material-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--admin-text-main);
            margin-bottom: 8px;
        }

        .material-trainer {
            font-size: 13px;
            color: var(--admin-text-muted);
            margin-bottom: 12px;
        }

        .material-meta {
            display: flex;
            gap: 24px;
            font-size: 12px;
            color: var(--admin-text-muted);
        }

        .material-actions {
            display: flex;
            gap: 8px;
        }

        .deadline-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.2px;
            margin-top: 8px;
        }

        .deadline-neutral {
            background: #f1f5f9;
            color: #475569;
        }

        .deadline-safe {
            background: #dcfce7;
            color: #166534;
        }

        .deadline-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .deadline-urgent {
            background: #fee2e2;
            color: #991b1b;
        }

        .deadline-overdue {
            background: #fecaca;
            color: #7f1d1d;
        }

        .btn-primary {
            padding: 8px 16px;
            background: var(--admin-secondary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            opacity: 0.9;
            box-shadow: 0 2px 8px rgba(67, 56, 202, 0.2);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon {
            font-size: 48px;
            margin-bottom: 16px;
            color: var(--admin-text-muted);
        }

        .empty-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--admin-text-main);
            margin-bottom: 8px;
        }

        .empty-text {
            font-size: 14px;
            color: var(--admin-text-muted);
        }

        .pagination {
            margin-top: 32px;
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <div class="container-main">
        <div class="page-header">
            <h1 class="page-title">Event Material Approvals</h1>
            <p class="page-subtitle">Review dan approve/reject materi event dari trainer</p>
        </div>

        @if (session('success'))
            <div style="background: #d4edda; color: #155724; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div style="background: #f8d7da; color: #721c24; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif

        <div class="search-filter-bar">
            <div class="search-box">
                <form method="GET" action="{{ route('admin.event-materials.index') }}" style="display: flex; gap: 8px;">
                    <input type="text" name="search" placeholder="Cari judul event atau nama trainer..."
                        value="{{ request('search') }}">
                    <button type="submit" class="btn-primary" style="flex: 0 0 auto;">Search</button>
                </form>
            </div>
        </div>

        @if ($materials->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <h2 class="empty-title">Tidak Ada Material untuk Di-review</h2>
                <p class="empty-text">Semua materi event sudah di-review atau tidak ada yang menunggu persetujuan</p>
            </div>
        @else
            @forelse ($materials as $material)
                @php
                    $event = $material->event;
                    $status = (string) ($material->material_status ?? 'pending_review');
                    $statusLabel = $status === 'pending_review' ? 'Pending Review' : ucfirst($status);
                    $submittedAt = $material->material_submitted_at ?? $material->updated_at;
                @endphp
                <div class="material-card">
                    <div class="material-info">
                        <div class="material-title">{{ $event->title ?? '-' }}</div>
                        <div class="material-trainer">
                            📌 Trainer: {{ $material->trainer->name ?? 'Unknown' }}
                        </div>
                        @php $deadline = $material->deadline_monitoring ?? ['label' => 'Tanpa tenggat', 'class' => 'neutral']; @endphp
                        <div>
                            <span class="deadline-badge deadline-{{ $deadline['class'] }}">
                                ⏱️ {{ $deadline['label'] }}
                            </span>
                            @if(!empty($event?->material_deadline))
                                <span style="font-size: 11px; color: var(--admin-text-muted); margin-left: 6px;">
                                    ({{ optional($event->material_deadline)->format('d M Y H:i') }})
                                </span>
                            @endif
                        </div>
                        <div class="material-meta">
                            <span>📅 Upload: {{ optional($submittedAt)->format('d M Y H:i') }}</span>
                            <span>🗂️ Status: {{ $statusLabel }}</span>
                        </div>
                    </div>
                    <div class="material-actions">
                        <a href="{{ route('admin.event-material.show', $material->event_id) }}?assignment_id={{ $material->id }}" class="btn-primary">
                            View Details
                        </a>
                    </div>
                </div>
            @empty
            @endforelse

            {{ $materials->render() }}
        @endif
    </div>
@endsection