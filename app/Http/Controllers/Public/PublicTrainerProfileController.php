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

        $activeCourses = Course::query()
            ->with(['category'])
            ->withCount([
                'enrollments as students_count' => function ($q) {
                    $q->where('status', 'active');
                },
            ])
            ->where('trainer_id', $trainer->id)
            ->where('status', 'active')
            ->orderByDesc('updated_at')
            ->take(6)
            ->get();

        $activeEvents = Event::query()
            ->withCount([
                'registrations as participants_count' => function ($q) {
                    $q->where('status', 'active');
                },
            ])
            ->where('trainer_id', $trainer->id)
            ->whereDate('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->take(6)
            ->get();

        $courseRatingStats = Review::query()
            ->join('courses', 'courses.id', '=', 'reviews.course_id')
            ->where('courses.trainer_id', $trainer->id)
            ->selectRaw('AVG(reviews.rating) as avg_rating, COUNT(reviews.id) as total_reviews')
            ->first();

        $eventRatingStats = Feedback::query()
            ->join('events', 'events.id', '=', 'feedback.event_id')
            ->where('events.trainer_id', $trainer->id)
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

        $courseStudents = Enrollment::query()
            ->join('courses', 'courses.id', '=', 'enrollments.course_id')
            ->where('courses.trainer_id', $trainer->id)
            ->where('enrollments.status', 'active')
            ->distinct('enrollments.user_id')
            ->count('enrollments.user_id');

        $eventStudents = EventRegistration::query()
            ->join('events', 'events.id', '=', 'event_registrations.event_id')
            ->where('events.trainer_id', $trainer->id)
            ->where('event_registrations.status', 'active')
            ->distinct('event_registrations.user_id')
            ->count('event_registrations.user_id');

        $eventIds = Event::query()
            ->where('trainer_id', $trainer->id)
            ->pluck('id');

        $feedbacks = Feedback::query()
            ->with('user:id,name,avatar')
            ->whereIn('event_id', $eventIds)
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
                ];
            });

        $experiences = $activeEvents->map(function ($event) {
            return (object) [
                'role' => (string) ($event->title ?? 'Training Session'),
                'company' => 'idSpora',
                'period' => optional($event->event_date)->format('Y') ?? now()->format('Y'),
                'description' => (int) ($event->participants_count ?? 0) . ' participants' .
                    (optional($event->event_date) ? ' • ' . optional($event->event_date)->format('d M Y') : ''),
            ];
        });

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

        $certificates = TrainerCertificate::query()
            ->with('certifiable')
            ->where('trainer_id', $trainer->id)
            ->where('status', 'sent')
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

        $reputation = [
            'rating' => round($combinedRating, 1),
            'rating_count' => $totalRatings,
            'students' => (int) $courseStudents + (int) $eventStudents,
            'experience_years' => 10, // Mocked for design preview
            'success_rate' => 98,    // Mocked for design preview
            'active_learners' => (int) $courseStudents + (int) $eventStudents,
        ];

        // Expertise tags (mocked or derived)
        $expertise = ['Data Science', 'Machine Learning', 'AI Ethics', 'Python', 'Neural Networks'];

        // Social links fallback
        $socials = [
            'linkedin' => $trainer->linkedin_url ?? '#',
            'portfolio' => $trainer->website ?? '#',
            'twitter' => '#',
            'github' => '#',
        ];

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
            'philosophy' => 'Fokus pada implementasi praktis dan studi kasus nyata untuk memastikan setiap peserta mampu menerapkan ilmu secara langsung di dunia kerja.',
            'outcomes' => 'Siswa akan mendapatkan pemahaman mendalam tentang arsitektur AI dan kemampuan untuk membangun solusi berbasis data yang skalabel.',
        ]);
    }
}
