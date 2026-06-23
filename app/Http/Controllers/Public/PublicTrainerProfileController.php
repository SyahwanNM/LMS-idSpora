<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Feedback;
use App\Models\Review;
use App\Models\TrainerCertificate;
use App\Models\User;
use Illuminate\Http\Request;

class PublicTrainerProfileController extends Controller
{
    public function show(Request $request, User $trainer)
    {
        $isTrainer = strtolower((string) ($trainer->role ?? '')) === 'trainer';
        if (!$isTrainer) {
            abort(404);
        }

        $trainerId = (int) $trainer->id;
        $trainerName = trim((string) ($trainer->name ?? ''));

        // Courses queries
        $coursesQuery = Course::query()
            ->where('trainer_id', $trainerId)
            ->whereIn('status', ['published', 'approved', 'active']);

        $coursesCount = (clone $coursesQuery)->count();

        $activeCourses = $coursesQuery
            ->with(['category'])
            ->withCount([
                'enrollments as students_count' => function ($q) {
                    $q->where('status', 'active');
                },
            ])
            ->withCount('modules')
            ->withAvg('reviews as rating', 'rating')
            ->orderByDesc('updated_at')
            ->take(6)
            ->get();

        // Events queries
        $eventsQuery = Event::query()
            ->where('is_published', true)
            ->where(function ($query) use ($trainerId, $trainerName) {
                $query->where('trainer_id', $trainerId)
                    ->orWhereHas('speakers', function ($speakerQuery) use ($trainerId) {
                        $speakerQuery->where('trainer_id', $trainerId);
                    })
                    ->orWhereHas('trainerAssignments', function ($assignmentQuery) use ($trainerId) {
                        $assignmentQuery->where('trainer_id', $trainerId)
                            ->where('status', 'accepted');
                    });
                if ($trainerName !== '') {
                    $query->orWhere('speaker', 'like', '%' . $trainerName . '%');
                }
            });

        $eventsCount = (clone $eventsQuery)->count();

        $activeEvents = $eventsQuery
            ->withCount([
                'registrations as participants_count' => function ($q) {
                    $q->where('status', 'active');
                },
            ])
            ->orderByDesc('event_date')
            ->take(6)
            ->get();

        // Course ratings statistics
        $courseRatingStats = Review::query()
            ->join('courses', 'courses.id', '=', 'reviews.course_id')
            ->where('courses.trainer_id', $trainerId)
            ->whereIn('courses.status', ['published', 'approved', 'active'])
            ->selectRaw('AVG(reviews.rating) as avg_rating, COUNT(reviews.id) as total_reviews')
            ->first();

        // Event ratings statistics
        $eventIds = (clone $eventsQuery)->pluck('id');

        $eventRatingStatsQuery = Feedback::query();
        if ($eventIds->isNotEmpty()) {
            $eventRatingStatsQuery->whereIn('feedback.event_id', $eventIds);
        } else {
            $eventRatingStatsQuery->whereRaw('1 = 0');
        }

        $eventRatingStats = $eventRatingStatsQuery
            ->whereNotNull('feedback.speaker_rating')
            ->selectRaw('AVG(feedback.speaker_rating) as avg_rating, COUNT(feedback.id) as total_reviews')
            ->first();

        $courseRatingCount = (int) ($courseRatingStats->total_reviews ?? 0);
        $eventRatingCount = (int) ($eventRatingStats->total_reviews ?? 0);
        $totalRatings = $courseRatingCount + $eventRatingCount;

        $courseAverage = (float) ($courseRatingStats->avg_rating ?? 0);
        $eventAverage = (float) ($eventRatingStats->avg_rating ?? 0);
        $combinedRating = $totalRatings > 0
            ? (($courseAverage * $courseRatingCount) + ($eventAverage * $eventRatingCount)) / $totalRatings
            : 0.0;

        // Course student count
        $courseStudents = Enrollment::query()
            ->join('courses', 'courses.id', '=', 'enrollments.course_id')
            ->where('courses.trainer_id', $trainerId)
            ->whereIn('courses.status', ['published', 'approved', 'active'])
            ->where('enrollments.status', 'active')
            ->distinct('enrollments.user_id')
            ->count('enrollments.user_id');

        // Event student count
        $eventStudentsQuery = EventRegistration::query()
            ->join('events', 'events.id', '=', 'event_registrations.event_id')
            ->where('event_registrations.status', 'active');

        if ($eventIds->isNotEmpty()) {
            $eventStudentsQuery->whereIn('events.id', $eventIds);
        } else {
            $eventStudentsQuery->whereRaw('1 = 0');
        }

        $eventStudents = $eventStudentsQuery
            ->distinct('event_registrations.user_id')
            ->count('event_registrations.user_id');

        // Combined feedback reviews
        $courseReviews = Review::query()
            ->with('user:id,name,avatar')
            ->join('courses', 'courses.id', '=', 'reviews.course_id')
            ->where('courses.trainer_id', $trainerId)
            ->whereIn('courses.status', ['published', 'approved', 'active'])
            ->select('reviews.*')
            ->latest('reviews.created_at')
            ->take(6)
            ->get()
            ->map(function ($review) {
                $user = $review->user;
                return (object) [
                    'rating' => (int) ($review->trainer_rating ?? $review->rating ?? 0),
                    'comment' => (string) ($review->comment ?? ''),
                    'user_name' => (string) ($user->name ?? 'Anonim'),
                    'user_role' => 'Siswa',
                    'user_avatar_url' => $user ? $user->avatar_url : null,
                    'created_at' => $review->created_at,
                ];
            });

        $eventFeedbacksQuery = Feedback::query()->with('user:id,name,avatar');
        if ($eventIds->isNotEmpty()) {
            $eventFeedbacksQuery->whereIn('event_id', $eventIds);
        } else {
            $eventFeedbacksQuery->whereRaw('1 = 0');
        }

        $eventFeedbacks = $eventFeedbacksQuery
            ->latest('created_at')
            ->take(6)
            ->get()
            ->map(function ($feedback) {
                $user = $feedback->user;
                return (object) [
                    'rating' => (int) ($feedback->speaker_rating ?? $feedback->rating ?? 0),
                    'comment' => (string) ($feedback->comment ?? ''),
                    'user_name' => (string) ($user->name ?? 'Anonim'),
                    'user_role' => 'Peserta',
                    'user_avatar_url' => $user ? $user->avatar_url : null,
                    'created_at' => $feedback->created_at,
                ];
            });

        $feedbacks = $courseReviews->concat($eventFeedbacks)
            ->sortByDesc('created_at')
            ->take(6)
            ->values();

        // Process experiences from model
        $experiences = collect();
        $rawExperiences = $trainer->trainer_experiences ?? [];
        foreach ($rawExperiences as $exp) {
            if (is_array($exp) || is_object($exp)) {
                $exp = (array) $exp;
                $period = 'PRESENT';
                if (!empty($exp['start_date'])) {
                    $start = \Carbon\Carbon::parse($exp['start_date'])->locale('id')->translatedFormat('M Y');
                    $end = !empty($exp['end_date'])
                        ? \Carbon\Carbon::parse($exp['end_date'])->locale('id')->translatedFormat('M Y')
                        : 'Sekarang';
                    $period = $start . ' - ' . $end;
                }
                $experiences->push((object) [
                    'role' => (string) ($exp['role'] ?? 'Trainer'),
                    'company' => (string) ($exp['company'] ?? 'idSpora'),
                    'period' => $period,
                    'description' => (string) ($exp['description'] ?? ''),
                ]);
            } elseif (is_string($exp) && trim($exp) !== '') {
                $experiences->push((object) [
                    'role' => trim($exp),
                    'company' => 'idSpora',
                    'period' => 'PRESENT',
                    'description' => '',
                ]);
            }
        }

        // If no experiences entered, fallback to events
        if ($experiences->isEmpty()) {
            $experiences = $activeEvents->map(function ($event) {
                return (object) [
                    'role' => (string) ($event->title ?? 'Training Session'),
                    'company' => 'idSpora',
                    'period' => optional($event->event_date)->format('Y') ?? now()->format('Y'),
                    'description' => (int) ($event->participants_count ?? 0) . ' participants' .
                        (optional($event->event_date) ? ' • ' . optional($event->event_date)->format('d M Y') : ''),
                ];
            });
        }

        if ($experiences->isEmpty()) {
            $experiences = collect([
                (object) [
                    'role' => (string) ($trainer->profession ?: 'Trainer'),
                    'company' => (string) ($trainer->institution ?: 'idSpora Trainer'),
                    'period' => 'PRESENT',
                    'description' => 'Aktif mengembangkan pengalaman belajar peserta dengan sesi training praktis.',
                ],
            ]);
        }

        // Process certificates (verified + manual)
        $systemCertificates = TrainerCertificate::query()
            ->with('certifiable')
            ->where('trainer_id', $trainer->id)
            ->whereIn('status', ['sent', 'published'])
            ->latest('issued_at')
            ->latest('created_at')
            ->take(6)
            ->get()
            ->map(function ($certificate) {
                $certifiable = $certificate->certifiable;
                $title = 'Sertifikat Trainer';

                if ($certifiable) {
                    $title = (string) ($certifiable->title ?? $certifiable->name ?? $title);
                }

                return (object) [
                    'icon_url' => null,
                    'title' => $title,
                    'issuer' => 'idSpora',
                    'year' => optional($certificate->issued_at)->format('Y') ?? optional($certificate->created_at)->format('Y') ?? now()->format('Y'),
                ];
            });

        $manualCertificates = collect();
        $rawCertifications = $trainer->trainer_certifications ?? [];
        foreach ($rawCertifications as $cert) {
            if (is_array($cert) || is_object($cert)) {
                $cert = (array) $cert;
                $year = !empty($cert['start_date']) ? \Carbon\Carbon::parse($cert['start_date'])->format('Y') : now()->format('Y');
                $manualCertificates->push((object) [
                    'icon_url' => null,
                    'title' => (string) ($cert['name'] ?? 'Sertifikasi'),
                    'issuer' => (string) ($cert['issuer'] ?? 'Lembaga Sertifikasi'),
                    'year' => $year,
                ]);
            } elseif (is_string($cert) && trim($cert) !== '') {
                $manualCertificates->push((object) [
                    'icon_url' => null,
                    'title' => trim($cert),
                    'issuer' => 'Lembaga Sertifikasi',
                    'year' => now()->format('Y'),
                ]);
            }
        }

        $certificates = $systemCertificates->concat($manualCertificates)->take(6);

        $systemCertificatesCount = TrainerCertificate::query()
            ->where('trainer_id', $trainerId)
            ->whereIn('status', ['sent', 'published'])
            ->count();
        $manualCertificatesCount = collect($trainer->trainer_certifications ?? [])->count();
        $certificatesCount = $systemCertificatesCount + $manualCertificatesCount;

        // Experience years calculation
        $experienceYears = 0;
        $rawExperiencesList = $trainer->trainer_experiences ?? [];
        if (!empty($rawExperiencesList)) {
            $earliestYear = (int) now()->format('Y');
            foreach ($rawExperiencesList as $exp) {
                if (is_array($exp) || is_object($exp)) {
                    $exp = (array) $exp;
                    if (!empty($exp['start_date'])) {
                        $startYear = (int) \Carbon\Carbon::parse($exp['start_date'])->format('Y');
                        if ($startYear < $earliestYear) {
                            $earliestYear = $startYear;
                        }
                    }
                }
            }
            $experienceYears = max(1, (int) now()->format('Y') - $earliestYear);
        }
        if ($experienceYears === 0) {
            $experienceYears = 5; // Fallback
        }

        // Dynamic success rate
        $successRate = 95;
        if ($combinedRating > 4.0) {
            $successRate += (int) (($combinedRating - 4.0) * 10);
        }
        $successRate = min(100, max(85, $successRate));

        $reputation = [
            'rating' => round($combinedRating, 1),
            'rating_count' => $totalRatings,
            'students' => (int) $courseStudents + (int) $eventStudents,
            'experience_years' => $experienceYears,
            'success_rate' => $successRate,
            'active_learners' => (int) $courseStudents + (int) $eventStudents,
        ];

        // Expertise tags from trainer profile
        $rawSkills = $trainer->trainer_skills ?? [];
        $expertise = [];
        foreach ($rawSkills as $skill) {
            if (is_array($skill) || is_object($skill)) {
                $skill = (array) $skill;
                if (!empty($skill['name'])) {
                    $expertise[] = $skill['name'];
                }
            } elseif (is_string($skill) && trim($skill) !== '') {
                $expertise[] = trim($skill);
            }
        }

        if (empty($expertise)) {
            $expertise = $activeCourses->pluck('category.name')->filter()->unique()->values()->all();
            if (empty($expertise) && !empty($trainer->profession)) {
                $expertise = array_slice(array_filter(explode(' ', strtoupper($trainer->profession))), 0, 4);
            }
            if (empty($expertise)) {
                $expertise = ['Data Science', 'Machine Learning', 'AI Ethics', 'Python', 'Neural Networks'];
            }
        }

        // Social links fallback
        $socials = [
            'linkedin' => $trainer->linkedin_url ?? '#',
            'portfolio' => $trainer->website ?? '#',
            'twitter' => '#',
            'github' => '#',
        ];

        // Tailored Philosophy & Outcomes based on profession
        $philosophy = 'Fokus pada implementasi praktis dan studi kasus nyata untuk memastikan setiap peserta mampu menerapkan ilmu secara langsung di dunia kerja.';
        $outcomes = 'Siswa akan mendapatkan pemahaman mendalam dan kemampuan untuk membangun solusi nyata yang skalabel di bidang ini.';
        
        $profession = strtolower((string) ($trainer->profession ?? ''));
        if (str_contains($profession, 'data') || str_contains($profession, 'ai') || str_contains($profession, 'artificial intelligence') || str_contains($profession, 'machine learning')) {
            $philosophy = 'Fokus pada pemahaman fundamental teori data/AI serta implementasi praktis pada studi kasus industri nyata.';
            $outcomes = 'Siswa akan mendapatkan pemahaman mendalam tentang arsitektur AI/data dan kemampuan untuk membangun model yang cerdas dan skalabel.';
        } elseif (str_contains($profession, 'design') || str_contains($profession, 'ui') || str_contains($profession, 'ux') || str_contains($profession, 'art')) {
            $philosophy = 'Menekankan estetika, fungsionalitas, serta metodologi user-centric dalam setiap proses pembuatan karya kreatif.';
            $outcomes = 'Siswa akan mampu merancang antarmuka yang intuitif dan memukau serta memahami alur pengalaman pengguna yang optimal.';
        } elseif (str_contains($profession, 'developer') || str_contains($profession, 'programmer') || str_contains($profession, 'engineer') || str_contains($profession, 'coding')) {
            $philosophy = 'Praktek langsung (hands-on coding), best practices industri, clean code, serta pemecahan masalah (problem solving) secara logis.';
            $outcomes = 'Siswa akan menguasai pemrograman web/aplikasi modern, integrasi database, dan siap membangun sistem yang andal.';
        }

        return view('public.trainer-profile', [
            'trainer' => $trainer,
            'activeCourses' => $activeCourses,
            'activeEvents' => $activeEvents,
            'reputation' => $reputation,
            'expertise' => $expertise,
            'socials' => $socials,
            'experiences' => $experiences,
            'certificates' => $certificates,
            'feedbacks' => $feedbacks,
            'philosophy' => $philosophy,
            'outcomes' => $outcomes,
            'coursesCount' => $coursesCount,
            'eventsCount' => $eventsCount,
            'certificatesCount' => $certificatesCount,
        ]);
    }
}
