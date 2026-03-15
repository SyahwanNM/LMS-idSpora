<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Report Course' }}</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10.5px; color: #111827; }
        .header { text-align: left; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #E5E7EB; }
        .header .title { font-size: 16px; font-weight: 700; margin: 0; }
        .header .subtitle { font-size: 11px; color: #6B7280; margin: 4px 0 0; }
        .meta { margin: 10px 0 12px; }
        .meta table { width: 100%; border-collapse: collapse; }
        .meta td { padding: 6px 8px; border: 1px solid #E5E7EB; }
        .meta .label { width: 18%; color: #6B7280; }

        table.report { width: 100%; border-collapse: collapse; }
        table.report th { background: #E5E7EB; font-size: 10px; font-weight: 700; text-align: left; padding: 7px 8px; border: 1px solid #D1D5DB; }
        table.report td { padding: 7px 8px; border: 1px solid #E5E7EB; vertical-align: top; }
        table.report tr:nth-child(even) { background: #F9FAFB; }

        .text-right { text-align: right; }
        .muted { color: #6B7280; }

        .footer { position: fixed; bottom: 0; left: 0; right: 0; padding-top: 6px; border-top: 1px solid #E5E7EB; font-size: 9px; color: #9CA3AF; text-align: center; }
    </style>
</head>
<body>
    @php
        $tab = (string)($tab ?? 'pendapatan');
        $generatedAt = $generatedAt ?? now();
        $rows = $rows ?? [];
        $from = (string)($from ?? '');
        $to = (string)($to ?? '');
        $periodLabel = (string)($periodLabel ?? '');
    @endphp

    <div class="header">
        <div class="title">{{ $title ?? 'Report Course' }}</div>
        @if(($subtitle ?? '') !== '')
            <div class="subtitle">{{ $subtitle }}</div>
        @endif
    </div>

    <div class="meta">
        <table>
            <tr>
                <td class="label">Tab</td>
                <td>{{ ucfirst($tab) }}</td>
                <td class="label">Periode</td>
                <td>{{ $periodLabel !== '' ? $periodLabel : '-' }}</td>
            </tr>
            <tr>
                <td class="label">Rentang</td>
                <td colspan="3">{{ ($from !== '' && $to !== '') ? ($from . ' s/d ' . $to) : '-' }}</td>
            </tr>
        </table>
    </div>

    @if($tab === 'pertumbuhan')
        <table class="report">
            <thead>
                <tr>
                    <th style="width:26%">Nama Course</th>
                    <th style="width:10%">Level</th>
                    <th style="width:10%" class="text-right">Total View</th>
                    <th style="width:14%" class="text-right">Avg Watch (min)</th>
                    <th style="width:16%" class="text-right">Completion Rate</th>
                    <th style="width:14%" class="text-right">Komentar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $r)
                    <tr>
                        <td>{{ $r['course_name'] ?? '-' }}</td>
                        <td>{{ $r['course_level'] ?? '-' }}</td>
                        <td class="text-right">{{ $r['total_views'] ?? 0 }}</td>
                        <td class="text-right">{{ $r['avg_watch_minutes'] ?? 0 }}</td>
                        <td class="text-right">{{ ($r['completion_rate'] ?? 0) }}%</td>
                        <td class="text-right">{{ $r['comments_count'] ?? 0 }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted" style="text-align:center">Belum ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <table class="report">
            <thead>
                <tr>
                    <th style="width:28%">Nama Course</th>
                    <th style="width:12%">Tanggal</th>
                    <th style="width:10%" class="text-right">Peserta</th>
                    <th style="width:14%" class="text-right">Harga</th>
                    <th style="width:18%" class="text-right">Pendapatan</th>
                    <th style="width:18%" class="text-right">Pengeluaran</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $r)
                    <tr>
                        <td>{{ $r['course_name'] ?? '-' }}</td>
                        <td>{{ $r['last_paid_at'] ?? '-' }}</td>
                        <td class="text-right">{{ (int)($r['participants_count'] ?? 0) }}</td>
                        <td class="text-right">{{ number_format((float)($r['course_price'] ?? 0), 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format((float)($r['revenue_total'] ?? 0), 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format((float)($r['expense_total'] ?? 0), 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted" style="text-align:center">Belum ada transaksi course pada rentang tanggal ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <div class="footer">
        Dicetak otomatis pada {{ $generatedAt instanceof \Carbon\Carbon ? $generatedAt->format('d/m/Y H:i:s') : (string)$generatedAt }}
    </div>
</body>
</html>
