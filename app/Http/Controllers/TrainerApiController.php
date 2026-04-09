<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrainerApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $trainers = User::query()
            ->where('role', 'trainer')
            // Return trainer profile (data diri) only; exclude sensitive/irrelevant fields.
            ->select([
                'id',
                'name',
                'email',
                'phone',
                'website',
                'bio',
                'profession',
                'institution',
                'avatar',
                'created_at',
                'updated_at',
            ])
            ->orderBy('name')
            ->get()
            ->map(function (User $trainer) {
                return [
                    'id' => $trainer->id,
                    'name' => $trainer->name,
                    'email' => $trainer->email,
                    'phone' => $trainer->phone,
                    'formatted_phone' => $trainer->formatted_phone,
                    'website' => $trainer->website,
                    'bio' => $trainer->bio,
                    'profession' => $trainer->profession,
                    'institution' => $trainer->institution,
                    'avatar' => $trainer->avatar,
                    'avatar_url' => $trainer->avatar_url,
                    'created_at' => optional($trainer->created_at)->toISOString(),
                    'updated_at' => optional($trainer->updated_at)->toISOString(),
                ];
            });

        return response()->json([
            'data' => $trainers,
        ]);
    }
}
