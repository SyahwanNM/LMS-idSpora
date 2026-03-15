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
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $trainers,
        ]);
    }
}
