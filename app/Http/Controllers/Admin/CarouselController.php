<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carousel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarouselController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $location = $request->get('location', 'dashboard');
        
        $carousels = Carousel::where('location', $location)
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get();

        $locations = [
            'dashboard' => 'Dashboard',
            'event' => 'Event',
            'course' => 'Course',
            'landing' => 'Landing Page',
        ];

        return view('admin.carousels.index', compact('carousels', 'location', 'locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $location = $request->get('location', 'dashboard');
        
        $locations = [
            'dashboard' => 'Dashboard',
            'event' => 'Event',
            'course' => 'Course',
            'landing' => 'Landing Page',
        ];

        return view('admin.carousels.create', compact('location', 'locations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'location' => 'required|in:dashboard,event,course,landing',
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB
            'link_url' => 'nullable|url|max:500',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Upload gambar
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('carousels', 'public');
            $validated['image_path'] = $imagePath;
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['order'] = $validated['order'] ?? 0;

        Carousel::create($validated);

        return redirect()->route('admin.carousels.index', ['location' => $validated['location']])
            ->with('success', 'Carousel berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Carousel $carousel)
    {
        return view('admin.carousels.show', compact('carousel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Carousel $carousel)
    {
        $locations = [
            'dashboard' => 'Dashboard',
            'event' => 'Event',
            'course' => 'Course',
            'landing' => 'Landing Page',
        ];

        return view('admin.carousels.edit', compact('carousel', 'locations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Carousel $carousel)
    {
        $validated = $request->validate([
            'location' => 'required|in:dashboard,event,course,landing',
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'link_url' => 'nullable|url|max:500',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Upload gambar baru jika ada
        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($carousel->image_path && Storage::disk('public')->exists($carousel->image_path)) {
                Storage::disk('public')->delete($carousel->image_path);
            }
            
            $imagePath = $request->file('image')->store('carousels', 'public');
            $validated['image_path'] = $imagePath;
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['order'] = $validated['order'] ?? $carousel->order;

        $carousel->update($validated);

        return redirect()->route('admin.carousels.index', ['location' => $validated['location']])
            ->with('success', 'Carousel berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Carousel $carousel)
    {
        $location = $carousel->location;
        
        // Hapus gambar dari storage
        if ($carousel->image_path && Storage::disk('public')->exists($carousel->image_path)) {
            Storage::disk('public')->delete($carousel->image_path);
        }

        $carousel->delete();

        return redirect()->route('admin.carousels.index', ['location' => $location])
            ->with('success', 'Carousel berhasil dihapus!');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(Carousel $carousel)
    {
        $carousel->is_active = !$carousel->is_active;
        $carousel->save();

        return response()->json([
            'success' => true,
            'is_active' => $carousel->is_active,
            'message' => $carousel->is_active ? 'Carousel diaktifkan' : 'Carousel dinonaktifkan'
        ]);
    }
}
