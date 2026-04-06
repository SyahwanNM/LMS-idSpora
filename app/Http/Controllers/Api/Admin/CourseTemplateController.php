<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseTemplateController extends Controller
{
    public function index(Request $request)
    {
        $perPage = max(1, min((int) $request->query('per_page', 10), 100));

        $query = CourseTemplate::query()
            ->with(['category:id,name', 'creator:id,name'])
            ->withCount(['modules', 'courses'])
            ->orderByDesc('created_at');

        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        $status = trim((string) $request->query('status', ''));
        if ($status !== '') {
            $query->where('status', $status);
        }

        $level = trim((string) $request->query('level', ''));
        if ($level !== '') {
            $query->where('level', $level);
        }

        $templates = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar template course (admin)',
            'data' => $templates,
        ]);
    }

    public function show(CourseTemplate $courseTemplate)
    {
        $courseTemplate->load([
            'category:id,name',
            'creator:id,name',
            'modules' => function ($q) {
                $q->orderBy('order_no');
            },
        ])->loadCount(['modules', 'courses']);

        return response()->json([
            'status' => 'success',
            'message' => 'Detail template course (admin)',
            'data' => $courseTemplate,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request, false);

        $template = DB::transaction(function () use ($validated, $request) {
            $baseName = trim((string) $validated['name']);
            $nextVersion = ((int) CourseTemplate::query()
                ->where('name', $baseName)
                ->max('version')) + 1;

            $template = CourseTemplate::create([
                'name' => $baseName,
                'category_id' => $validated['category_id'] ?? null,
                'level' => $validated['level'],
                'version' => $nextVersion,
                'status' => $validated['status'] ?? 'active',
                'created_by' => $request->user()?->id,
                'description' => $validated['description'] ?? null,
            ]);

            $this->syncModules($template, $validated['modules'] ?? [], (string) $validated['level']);

            return $template->fresh(['modules']);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Template course berhasil dibuat',
            'data' => $template,
        ], 201);
    }

    public function update(Request $request, CourseTemplate $courseTemplate)
    {
        $validated = $this->validatePayload($request, true);
        $createNewVersion = (bool) ($validated['create_new_version'] ?? true);

        $result = DB::transaction(function () use ($validated, $request, $courseTemplate, $createNewVersion) {
            if ($createNewVersion) {
                $baseName = trim((string) ($validated['name'] ?? $courseTemplate->name));
                $nextVersion = ((int) CourseTemplate::query()
                    ->where('name', $baseName)
                    ->max('version')) + 1;

                $newTemplate = CourseTemplate::create([
                    'name' => $baseName,
                    'category_id' => $validated['category_id'] ?? $courseTemplate->category_id,
                    'level' => $validated['level'] ?? $courseTemplate->level,
                    'version' => $nextVersion,
                    'status' => $validated['status'] ?? 'active',
                    'created_by' => $request->user()?->id,
                    'description' => $validated['description'] ?? $courseTemplate->description,
                ]);

                $modulePayload = array_key_exists('modules', $validated)
                    ? (array) $validated['modules']
                    : $courseTemplate->modules()->orderBy('order_no')->get()->map(function ($m) {
                        return [
                            'order_no' => (int) $m->order_no,
                            'title' => (string) $m->title,
                            'description' => $m->description,
                            'type' => (string) $m->type,
                            'is_required' => (bool) $m->is_required,
                            'duration' => (int) $m->duration,
                        ];
                    })->all();

                $this->syncModules($newTemplate, $modulePayload, (string) ($validated['level'] ?? $courseTemplate->level));

                return [
                    'template' => $newTemplate->fresh(['modules']),
                    'message' => 'Template course versi baru berhasil dibuat',
                ];
            }

            $courseTemplate->update([
                'name' => $validated['name'] ?? $courseTemplate->name,
                'category_id' => $validated['category_id'] ?? $courseTemplate->category_id,
                'level' => $validated['level'] ?? $courseTemplate->level,
                'status' => $validated['status'] ?? $courseTemplate->status,
                'description' => $validated['description'] ?? $courseTemplate->description,
            ]);

            $modulePayload = array_key_exists('modules', $validated)
                ? (array) $validated['modules']
                : $courseTemplate->modules()->orderBy('order_no')->get()->map(function ($module) {
                    return [
                        'order_no' => (int) $module->order_no,
                        'title' => (string) $module->title,
                        'description' => $module->description,
                        'type' => (string) $module->type,
                        'is_required' => (bool) $module->is_required,
                        'duration' => (int) $module->duration,
                    ];
                })->all();

            $this->syncModules($courseTemplate, $modulePayload, (string) ($validated['level'] ?? $courseTemplate->level));

            return [
                'template' => $courseTemplate->fresh(['modules']),
                'message' => 'Template course berhasil diupdate',
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => $result['message'],
            'data' => $result['template'],
        ]);
    }

    public function destroy(CourseTemplate $courseTemplate)
    {
        $courseTemplate->update(['status' => 'archive']);

        return response()->json([
            'status' => 'success',
            'message' => 'Template course berhasil diarsipkan',
        ]);
    }

    private function validatePayload(Request $request, bool $isUpdate): array
    {
        $nameRule = $isUpdate ? 'sometimes|required' : 'required';
        $levelRule = $isUpdate ? 'sometimes|required' : 'required';
        $statusRule = $isUpdate ? 'sometimes|required' : 'nullable';

        return $request->validate([
            'name' => $nameRule . '|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'level' => $levelRule . '|in:beginner,intermediate,advanced',
            'status' => $statusRule . '|in:active,archive',
            'description' => 'nullable|string',
            'create_new_version' => 'nullable|boolean',
            'modules' => 'nullable|array|min:1',
            'modules.*.order_no' => 'nullable|integer|min:1',
            'modules.*.title' => 'required_with:modules|string|max:255',
            'modules.*.description' => 'nullable|string',
            'modules.*.type' => 'required_with:modules|in:video,pdf,quiz',
            'modules.*.is_required' => 'nullable|boolean',
            'modules.*.duration' => 'nullable|integer|min:0',
        ]);
    }

    private function syncModules(CourseTemplate $template, array $modules, string $level): void
    {
        $targetRows = $this->defaultModulesForLevel($level);
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

        if (count($rows) < count($targetRows)) {
            $rows = array_values(array_merge($rows, array_slice($targetRows, count($rows))));
        } elseif (count($rows) > count($targetRows)) {
            $rows = array_slice($rows, 0, count($targetRows));
        }

        $template->modules()->delete();

        if (!empty($rows)) {
            $template->modules()->createMany($rows);
        }
    }

    private function defaultModulesForLevel(string $level): array
    {
        $level = strtolower(trim($level));
        $sectionCount = match ($level) {
            'intermediate' => 7,
            'advanced' => 6,
            default => 5,
        };

        $rows = [];
        $orderNo = 1;
        for ($section = 1; $section <= $sectionCount; $section++) {
            $rows[] = [
                'order_no' => $orderNo++,
                'title' => 'Bagian ' . $section . ' - Materi',
                'description' => null,
                'type' => 'pdf',
                'is_required' => true,
                'duration' => 0,
            ];
            $rows[] = [
                'order_no' => $orderNo++,
                'title' => 'Bagian ' . $section . ' - Video',
                'description' => null,
                'type' => 'video',
                'is_required' => true,
                'duration' => 0,
            ];
            $rows[] = [
                'order_no' => $orderNo++,
                'title' => 'Bagian ' . $section . ' - Quiz',
                'description' => null,
                'type' => 'quiz',
                'is_required' => true,
                'duration' => 0,
            ];
        }

        return $rows;
    }
}
