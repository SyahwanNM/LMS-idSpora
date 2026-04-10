<?php

namespace App\Http\Controllers\Admin;

use App\Models\CourseTemplate;
use App\Models\CourseTemplateModule;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CourseTemplateAdminController extends Controller
{
    public function index()
    {
        $templates = CourseTemplate::query()
            ->with('category:id,name', 'creator:id,name')
            ->withCount(['modules', 'courses'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.templates.index', compact('templates'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.templates.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'level' => 'required|in:beginner,intermediate,advanced',
            'description' => 'nullable|string',
            'modules' => 'nullable|array|min:1',
            'modules.*.title' => 'required_with:modules|string|max:255',
            'modules.*.description' => 'nullable|string',
            'modules.*.type' => 'required_with:modules|in:video,pdf,quiz',
            'modules.*.duration' => 'nullable|integer|min:0',
            'modules.*.is_required' => 'nullable|boolean',
        ]);

        $template = CourseTemplate::create([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'] ?? null,
            'level' => $validated['level'],
            'version' => 1,
            'status' => 'active',
            'created_by' => Auth::id(),
            'description' => $validated['description'] ?? null,
        ]);

        if (!empty($validated['modules'])) {
            $this->syncModules($template, $validated['modules']);
        }

        return redirect()->route('admin.templates.show', $template)->with('success', 'Template berhasil dibuat!');
    }

    public function show(CourseTemplate $template)
    {
        $template->load([
            'category:id,name',
            'creator:id,name',
            'modules' => function ($q) {
                $q->orderBy('order_no');
            },
        ])->loadCount(['modules', 'courses']);

        return view('admin.templates.show', compact('template'));
    }

    public function edit(CourseTemplate $template)
    {
        $template->load('modules');
        $categories = Category::all();

        return view('admin.templates.edit', compact('template', 'categories'));
    }

    public function update(Request $request, CourseTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'level' => 'required|in:beginner,intermediate,advanced',
            'description' => 'nullable|string',
            'create_new_version' => 'nullable|boolean',
            'modules' => 'nullable|array|min:0',
            'modules.*.title' => 'required_with:modules|string|max:255',
            'modules.*.description' => 'nullable|string',
            'modules.*.type' => 'required_with:modules|in:video,pdf,quiz',
            'modules.*.duration' => 'nullable|integer|min:0',
            'modules.*.is_required' => 'nullable|boolean',
        ]);

        $createNewVersion = (bool) ($validated['create_new_version'] ?? false);

        if ($createNewVersion) {
            // Buat versi baru
            $baseName = trim((string) $validated['name']);
            $nextVersion = ((int) CourseTemplate::query()
                ->where('name', $baseName)
                ->max('version')) + 1;

            $newTemplate = CourseTemplate::create([
                'name' => $baseName,
                'category_id' => $validated['category_id'] ?? $template->category_id,
                'level' => $validated['level'],
                'version' => $nextVersion,
                'status' => 'active',
                'created_by' => Auth::id(),
                'description' => $validated['description'] ?? $template->description,
            ]);

            if (!empty($validated['modules'])) {
                $this->syncModules($newTemplate, $validated['modules']);
            }

            return redirect()->route('admin.templates.show', $newTemplate)
                ->with('success', 'Template versi baru berhasil dibuat!');
        }

        // Update template saat ini
        $template->update([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'level' => $validated['level'],
            'description' => $validated['description'],
        ]);

        if (array_key_exists('modules', $validated)) {
            $this->syncModules($template, $validated['modules']);
        }

        return redirect()->route('admin.templates.show', $template)
            ->with('success', 'Template berhasil diupdate!');
    }

    public function destroy(CourseTemplate $template)
    {
        $template->update(['status' => 'archive']);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template berhasil diarsipkan!');
    }

    private function syncModules(CourseTemplate $template, array $modules): void
    {
        $rows = collect($modules)
            ->values()
            ->map(function ($module, $index) {
                $orderNo = (int) ($module['order_no'] ?? ($index + 1));
                return [
                    'order_no' => max(1, $orderNo),
                    'title' => (string) ($module['title'] ?? ('Module ' . ($index + 1))),
                    'description' => $module['description'] ?? null,
                    'type' => (string) ($module['type'] ?? 'video'),
                    'is_required' => (bool) ($module['is_required'] ?? true),
                    'duration' => (int) ($module['duration'] ?? 0),
                ];
            })
            ->sortBy('order_no')
            ->values()
            ->map(function ($row, $index) {
                $row['order_no'] = $index + 1;
                return $row;
            })
            ->all();

        $template->modules()->delete();

        if (!empty($rows)) {
            $template->modules()->createMany($rows);
        }
    }
}
