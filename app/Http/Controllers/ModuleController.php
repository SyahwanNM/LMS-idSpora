<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModuleController extends Controller
{
    public function index(Course $course)
    {
        $modules = $course->modules;
        return view('admin.modules.index', compact('course', 'modules'));
    }

    public function create(Course $course)
    {
        return view('admin.modules.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:video,pdf,quiz',
            'content_file' => 'required|file|mimes:mp4,avi,mov,pdf|max:102400', // 100MB max
            'is_free' => 'boolean',
            'preview_pages' => 'integer|min:0',
            'duration' => 'required|integer|min:0',
        ]);

        // Handle file upload
        $file = $request->file('content_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('modules', $fileName, 'public');

        // Get next order number
        $nextOrder = $course->modules()->max('order_no') + 1;

        // Create module
        CourseModule::create([
            'course_id' => $course->id,
            'order_no' => $nextOrder,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'content_url' => $filePath,
            'is_free' => $request->has('is_free'),
            'preview_pages' => $request->preview_pages ?? 0,
            'duration' => $request->duration,
        ]);

        return redirect()->route('admin.courses.modules.index', $course)
            ->with('success', 'Module berhasil ditambahkan!');
    }

    public function show(Course $course, CourseModule $module)
    {
        return view('admin.modules.show', compact('course', 'module'));
    }

    public function edit(Course $course, CourseModule $module)
    {
        return view('admin.modules.edit', compact('course', 'module'));
    }

    public function update(Request $request, Course $course, CourseModule $module)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:video,pdf,quiz',
            'content_file' => 'nullable|file|mimes:mp4,avi,mov,pdf|max:102400',
            'is_free' => 'boolean',
            'preview_pages' => 'integer|min:0',
            'duration' => 'required|integer|min:0',
            'order_no' => 'required|integer|min:1',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'is_free' => $request->has('is_free'),
            'preview_pages' => $request->preview_pages ?? 0,
            'duration' => $request->duration,
            'order_no' => $request->order_no,
        ];

        // Handle file upload if new file is provided
        if ($request->hasFile('content_file')) {
            // Delete old file
            if ($module->content_url && Storage::disk('public')->exists($module->content_url)) {
                Storage::disk('public')->delete($module->content_url);
            }

            $file = $request->file('content_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('modules', $fileName, 'public');
            $data['content_url'] = $filePath;
        }

        $module->update($data);

        return redirect()->route('admin.courses.modules.index', $course)
            ->with('success', 'Module berhasil diperbarui!');
    }

    public function destroy(Course $course, CourseModule $module)
    {
        // Delete file
        if ($module->content_url && Storage::disk('public')->exists($module->content_url)) {
            Storage::disk('public')->delete($module->content_url);
        }

        $module->delete();

        return redirect()->route('admin.courses.modules.index', $course)
            ->with('success', 'Module berhasil dihapus!');
    }

    public function reorder(Request $request, Course $course)
    {
        $request->validate([
            'modules' => 'required|array',
            'modules.*.id' => 'required|exists:course_module,id',
            'modules.*.order_no' => 'required|integer|min:1',
        ]);

        foreach ($request->modules as $moduleData) {
            CourseModule::where('id', $moduleData['id'])
                ->where('course_id', $course->id)
                ->update(['order_no' => $moduleData['order_no']]);
        }

        return response()->json(['success' => true]);
    }
}