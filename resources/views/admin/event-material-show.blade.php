@extends('layouts.admin')

@section('title', 'Review Event Material - ' . $event->title)

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
            max-width: 1200px;
            margin: 0 auto;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: white;
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            text-decoration: none;
            color: var(--admin-text-main);
            font-weight: 600;
            margin-bottom: 24px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .back-button:hover {
            background: var(--admin-bg);
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
            margin-bottom: 32px;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 24px;
        }

        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        .material-panel {
            background: white;
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            padding: 24px;
        }

        .material-section-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--admin-text-main);
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--admin-border);
        }

        .material-info-item {
            margin-bottom: 16px;
        }

        .material-info-label {
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 600;
            color: var(--admin-text-muted);
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .material-info-value {
            font-size: 14px;
            color: var(--admin-text-main);
        }

        .material-file {
            background: var(--admin-bg);
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            text-decoration: none;
            color: var(--admin-text-main);
            transition: all 0.2s ease;
        }

        .material-file:hover {
            background: #f1f5f9;
            border-color: var(--admin-secondary);
        }

        .material-file-icon {
            font-size: 24px;
        }

        .material-file-info {
            flex: 1;
        }

        .material-file-name {
            font-weight: 600;
            font-size: 13px;
            word-break: break-word;
        }

        .material-file-size {
            font-size: 12px;
            color: var(--admin-text-muted);
        }

        .sidebar-panel {
            background: white;
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            padding: 24px;
            height: fit-content;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        .status-badge.pending {
            background: #fffbeb;
            color: #b45309;
        }

        .status-badge.approved {
            background: #dcfce7;
            color: #166534;
        }

        .status-badge.rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-form {
            margin-top: 16px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--admin-text-main);
            margin-bottom: 8px;
        }

        .form-input,
        .form-textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            font-size: 13px;
            font-family: inherit;
        }

        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-input:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--admin-secondary);
            box-shadow: 0 0 0 3px rgba(67, 56, 202, 0.1);
        }

        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 100%;
        }

        .btn-approve {
            background: #10b981;
            color: white;
            margin-bottom: 8px;
        }

        .btn-approve:hover {
            background: #059669;
        }

        .btn-reject {
            background: #ef4444;
            color: white;
        }

        .btn-reject:hover {
            background: #dc2626;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 13px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
@endsection

@section('content')
    <div class="container-main">
        <button onclick="window.history.back()" class="back-button">
            <span>←</span> Kembali
        </button>

        <h1 class="page-title">{{ $event->title }}</h1>
        <p class="page-subtitle">Review materi yang di-upload oleh trainer</p>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <div class="content-grid">
            <div>
                <div class="material-panel">
                    <div class="material-section-title">Event Information</div>

                    <div class="material-info-item">
                        <div class="material-info-label">Trainer</div>
                        <div class="material-info-value">{{ $event->trainer->name ?? 'Unknown' }}</div>
                    </div>

                    <div class="material-info-item">
                        <div class="material-info-label">Event Date</div>
                        <div class="material-info-value">
                            {{ optional($event->event_date)->format('d M Y') }}
                            @if($event->event_time)
                                - {{ optional($event->event_time)->format('H:i') }}
                            @endif
                        </div>
                    </div>

                    <div class="material-info-item">
                        <div class="material-info-label">Location</div>
                        <div class="material-info-value">{{ $event->location ?? '-' }}</div>
                    </div>
                </div>

                <div class="material-panel" style="margin-top: 24px;">
                    <div class="material-section-title">Uploaded Material</div>

                    @if ($event->module_path)
                        <div class="material-file" target="_blank">
                            <div class="material-file-icon">📄</div>
                            <div class="material-file-info">
                                <div class="material-file-name">
                                    {{ basename($event->module_path) }}
                                </div>
                                <div class="material-file-size">
                                    Uploaded: {{ optional($event->updated_at)->format('d M Y H:i') }}
                                </div>
                            </div>
                        </div>

                        <a href="{{ Storage::url($event->module_path) }}" target="_blank" class="btn"
                            style="background: var(--admin-secondary); color: white; text-decoration: none; text-align: center;">
                            📥 Download Material
                        </a>
                    @else
                        <p style="color: var(--admin-text-muted); font-size: 13px;">No material uploaded</p>
                    @endif
                </div>
            </div>

            <div>
                <div class="sidebar-panel">
                    <div class="status-badge {{ $event->material_status ?? 'pending' }}">
                        {{ ucfirst($event->material_status ?? 'pending') }}
                    </div>

                    @if ($event->material_status === 'pending' || $event->material_status === 'pending_review')
                        <div class="action-form">
                            <!-- Approve Button -->
                            <form method="POST" action="{{ route('admin.event-material.approve', $event->id) }}"
                                style="margin-bottom: 8px;">
                                @csrf
                                <button type="submit" class="btn btn-approve" onclick="return confirm('Approve material ini?')">
                                    ✓ Approve Material
                                </button>
                            </form>

                            <!-- Reject Form -->
                            <div id="rejectForm" style="display: none;">
                                <form method="POST" action="{{ route('admin.event-material.reject', $event->id) }}">
                                    @csrf
                                    <div class="form-group">
                                        <label class="form-label">Alasan Penolakan</label>
                                        <textarea name="rejection_reason" class="form-textarea"
                                            placeholder="Jelaskan mengapa materi ini ditolak..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-reject"
                                        onclick="return confirm('Reject material ini?')">
                                        ✗ Reject Material
                                    </button>
                                    <button type="button" class="btn"
                                        style="background: var(--admin-border); color: var(--admin-text-main); margin-top: 8px;"
                                        onclick="toggleRejectForm()">
                                        Batal
                                    </button>
                                </form>
                            </div>

                            <button type="button" class="btn btn-reject" onclick="toggleRejectForm()">
                                ✗ Reject Material
                            </button>
                        </div>
                    @else
                        <div
                            style="padding: 12px; background: var(--admin-bg); border-radius: 8px; font-size: 12px; color: var(--admin-text-muted);">
                            Material ini sudah di-review pada {{ optional($event->material_approved_at)->format('d M Y H:i') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleRejectForm() {
            const form = document.getElementById('rejectForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
@endsection