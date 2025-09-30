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

		return view('event', compact('events', 'locations'));
	}
}
