<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Course;
use App\Models\Event;
use App\Models\TrainerCertificate;
use App\Models\TrainerNotification;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use Illuminate\Support\Str;
use App\Models\CourseModule;
use App\Models\EventTrainerModule;
use App\Models\Review;
use App\Models\Feedback;
use Carbon\Carbon;
use App\Models\Category;

class TrainerManagementController extends Controller
{
    private function buildChartPoints(\Illuminate\Support\Collection $series, int $xStart, int $xEnd, int $yTop, int $yBottom, int $max): string
    {
        $count = max(1, $series->count());
        $step = $count > 1 ? ($xEnd - $xStart) / ($count - 1) : 0;
        $range = max(1, $yBottom - $yTop);
        $points = [];

        foreach ($series->values() as $index => $value) {
            $ratio = $max > 0 ? ((int) $value / $max) : 0;
            $y = $yBottom - ($ratio * $range);
            $x = $xStart + ($step * $index);
            $points[] = round($x, 1) . ',' . round($y, 1);
        }

        return implode(' ', $points);
    }

    private function buildMetricChange(int $current, int $previous): array
    {
        $delta = $current - $previous;
        $direction = $delta >= 0 ? 'up' : 'down';
        $abs = abs($delta);

        return [
            'text' => ($delta >= 0 ? '+' : '-') . $abs . ' dari periode sebelumnya',
            'direction' => $direction,
        ];
    }

