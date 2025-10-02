<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class PublicEventController extends Controller
{
	public function index(Request $request)
	{
		$query = Event::query();

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

	public function show(Event $event, Request $request)
	{
		// Tandai sudah terdaftar (reuse logic ringkas)
		$isRegistered = false;
		if($request->user()){
			$isRegistered = $request->user()->eventRegistrations()->where('event_id',$event->id)->exists();
		}
		$event->is_registered = $isRegistered;
		// Jika sudah terdaftar: tetap tampilkan halaman detail (tidak redirect ke payment)
		return view('events.detail', compact('event'));
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
