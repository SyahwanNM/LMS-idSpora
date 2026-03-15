<!DOCTYPE html>
<html>
<head>
    <title>Daftar Course - {{ $periodName ?? 'Semua Data' }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #333; line-height: 1.35; }
        .header { text-align: center; margin-bottom: 18px; border-bottom: 2px solid #444; padding-bottom: 12px; }
        .header h1 { margin: 0; color: #1A1D1F; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 3px 0 0; color: #6F767E; font-size: 12px; }

        .meta { margin: 10px 0 14px; }
        .meta table { width: 100%; border-collapse: collapse; }
        .meta td { padding: 6px 8px; border: 1px solid #EEE; }
        .meta .label { width: 18%; color: #6F767E; }

        .table-title { font-size: 12px; font-weight: bold; margin: 0 0 8px; }
        table.report { width: 100%; border-collapse: collapse; }
        table.report th { background-color: #1A1D1F; color: #FFF; padding: 8px 8px; text-align: left; text-transform: uppercase; font-size: 9px; }
        table.report td { padding: 7px 8px; border-bottom: 1px solid #EEE; vertical-align: top; }
        table.report tr:nth-child(even) { background-color: #F8F9FA; }

        .text-right { text-align: right; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 10px; font-size: 9px; color: #fff; }
        .badge-active { background: #28a745; }
        .badge-archive { background: #6c757d; }
        .badge-missing { background: #dc3545; }
        .badge-progress { background: #ffc107; color: #1A1D1F; }
        .badge-complete { background: #17a2b8; }

        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #999; padding: 8px 0; border-top: 1px solid #EEE; }
        .muted { color: #6F767E; }
    </style>
</head>
<body>
    @php
        /** @var \Illuminate\Support\Collection|array $courses */
        use Illuminate\Support\Str;
    @endphp
    <div class="header">
        <h1>LMS IDSPORA</h1>
        <p>Daftar Course (Export)</p>
        <p style="font-size: 11px; font-weight: bold;">{{ $periodName ?? 'Semua Data' }}</p>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td class="label">Total Course</td>
                <td>{{ is_countable($courses ?? []) ? count($courses) : 0 }}</td>
                <td class="label">Filter</td>
                <td>{{ ($q ?? '') !== '' ? ('Pencarian: ' . $q) : 'Tidak ada' }}</td>
            </tr>
        </table>
    </div>

    <div class="table-title">Rincian Course</div>
    <table class="report">
        <thead>
            <tr>
                <th style="width:4%">ID</th>
                <th style="width:22%">Nama</th>
                <th style="width:14%">Kategori</th>
                <th style="width:9%">Level</th>
                <th style="width:10%" class="text-right">Harga</th>
                <th style="width:9%">Status</th>
                <th style="width:12%">Kelengkapan</th>
                <th style="width:8%" class="text-right">Modul</th>
                <th style="width:12%">Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($courses ?? []) as $course)
                @php
                    $modules = $course->modules ?? collect();
                    $totalModules = $modules->count();
                    $hasModules = $totalModules > 0;
                    $isPublished = ((string) $course->status) === 'active';
                    $kelengkapan = $isPublished ? 'Complete' : ($hasModules ? 'In Progress' : 'Missing Material');
                    $statusBadge = $isPublished ? 'badge-active' : 'badge-archive';
                    $kelBadge = $isPublished ? 'badge-complete' : ($hasModules ? 'badge-progress' : 'badge-missing');
                @endphp
                <tr>
                    <td>{{ $course->id }}</td>
                    <td>
                        <div style="font-weight:bold">{{ $course->name }}</div>
                        <div class="muted">{{ $course->description ? Str::limit(strip_tags((string) $course->description), 80) : '' }}</div>
                    </td>
                    <td>{{ optional($course->category)->name ?? '-' }}</td>
                    <td>{{ ucfirst((string) $course->level) }}</td>
                    <td class="text-right">Rp {{ number_format((int) $course->price, 0, ',', '.') }}</td>
                    <td><span class="badge {{ $statusBadge }}">{{ (string) $course->status }}</span></td>
                    <td><span class="badge {{ $kelBadge }}">{{ $kelengkapan }}</span></td>
                    <td class="text-right">{{ $totalModules }}</td>
                    <td>{{ $course->created_at ? $course->created_at->format('d/m/Y H:i') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center" class="muted">Tidak ada course untuk diexport.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis oleh Sistem LMS IdSpora pada {{ now()->format('d F Y, H:i:s') }}
    </div>
</body>
</html>
