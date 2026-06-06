<?php

namespace App\Http\Controllers\Api\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseTemplate;
use App\Models\CourseUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseSubmissionController extends Controller
{
    /**
     * POST /api/trainer/course-submissions
     *
     * Dua mode penggunaan:
     *
     * MODE A — Banyak course, masing-masing 1 judul (setiap judul = 1 course + 1 unit):
     * {
     *   "courses": [
     *     { "name": "Ubah",    "level": "beginner" },
     *     { "name": "Basicly", "level": "beginner" }
     *   ]
     * }
     *
     * MODE B — 1 course, judul-judul menjadi unit titles (2 judul = 2 unit = 6 modul):
     * {
     *   "course_name": "Data Science Google Colab",
     *   "unit_titles": ["Ubah", "Basicly"],
     *   "level": "beginner"
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            // Mode A
            'courses'               => 'nullable|array|min:1|max:20',
            'courses.*.name'        => 'required_with:courses|string|max:255',
            'courses.*.level'       => 'nullable|in:beginner,intermediate,advanced',
            'courses.*.price'       => 'nullable|numeric|min:0',
            'courses.*.category_id' => 'nullable|integer|exists:categories,id',
            'courses.*.description' => 'nullable|string',
            // Mode B
            'course_name'           => 'nullable|string|max:255',
            'unit_titles'           => 'nullable|array|min:1|max:20',
            'unit_titles.*'         => 'required_with:unit_titles|string|max:255',
            'level'                 => 'nullable|in:beginner,intermediate,advanced',
            // Global defaults
            'category_id'           => 'nullable|integer|exists:categories,id',
            'price'                 => 'nullable|numeric|min:0',
        ]);

        if ($request->filled('unit_titles')) {
            return $this->storeSingleCourseWithUnits($request);
        }

        return $this->storeMultipleCourses($request);
    }

    /**
     * Mode A: Setiap judul → 1 course terpisah dengan 1 unit (3 modul slot).
     */
    private function storeMultipleCourses(Request $request): JsonResponse
    {
        $trainer     = $request->user();
        $globalCat   = $request->input('category_id');
        $globalPrice = $request->input('price', 0);
        $created     = [];
        $skipped     = [];

        DB::transaction(function () use ($request, $trainer, $globalCat, $globalPrice, &$created, &$skipped) {
            foreach ($request->input('courses', []) as $item) {
                $name        = trim((string) ($item['name'] ?? ''));
                $level       = $item['level'] ?? 'beginner';
                $price       = $item['price'] ?? $globalPrice;
                $categoryId  = $item['category_id'] ?? $globalCat;
                $description = $item['description'] ?? null;

                if ($name === '') {
                    continue;
                }

                // Cegah duplikat
                if (Course::query()
                    ->where('trainer_id', $trainer->id)
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
                    ->exists()
                ) {
                    $skipped[] = ['name' => $name, 'reason' => 'Course dengan judul ini sudah ada.'];
                    continue;
                }

                $template = $this->resolveTemplate($level);

                $course = Course::create([
                    'name'             => $name,
                    'trainer_id'       => $trainer->id,
                    'category_id'      => $categoryId,
                    'level'            => $level,
                    'price'            => (float) $price,
                    'status'           => 'draft',
                    'description'      => $description,
                    'template_id'      => $template?->id,
                    'template_version' => $template?->version,
                    'duration'         => 0,
                ]);

                // Clone hanya 1 unit (3 slot) dan simpan judul sebagai unit title
                $clonedCount = $this->cloneOneUnit($course, $template, unitNo: 1, unitTitle: $name);

                $created[] = [
                    'course_id'      => $course->id,
                    'name'           => $course->name,
                    'level'          => $course->level,
                    'status'         => $course->status,
                    'template_used'  => $template ? ['id' => $template->id, 'name' => $template->name] : null,
                    'modules_cloned' => $clonedCount,
                    'units'          => [['unit_no' => 1, 'title' => $name]],
                ];
            }
        });

        $message = count($created) . ' course berhasil dibuat';
        if (!empty($skipped)) {
            $message .= ', ' . count($skipped) . ' dilewati (duplikat)';
        }

        return response()->json([
            'status'  => 'success',
            'message' => $message . '.',
            'data'    => ['created' => $created, 'skipped' => $skipped],
        ], 201);
    }

    /**
     * Mode B: 1 course, judul-judul menjadi unit titles.
     * Contoh: course_name="Data Science", unit_titles=["Ubah","Basicly"]
     * → 1 course dengan 2 unit, masing-masing 3 modul slot = 6 modul total.
     */
    private function storeSingleCourseWithUnits(Request $request): JsonResponse
    {
        $trainer    = $request->user();
        $courseName = trim((string) $request->input('course_name', ''));
        $unitTitles = array_values(array_filter(
            array_map('trim', (array) $request->input('unit_titles', []))
        ));
        $level      = $request->input('level', 'beginner');
        $categoryId = $request->input('category_id');
        $price      = $request->input('price', 0);

        if ($courseName === '') {
            return response()->json([
                'status'  => 'error',
                'message' => 'course_name wajib diisi saat menggunakan unit_titles.',
            ], 422);
        }

        if (empty($unitTitles)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'unit_titles tidak boleh kosong.',
            ], 422);
        }

        // Cegah duplikat
        if (Course::query()
            ->where('trainer_id', $trainer->id)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($courseName)])
            ->exists()
        ) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Course dengan judul ini sudah ada.',
            ], 422);
        }

        $template = $this->resolveTemplate($level);

        $result = DB::transaction(function () use (
            $trainer, $courseName, $unitTitles, $level, $categoryId, $price, $template
        ) {
            $course = Course::create([
                'name'             => $courseName,
                'trainer_id'       => $trainer->id,
                'category_id'      => $categoryId,
                'level'            => $level,
                'price'            => (float) $price,
                'status'           => 'draft',
                'template_id'      => $template?->id,
                'template_version' => $template?->version,
                'duration'         => 0,
            ]);

            $totalCloned = 0;
            $units       = [];

            foreach ($unitTitles as $idx => $title) {
                $unitNo       = $idx + 1;
                $clonedCount  = $this->cloneOneUnit($course, $template, unitNo: $unitNo, unitTitle: $title);
                $totalCloned += $clonedCount;
                $units[]      = ['unit_no' => $unitNo, 'title' => $title];
            }

            return [
                'course_id'      => $course->id,
                'name'           => $course->name,
                'level'          => $course->level,
                'status'         => $course->status,
                'template_used'  => $template ? ['id' => $template->id, 'name' => $template->name] : null,
                'modules_cloned' => $totalCloned,
                'units'          => $units,
            ];
        });

        return response()->json([
            'status'  => 'success',
            'message' => '1 course berhasil dibuat dengan ' . count($unitTitles) . ' unit.',
            'data'    => ['created' => [$result], 'skipped' => []],
        ], 201);
    }

    /**
     * Clone tepat 1 unit (3 slot: PDF + Video + Quiz) dari template ke course,
     * dan simpan judul unit ke tabel course_units.
     *
     * Slot diambil dari template berdasarkan posisi unit: (unitNo - 1) * 3.
     * Jika template tidak punya cukup slot, buat slot generik.
     */
    private function cloneOneUnit(
        Course $course,
        ?CourseTemplate $template,
        int $unitNo,
        string $unitTitle
    ): int {
        // Simpan judul unit ke course_units
        CourseUnit::updateOrCreate(
            ['course_id' => $course->id, 'unit_no' => $unitNo],
            ['title' => $unitTitle]
        );

        // Ambil 3 slot dari template sesuai posisi unit ini
        $offset        = ($unitNo - 1) * 3;
        $templateSlots = $template
            ? $template->modules()->orderBy('order_no')->skip($offset)->take(3)->get()
            : collect();

        $baseOrderNo = $offset + 1;

        if ($templateSlots->isNotEmpty()) {
            $rows = $templateSlots->map(function ($slot, $i) use ($course, $baseOrderNo) {
                return [
                    'course_id'     => $course->id,
                    'order_no'      => $baseOrderNo + $i,
                    'title'         => (string) $slot->title,
                    'description'   => $slot->description,
                    'type'          => (string) $slot->type,
                    'content_url'   => '',
                    'file_name'     => null,
                    'mime_type'     => null,
                    'file_size'     => 0,
                    'is_free'       => false,
                    'preview_pages' => 0,
                    'duration'      => (int) ($slot->duration ?? 0),
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            })->values()->all();
        } else {
            // Fallback: buat 3 slot generik jika template tidak ada
            $types  = ['pdf', 'video', 'quiz'];
            $labels = ['Material', 'Video Lesson', 'Quiz'];
            $rows   = [];
            foreach ($types as $i => $type) {
                $rows[] = [
                    'course_id'     => $course->id,
                    'order_no'      => $baseOrderNo + $i,
                    'title'         => $unitTitle . ' - ' . $labels[$i],
                    'description'   => null,
                    'type'          => $type,
                    'content_url'   => '',
                    'file_name'     => null,
                    'mime_type'     => null,
                    'file_size'     => 0,
                    'is_free'       => false,
                    'preview_pages' => 0,
                    'duration'      => 0,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }
        }

        CourseModule::insert($rows);

        return count($rows);
    }

    /**
     * Ambil template aktif terbaru berdasarkan level.
     */
    private function resolveTemplate(string $level): ?CourseTemplate
    {
        return CourseTemplate::query()
            ->where('level', $level)
            ->where('status', 'active')
            ->withCount('modules')
            ->having('modules_count', '>', 0)
            ->orderByDesc('version')
            ->first();
    }
}
