<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseTemplateModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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
            'content_file' => 'required|file|mimes:mp4,avi,mov,webm,ogg,mkv,pdf|max:102400', // 100MB max
            'is_free' => 'boolean',
            'preview_pages' => 'integer|min:0',
            'duration' => 'required|integer|min:0',
        ]);

        $contentFile = $request->file('content_file');
        $isVideo = $request->input('type') === 'video';
        $videoBounds = $this->resolveVideoDurationBounds($course);

        // Handle file upload
        $fileName = time() . '_' . $contentFile->getClientOriginalName();
        $filePath = $contentFile->storeAs('modules', $fileName, 'public');

        if ($isVideo && !empty($videoBounds)) {
            $durationSeconds = $this->probeVideoDurationSeconds(Storage::disk('public')->path($filePath));
            if (is_int($durationSeconds) && $durationSeconds > 0) {
                $durationMinutes = (int) ceil($durationSeconds / 60);

                if (($videoBounds['min'] ?? null) !== null && $durationMinutes < $videoBounds['min']) {
                    Storage::disk('public')->delete($filePath);
                    throw ValidationException::withMessages([
                        'content_file' => 'Durasi video minimal ' . $videoBounds['min'] . ' menit untuk template course ini.',
                    ]);
                }

                if (($videoBounds['max'] ?? null) !== null && $durationMinutes > $videoBounds['max']) {
                    Storage::disk('public')->delete($filePath);
                    throw ValidationException::withMessages([
                        'content_file' => 'Durasi video maksimal ' . $videoBounds['max'] . ' menit untuk template course ini.',
                    ]);
                }
            }
        }

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
            'content_file' => 'nullable|file|mimes:mp4,avi,mov,webm,ogg,mkv,pdf|max:102400',
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

            if ($request->input('type') === 'video') {
                $videoBounds = $this->resolveVideoDurationBounds($course);
                if (!empty($videoBounds)) {
                    $durationSeconds = $this->probeVideoDurationSeconds(Storage::disk('public')->path($filePath));
                    if (is_int($durationSeconds) && $durationSeconds > 0) {
                        $durationMinutes = (int) ceil($durationSeconds / 60);

                        if (($videoBounds['min'] ?? null) !== null && $durationMinutes < $videoBounds['min']) {
                            Storage::disk('public')->delete($filePath);
                            throw ValidationException::withMessages([
                                'content_file' => 'Durasi video minimal ' . $videoBounds['min'] . ' menit untuk template course ini.',
                            ]);
                        }

                        if (($videoBounds['max'] ?? null) !== null && $durationMinutes > $videoBounds['max']) {
                            Storage::disk('public')->delete($filePath);
                            throw ValidationException::withMessages([
                                'content_file' => 'Durasi video maksimal ' . $videoBounds['max'] . ' menit untuk template course ini.',
                            ]);
                        }
                    }
                }
            }
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

    private function resolveVideoDurationBounds(Course $course): array
    {
        $template = $course->template()->with('modules')->first();
        if (!$template) {
            return [];
        }

        $videoDurations = $template->modules
            ->filter(fn(CourseTemplateModule $module) => (string) $module->type === 'video' && (int) $module->duration > 0)
            ->pluck('duration')
            ->map(fn($duration) => (int) $duration)
            ->values();

        if ($videoDurations->isEmpty()) {
            return [];
        }

        return [
            'min' => $videoDurations->min(),
            'max' => $videoDurations->max(),
        ];
    }

    private function probeVideoDurationSeconds(string $absolutePath): ?int
    {
        $configured = config('media.ffprobe_path');
        $arg = escapeshellarg($absolutePath);
        $bins = [];

        if (is_string($configured) && $configured !== '') {
            $bins[] = $configured;
        }

        $bins = array_merge($bins, [
            'ffprobe',
            'C:\\ffmpeg\\bin\\ffprobe.exe',
            'C:\\Program Files\\ffmpeg\\bin\\ffprobe.exe',
            'C:\\laragon\\bin\\ffmpeg\\ffprobe.exe',
            '/usr/bin/ffprobe',
            '/usr/local/bin/ffprobe',
        ]);

        $bins = array_values(array_unique($bins));
        foreach ($bins as $bin) {
            $cmd = sprintf('%s -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 %s', $bin, $arg);
            try {
                $out = @shell_exec($cmd);
                if ($out !== null) {
                    $out = trim($out);
                    if ($out !== '') {
                        $seconds = (int) round((float) $out);
                        if ($seconds > 0) {
                            return $seconds;
                        }
                    }
                }
            } catch (\Throwable $e) {
                // try next
            }
        }

        return null;
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