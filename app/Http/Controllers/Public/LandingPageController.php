<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

use App\Models\Course;
use App\Models\Event;
use App\Models\Carousel;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        // Get 4 latest courses or best rated courses
        $featuredCourses = Course::with(['category', 'modules'])
            ->withAvg('reviews as rating_avg', 'rating')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        // Ambil 4 event aktif (belum selesai). Tetap urutkan berdasarkan terbaru dibuat.
        // Menggunakan scope active agar event yang sudah selesai otomatis tidak tampil.
        $upcomingEvents = Event::active()
            ->where('is_published', true)
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        // Ambil sampai 3 event untuk hero carousel (gambar poster event) - fallback jika tidak ada carousel
        $carouselEvents = Event::active()
            ->where('is_published', true)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        // Get carousel images for landing page
        $landingCarousels = Carousel::active()
            ->forLocation('landing')
            ->orderBy('order')
            ->get();

        // View membutuhkan variabel $upcomingEvents untuk menampilkan daftar event.
        return view('public.landing-page', compact('featuredCourses', 'upcomingEvents', 'carouselEvents', 'landingCarousels'));
    }
}