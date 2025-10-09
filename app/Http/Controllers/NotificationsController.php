<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserNotification;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        $uid = Auth::id();
        if(!$uid){ return response()->json(['items'=>[], 'unread'=>0]); }
        $query = UserNotification::where('user_id', $uid)
            ->where(function($q){ $q->whereNull('expires_at')->orWhere('expires_at','>', now()); })
            ->latest();
        $items = $query->limit(15)->get()->map(function($n){
            return [
                'id' => $n->id,
                'title' => $n->title,
                'message' => $n->message,
                'type' => $n->type,
                'time_ago' => optional($n->created_at)?->diffForHumans(),
                'url' => data_get($n->data, 'url'),
                'read_at' => optional($n->read_at)?->toIso8601String(),
            ];
        });
        $unread = UserNotification::where('user_id',$uid)
            ->whereNull('read_at')
            ->where(function($q){ $q->whereNull('expires_at')->orWhere('expires_at','>', now()); })
            ->count();
        return response()->json(['items'=>$items,'unread'=>$unread]);
    }

    public function markAllRead()
    {
        $uid = Auth::id();
        if(!$uid){ return response()->json(['ok'=>true]); }
        UserNotification::where('user_id',$uid)->whereNull('read_at')->update(['read_at'=>now()]);
        return response()->json(['ok'=>true]);
    }
}
