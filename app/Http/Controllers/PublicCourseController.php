<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Carousel;
use Illuminate\Http\Request;

class PublicCourseController extends Controller
{
    public function index(Request $request)
    {
        // Only show published courses to users
        $query = Course::query()->with(['category','modules'])
            ->where('status','active');

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
        // Featured: published-only, most recently updated
        $featuredCourses = Course::with(['category','modules'])
            ->where('status','active')
            ->orderBy('updated_at','desc')
            ->take(8)
            ->get();

        // Get carousel images for course page
        $courseCarousels = Carousel::active()
            ->forLocation('course')
            ->orderBy('order')
            ->get();

        return view('course.index', compact('courses','featuredCourses', 'courseCarousels'));
    }
}
