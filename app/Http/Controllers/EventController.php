<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $threshold = now()->subHours(6)->format('Y-m-d H:i:s');
        $events = Event::query()
            ->where(function($q) use ($threshold){
                $q->whereNull('event_date')
                  ->orWhereRaw("TIMESTAMP(event_date, COALESCE(event_time,'00:00:00')) >= ?", [$threshold]);
            })
            ->latest()
            ->paginate(10);
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
            'terms_and_conditions' => 'nullable|string',
            'location' => 'required|string|max:255',
            'whatsapp_link' => 'nullable|url|max:255',
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
            'terms_and_conditions' => $request->terms_and_conditions,
            'location' => $request->location,
            'whatsapp_link' => $request->whatsapp_link,
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
            'terms_and_conditions' => 'nullable|string',
            'location' => 'required|string|max:255',
            'whatsapp_link' => 'nullable|url|max:255',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only([
            'title', 'speaker', 'description', 'terms_and_conditions', 'location', 'whatsapp_link', 'price', 'discount_percentage', 'event_date', 'event_time'
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
            return response()->json([
                'status' => 'already',
                'message' => 'Sudah terdaftar',
                'event_title' => $event->title,
                'redirect' => route('events.ticket', $event)
            ]);
        }
        // Hitung final price (sesudah diskon bila ada)
        $finalPrice = $event->hasDiscount() ? $event->discounted_price : $event->price;
        $isFree = (int)$finalPrice === 0;

        if(!$isFree){
            // Event berbayar: jangan langsung daftar; arahkan ke payment
            return response()->json([
                'status' => 'payment_required',
                'message' => 'Pembayaran diperlukan sebelum pendaftaran dikonfirmasi.',
                'redirect' => route('payment', $event)
            ], 200);
        }

        // Event gratis: langsung daftarkan
        $reg = EventRegistration::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'active',
            'registration_code' => 'EVT-'.strtoupper(uniqid())
        ]);
        // Create notification (expires in 14 days)
        try{
            UserNotification::create([
                'user_id' => $user->id,
                'type' => 'event_registration',
                'title' => 'Pendaftaran Event Berhasil',
                'message' => 'Kamu terdaftar di "'.$event->title.'".',
                'data' => ['url' => route('events.show', $event)],
                'expires_at' => now()->addDays(14),
            ]);
        }catch(\Throwable $e){ /* ignore */ }
        return response()->json([
            'status' => 'ok',
            'message' => 'Berhasil daftar event (GRATIS)',
            'event_title' => $event->title,
            'button_text' => 'Anda Terdaftar',
            'redirect' => route('events.ticket', $event)
        ]);
    }
}