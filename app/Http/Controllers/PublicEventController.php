<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Carousel;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PublicEventController extends Controller
{
	public function index(Request $request)
	{
		$query = Event::query()
			->withCount([
				'registrations as registrations_count' => function($q){
					$q->select(DB::raw('COUNT(DISTINCT user_id)'))
					  ->where('status','active');
				}
			]);

		// Server-side status filter: upcoming | ongoing | finished | default(not finished)
		$now = now()->format('Y-m-d H:i:s');
		$status = $request->get('status');
		$startExpr = "TIMESTAMP(event_date, COALESCE(event_time,'00:00:00'))";
		$endExpr = "TIMESTAMP(event_date, COALESCE(event_time_end, COALESCE(event_time,'23:59:59')))";
		if ($status === 'finished') {
			$query->whereRaw("$endExpr < ?", [$now]);
		} elseif ($status === 'ongoing') {
			$query->whereRaw("$startExpr <= ? AND $endExpr >= ?", [$now, $now]);
		} elseif ($status === 'upcoming') {
			$query->whereRaw("$startExpr > ?", [$now]);
		}
        // else: show all (upcoming + finished)

		// Search
		if ($search = $request->get('search')) {
			$query->where(function ($q) use ($search) {
				$q->where('title', 'like', "%$search%")
					->orWhere('speaker', 'like', "%$search%")
					->orWhere('location', 'like', "%$search%");
			});
		}

		// Filter: location
		if ($location = $request->get('location')) {
			$query->where('location', $location);
		}

		// Filter: day (weekdays|weekend|today)
		if ($day = $request->get('day')) {
			if ($day === 'today') {
				$query->whereDate('event_date', now()->toDateString());
			} elseif ($day === 'weekdays') {
				// MySQL/MariaDB: DAYOFWEEK() returns 1=Sunday ... 7=Saturday
				$query->whereRaw('DAYOFWEEK(event_date) BETWEEN 2 AND 6');
			} elseif ($day === 'weekend') {
				$query->whereRaw('DAYOFWEEK(event_date) IN (1,7)');
			}
		}

		// Filter: event_type (online|onsite|hybrid)
		if ($type = $request->get('event_type')) {
			if ($type === 'online') {
				$query->whereNotNull('zoom_link')
					->where(function($q){
						$q->whereNull('location')
						  ->orWhere('location', 'like', '%online%');
					});
			} elseif ($type === 'onsite') {
				$query->whereNull('zoom_link')
					->whereNotNull('location');
			} elseif ($type === 'hybrid') {
				$query->whereNotNull('zoom_link')
					->whereNotNull('location');
			}
		}

		// Filter: category (map to jenis)
		if ($category = $request->get('category')) {
			$query->whereRaw('LOWER(jenis) = ?', [mb_strtolower($category)]);
		}

		// Filter: free only
		if ($request->boolean('free')) {
			$query->where('price', 0);
		}

		// Sorting by price
		if ($priceOrder = $request->get('price')) {
			if (in_array($priceOrder, ['asc', 'desc'])) {
				$query->orderBy('price', $priceOrder);
			}
		} else {
			// For finished, show most recently finished first by end time; else newest created
			if ($status === 'finished') {
				$query->orderByRaw("$endExpr DESC");
			} elseif ($status === 'ongoing' || $status === 'upcoming') {
                $query->orderByRaw("$startExpr ASC");
            } else {
                // "Semua Status" or default:
                // Group 1: Active (Upcoming/Ongoing) -> Order 0
                // Group 2: Finished -> Order 1
                // Inside Active: Sort by Start Date ASC (Soonest first)
                // Inside Finished: Sort by End Date DESC (Recently finished first)
                $query->orderByRaw("
                    CASE WHEN $endExpr < '$now' THEN 1 ELSE 0 END ASC,
                    CASE WHEN $endExpr < '$now' THEN $endExpr END DESC,
                    CASE WHEN $endExpr >= '$now' OR event_date IS NULL THEN $startExpr END ASC
                ");
			}
		}

		$events = $query->paginate(12)->withQueryString();
		$locations = Event::select('location')->whereNotNull('location')->distinct()->orderBy('location')->pluck('location');

		// Tandai event yang sudah diregistrasi user login
		if($request->user()){
			$userRegEventIds = $request->user()->eventRegistrations()
                ->where('status', '!=', 'rejected')
                ->pluck('event_id')->toArray();
			$events->getCollection()->transform(function($ev) use ($userRegEventIds){
				$ev->is_registered = in_array($ev->id, $userRegEventIds);
				return $ev;
			});
		}

		// Get carousel images for event page
		$eventCarousels = Carousel::active()
			->forLocation('event')
			->orderBy('order')
			->get();

		return view('event', compact('events', 'locations', 'eventCarousels'));
	}

	public function searchRedirect(Request $request)
	{
		$search = trim((string) $request->get('search', ''));
		if ($search === '') {
			return redirect()->route('events.index');
		}

		// Prefer exact (case-insensitive) match first
		$exact = Event::whereRaw('LOWER(title) = ?', [mb_strtolower($search)])->first();
		if ($exact) {
			return redirect()->route('events.show', $exact);
		}

		// Then try best partial match, prioritize prefix matches
		$likeTerm = "%{$search}%";
		$prefixTerm = "{$search}%";
		$best = Event::query()
			->orderByRaw('CASE WHEN title LIKE ? THEN 0 WHEN title LIKE ? THEN 1 ELSE 2 END', [$prefixTerm, $likeTerm])
			->orderByDesc('created_at')
			->where(function($q) use ($likeTerm){
				$q->where('title','like',$likeTerm)
				  ->orWhere('speaker','like',$likeTerm)
				  ->orWhere('location','like',$likeTerm);
			})
			->first();
		if ($best) {
			return redirect()->route('events.show', $best);
		}

		// Fallback to listing with querystring so user sees filtered results
		return redirect()->route('events.index', ['search' => $search]);
	}

	public function show(Event $event, Request $request)
	{
		// Tandai sudah terdaftar (reuse logic ringkas)
		$isRegistered = false;
		if($request->user()){
			$isRegistered = $request->user()->eventRegistrations()
                ->where('event_id',$event->id)
                ->where('status', '!=', 'rejected')
                ->exists();
		}
		$event->is_registered = $isRegistered;
		// Load feedbacks for display on the event detail page
		$feedbacks = \App\Models\Feedback::with('user')->where('event_id', $event->id)->orderBy('created_at', 'desc')->get();
		// Tampilkan halaman detail menggunakan tampilan "detail-event-registered"
		return view('detail-event-registered', compact('event', 'feedbacks'));
	}

	public function ticket(Event $event, Request $request)
	{
		$user = $request->user();
		if(!$user){
			return redirect()->route('login', ['redirect'=>request()->fullUrl()]);
		}
		$registration = $user->eventRegistrations()->where('event_id',$event->id)->first();
		if(!$registration){
			return redirect()->route('events.show',$event)->with('warning','Anda belum terdaftar pada event ini.');
		}
		return view('events.ticket', [
			'event' => $event,
			'registration' => $registration
		]);
	}
}
