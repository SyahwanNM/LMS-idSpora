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
                    $isPublished = ((string) $course->status) === 'active';

                    $pdfSlots = $modules->where('type', 'pdf');
                    $videoSlots = $modules->where('type', 'video');
                    $quizSlots = $modules->where('type', 'quiz');

                    $hasMissing = false;
                    if ($totalModules <= 0) {
                        $hasMissing = true;
                    }
                    if ($pdfSlots->count() <= 0 || $pdfSlots->filter(fn($m) => empty($m->content_url))->count() > 0) {
                        $hasMissing = true;
                    }
                    if ($videoSlots->count() <= 0 || $videoSlots->filter(fn($m) => empty($m->content_url))->count() > 0) {
                        $hasMissing = true;
                    }
                    if ($quizSlots->count() <= 0) {
                        $hasMissing = true;
                    } else {
                        $missingQuiz = $quizSlots->filter(function ($m) {
                            $cnt = null;
                            if (isset($m->quiz_questions_count)) {
                                $cnt = (int) $m->quiz_questions_count;
                            } elseif (method_exists($m, 'relationLoaded') && $m->relationLoaded('quizQuestions')) {
                                $cnt = $m->quizQuestions ? (int) $m->quizQuestions->count() : 0;
                            }
                            $cnt = (int) ($cnt ?? 0);
                            return $cnt <= 0;
                        })->count();
                        if ($missingQuiz > 0) {
                            $hasMissing = true;
                        }
                    }

                    $kelengkapan = $isPublished ? 'Complete' : ($hasMissing ? 'Missing Material' : 'In Progress');
                    $statusBadge = $isPublished ? 'badge-active' : 'badge-archive';
                    $kelBadge = $isPublished ? 'badge-complete' : ($hasMissing ? 'badge-missing' : 'badge-progress');
                @endphp
                <tr>
                    <td>{{ $course->id }}</td>
                    <td>
                        <div style="font-weight:bold">{{ $course->name }}</div>
                        <div class="muted">{{ $course->description ? \Illuminate\Support\Str::limit(strip_tags((string) $course->description), 80) : '' }}</div>
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
