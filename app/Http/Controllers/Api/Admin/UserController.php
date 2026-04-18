<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * List users (admin).
     * Optional query: per_page, search, role.
     */
    public function index(Request $request)
    {
        $perPage = max(1, min((int) $request->query('per_page', 10), 100));

        $query = User::query()->orderByDesc('id');

        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $role = trim((string) $request->query('role', ''));
        if ($role !== '') {
            $query->where('role', $role);
        }

        $users = $query->paginate($perPage);

        // Ensure sensitive fields are not exposed even if model changes.
        $users->getCollection()->transform(function (User $u) {
            return [
                'id' => (int) $u->id,
                'name' => (string) ($u->name ?? ''),
                'email' => (string) ($u->email ?? ''),
                'role' => (string) ($u->role ?? ''),
                'referral_code' => $u->referral_code,
                'wallet_balance' => (float) ($u->wallet_balance ?? 0),
                'created_at' => $u->created_at,
                'updated_at' => $u->updated_at,
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar user (admin)',
            'data' => $users,
        ]);
    }

    /**
     * Show user detail (admin).
     */
    public function show(User $user)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Detail user (admin)',
            'data' => [
                'id' => (int) $user->id,
                'name' => (string) ($user->name ?? ''),
                'email' => (string) ($user->email ?? ''),
                'role' => (string) ($user->role ?? ''),
                'phone' => $user->phone,
                'website' => $user->website,
                'bio' => $user->bio,
                'points' => (int) ($user->points ?? 0),
                'badge' => $user->badge,
                'profession' => $user->profession,
                'institution' => $user->institution,
                'last_event_date' => $user->last_event_date,
                'referral_code' => $user->referral_code,
                'wallet_balance' => (float) ($user->wallet_balance ?? 0),
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ]);
    }
}
