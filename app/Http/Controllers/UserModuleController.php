<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserModuleController extends Controller
{
    public function index(Course $course)
    {
        // Check if user is enrolled in this course
        // For now, we'll allow all authenticated users to view modules
        // You can add enrollment check later
        
        $modules = $course->modules;
        return view('user.modules.index', compact('course', 'modules'));
    }

    public function show(Course $course, CourseModule $module)
    {
        // Check if user is enrolled in this course
        // For now, we'll allow all authenticated users to view modules
        
        $nextModule = $course->modules()
            ->where('order_no', '>', $module->order_no)
            ->orderBy('order_no')
            ->first();
            
        $prevModule = $course->modules()
            ->where('order_no', '<', $module->order_no)
            ->orderBy('order_no', 'desc')
            ->first();

        return view('user.modules.show', compact('course', 'module', 'nextModule', 'prevModule'));
    }

    public function download(Course $course, CourseModule $module)
    {
        // Check if user is enrolled in this course
        // For now, we'll allow all authenticated users to download
        
        if (!Storage::disk('public')->exists($module->content_url)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('public')->download($module->content_url);
    }

    public function stream(Course $course, CourseModule $module)
    {
        // Check if user is enrolled in this course
        // For now, we'll allow all authenticated users to stream
        
        if (!Storage::disk('public')->exists($module->content_url)) {
            abort(404, 'File tidak ditemukan');
        }

        $filePath = Storage::disk('public')->path($module->content_url);
        $fileSize = filesize($filePath);
        $fileName = basename($module->content_url);

        return response()->file($filePath, [
            'Content-Type' => $module->isVideo() ? 'video/mp4' : 'application/pdf',
            'Content-Length' => $fileSize,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }
}