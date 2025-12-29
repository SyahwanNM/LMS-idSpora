<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('category', 'modules')->paginate(10);
        return view('admin.courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        $course->load('category', 'modules');
        return view('admin.courses.show', compact('course'));
    }

    public function create()
    {
        $categories = Category::all();
        // Use the Tailwind-based Manage Courses create view
        return view('admin.courses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'price' => 'required|integer|min:0',
            'duration' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle image upload
        $imagePath = $request->file('image')->store('courses', 'public');

        // Create course
        Course::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'level' => $request->level,
            'price' => $request->price,
            'duration' => $request->duration,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully!');
    }

    public function edit(Course $course)
    {
        $categories = Category::all();
        return view('admin.courses.edit', compact('course', 'categories'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'price' => 'required|integer|min:0',
            'duration' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = [
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'level' => $request->level,
            'price' => $request->price,
            'duration' => $request->duration,
        ];

        // Handle image upload if new image is provided
        if ($request->hasFile('image')) {
            // Delete old image
            if ($course->image && \Storage::disk('public')->exists($course->image)) {
                \Storage::disk('public')->delete($course->image);
            }

            $imagePath = $request->file('image')->store('courses', 'public');
            $data['image'] = $imagePath;
        }

        $course->update($data);

        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully!');
    }

    public function destroy(Course $course)
    {
        // Delete image
        if ($course->image && \Storage::disk('public')->exists($course->image)) {
            \Storage::disk('public')->delete($course->image);
        }

        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully!');
    }
}