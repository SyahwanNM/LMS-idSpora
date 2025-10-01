<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::latest()->paginate(10);
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'speaker' => 'required|string|max:255',
            'description' => 'required',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Simpan gambar ke storage
        $imagePath = $request->file('image')->store('events', 'public');

        // Simpan data ke database
        Event::create([
            'title' => $request->title,
            'speaker' => $request->speaker,
            'description' => $request->description,
            'location' => $request->location,
            'price' => $request->price,
            'discount_percentage' => $request->discount_percentage ?? 0,
            'event_date' => $request->event_date,
            'event_time' => $request->event_time,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil ditambahkan!');
    }

    public function show(Event $event)
    {
        return view('admin.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'speaker' => 'required|string|max:255',
            'description' => 'required',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only([
            'title', 'speaker', 'description', 'location', 'price', 'discount_percentage', 'event_date', 'event_time'
        ]);

        // Jika ada gambar baru, simpan ke storage
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($data);

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil diupdate!');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dihapus!');
    }

    // Public registration (AJAX)
    public function register(Request $request, Event $event)
    {
        $request->validate([]); // no fields yet, just user context
        $user = $request->user();
        if(!$user){
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $existing = EventRegistration::where('user_id',$user->id)->where('event_id',$event->id)->first();
        if($existing){
            return response()->json(['status' => 'already', 'message' => 'Sudah terdaftar', 'event_title' => $event->title]);
        }
        $reg = EventRegistration::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'active',
            'registration_code' => 'EVT-'.strtoupper(uniqid())
        ]);
        return response()->json([
            'status' => 'ok',
            'message' => 'Berhasil daftar event',
            'event_title' => $event->title,
            'button_text' => 'Anda Terdaftar'
        ]);
    }
}