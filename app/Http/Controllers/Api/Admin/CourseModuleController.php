<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseModuleController extends Controller
{
    public function index(Course $course)
    {
        $modules = $course->modules()->orderBy('order_no')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar modul course',
            'data' => $modules,
        ]);
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:video,pdf,quiz',
            'content_file' => 'nullable|file|mimes:pdf,mp4,webm,ogg,avi,mov|max:204800',
            'content_url' => 'nullable|string|max:255',
            'is_free' => 'boolean',
            'preview_pages' => 'integer|min:0',
            'duration' => 'required|integer|min:0',
            'order_no' => 'nullable|integer|min:1',
        ]);

        $type = (string) $validated['type'];

        $contentUrl = null;
        $fileNameMeta = null;
        $mimeMeta = null;
        $sizeMeta = 0;

        if ($request->hasFile('content_file')) {
            $file = $request->file('content_file');
            $contentUrl = $file->store("courses/{$course->id}/modules", 'public');
            $fileNameMeta = $file->getClientOriginalName();
            $mimeMeta = $file->getMimeType();
            $sizeMeta = (int) $file->getSize();
        } elseif ($type === 'quiz') {
            $contentUrl = (string) ($validated['content_url'] ?? 'quiz');
        }

        if (!$contentUrl) {
            return response()->json([
                'status' => 'error',
                'message' => 'content_file wajib untuk tipe video/pdf, atau content_url untuk quiz',
            ], 422);
        }

        $nextOrder = (int) ($validated['order_no'] ?? 0);
        if ($nextOrder <= 0) {
            $nextOrder = ((int) ($course->modules()->max('order_no') ?? 0)) + 1;
        }

        $module = CourseModule::create([
            'course_id' => $course->id,
            'order_no' => $nextOrder,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $type,
            'content_url' => $contentUrl,
            'file_name' => $fileNameMeta,
            'mime_type' => $mimeMeta,
            'file_size' => $sizeMeta,
            'is_free' => (bool) ($request->boolean('is_free')),
            'preview_pages' => (int) ($validated['preview_pages'] ?? 0),
            'duration' => (int) ($validated['duration'] ?? 0),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Modul berhasil dibuat',
            'data' => $module,
        ], 201);
    }

    public function update(Request $request, Course $course, CourseModule $module)
    {
        if ((int) $module->course_id !== (int) $course->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Module tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:video,pdf,quiz',
            'content_file' => 'nullable|file|mimes:pdf,mp4,webm,ogg,avi,mov|max:204800',
            'content_url' => 'nullable|string|max:255',
            'is_free' => 'boolean',
            'preview_pages' => 'integer|min:0',
            'duration' => 'required|integer|min:0',
            'order_no' => 'required|integer|min:1',
        ]);

        $type = (string) $validated['type'];

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $type,
            'is_free' => (bool) ($request->boolean('is_free')),
            'preview_pages' => (int) ($validated['preview_pages'] ?? 0),
            'duration' => (int) ($validated['duration'] ?? 0),
            'order_no' => (int) $validated['order_no'],
        ];

        if ($request->hasFile('content_file')) {
            if ($module->content_url && Storage::disk('public')->exists($module->content_url)) {
                Storage::disk('public')->delete($module->content_url);
            }
            $file = $request->file('content_file');
            $storedPath = $file->store("courses/{$course->id}/modules", 'public');
            $data['content_url'] = $storedPath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['mime_type'] = $file->getMimeType();
            $data['file_size'] = (int) $file->getSize();
        } elseif ($type === 'quiz' && $request->filled('content_url')) {
            $data['content_url'] = (string) $validated['content_url'];
        }

        $module->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Modul berhasil diupdate',
            'data' => $module->fresh(),
        ]);
    }

    public function destroy(Course $course, CourseModule $module)
    {
        if ((int) $module->course_id !== (int) $course->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Module tidak ditemukan',
            ], 404);
        }

        if ($module->content_url && Storage::disk('public')->exists($module->content_url)) {
            Storage::disk('public')->delete($module->content_url);
        }

        $module->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Modul berhasil dihapus',
        ]);
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

        return response()->json([
            'status' => 'success',
            'message' => 'Urutan modul berhasil disimpan',
        ]);
    }
}
