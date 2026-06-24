<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Models\Event;
use App\Models\Review;
use App\Models\Feedback;
use App\Models\TrainerCertificate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicTrainerProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function public_trainer_profile_displays_correct_data_and_statistics(): void
    {
        // 1. Create a Trainer
        $trainer = User::factory()->create([
            'role' => 'trainer',
            'name' => 'John Doe Trainer',
            'profession' => 'Expert Software Engineer',
            'institution' => 'idSpora Academy',
            'bio' => 'An experienced software trainer.',
        ]);

        // Create a Category
        $category = Category::create([
            'name' => 'Software Development',
            'description' => 'Courses about software development',
        ]);

        // 2. Create Courses (some active, some not)
        $course1 = Course::create([
            'trainer_id' => $trainer->id,
            'category_id' => $category->id,
            'status' => 'published',
            'name' => 'Laravel Advanced Course',
            'level' => 'Advanced',
            'price' => 100000,
            'duration' => 60,
            'media' => 'placeholder.jpg',
            'media_type' => 'image',
        ]);
        $course2 = Course::create([
            'trainer_id' => $trainer->id,
            'category_id' => $category->id,
            'status' => 'approved',
            'name' => 'Vue.js Basics',
            'level' => 'Beginner',
            'price' => 50000,
            'duration' => 30,
            'media' => 'placeholder.jpg',
            'media_type' => 'image',
        ]);
        // A draft/rejected course that shouldn't be counted in stats/list
        Course::create([
            'trainer_id' => $trainer->id,
            'category_id' => $category->id,
            'status' => 'draft',
            'name' => 'Hidden Draft Course',
            'price' => 0,
            'duration' => 10,
            'media' => 'placeholder.jpg',
            'media_type' => 'image',
        ]);

        // Create a module for course 1 to test modules count
        $course1->modules()->create([
            'title' => 'Introduction to Laravel',
            'order_no' => 1,
            'content_url' => 'https://example.com/laravel-intro',
        ]);

        // Create reviews for course 1 and course 2
        Review::create([
            'user_id' => User::factory()->create()->id,
            'course_id' => $course1->id,
            'rating' => 5,
            'comment' => 'Awesome advanced Laravel course!',
        ]);
        Review::create([
            'user_id' => User::factory()->create()->id,
            'course_id' => $course2->id,
            'rating' => 4,
            'comment' => 'Very clear explanation.',
        ]);

        // 3. Create Events (where trainer is owner, speaker, or assigned)
        $eventOwned = Event::create([
            'trainer_id' => $trainer->id,
            'is_published' => true,
            'title' => 'Web Development Workshop',
            'image' => 'placeholder.png',
            'speaker' => $trainer->name,
            'description' => 'Test description',
            'location' => 'Jakarta',
            'price' => 0.00,
            'event_time' => '09:00:00',
            'event_date' => now()->format('Y-m-d'),
        ]);
        
        $eventCoSpeaker = Event::create([
            'is_published' => true,
            'title' => 'AI and ML Summit',
            'image' => 'placeholder.png',
            'speaker' => 'Some Guest | ' . $trainer->name,
            'description' => 'Co-speaker event test description',
            'location' => 'Online',
            'price' => 0.00,
            'event_time' => '10:00:00',
            'event_date' => now()->format('Y-m-d'),
        ]);
        $eventCoSpeaker->speakers()->create([
            'trainer_id' => $trainer->id,
            'name' => $trainer->name,
            'order' => 1,
        ]);

        // An unpublished event that shouldn't be counted
        Event::create([
            'trainer_id' => $trainer->id,
            'is_published' => false,
            'title' => 'Secret Event',
            'image' => 'placeholder.png',
            'speaker' => $trainer->name,
            'description' => 'Test description',
            'location' => 'Jakarta',
            'price' => 0.00,
            'event_time' => '09:00:00',
            'event_date' => now()->format('Y-m-d'),
        ]);

        // Create event feedbacks
        Feedback::create([
            'user_id' => User::factory()->create()->id,
            'event_id' => $eventOwned->id,
            'speaker_rating' => 5,
            'rating' => 5,
            'comment' => 'Amazing workshop!',
        ]);

        // 4. Create Trainer Certificate
        TrainerCertificate::create([
            'trainer_id' => $trainer->id,
            'certifiable_id' => $course1->id,
            'certifiable_type' => Course::class,
            'status' => 'published',
            'file_path' => 'uploads/certs/cert.pdf',
            'activity_code' => 'ACT-LARAVEL-101',
            'type_code' => 'course',
            'certificate_number' => 'CERT-12345',
            'issued_at' => now(),
        ]);

        // 5. Request the public trainer profile
        $response = $this->get(route('public.trainer-profile.show', $trainer));

        // 6. Assertions
        $response->assertStatus(200);

        // Assert basic profile info is visible
        $response->assertSee('John Doe Trainer');
        $response->assertSee('Expert Software Engineer');
        $response->assertSee('IDSPORA ACADEMY');

        // Assert stats counts are correct:
        // Total courses = 2 (Laravel Advanced, Vue Basics)
        // Total events = 2 (Web Dev Workshop, AI and ML Summit)
        // Total = 4
        $response->assertSee('4'); // Courses & Events stat
        $response->assertSee('2'); // Courses Created / Events Hosted stats

        // Assert course details display real module count and rating
        $response->assertSee('1 Modules');
        $response->assertSee('5.0'); // Course 1 rating

        // Assert combined feedbacks are visible
        $response->assertSee('Awesome advanced Laravel course!');
        $response->assertSee('Amazing workshop!');
    }
}