    // Menampilkan daftar trainer
    public function index(Request $request)
    {
        $totalCourses = Course::count();
        $totalEvents = Event::count();

        $pendingCourseCount = Course::where(function ($q) {
            $q->where('status', 'pending_review')
                ->orWhereHas('modules', function ($mq) {
                    $mq->where('review_status', 'pending_review')
                        ->whereNotNull('content_url')
                        ->where('content_url', '!=', '');
                });
        })->count();

        $pendingEventCount = EventTrainerModule::where('status', 'pending_review')->count();
        $pendingReviews = $pendingCourseCount + $pendingEventCount;

        $approvedMaterials = Course::whereIn('status', ['approved', 'active'])->count()
            + EventTrainerModule::where('status', 'approved')->count();
        $rejectedMaterials = Course::where('status', 'rejected')->count()
            + EventTrainerModule::where('status', 'rejected')->count();

        $approvalTotal = $pendingReviews + $approvedMaterials + $rejectedMaterials;
        $approvalStats = [
            'pending' => $pendingReviews,
            'approved' => $approvedMaterials,
            'rejected' => $rejectedMaterials,
            'total' => $approvalTotal,
            'pending_pct' => $approvalTotal > 0 ? round(($pendingReviews / $approvalTotal) * 100, 1) : 0,
            'approved_pct' => $approvalTotal > 0 ? round(($approvedMaterials / $approvalTotal) * 100, 1) : 0,
            'rejected_pct' => $approvalTotal > 0 ? round(($rejectedMaterials / $approvalTotal) * 100, 1) : 0,
        ];

        $courseLast30 = Course::where('created_at', '>=', now()->subDays(30))->count();
        $coursePrev30 = Course::whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])->count();
        $eventLast30 = Event::where('created_at', '>=', now()->subDays(30))->count();
        $eventPrev30 = Event::whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])->count();

        $pendingLast7 = CourseModule::where('review_status', 'pending_review')
            ->where('updated_at', '>=', now()->subDays(7))
            ->count()
            + EventTrainerModule::where('status', 'pending_review')
                ->where('created_at', '>=', now()->subDays(7))
                ->count();
        $pendingPrev7 = CourseModule::where('review_status', 'pending_review')
            ->whereBetween('updated_at', [now()->subDays(14), now()->subDays(7)])
            ->count()
            + EventTrainerModule::where('status', 'pending_review')
                ->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])
                ->count();

        $approvedLast30 = Course::whereIn('status', ['approved', 'active'])
            ->where('updated_at', '>=', now()->subDays(30))
            ->count()
            + EventTrainerModule::where('status', 'approved')
                ->where('updated_at', '>=', now()->subDays(30))
                ->count();
        $approvedPrev30 = Course::whereIn('status', ['approved', 'active'])
            ->whereBetween('updated_at', [now()->subDays(60), now()->subDays(30)])
            ->count()
            + EventTrainerModule::where('status', 'approved')
                ->whereBetween('updated_at', [now()->subDays(60), now()->subDays(30)])
                ->count();

        $metricChanges = [
            'courses' => $this->buildMetricChange($courseLast30, $coursePrev30),
            'events' => $this->buildMetricChange($eventLast30, $eventPrev30),
            'pending' => $this->buildMetricChange($pendingLast7, $pendingPrev7),
            'approved' => $this->buildMetricChange($approvedLast30, $approvedPrev30),
        ];

        $totalTrainers = User::whereIn('role', ['trainer', 'Trainer'])->count();
        $activeTrainers = User::whereIn('role', ['trainer', 'Trainer'])
            ->where('user_status', 'active')
            ->count();
        $teachingTrainers = User::whereIn('role', ['trainer', 'Trainer'])
            ->where(function ($query) {
                $query->whereHas('coursesAsTrainer')
                    ->orWhereHas('eventsAsTrainer');
            })
            ->count();

        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $trainerCollection = User::whereIn('role', ['trainer', 'Trainer'])
            ->withCount([
                'coursesAsTrainer' => function ($query) use ($startOfMonth, $endOfMonth) {
                    $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                },
                'eventsAsTrainer' => function ($query) use ($startOfMonth, $endOfMonth) {
                    $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                }
            ])
            ->get();
        $topTrainers = $trainerCollection
            ->map(function (User $trainer) {
                $courseCount = (int) ($trainer->courses_as_trainer_count ?? 0);
                $eventCount = (int) ($trainer->events_as_trainer_count ?? 0);
                $trainer->score = $courseCount + $eventCount;
                return $trainer;
            })
            ->sortByDesc('score')
            ->take(3)
            ->values();

        $maxScore = max(1, (int) $topTrainers->max('score'));
        $topTrainers = $topTrainers->map(function (User $trainer) use ($maxScore) {
            $trainer->score_pct = (int) round(((int) ($trainer->score ?? 0) / $maxScore) * 100);
            return $trainer;
        });

        $deadlineItems = collect();
        $courseInvites = TrainerNotification::query()
            ->where('type', 'course_invitation')
            ->orderByDesc('created_at')
            ->take(80)
            ->get();
        $courseInvites = $courseInvites->filter(fn($invite) => data_get($invite->data, 'entity_type') === 'course'
            && !empty(data_get($invite->data, 'due_at')));

        $courseIds = $courseInvites
            ->map(fn($invite) => (int) data_get($invite->data, 'entity_id'))
            ->filter()
            ->unique()
            ->values();

        $courses = Course::with('trainer:id,name')
            ->whereIn('id', $courseIds)
            ->get()
            ->keyBy('id');

        foreach ($courseInvites as $invite) {
            $courseId = (int) data_get($invite->data, 'entity_id');
            $course = $courses->get($courseId);
            $dueRaw = data_get($invite->data, 'due_at');
            if (!$course || empty($dueRaw)) {
                continue;
            }
            try {
                $dueAt = Carbon::parse($dueRaw);
            } catch (\Throwable $e) {
                continue;
            }
            $deadlineItems->push([
                'type' => 'course',
                'title' => $course->name ?? 'Course',
                'trainer' => $course->trainer?->name ?? 'Trainer',
                'trainer_id' => $course->trainer_id,
                'due_at' => $dueAt,
            ]);
        }

        $eventDeadlines = Event::query()
            ->whereNotNull('material_deadline')
            ->with('trainer:id,name')
            ->orderBy('material_deadline')
            ->take(50)
            ->get();

        foreach ($eventDeadlines as $event) {
            if (!$event->material_deadline) {
                continue;
            }
            $deadlineItems->push([
                'type' => 'event',
                'title' => $event->title ?? 'Event',
                'trainer' => $event->trainer?->name ?? 'Trainer',
                'trainer_id' => $event->trainer_id ?? $event->trainer?->id,
                'due_at' => Carbon::parse($event->material_deadline),
            ]);
        }

        $deadlineItems = $deadlineItems
            ->sortBy('due_at')
            ->take(3)
            ->values()
            ->map(function (array $item) {
                $now = Carbon::now();
                $dueAt = $item['due_at'];
                $isLate = $dueAt->lt($now);
                $daysLeft = $isLate ? 0 : $now->diffInDays($dueAt);
                $badgeClass = $isLate ? 'red' : ($daysLeft <= 2 ? 'yellow' : 'blue');
                $badgeText = $isLate ? 'Lewat Tenggat' : ($daysLeft . ' Hari Lagi');

                return array_merge($item, [
                    'badge_class' => $badgeClass,
                    'badge_text' => $badgeText,
                    'date_text' => $dueAt->translatedFormat('d M Y'),
                ]);
            });

        $courseReviews = Review::with(['user:id,name', 'course:id,name'])
            ->latest()
            ->take(10)
            ->get();
        $eventFeedback = Feedback::with(['user:id,name', 'event:id,title'])
            ->latest()
            ->take(10)
            ->get();

        $feedbackItems = $courseReviews
            ->map(function (Review $review) {
                $rating = (int) ($review->rating ?? $review->trainer_rating ?? 0);
                $rating = max(0, min(5, $rating));

                return [
                    'title' => 'Kursus: ' . ($review->course?->name ?? 'Kursus'),
                    'name' => $review->user?->name ?? 'User',
                    'stars' => str_repeat('★', $rating) . str_repeat('☆', 5 - $rating),
                    'time' => $review->created_at?->diffForHumans() ?? '-',
                    'created_at' => $review->created_at ?? now(),
                    'comment' => $review->comment,
                ];
            })
            ->merge($eventFeedback->map(function (Feedback $feedback) {
                $rating = (int) ($feedback->rating ?? $feedback->speaker_rating ?? $feedback->committee_rating ?? 0);
                $rating = max(0, min(5, $rating));

                return [
                    'title' => 'Acara: ' . ($feedback->event?->title ?? 'Acara'),
                    'name' => $feedback->user?->name ?? 'User',
                    'stars' => str_repeat('★', $rating) . str_repeat('☆', 5 - $rating),
                    'time' => $feedback->created_at?->diffForHumans() ?? '-',
                    'created_at' => $feedback->created_at ?? now(),
                    'comment' => $feedback->comment,
                ];
            }))
            ->sortByDesc('created_at')
            ->take(3)
            ->values();

        $categoryCounts = Course::query()
            ->selectRaw('category_id, COUNT(*) as total')
            ->whereNotNull('category_id')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get();
        $categoryIds = $categoryCounts->pluck('category_id')->filter()->unique()->values();
        $categories = Category::whereIn('id', $categoryIds)->get()->keyBy('id');
        $categoryPalette = ['#2f5bff', '#19bd6b', '#ff970f', '#8d54df', '#9ca3af'];
        $categoryStats = $categoryCounts->take(5)->values()->map(function ($row, $index) use ($categories, $totalCourses, $categoryPalette) {
            $name = $categories->get($row->category_id)?->name ?? 'Lainnya';
            $pct = $totalCourses > 0 ? round(($row->total / $totalCourses) * 100, 1) : 0;

            return [
                'name' => $name,
                'total' => (int) $row->total,
                'pct' => $pct,
                'color' => $categoryPalette[$index] ?? '#9ca3af',
            ];
        });

        $categoryGradient = '';
        $cursor = 0;
        foreach ($categoryStats as $stat) {
            $next = min(100, $cursor + $stat['pct']);
            $categoryGradient .= $stat['color'] . ' ' . $cursor . '% ' . $next . '%, ';
            $cursor = $next;
        }
        if ($cursor < 100) {
            $categoryGradient .= '#9ca3af ' . $cursor . '% 100%';
        } else {
            $categoryGradient = rtrim($categoryGradient, ', ');
        }

        $chartDays = collect(range(6, 0))
            ->map(fn($i) => now()->subDays($i)->toDateString());
        $courseSeries = $chartDays->map(fn($day) => Course::whereDate('created_at', $day)->count());
        $eventSeries = $chartDays->map(fn($day) => Event::whereDate('created_at', $day)->count());
        $materialSeries = $chartDays->map(function ($day) {
            $courseCount = CourseModule::where('review_status', 'pending_review')
                ->whereDate('updated_at', $day)
                ->count();
            $eventCount = EventTrainerModule::where('status', 'pending_review')
                ->whereDate('created_at', $day)
                ->count();
            return $courseCount + $eventCount;
        });
        $approvedSeries = $chartDays->map(function ($day) {
            $courseCount = Course::whereIn('status', ['approved', 'active'])
                ->whereDate('updated_at', $day)
                ->count();
            $eventCount = EventTrainerModule::where('status', 'approved')
                ->whereDate('updated_at', $day)
                ->count();
            return $courseCount + $eventCount;
        });

        $chartLabels = $chartDays
            ->map(fn($day) => Carbon::parse($day)->translatedFormat('d M'))
            ->values();
        $chartData = [
            'labels' => $chartLabels,
            'course' => $courseSeries->values(),
            'event' => $eventSeries->values(),
            'material' => $materialSeries->values(),
        ];

        $chartMax = max(1, (int) $courseSeries->max(), (int) $eventSeries->max(), (int) $materialSeries->max());
        $sparkMaxCourse = max(1, (int) $courseSeries->max());
        $sparkMaxEvent = max(1, (int) $eventSeries->max());
        $sparkMaxMaterial = max(1, (int) $materialSeries->max());
        $sparkMaxApproved = max(1, (int) $approvedSeries->max());

        $chartPoints = [
            'course' => $this->buildChartPoints($courseSeries, 15, 665, 35, 215, $chartMax),
            'event' => $this->buildChartPoints($eventSeries, 15, 665, 35, 215, $chartMax),
            'material' => $this->buildChartPoints($materialSeries, 15, 665, 35, 215, $chartMax),
            'spark_course' => $this->buildChartPoints($courseSeries, 0, 120, 10, 45, $sparkMaxCourse),
            'spark_event' => $this->buildChartPoints($eventSeries, 0, 120, 10, 45, $sparkMaxEvent),
            'spark_pending' => $this->buildChartPoints($materialSeries, 0, 120, 10, 45, $sparkMaxMaterial),
            'spark_approved' => $this->buildChartPoints($approvedSeries, 0, 120, 10, 45, $sparkMaxApproved),
        ];

        // Ambil data trainer dengan filter & search
        $query = User::query()
            ->where(function ($q) {
                $q->where('role', 'trainer')
                    ->orWhere('role', 'Trainer');
            })
            ->withCount(['coursesAsTrainer', 'eventsAsTrainer', 'trainerEnrollments']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // Sorting Logic
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // 1. Pending Course Modules & Event Modules list
        $pendingCourseModules = CourseModule::with(['course', 'course.trainer'])
            ->where('review_status', 'pending_review')
            ->whereNotNull('content_url')
            ->where('content_url', '!=', '')
            ->latest('updated_at')
            ->get()
            ->map(function ($module) {
                return [
                    'type' => 'course',
                    'title' => $module->title,
                    'source' => $module->course?->name ?? 'Kursus',
                    'trainer' => $module->course?->trainer?->name ?? 'Trainer',
                    'date' => $module->updated_at,
                    'url' => route('admin.trainer.material.show', $module->course_id),
                ];
            });

        $pendingEventModulesList = EventTrainerModule::with(['event', 'trainer'])
            ->where('status', 'pending_review')
            ->latest('created_at')
            ->get()
            ->map(function ($module) {
                return [
                    'type' => 'event',
                    'title' => $module->original_name ?? 'Materi Acara',
                    'source' => $module->event?->title ?? 'Acara',
                    'trainer' => $module->trainer?->name ?? 'Trainer',
                    'date' => $module->created_at,
                    'url' => route('admin.event-material.show', $module->event_id),
                ];
            });

        $pendingMaterialsQueue = $pendingCourseModules->concat($pendingEventModulesList)
            ->sortByDesc('date')
            ->take(5)
            ->values();

        // 2. Unsent Certificates list (completed events/courses without certificates)
        $publishedCertKeys = TrainerCertificate::query()
            ->whereIn('status', ['sent', 'published', 'revoked'])
            ->get(['certifiable_type', 'certifiable_id'])
            ->mapWithKeys(function ($cert) {
                return [$cert->certifiable_type . ':' . $cert->certifiable_id => true];
            });

        $unsentEvents = Event::query()
            ->whereNotNull('trainer_id')
            ->whereNotNull('event_date')
            ->whereDate('event_date', '<=', now()->toDateString())
            ->with('trainer:id,name')
            ->get()
            ->filter(fn($event) => !$publishedCertKeys->has(Event::class . ':' . $event->id));

        $unsentCourses = Course::query()
            ->whereNotNull('trainer_id')
            ->whereIn('status', ['published', 'approved', 'active'])
            ->with('trainer:id,name')
            ->get()
            ->filter(fn($course) => !$publishedCertKeys->has(Course::class . ':' . $course->id));

        $pendingCertificatesQueue = $unsentEvents->map(function ($event) {
            return [
                'type' => 'event',
                'id' => $event->id,
                'title' => $event->title,
                'trainer' => $event->trainer?->name ?? 'Trainer',
                'date' => $event->event_date,
                'url' => route('admin.trainer.certificates.edit', [
                    'trainer' => $event->trainer_id,
                    'context' => 'event',
                    'id' => $event->id,
                ]),
            ];
        })->concat($unsentCourses->map(function ($course) {
            return [
                'type' => 'course',
                'id' => $course->id,
                'title' => $course->name,
                'trainer' => $course->trainer?->name ?? 'Trainer',
                'date' => $course->updated_at,
                'url' => route('admin.trainer.certificates.edit', [
                    'trainer' => $course->trainer_id,
                    'context' => 'course',
                    'id' => $course->id,
                ]),
            ];
        }))->sortByDesc('date')->take(5)->values();

        $trainers = $query->paginate(10)->withQueryString();

        return view('admin.trainer.index', compact(
            'trainers',
            'totalTrainers',
            'activeTrainers',
            'teachingTrainers',
            'totalCourses',
            'totalEvents',
            'pendingReviews',
            'approvedMaterials',
            'approvalStats',
            'metricChanges',
            'topTrainers',
            'deadlineItems',
            'feedbackItems',
            'chartPoints',
            'chartData',
            'categoryStats',
            'categoryGradient',
            'pendingMaterialsQueue',
            'pendingCertificatesQueue',
            'courseLast30',
            'eventLast30'
        ));
    }

    public function create()
    {
        return view('admin.trainer.create');
    }

    // [UPDATED] SIMPAN TRAINER BARU
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:30'],
            'profession' => ['nullable', 'string', 'max:100'], // Field Baru
            'institution' => ['nullable', 'string', 'max:255'], // Field Baru
            'website' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // Validasi Foto
        ]);

        $data['role'] = 'trainer';
        $data['password'] = Hash::make($data['password']);

        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        User::create($data);

        return redirect()->route('admin.trainer.index')->with('success', 'Trainer berhasil dibuat!');
    }

    public function show(User $trainer)
    {
        if ($trainer->role !== 'trainer')
            abort(404);

        $trainer->loadCount(['coursesAsTrainer', 'eventsAsTrainer']);

        $trainerCourses = Course::query()
            ->where('trainer_id', $trainer->id)
            ->withCount(['enrollments' => function($q) {
                $q->where('status', 'active');
            }])
            ->orderByDesc('approved_at')
            ->orderByDesc('created_at')
            ->get(['id', 'name', 'status', 'approved_at', 'created_at']);

        $trainerEvents = Event::query()
            ->where(function($query) use ($trainer) {
                $query->where('trainer_id', $trainer->id)
                    ->orWhereHas('speakers', function ($q) use ($trainer) {
                        $q->where('trainer_id', $trainer->id);
                    })
                    ->orWhereHas('trainerAssignments', function ($q) use ($trainer) {
                        $q->where('trainer_id', $trainer->id)
                            ->where('status', 'accepted');
                    });
                if ($trainer->name) {
                    $query->orWhere('speaker', 'like', '%' . $trainer->name . '%');
                }
            })
            ->withCount(['registrations' => function($q) {
                $q->where('status', 'active');
            }])
            ->orderByDesc('event_date')
            ->orderByDesc('created_at')
            ->get(['id', 'title', 'jenis', 'event_date', 'created_at', 'certificate_template', 'material_deadline', 'trainer_id', 'speaker']);

        $trainerCertificates = TrainerCertificate::query()
            ->with(['issuer:id,name', 'certifiable'])
            ->where('trainer_id', $trainer->id)
            ->latest('issued_at')
            ->latest('created_at')
            ->get();

        $courseReviews = \App\Models\Review::query()
            ->whereHas('course', function($q) use ($trainer) {
                $q->where('trainer_id', $trainer->id);
            })
            ->with(['user:id,name', 'course:id,name'])
            ->latest()
            ->get();

        $eventFeedback = \App\Models\Feedback::query()
            ->whereHas('event', function($q) use ($trainer) {
                $q->where(function($query) use ($trainer) {
                    $query->where('trainer_id', $trainer->id)
                        ->orWhereHas('speakers', function ($sq) use ($trainer) {
                            $sq->where('trainer_id', $trainer->id);
                        })
                        ->orWhereHas('trainerAssignments', function ($aq) use ($trainer) {
                            $aq->where('trainer_id', $trainer->id)
                                ->where('status', 'accepted');
                        });
                    if ($trainer->name) {
                        $query->orWhere('speaker', 'like', '%' . $trainer->name . '%');
                    }
                });
            })
            ->with(['user:id,name', 'event:id,title'])
            ->latest()
            ->get();

        $trainerPayouts = \App\Models\TrainerPayment::query()
            ->where('user_id', $trainer->id)
            ->latest('payment_date')
            ->get();

        // Compute aggregate rating stats
        $totalRatings = $courseReviews->count() + $eventFeedback->count();
        $ratingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        foreach($courseReviews as $r) { 
            $val = (int) round((float) $r->rating);
            if($val >= 1 && $val <= 5) $ratingCounts[$val]++; 
        }
        foreach($eventFeedback as $f) { 
            $val = (int) round((float) $f->rating);
            if($val >= 1 && $val <= 5) $ratingCounts[$val]++; 
        }
        
        $ratingPercentages = [];
        foreach($ratingCounts as $star => $count) {
            $ratingPercentages[$star] = $totalRatings > 0 ? round(($count / $totalRatings) * 100) : 0;
        }

        $trainerActivity = $trainer->trainer_activity_summary;
        $averageRating = (float) data_get($trainerActivity, 'average_rating', 0);
        
        $ratingBadge = 'Cukup';
        if ($averageRating >= 4.5) {
            $ratingBadge = 'Sangat Baik';
        } elseif ($averageRating >= 4.0) {
            $ratingBadge = 'Baik';
        }


        $totalPaidOut = $trainerPayouts->where('status', 'approved')->sum('amount');
        $walletBalance = (float) ($trainer->wallet_balance ?? 0);

        return view('admin.trainer.show', compact(
            'trainer', 'trainerCourses', 'trainerEvents', 'trainerCertificates',
            'courseReviews', 'eventFeedback', 'trainerPayouts', 'totalPaidOut', 'walletBalance',
            'trainerActivity', 'averageRating', 'ratingBadge', 'ratingCounts', 'ratingPercentages', 'totalRatings'
        ));
    }

    public function issueCertificate(Request $request, User $trainer)
    {
        if ($trainer->role !== 'trainer') {
            abort(404);
        }

        $data = $request->validate([
            'context' => ['required', 'in:event,course'],
            'context_id' => ['required', 'integer'],
            'activity_code' => ['required', 'string', 'size:3'],
            'type_code' => ['required', 'string', 'min:2', 'max:3'],
            'sequence' => ['required', 'string', 'max:10'],
            'issued_at' => ['nullable', 'date'],
        ]);

        $issuedAt = $request->filled('issued_at')
            ? \Carbon\Carbon::parse($data['issued_at'])
            : now();

        $certifiableType = $data['context'] === 'event' ? Event::class : Course::class;
        $certifiable = ($data['context'] === 'event')
            ? Event::where('id', $data['context_id'])->where('trainer_id', $trainer->id)->firstOrFail()
            : Course::where('id', $data['context_id'])->where('trainer_id', $trainer->id)->firstOrFail();

        $certificateNumber = $this->buildIdsporaCertificateNumber(
            $data['activity_code'],
            $data['type_code'],
            $data['sequence'],
            $issuedAt
        );

        $exists = TrainerCertificate::where('certificate_number', $certificateNumber)->exists();
        if ($exists) {
            // Informational only: duplicate certificate numbers are expected in some trainer flows
            return back()->with('info', "Nomor sertifikat sudah dipakai: {$certificateNumber}. Sesuaikan nomor urut jika perlu.");
        }

        $trainerCertificate = TrainerCertificate::create([
            'trainer_id' => $trainer->id,
            'certifiable_type' => $certifiableType,
            'certifiable_id' => $certifiable->id,
            'activity_code' => strtoupper($data['activity_code']),
            'type_code' => strtoupper($data['type_code']),
            'sequence' => $data['sequence'],
            'certificate_number' => $certificateNumber,
            'issued_at' => $issuedAt,
            'issued_by' => Auth::id(),
            'status' => 'sent',
        ]);

        // Generate & store PDF so trainer download can fetch from file_path
        $roleLabelMap = [
            'SRT' => 'Peserta',
            'MC' => 'MC',
            'TRN' => 'Narasumber',
            'PNT' => 'Panitia',
            'CLB' => 'Kolaborator',
            'MOD' => 'Moderator',
            'GRD' => 'Kelulusan',
            'SPV' => 'Supervisor/penilai',
        ];
        $roleLabel = $roleLabelMap[strtoupper((string) $data['type_code'])] ?? strtoupper((string) $data['type_code']);

        $logosBase64 = [];
        $signaturesBase64 = [];
        if ($data['context'] === 'event') {
            $event = $certifiable;
            foreach (is_array($event->certificate_logo) ? $event->certificate_logo : [] as $l) {
                $path = str_replace('storage/', '', (string) $l);
                if ($path !== '' && Storage::disk('public')->exists($path)) {
                    $absolutePath = Storage::disk('public')->path($path);
                    $mime = (is_string($absolutePath) && is_file($absolutePath)) ? (mime_content_type($absolutePath) ?: 'application/octet-stream') : 'application/octet-stream';
                    $logosBase64[] = 'data:' . $mime . ';base64,' . base64_encode(Storage::disk('public')->get($path));
                }
            }
            foreach (is_array($event->certificate_signature) ? $event->certificate_signature : [] as $s) {
                $path = str_replace('storage/', '', (string) $s);
                if ($path !== '' && Storage::disk('public')->exists($path)) {
                    $absolutePath = Storage::disk('public')->path($path);
                    $mime = (is_string($absolutePath) && is_file($absolutePath)) ? (mime_content_type($absolutePath) ?: 'application/octet-stream') : 'application/octet-stream';
                    $signaturesBase64[] = 'data:' . $mime . ';base64,' . base64_encode(Storage::disk('public')->get($path));
                }
            }
        }

        $pdfData = [
            'context' => $data['context'],
            'event' => $data['context'] === 'event' ? $certifiable : null,
            'course' => $data['context'] === 'course' ? $certifiable : null,
            'user' => $trainer,
            'issuedAt' => $issuedAt,
            'certificateNumber' => $certificateNumber,
            'logosBase64' => $logosBase64,
            'signaturesBase64' => $signaturesBase64,
            'roleLabel' => $roleLabel,
        ];

        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->setIsRemoteEnabled(true);
        $options->setIsHtml5ParserEnabled(true);
        $dompdf->setOptions($options);

        $html = view('trainer.certificates.certificate-pdf', $pdfData)->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $relativeDir = 'trainer_certificates/' . $trainer->id . '/' . $data['context'] . '/' . $certifiable->id;
        $filename = Str::slug($certificateNumber, '_') . '.pdf';
        $relativePath = $relativeDir . '/' . $filename;
        $absolutePath = storage_path('app/' . $relativePath);

        if (!is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0755, true);
        }
        file_put_contents($absolutePath, $dompdf->output());

        $trainerCertificate->update(['file_path' => $relativePath]);

        $contextLabel = $data['context'] === 'event' ? 'event' : 'course';
        $contextTitle = $data['context'] === 'event'
            ? (string) ($certifiable->title ?? '')
            : (string) ($certifiable->name ?? '');
        $trainerUrl = route('trainer.certificates.index') . '?context=' . $data['context'] . '&id=' . (int) $certifiable->id;

        TrainerNotification::create([
            'trainer_id' => $trainer->id,
            'type' => 'certificate_issued',
            'title' => 'Sertifikat telah diterbitkan',
            'message' => 'Sertifikat untuk ' . $contextLabel . ($contextTitle !== '' ? ' "' . $contextTitle . '"' : '') . ' sudah tersedia. No: ' . $certificateNumber,
            'data' => [
                'entity_type' => $data['context'],
                'entity_id' => (int) $certifiable->id,
                'certificate_number' => $certificateNumber,
                'url' => $trainerUrl,
            ],
            'expires_at' => now()->addDays(30),
        ]);

        return back()->with('success', "Sertifikat trainer diterbitkan: {$certificateNumber}");
    }

    /**
     * Admin uploads a ready-made certificate PDF for a trainer (manual send).
     */
    public function sendCertificate(Request $request, User $trainer)
    {
        if ($trainer->role !== 'trainer') {
            abort(404);
        }

        $data = $request->validate([
            'recipient' => ['required', 'string', 'max:255'],
            'certificate_file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $file = $request->file('certificate_file');
        $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
        $relativeDir = 'trainer_certificates/' . $trainer->id . '/manual';
        $relativePath = $file->storeAs($relativeDir, $filename);

        $certificateNumber = 'MANUAL-' . time() . '-' . Str::upper(Str::random(6));

        $trainerCertificate = TrainerCertificate::create([
            'trainer_id' => $trainer->id,
            'file_path' => $relativePath,
            'status' => 'sent',
            'issued_at' => now(),
            'issued_by' => Auth::id(),
            'certificate_number' => $certificateNumber,
        ]);

        TrainerNotification::create([
            'trainer_id' => $trainer->id,
            'type' => 'certificate_issued',
            'title' => 'Sertifikat telah diterbitkan (manual)',
            'message' => 'Sertifikat manual telah diunggah: ' . $certificateNumber,
            'data' => [
                'certificate_number' => $certificateNumber,
                'url' => route('trainer.certificates.index')
            ],
            'expires_at' => now()->addDays(30),
        ]);

        return back()->with('success', 'Sertifikat manual berhasil diunggah dan disimpan.');
    }

    /**
     * Show admin form to upload/send a certificate for the given trainer.
     */
    public function showSendCertificateForm(User $trainer)
    {
        if ($trainer->role !== 'trainer') {
            abort(404);
        }

        return view('admin.trainer.send_certificate', compact('trainer'));
    }

    /**
     * Display trainer certificates queue for admin.
     */
    public function certificatesQueue(Request $request)
    {
        $query = User::where('role', 'trainer')->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        } else {
            $search = null;
        }

        $trainers = $query->paginate(15);

        // Compute real pending certificates per trainer.
        $trainers->getCollection()->transform(function ($t) {
            $trainerId = (int) ($t->id ?? 0);

            $finishedEventIds = Event::query()
                ->where('trainer_id', $trainerId)
                ->whereNotNull('event_date')
                ->whereDate('event_date', '<', now()->toDateString())
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->all();

            $finishedCourseIds = Course::query()
                ->where('trainer_id', $trainerId)
                ->where('status', 'approved')
                ->whereNotNull('approved_at')
                ->where('approved_at', '<', now())
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->all();

            $issuedEventIds = TrainerCertificate::query()
                ->where('trainer_id', $trainerId)
                ->whereIn('status', ['sent', 'published'])
                ->where('certifiable_type', Event::class)
                ->pluck('certifiable_id')
                ->map(fn($id) => (int) $id)
                ->all();

            $issuedCourseIds = TrainerCertificate::query()
                ->where('trainer_id', $trainerId)
                ->whereIn('status', ['sent', 'published'])
                ->where('certifiable_type', Course::class)
                ->pluck('certifiable_id')
                ->map(fn($id) => (int) $id)
                ->all();

            $pendingEvents = array_values(array_diff($finishedEventIds, $issuedEventIds));
            $pendingCourses = array_values(array_diff($finishedCourseIds, $issuedCourseIds));

            $t->pending_events_certificates = count($pendingEvents);
            $t->pending_courses_certificates = count($pendingCourses);
            $t->pending_certificates_count = $t->pending_events_certificates + $t->pending_courses_certificates;
            return $t;
        });

        return view('admin.trainer.certificates_queue', compact('trainers', 'search'));
    }

    /**
     * Return rendered certificate HTML for preview (AJAX).
     */
    public function previewCertificate(Request $request)
    {
        $data = $request->validate([
            'trainer_id' => ['required', 'integer'],
            'context' => ['required', 'in:event,course'],
            'context_id' => ['required', 'integer'],
            'activity_code' => ['required', 'string', 'size:3'],
            'type_code' => ['required', 'string', 'min:2', 'max:3'],
            'sequence' => ['required', 'string'],
            'issued_at' => ['nullable', 'date'],
        ]);

        $trainer = User::find($data['trainer_id']);
        if (!$trainer) {
            return response('Trainer not found', 404);
        }

        $issuedAt = $request->filled('issued_at') ? \Carbon\Carbon::parse($data['issued_at']) : now();

        $certifiable = null;
        $certifiableType = $data['context'] === 'event' ? Event::class : Course::class;
        if ($data['context'] === 'event') {
            $certifiable = Event::where('id', $data['context_id'])->first();
        } else {
            $certifiable = Course::where('id', $data['context_id'])->first();
        }

        $certificateNumber = $this->buildIdsporaCertificateNumber(
            $data['activity_code'],
            $data['type_code'],
            $data['sequence'],
            $issuedAt
        );

        // Prepare logos and signatures similar to issueCertificate
        $logosBase64 = [];
        $signaturesBase64 = [];
        if ($data['context'] === 'event' && $certifiable) {
            foreach (is_array($certifiable->certificate_logo) ? $certifiable->certificate_logo : [] as $l) {
                $path = str_replace('storage/', '', (string) $l);
                if ($path !== '' && Storage::disk('public')->exists($path)) {
                    $logosBase64[] = 'data:' . (mime_content_type(Storage::disk('public')->path($path)) ?: 'application/octet-stream') . ';base64,' . base64_encode(Storage::disk('public')->get($path));
                }
            }
            foreach (is_array($certifiable->certificate_signature) ? $certifiable->certificate_signature : [] as $s) {
                $path = str_replace('storage/', '', (string) $s);
                if ($path !== '' && Storage::disk('public')->exists($path)) {
                    $signaturesBase64[] = 'data:' . (mime_content_type(Storage::disk('public')->path($path)) ?: 'application/octet-stream') . ';base64,' . base64_encode(Storage::disk('public')->get($path));
                }
            }
        }

        $roleLabelMap = [
            'SRT' => 'Peserta',
            'MC' => 'MC',
            'TRN' => 'Narasumber',
            'PNT' => 'Panitia',
            'CLB' => 'Kolaborator',
            'MOD' => 'Moderator',
            'GRD' => 'Kelulusan',
            'SPV' => 'Supervisor/penilai',
        ];

        $roleLabel = $roleLabelMap[strtoupper((string) $data['type_code'])] ?? strtoupper((string) $data['type_code']);

        $pdfData = [
            'context' => $data['context'],
            'event' => $data['context'] === 'event' ? $certifiable : null,
            'course' => $data['context'] === 'course' ? $certifiable : null,
            'user' => $trainer,
            'issuedAt' => $issuedAt,
            'certificateNumber' => $certificateNumber,
            'logosBase64' => $logosBase64,
            'signaturesBase64' => $signaturesBase64,
            'roleLabel' => $roleLabel,
            'is_preview' => true,
        ];

        return view('trainer.certificates.certificate-pdf', $pdfData);
    }

    public function revokeCertificate(TrainerCertificate $trainerCertificate)
    {
        if (!empty($trainerCertificate->file_path)) {
            $absolute = storage_path('app/' . $trainerCertificate->file_path);
            if (is_file($absolute)) {
                @unlink($absolute);
            }
        }

        $trainerCertificate->update(['status' => 'revoked']);
        return back()->with('success', 'Sertifikat trainer berhasil dicabut.');
    }

    private function buildIdsporaCertificateNumber(string $activityCode, string $typeCode, string $sequence, \Carbon\CarbonInterface $issuedAt): string
    {
        $romanMonths = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        $monthRoman = $romanMonths[(int) $issuedAt->format('n')] ?? '';
        $year = $issuedAt->format('Y');
        $seqDigits = preg_replace('/\D+/', '', $sequence) ?: '1';
        $seq = str_pad(substr($seqDigits, -3), 3, '0', STR_PAD_LEFT);

        $activity = strtoupper(trim($activityCode ?: 'WBN'));
        $type = strtoupper(trim($typeCode ?: 'TRN'));

        return "IDSP/{$activity}/{$type}/{$seq}/{$monthRoman}/{$year}";
    }

    public function edit(User $trainer)
    {
        if ($trainer->role !== 'trainer')
            abort(404);
        return view('admin.trainer.edit', compact('trainer'));
    }

    // [UPDATED] UPDATE TRAINER (YANG ANDA CARI)
    public function update(Request $request, User $trainer)
    {
        if ($trainer->role !== 'trainer')
            abort(404);

        $editBox = (string) $request->input('edit_box', 'all');
        $data = [];

        if ($editBox === 'personal') {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($trainer->id)],
                'phone' => ['nullable', 'string', 'max:30'],
                'profession' => ['nullable', 'string', 'max:100'],
                'institution' => ['nullable', 'string', 'max:255'],
                'website' => ['nullable', 'string', 'max:255'],
                'linkedin_url' => ['nullable', 'url', 'max:255'],
                'user_status' => ['nullable', 'in:active,inactive,suspended'],
                'trainer_skills' => ['nullable', 'string'],
                'trainer_specializations' => ['nullable', 'string'],
                'trainer_experiences' => ['nullable', 'string'],
                'trainer_educations' => ['nullable', 'string'],
                'trainer_certifications' => ['nullable', 'string'],
            ]);
        } elseif ($editBox === 'bio') {
            $data = $request->validate([
                'bio' => ['nullable', 'string'],
            ]);
        } elseif ($editBox === 'account') {
            $data = $request->validate([
                'password' => ['nullable', 'string', 'min:6', 'confirmed'],
                'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ]);
        } else {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($trainer->id)],
                'password' => ['nullable', 'string', 'min:6', 'confirmed'],
                'phone' => ['nullable', 'string', 'max:30'],
                'profession' => ['nullable', 'string', 'max:100'],
                'institution' => ['nullable', 'string', 'max:255'],
                'website' => ['nullable', 'string', 'max:255'],
                'linkedin_url' => ['nullable', 'url', 'max:255'],
                'bio' => ['nullable', 'string'],
                'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
                'trainer_skills' => ['nullable', 'string'],
                'trainer_specializations' => ['nullable', 'string'],
                'trainer_experiences' => ['nullable', 'string'],
                'trainer_educations' => ['nullable', 'string'],
                'trainer_certifications' => ['nullable', 'string'],
            ]);
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make((string) $request->input('password'));
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($trainer->avatar && !str_starts_with($trainer->avatar, 'http')) {
                Storage::disk('public')->delete($trainer->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        // Process array fields
        $arrayFields = ['trainer_skills', 'trainer_specializations', 'trainer_experiences', 'trainer_educations', 'trainer_certifications'];
        foreach ($arrayFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = array_values(array_filter(array_map('trim', explode(',', $data[$field]))));
            } else if ($editBox === 'personal' || $editBox === 'all') {
                $data[$field] = [];
            }
        }

        $trainer->update($data);

        return redirect()->route('admin.trainer.show', [
            'trainer' => $trainer->id,
            'edit' => $editBox,
        ])->with('success', 'Data trainer berhasil diperbarui!');
    }

    public function destroy(User $trainer)
    {
        if ($trainer->role !== 'trainer')
            abort(404);

        if ($trainer->avatar && !str_starts_with($trainer->avatar, 'http')) {
            Storage::disk('public')->delete($trainer->avatar);
        }

        $name = $trainer->name;
        $trainer->delete();

        return redirect()->route('admin.trainer.index')->with('success', "Trainer {$name} berhasil dihapus!");
    }

    public function updateCourseDeadline(Request $request, User $trainer, \App\Models\Course $course)
    {
        if ($trainer->role !== 'trainer') {
            abort(404);
        }

        $request->validate([
            'material_deadline' => 'required|date|after_or_equal:today',
        ]);

        $deadline = \Carbon\Carbon::parse($request->material_deadline);

        $course->material_deadline = $deadline;
        $course->save();

        // Notify trainer about the deadline
        \App\Models\TrainerNotification::create([
            'trainer_id' => $trainer->id,
            'type'       => 'material_deadline_set',
            'title'      => 'Deadline Materi Kelas Ditetapkan',
            'message'    => 'Batas waktu upload materi untuk kelas "' . $course->name . '" telah ditetapkan: ' . $deadline->translatedFormat('d F Y, H:i') . '.',
            'data'       => [
                'entity_type'       => 'course',
                'entity_id'         => $course->id,
                'material_deadline' => $deadline->toIso8601String(),
                'due_at'            => $deadline->toIso8601String(),
                'url'               => route('trainer.detail-course', $course->id),
            ],
            'expires_at' => $deadline->copy()->addDays(3),
        ]);

        return back()->with('success', 'Deadline materi kelas "' . $course->name . '" berhasil diperbarui.');
    }

    /**
     * Update material deadline for a specific event assigned to trainer.
     */
    public function updateEventDeadline(Request $request, User $trainer, \App\Models\Event $event)
    {
        if ($trainer->role !== 'trainer') {
            abort(404);
        }

        $rules = ['material_deadline' => 'required|date|after_or_equal:today'];
        if ($event->event_date) {
            $rules['material_deadline'] .= '|before_or_equal:' . Carbon::parse($event->event_date)->format('Y-m-d');
        }

        $request->validate($rules, [
            'material_deadline.before_or_equal' => 'Deadline materi harus sebelum atau sama dengan tanggal event (' . ($event->event_date ? Carbon::parse($event->event_date)->format('d/m/Y') : '-') . ').',
        ]);

        $deadline = Carbon::parse($request->material_deadline);

        // Determine urgency based on days until event
        $urgencyLabel = '';
        if ($event->event_date) {
            $daysUntilEvent = (int) now()->diffInDays(Carbon::parse($event->event_date), false);
            if ($daysUntilEvent <= 1) {
                $urgencyLabel = ' 🚨 [DARURAT]';
            } elseif ($daysUntilEvent <= 3) {
                $urgencyLabel = ' ⚠️ [URGENT]';
            } elseif ($daysUntilEvent <= 7) {
                $urgencyLabel = ' [SEGERA]';
            }
        }

        $event->material_deadline = $deadline;
        $event->save();

        // Notify trainer
        TrainerNotification::create([
            'trainer_id' => $trainer->id,
            'type'       => 'material_deadline_set',
            'title'      => 'Deadline Materi Event Ditetapkan' . $urgencyLabel,
            'message'    => 'Batas waktu upload materi untuk event "' . $event->title . '" telah ditetapkan: ' . $deadline->translatedFormat('d F Y, H:i') . '.' . ($urgencyLabel ? ' Harap segera upload materi.' : ''),
            'data'       => [
                'entity_type'       => 'event',
                'entity_id'         => $event->id,
                'material_deadline' => $deadline->toIso8601String(),
                'due_at'            => $deadline->toIso8601String(),
                'url'               => route('trainer.events.show', $event->id),
            ],
            'expires_at' => $deadline->copy()->addDays(3),
        ]);

        return back()->with('success', 'Deadline materi event "' . $event->title . '" berhasil diperbarui.' . ($urgencyLabel ? ' ' . trim($urgencyLabel) : ''));
    }
}