<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Carousel;
use Illuminate\Http\Request;

class PublicCourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query()->with('category');

        if($search = $request->get('search')){
            $query->where(function($q) use ($search){
                $q->where('name','like',"%$search%")
                  ->orWhere('description','like',"%$search%")
                  ->orWhereHas('category', function($c) use ($search){
                      $c->where('name','like',"%$search%");
                  });
            });
        }

        if($level = $request->get('level')){
            $query->where('level',$level);
        }

        if($request->boolean('free')){
            $query->where('price',0);
        }

        if($sort = $request->get('price')){
            if(in_array($sort,['asc','desc'])){
                $query->orderBy('price',$sort);
            }
        } else {
            $query->latest();
        }

        $courses = $query->paginate(12)->withQueryString();
        // Sementara: pakai list yang sama sebagai featured agar variable tidak undefined.
        // Bisa diganti logika berbeda (misal: where('is_featured',true)->take(8)) nanti.
        $featuredCourses = $courses;

        // Get carousel images for course page
        $courseCarousels = Carousel::active()
            ->forLocation('course')
            ->orderBy('order')
            ->get();

        return view('course.index', compact('courses','featuredCourses', 'courseCarousels'));
    }
}
