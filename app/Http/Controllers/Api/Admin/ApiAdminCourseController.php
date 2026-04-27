<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApiAdminCourseController extends Controller
{
    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function jsonSuccess(string $message, $data = null, $pagination = null, int $status = 200)
    {
        return response()->json([
            'status'     => 'success',
            'message'    => $message,
            'data'       => $data,
            'pagination' => $pagination,
        ], $status);
    }

    private function jsonError(string $message, int $status = 400, $data = null)
    {
        return response()->json([
            'status'     => 'error',
            'message'    => $message,
            'data'       => $data,
            'pagination' => null,
        ], $status);
    }

    // -------------------------------------------------------------------------
    // GET /api/admin/courses
    // -------------------------------------------------------------------------

    public function index(Request $request)
    {
        $perPage = max(1, min((int) $request->query('per_page', 10), 100));

        $query = Course::query()
            ->with(['category', 'trainer:id,name,email'])
            ->withCount(['modules', 'enrollments'])
            ->withAvg('reviews as rating_avg', 'rating')
            ->orderByDesc('created_at');

        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $status = trim((string) $request->query('status', ''));
        if ($status !== '') {
            $query->where('status', $status);
        }

        $level = trim((string) $request->query('level', ''));
        if ($level !== '') {
            $query->where('level', $level);
        }

        $courses = $query->paginate($perPage)->appends($request->query());

        return $this->jsonSuccess('Daftar course (admin)', $courses->items(), [
            'current_page' => $courses->currentPage(),
            'per_page'     => $courses->perPage(),
            'total'        => $courses->total(),
            'last_page'    => $courses->lastPage(),
        ]);
    }

    // -------------------------------------------------------------------------
    // GET /api/admin/courses/{course}
    // -------------------------------------------------------------------------

    public function show(Course $course)
    {
        $course->load([
            'category',
            'trainer:id,name,email',
            'modules' => fn($q) => $q->orderBy('order_no'),
            'modules.quizQuestions.answers',
        ])->loadCount(['modules', 'enrollments'])
          ->loadAvg('reviews as rating_avg', 'rating');

        return $this->jsonSuccess('Detail course (admin)', $course);
    }

    // -------------------------------------------------------------------------
    // POST /api/admin/courses
    // -------------------------------------------------------------------------

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request, isUpdate: false);

        [$mediaPath, $mediaType] = $this->handleMediaUpload($request, null);
        $cardThumbPath = $this->handleCardThumbnailUpload($request, null);

        $course = Course::create([
            'name'                       => $validated['name'],
            'category_id'                => $validated['category_id'],
            'trainer_id'                 => $validated['trainer_id'] ?? null,
            'description'                => $validated['description'] ?? null,
            'level'                      => $validated['level'],
            'status'                     => $validated['status'] ?? 'draft',
            'price'                      => $validated['price'],
            'duration'                   => $validated['duration'] ?? 0,
            'free_access_mode'           => $validated['free_access_mode'] ?? 'limit_2',
            'is_reseller_course'         => (bool) ($validated['is_reseller_course'] ?? false),
            'discount_percent'           => $validated['discount_percent'] ?? null,
            'discount_start'             => $validated['discount_start'] ?? null,
            'discount_end'               => $validated['discount_end'] ?? null,
            'expenses_json'              => $this->normalizeExpenses($request),
            'media'                      => $mediaPath,
            'media_type'                 => $mediaType,
            'card_thumbnail'             => $cardThumbPath,
        ]);

        return $this->jsonSuccess('Course berhasil dibuat', $course->fresh()->load('category', 'trainer:id,name,email'), null, 201);
    }

    // -------------------------------------------------------------------------
    // PUT/PATCH /api/admin/courses/{course}
    // -------------------------------------------------------------------------

    public function update(Request $request, Course $course)
    {
        $validated = $this->validatePayload($request, isUpdate: true);

        $data = [
            'name'               => $validated['name'],
            'category_id'        => $validated['category_id'],
            'trainer_id'         => $validated['trainer_id'] ?? $course->trainer_id,
            'description'        => $validated['description'] ?? $course->description,
            'level'              => $validated['level'],
            'status'             => $validated['status'] ?? $course->status,
            'price'              => $validated['price'],
            'duration'           => $validated['duration'] ?? $course->duration ?? 0,
            'free_access_mode'   => $validated['free_access_mode'] ?? ($course->free_access_mode ?? 'limit_2'),
            'is_reseller_course' => (bool) ($validated['is_reseller_course'] ?? $course->is_reseller_course),
            'discount_percent'   => $validated['discount_percent'] ?? null,
            'discount_start'     => $validated['discount_start'] ?? null,
            'discount_end'       => $validated['discount_end'] ?? null,
        ];

        if ($request->has('expenses')) {
            $data['expenses_json'] = $this->normalizeExpenses($request);
        }

        [$mediaPath, $mediaType] = $this->handleMediaUpload($request, $course);
        if ($mediaPath !== null) {
            $data['media']      = $mediaPath;
            $data['media_type'] = $mediaType;
        }

        $cardThumbPath = $this->handleCardThumbnailUpload($request, $course);
        if ($cardThumbPath !== null) {
            $data['card_thumbnail'] = $cardThumbPath;
        }

        $course->update($data);

        return $this->jsonSuccess('Course berhasil diupdate', $course->fresh()->load('category', 'trainer:id,name,email'));
    }

    // -------------------------------------------------------------------------
    // DELETE /api/admin/courses/{course}
    // -------------------------------------------------------------------------

    public function destroy(Course $course)
    {
        // Cleanup media files
        foreach ([$course->media, $course->card_thumbnail] as $file) {
            if ($file && Storage::disk('public')->exists($file)) {
                Storage::disk('public')->delete($file);
            }
        }

        // Cleanup module content files
        $course->loadMissing('modules');
        foreach ($course->modules as $mod) {
            if ($mod->content_url && Storage::disk('public')->exists($mod->content_url)) {
                Storage::disk('public')->delete($mod->content_url);
            }
        }

        $course->delete();

        return $this->jsonSuccess('Course berhasil dihapus');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function validatePayload(Request $request, bool $isUpdate): array
    {
        $mediaRule = $isUpdate
            ? 'nullable|file|mimes:jpeg,png,jpg,gif,webp,mp4,webm,ogg|max:204800'
            : 'nullable|file|mimes:jpeg,png,jpg,gif,webp,mp4,webm,ogg|max:204800';

        return $request->validate([
            'name'               => 'required|string|max:255',
            'category_id'        => 'required|exists:categories,id',
            'trainer_id'         => 'nullable|exists:users,id',
            'description'        => 'nullable|string',
            'level'              => 'required|in:beginner,intermediate,advanced',
            'status'             => 'nullable|in:draft,active,archive,approved',
            'price'              => 'required|integer|min:0',
            'duration'           => 'nullable|integer|min:0',
            'free_access_mode'   => 'nullable|in:all,limit_2,none',
            'is_reseller_course' => 'nullable|boolean',
            'discount_percent'   => 'nullable|integer|min:1|max:100',
            'discount_start'     => 'nullable|date',
            'discount_end'       => 'nullable|date|after_or_equal:discount_start',
            'expenses'           => 'nullable|array',
            'expenses.*.item'    => 'nullable|string|max:255',
            'expenses.*.quantity'   => 'nullable|integer|min:0',
            'expenses.*.unit_price' => 'nullable|integer|min:0',
            'expenses.*.total'      => 'nullable|integer|min:0',
            'media'              => $mediaRule,
            'image'              => $mediaRule,
            'card_thumbnail'     => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:20480',
        ]);
    }

    private function handleMediaUpload(Request $request, ?Course $course): array
    {
        // Accept both 'media' (API) and 'image' (web form) field names
        $fileKey = $request->hasFile('media') ? 'media' : ($request->hasFile('image') ? 'image' : null);

        if ($fileKey === null) {
            return [null, null];
        }

        // Delete old file
        if ($course && $course->media && Storage::disk('public')->exists($course->media)) {
            Storage::disk('public')->delete($course->media);
        }

        $file      = $request->file($fileKey);
        $path      = $file->store('courses', 'public');
        $mediaType = str_starts_with((string) $file->getMimeType(), 'video/') ? 'video' : 'image';

        return [$path, $mediaType];
    }

    private function handleCardThumbnailUpload(Request $request, ?Course $course): ?string
    {
        if (!$request->hasFile('card_thumbnail')) {
            return null;
        }

        if ($course && $course->card_thumbnail && Storage::disk('public')->exists($course->card_thumbnail)) {
            Storage::disk('public')->delete($course->card_thumbnail);
        }

        return $request->file('card_thumbnail')->store('courses/card_thumbnails', 'public');
    }

    private function normalizeExpenses(Request $request): ?array
    {
        $input = $request->input('expenses');
        if (!is_array($input)) {
            return null;
        }

        $rows = [];
        foreach ($input as $row) {
            if (!is_array($row)) continue;
            $item = trim((string) ($row['item'] ?? ''));
            $qty  = max(0, (int) ($row['quantity'] ?? 0));
            $unit = max(0, (int) ($row['unit_price'] ?? 0));
            $total = isset($row['total']) ? max(0, (int) $row['total']) : $qty * $unit;
            if ($item === '' && $qty === 0 && $unit === 0 && $total === 0) continue;
            $rows[] = compact('item', 'qty', 'unit', 'total') + ['quantity' => $qty, 'unit_price' => $unit];
        }

        return !empty($rows) ? $rows : null;
    }
}
