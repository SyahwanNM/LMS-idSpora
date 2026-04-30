<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\ManualPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventGrowthReportController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'month'          => 'nullable|date_format:Y-m',
            'search'         => 'nullable|string|max:255',
            'manage_action'  => 'nullable|in:manage,create',
            'price_type'     => 'nullable|in:free,paid',
        ]);

        // --- Resolve selected month (default: current month) ---
        $monthRaw = trim((string) $request->query('month', ''));
        if ($monthRaw && preg_match('/^(\d{4})-(\d{2})$/', $monthRaw, $m)) {
            $selectedDate = Carbon::create((int) $m[1], (int) $m[2], 1);
        } else {
            $selectedDate = Carbon::now()->startOfMonth();
        }

        $yearStart  = $selectedDate->copy()->startOfMonth()->startOfDay();
        $yearEnd    = $selectedDate->copy()->endOfMonth()->endOfDay();
        $daysInMonth = (int) $selectedDate->daysInMonth;

        // --- Base event query for the selected month ---
        $query = Event::query()
            ->whereYear('event_date', $selectedDate->year)
            ->whereMonth('event_date', $selectedDate->month)
            ->withCount('registrations')
            ->withAvg('feedbacks as event_rating_avg', 'rating')
            ->withAvg('feedbacks as speaker_rating_avg', 'speaker_rating')
            ->orderBy('event_date', 'desc');

        // --- Filters ---
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('speaker', 'like', "%{$search}%");
            });
        }

        if ($manageAction = $request->query('manage_action')) {
            $query->where('manage_action', $manageAction);
        }

        if ($priceType = $request->query('price_type')) {
            if ($priceType === 'free') {
                $query->whereRaw('COALESCE(price, 0) <= 0');
            } else {
                $query->whereRaw('COALESCE(price, 0) > 0');
            }
        }

        $events = $query->get();

        // --- Build growth rows (per event) ---
        $rows = $events->map(function ($e) {
            $manageAction = strtolower(trim((string) ($e->manage_action ?? 'create')));
            $isFree       = (float) ($e->price ?? 0) <= 0;
            $eventRating  = $e->event_rating_avg;
            $speakerRating = $e->speaker_rating_avg;

            return [
                'id'             => $e->id,
                'name'           => $e->title,
                'date'           => optional($e->event_date)->format('Y-m-d'),
                'participants'   => (int) $e->registrations_count,
                'speaker'        => $e->speaker,
                'manage_action'  => $manageAction ?: 'create',
                'price'          => (float) ($e->price ?? 0),
                'is_free'        => $isFree,
                'event_rating'   => is_null($eventRating)   ? null : round((float) $eventRating, 1),
                'speaker_rating' => is_null($speakerRating) ? null : round((float) $speakerRating, 1),
            ];
        });

        // --- Summary stats ---
        $totalFreeParticipants  = $rows->where('is_free', true)->sum('participants');
        $totalPaidParticipants  = $rows->where('is_free', false)->sum('participants');
        $totalManageEvents      = $rows->where('manage_action', 'manage')->count();
        $totalCreateEvents      = $rows->where('manage_action', 'create')->count();
        $totalParticipants      = $rows->sum('participants');

        // --- Chart: participants per day (free vs paid) ---
        $freeByDay = [];
        $paidByDay = [];
        foreach ($rows as $row) {
            if (empty($row['date'])) continue;
            try {
                $day = (int) Carbon::parse($row['date'])->day;
            } catch (\Throwable $e) {
                continue;
            }
            if ($row['is_free']) {
                $freeByDay[$day] = ($freeByDay[$day] ?? 0) + $row['participants'];
            } else {
                $paidByDay[$day] = ($paidByDay[$day] ?? 0) + $row['participants'];
            }
        }

        $chartFree = [];
        $chartPaid = [];
        $chartLabels = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $chartLabels[] = $d;
            $chartFree[]   = (int) ($freeByDay[$d] ?? 0);
            $chartPaid[]   = (int) ($paidByDay[$d] ?? 0);
        }

        // --- Event composition (free/paid/manage/create) for the selected month ---
        $composition = [
            'free'   => $rows->where('is_free', true)->count(),
            'paid'   => $rows->where('is_free', false)->count(),
            'manage' => $totalManageEvents,
            'create' => $totalCreateEvents,
        ];

        return response()->json([
            'status'  => 'success',
            'message' => 'Growth report event',
            'meta' => [
                'month'    => $selectedDate->format('Y-m'),
                'filters'  => [
                    'search'        => $request->query('search'),
                    'manage_action' => $request->query('manage_action'),
                    'price_type'    => $request->query('price_type'),
                ],
            ],
            'summary' => [
                'total_events'           => $rows->count(),
                'total_participants'     => $totalParticipants,
                'total_free_participants'=> $totalFreeParticipants,
                'total_paid_participants'=> $totalPaidParticipants,
                'total_manage_events'    => $totalManageEvents,
                'total_create_events'    => $totalCreateEvents,
            ],
            'chart' => [
                'labels' => $chartLabels,
                'series' => [
                    'free_participants' => $chartFree,
                    'paid_participants' => $chartPaid,
                ],
                'composition' => $composition,
            ],
            'rows' => $rows->values(),
        ]);
    }
}
