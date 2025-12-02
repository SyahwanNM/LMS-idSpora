<?php

namespace App\Http\Controllers;

use App\Models\Event;
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

		// Sembunyikan event yang sudah berlangsung > 6 jam (antisipasi sebelum cleanup jalan)
		$threshold = now()->subHours(6)->format('Y-m-d H:i:s');
		$query->where(function($q) use ($threshold){
			$q->whereNull('event_date')
			  ->orWhereRaw("TIMESTAMP(event_date, COALESCE(event_time,'00:00:00')) >= ?", [$threshold]);
		});

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
			$query->latest(); // default newest
		}

		$events = $query->paginate(12)->withQueryString();
		$locations = Event::select('location')->whereNotNull('location')->distinct()->orderBy('location')->pluck('location');

		// Tandai event yang sudah diregistrasi user login
		if($request->user()){
			$userRegEventIds = $request->user()->eventRegistrations()->pluck('event_id')->toArray();
			$events->getCollection()->transform(function($ev) use ($userRegEventIds){
				$ev->is_registered = in_array($ev->id, $userRegEventIds);
				return $ev;
			});
		}

		return view('event', compact('events', 'locations'));
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
			$isRegistered = $request->user()->eventRegistrations()->where('event_id',$event->id)->exists();
		}
		$event->is_registered = $isRegistered;
		// Tampilkan halaman detail menggunakan tampilan "detail-event-registered"
		return view('detail-event-registered', compact('event'));
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
