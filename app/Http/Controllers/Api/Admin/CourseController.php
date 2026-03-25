<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $perPage = max(1, min((int) $request->query('per_page', 10), 100));

        $query = Course::query()->with(['category'])->withCount('modules')->orderByDesc('created_at');

        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($cat) use ($search) {
                        $cat->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $status = trim((string) $request->query('status', ''));
        if ($status !== '') {
            $query->where('status', $status);
        }

        $courses = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar course (admin)',
            'data' => $courses,
        ]);
    }

    public function show(Course $course)
    {
        $course->load([
            'category',
            'modules' => function ($q) {
                $q->orderBy('order_no');
            },
            'modules.quizQuestions.answers',
        ])->loadCount('modules');

        return response()->json([
            'status' => 'success',
            'message' => 'Detail course (admin)',
            'data' => $course,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request, isUpdate: false);

        $expensesJson = $this->normalizeExpenses($request);

        $mediaPath = null;
        $mediaType = null;
        if ($request->hasFile('media')) {
            $mediaFile = $request->file('media');
            $mediaPath = $mediaFile->store('courses', 'public');
            $mediaType = str_starts_with((string) $mediaFile->getMimeType(), 'video/') ? 'video' : 'image';
        }

        $cardThumbPath = null;
        if ($request->hasFile('card_thumbnail')) {
            $cardThumbPath = $request->file('card_thumbnail')->store('courses/card_thumbnails', 'public');
        }

        $course = Course::create([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'description' => $this->sanitizeDescription($validated['description'] ?? null),
            'level' => $validated['level'],
            'status' => $validated['status'],
            'price' => $validated['price'],
            'duration' => $validated['duration'],
            'free_access_mode' => $validated['free_access_mode'] ?? 'limit_2',
            'discount_percent' => $validated['discount_percent'] ?? null,
            'discount_start' => $validated['discount_start'] ?? null,
            'discount_end' => $validated['discount_end'] ?? null,
            'expenses_json' => $expensesJson,
            'media' => $mediaPath,
            'media_type' => $mediaType,
            'card_thumbnail' => $cardThumbPath,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Course berhasil dibuat',
            'data' => $course->fresh(),
        ], 201);
    }

    public function update(Request $request, Course $course)
    {
        $validated = $this->validatePayload($request, isUpdate: true);

        $data = [
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'description' => $this->sanitizeDescription($validated['description'] ?? null),
            'level' => $validated['level'],
            'status' => $validated['status'],
            'price' => $validated['price'],
            'duration' => $validated['duration'],
            'free_access_mode' => $validated['free_access_mode'] ?? ($course->free_access_mode ?? 'limit_2'),
            'discount_percent' => $validated['discount_percent'] ?? null,
            'discount_start' => $validated['discount_start'] ?? null,
            'discount_end' => $validated['discount_end'] ?? null,
        ];

        if ($request->has('expenses')) {
            $data['expenses_json'] = $this->normalizeExpenses($request);
        }

        if ($request->hasFile('media')) {
            if ($course->media && Storage::disk('public')->exists($course->media)) {
                Storage::disk('public')->delete($course->media);
            }
            $mediaFile = $request->file('media');
            $mediaPath = $mediaFile->store('courses', 'public');
            $data['media'] = $mediaPath;
            $data['media_type'] = str_starts_with((string) $mediaFile->getMimeType(), 'video/') ? 'video' : 'image';
        }

        if ($request->hasFile('card_thumbnail')) {
            if ($course->card_thumbnail && Storage::disk('public')->exists($course->card_thumbnail)) {
                Storage::disk('public')->delete($course->card_thumbnail);
            }
            $data['card_thumbnail'] = $request->file('card_thumbnail')->store('courses/card_thumbnails', 'public');
        }

        $course->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Course berhasil diupdate',
            'data' => $course->fresh(),
        ]);
    }

    public function destroy(Course $course)
    {
        // Cleanup media files
        if ($course->media && Storage::disk('public')->exists($course->media)) {
            Storage::disk('public')->delete($course->media);
        }
        if ($course->card_thumbnail && Storage::disk('public')->exists($course->card_thumbnail)) {
            Storage::disk('public')->delete($course->card_thumbnail);
        }

        // Cleanup module files
        $course->loadMissing('modules');
        foreach ($course->modules as $mod) {
            if ($mod->content_url && Storage::disk('public')->exists($mod->content_url)) {
                Storage::disk('public')->delete($mod->content_url);
            }
        }

        $course->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Course berhasil dihapus',
        ]);
    }

    private function validatePayload(Request $request, bool $isUpdate): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'status' => 'required|in:active,archive',
            'price' => 'required|integer|min:0',
            'duration' => 'required|integer|min:0',
            'free_access_mode' => 'nullable|in:all,limit_2',
            'discount_percent' => 'nullable|integer|min:1|max:100',
            'discount_start' => 'nullable|date',
            'discount_end' => 'nullable|date|after_or_equal:discount_start',
            'expenses' => 'nullable|array',
            'expenses.*.item' => 'nullable|string|max:255',
            'expenses.*.quantity' => 'nullable|integer|min:0',
            'expenses.*.unit_price' => 'nullable|integer|min:0',
            'expenses.*.total' => 'nullable|integer|min:0',
            'media' => ($isUpdate ? 'nullable' : 'required') . '|file|mimes:jpeg,png,jpg,gif,mp4,webm,ogg|max:204800',
            'card_thumbnail' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:20480',
        ];

        return $request->validate($rules);
    }

    private function sanitizeDescription(?string $html): string
    {
        $text = strip_tags((string) $html);
        $text = preg_replace('/\s+/u', ' ', $text ?? '');
        return trim($text ?? '');
    }

    private function normalizeExpenses(Request $request): ?array
    {
        $expensesInput = $request->input('expenses');
        if (!is_array($expensesInput)) {
            return null;
        }

        $normalized = [];
        foreach ($expensesInput as $row) {
            if (!is_array($row)) {
                continue;
            }
            $item = trim((string) ($row['item'] ?? ''));
            $qty = (int) ($row['quantity'] ?? 0);
            $unit = (int) ($row['unit_price'] ?? 0);
            if ($item === '' && $qty === 0 && $unit === 0) {
                continue;
            }
            $qty = max(0, $qty);
            $unit = max(0, $unit);
            $total = max(0, $qty * $unit);
            $normalized[] = [
                'item' => $item,
                'quantity' => $qty,
                'unit_price' => $unit,
                'total' => $total,
            ];
        }

        return !empty($normalized) ? $normalized : null;
    }
}
