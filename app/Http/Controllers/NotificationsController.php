<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\UserNotification;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        $uid = Auth::id();
        if(!$uid){
            // Return an empty JSON payload for unauthenticated AJAX calls
            return response()->json(['items'=>[], 'unread'=>0]);
        }

        try {
            $supportsReadAt = Schema::hasColumn('user_notifications', 'read_at');
            $supportsCreatedAt = Schema::hasColumn('user_notifications', 'created_at');

            $query = UserNotification::where('user_id', $uid);
            // Order by created_at desc if exists, else by id desc
            if ($supportsCreatedAt) {
                $query->orderByDesc('created_at');
            } else {
                $query->orderByDesc('id');
            }

            $items = $query->limit(15)->get()->map(function($n) use ($supportsReadAt){
                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'type' => $n->type,
                    'time_ago' => optional($n->created_at)?->diffForHumans(),
                    'url' => data_get($n->data, 'url'),
                    'read_at' => $supportsReadAt ? optional($n->read_at)?->toIso8601String() : null,
                ];
            })->values();

            if ($supportsReadAt) {
                $unread = UserNotification::where('user_id',$uid)->whereNull('read_at')->count();
            } else {
                // If no read_at column, we can't compute unread accurately
                $unread = 0;
            }

            return response()->json(['items'=>$items->all(),'unread'=>$unread]);
        } catch (\Throwable $e) {
            // Gracefully degrade if DB table missing or query fails
            \Log::warning('NotificationsController@index failed: '.$e->getMessage());
            return response()->json(['items'=>[], 'unread'=>0]);
        }
    }

    public function markAllRead()
    {
        $uid = Auth::id();
        if(!$uid){ return response()->json(['ok'=>true]); }
        try {
            if (Schema::hasColumn('user_notifications','read_at')) {
                UserNotification::where('user_id',$uid)->whereNull('read_at')->update(['read_at'=>now()]);
            }
            return response()->json(['ok'=>true]);
        } catch (\Throwable $e) {
            \Log::warning('NotificationsController@markAllRead failed: '.$e->getMessage());
            return response()->json(['ok'=>false]);
        }
    }
}
