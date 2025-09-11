<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event; // kalau sudah ada model Event

class EventController extends Controller
{
    // Halaman index (list semua event)
    public function index(Request $request)
    {
        // kalau ada model Event, bisa query dari DB
        // $events = Event::latest()->paginate(9);

        // sementara pakai dummy data (sampai DB siap)
        $events = collect(range(1,9))->map(fn($i)=> (object)[
            'title' => 'AI for Lectures',
            'date' => '04 September 2025',
            'city' => 'Bandung',
            'time' => '09:00 WIB',
            'quota' => 50,
            'price_original' => 100000,
            'price_sale' => 75000,
            'image_url' => 'https://via.placeholder.com/400x300.png?text=Event+Poster',
            'slug' => 'ai-for-lectures-'.$i,
        ]);

        return view('events.index', compact('events'));
    }

    // Halaman detail event
    public function show($slug)
    {
        // kalau ada model Event:
        // $event = Event::where('slug',$slug)->firstOrFail();

        // dummy untuk contoh
        $event = (object)[
            'title' => 'AI for Lectures',
            'date' => '04 September 2025',
            'city' => 'Bandung',
            'time' => '09:00 WIB',
            'quota' => 50,
            'price_original' => 100000,
            'price_sale' => 75000,
            'image_url' => 'https://via.placeholder.com/800x500.png?text=Event+Poster',
            'slug' => $slug,
            'description' => 'Seminar AI khusus dosen untuk membahas integrasi AI di dunia pendidikan.',
        ];

        return view('events.show', compact('event'));
    }
}
