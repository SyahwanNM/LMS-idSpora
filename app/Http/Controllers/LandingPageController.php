<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Event;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        // Get 4 latest courses or best rated courses
        $featuredCourses = Course::with(['category', 'modules'])
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        // Ambil 4 event aktif (belum selesai). Tetap urutkan berdasarkan terbaru dibuat.
        // Menggunakan scope active agar event yang sudah selesai otomatis tidak tampil.
        $upcomingEvents = Event::active()
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        // Ambil sampai 3 event untuk hero carousel (gambar poster event)
        $carouselEvents = Event::active()
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        // View membutuhkan variabel $upcomingEvents untuk menampilkan daftar event.
        return view('landing-page', compact('featuredCourses', 'upcomingEvents', 'carouselEvents'));
    }
}