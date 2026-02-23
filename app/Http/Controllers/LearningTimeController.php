<?php

namespace App\Http\Controllers;

use App\Models\LearningTimeDaily;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LearningTimeController extends Controller
{
    public function heartbeat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'seconds' => 'required|integer|min:1|max:60',
            'course_id' => 'nullable|integer|exists:courses,id',
        ]);

        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['ok' => false, 'message' => 'Unauthenticated'], 401);
        }

        $deltaSeconds = (int) $validated['seconds'];
        $courseId = $validated['course_id'] ?? null;
        $learnedOn = Carbon::now()->toDateString();

        // Keep this endpoint lightweight: aggregate per user+day (+ optional course).
        $attributes = [
            'user_id' => $userId,
            'course_id' => $courseId,
            'learned_on' => $learnedOn,
        ];

        try {
            $row = LearningTimeDaily::query()->firstOrCreate($attributes, ['seconds' => 0]);
            $row->increment('seconds', $deltaSeconds);
        } catch (QueryException $e) {
            // In case of a race on the unique index, retry once.
            $row = LearningTimeDaily::query()->where($attributes)->first();
            if ($row) {
                $row->increment('seconds', $deltaSeconds);
            } else {
                throw $e;
            }
        }

        return response()->json([
            'ok' => true,
        ]);
    }

    public function chart(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'range' => 'nullable|in:week,month',
        ]);

        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['ok' => false, 'message' => 'Unauthenticated'], 401);
        }

        $range = $validated['range'] ?? 'week';

        if ($range === 'month') {
            return $this->chartMonth($userId);
        }

        return $this->chartWeek($userId);
    }

    private function chartWeek(int $userId): JsonResponse
    {
        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->endOfWeek();

        $rows = LearningTimeDaily::query()
            ->select('learned_on', DB::raw('SUM(seconds) as total_seconds'))
            ->where('user_id', $userId)
            ->whereBetween('learned_on', [$start->toDateString(), $end->toDateString()])
            ->groupBy('learned_on')
            ->get();

        $labels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $dataByLabel = array_fill_keys($labels, 0.0);

        // Map 1..7 (Mon..Sun) to Indonesian labels
        $labelByIsoDow = [
            1 => 'Sen',
            2 => 'Sel',
            3 => 'Rab',
            4 => 'Kam',
            5 => 'Jum',
            6 => 'Sab',
            7 => 'Min',
        ];

        foreach ($rows as $row) {
            $date = Carbon::parse($row->learned_on);
            $label = $labelByIsoDow[$date->isoWeekday()] ?? null;
            if (!$label) {
                continue;
            }
            $hours = ((int) $row->total_seconds) / 3600;
            $dataByLabel[$label] = (float) number_format($hours, 1);
        }

        return response()->json([
            'ok' => true,
            'range' => 'week',
            'labels' => $labels,
            'data' => array_values($dataByLabel),
        ]);
    }

    private function chartMonth(int $userId): JsonResponse
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $rows = LearningTimeDaily::query()
            ->select('learned_on', DB::raw('SUM(seconds) as total_seconds'))
            ->where('user_id', $userId)
            ->whereBetween('learned_on', [$start->toDateString(), $end->toDateString()])
            ->groupBy('learned_on')
            ->get();

        // Group by "Pekan" within the month: 1-7 => Pekan 1, 8-14 => Pekan 2, etc.
        $labels = ['Pekan 1', 'Pekan 2', 'Pekan 3', 'Pekan 4', 'Pekan 5'];
        $secondsByWeek = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

        foreach ($rows as $row) {
            $date = Carbon::parse($row->learned_on);
            $weekOfMonth = (int) ceil($date->day / 7);
            $weekOfMonth = max(1, min(5, $weekOfMonth));
            $secondsByWeek[$weekOfMonth] += (int) $row->total_seconds;
        }

        $data = [];
        for ($i = 1; $i <= 5; $i++) {
            $hours = $secondsByWeek[$i] / 3600;
            $data[] = (float) number_format($hours, 1);
        }

        return response()->json([
            'ok' => true,
            'range' => 'month',
            'labels' => $labels,
            'data' => $data,
        ]);
    }
}
