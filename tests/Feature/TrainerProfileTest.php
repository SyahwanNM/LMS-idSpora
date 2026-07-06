<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainerProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function trainer_can_update_profile_and_bank_details(): void
    {
        $trainer = User::factory()->create([
            'role' => 'trainer',
            'name' => 'Original Name',
            'phone' => '1234567890',
            'bank_name' => 'BCA',
            'bank_account_number' => '111111',
            'bank_account_holder' => 'Original Holder',
        ]);

        $response = $this->actingAs($trainer)->put(route('trainer.profile.update'), [
            'name' => 'Updated Name',
            'phone' => '08123456789',
            'bank_name' => 'Mandiri',
            'bank_account_number' => '222222',
            'bank_account_holder' => 'Updated Holder',
            'profession' => 'Expert Trainer',
            'institution' => 'Spora',
            'bio' => 'New short bio',
        ]);

        $response->assertRedirect(route('trainer.profile') . '#tab-tentang');
        $response->assertSessionHas('success');

        $trainer->refresh();

        $this->assertEquals('Updated Name', $trainer->name);
        $this->assertEquals('08123456789', $trainer->phone);
        $this->assertEquals('Mandiri', $trainer->bank_name);
        $this->assertEquals('222222', $trainer->bank_account_number);
        $this->assertEquals('Updated Holder', $trainer->bank_account_holder);
        $this->assertEquals('Expert Trainer', $trainer->profession);
        $this->assertEquals('Spora', $trainer->institution);
        $this->assertEquals('New short bio', $trainer->bio);
    }

    /** @test */
    public function updating_bio_only_does_not_clear_bank_details(): void
    {
        $trainer = User::factory()->create([
            'role' => 'trainer',
            'name' => 'Original Name',
            'bank_name' => 'BCA',
            'bank_account_number' => '111111',
            'bank_account_holder' => 'Original Holder',
            'bio' => 'Old bio',
        ]);

        $response = $this->actingAs($trainer)->put(route('trainer.profile.update'), [
            'name' => 'Original Name',
            'bio' => 'New bio only',
        ]);

        $response->assertRedirect(route('trainer.profile') . '#tab-tentang');
        $trainer->refresh();

        $this->assertEquals('New bio only', $trainer->bio);
        $this->assertEquals('BCA', $trainer->bank_name);
        $this->assertEquals('111111', $trainer->bank_account_number);
        $this->assertEquals('Original Holder', $trainer->bank_account_holder);
    }

    /** @test */
    public function profile_update_fails_on_invalid_linkedin_url(): void
    {
        $trainer = User::factory()->create([
            'role' => 'trainer',
            'name' => 'Original Name',
        ]);

        $response = $this->actingAs($trainer)->put(route('trainer.profile.update'), [
            'name' => 'Original Name',
            'linkedin_url' => 'not-a-valid-url',
        ]);

        $response->assertSessionHasErrors(['linkedin_url']);
        
        $trainer->refresh();
        $this->assertNull($trainer->linkedin_url);
    }

    /** @test */
    public function profile_update_fails_if_name_is_missing_and_not_avatar_only(): void
    {
        $trainer = User::factory()->create([
            'role' => 'trainer',
            'name' => 'Original Name',
        ]);

        $response = $this->actingAs($trainer)->put(route('trainer.profile.update'), [
            'name' => '', // blank name
        ]);

        $response->assertSessionHasErrors(['name']);
        
        $trainer->refresh();
        $this->assertEquals('Original Name', $trainer->name);
    }

    /** @test */
    public function trainer_can_update_email(): void
    {
        $trainer = User::factory()->create([
            'role' => 'trainer',
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $response = $this->actingAs($trainer)->put(route('trainer.profile.update'), [
            'name' => 'Original Name',
            'email' => 'updated@example.com',
        ]);

        $response->assertRedirect(route('trainer.profile') . '#tab-tentang');
        $response->assertSessionHas('success');

        $trainer->refresh();
        $this->assertEquals('updated@example.com', $trainer->email);
    }

    /** @test */
    public function trainer_can_change_password_with_correct_current_password(): void
    {
        $trainer = User::factory()->create([
            'role' => 'trainer',
            'name' => 'Original Name',
            'password' => \Hash::make('oldpassword'),
        ]);

        $response = $this->actingAs($trainer)->put(route('trainer.profile.update'), [
            'name' => 'Original Name',
            'current_password' => 'oldpassword',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertRedirect(route('trainer.profile') . '#tab-tentang');
        $response->assertSessionHas('success');

        $trainer->refresh();
        $this->assertTrue(\Hash::check('newpassword', $trainer->password));
    }

    /** @test */
    public function trainer_cannot_change_password_with_incorrect_current_password(): void
    {
        $trainer = User::factory()->create([
            'role' => 'trainer',
            'name' => 'Original Name',
            'password' => \Hash::make('oldpassword'),
        ]);

        $response = $this->actingAs($trainer)->put(route('trainer.profile.update'), [
            'name' => 'Original Name',
            'current_password' => 'wrongpassword',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertSessionHasErrors(['current_password']);

        $trainer->refresh();
        $this->assertTrue(\Hash::check('oldpassword', $trainer->password));
    }

    /** @test */
    public function trainer_can_update_specializations(): void
    {
        $trainer = User::factory()->create([
            'role' => 'trainer',
            'name' => 'Original Name',
            'trainer_specializations' => ['old_specialization'],
        ]);

        $response = $this->actingAs($trainer)->put(route('trainer.profile.update'), [
            'name' => 'Original Name',
            'trainer_specializations' => ['Specialization A', 'Specialization B'],
        ]);

        $response->assertRedirect(route('trainer.profile') . '#tab-tentang');
        $response->assertSessionHas('success');

        $trainer->refresh();
        $this->assertEquals(['Specialization A', 'Specialization B'], $trainer->trainer_specializations);
    }

    /** @test */
    public function trainer_can_add_skill_to_list(): void
    {
        $trainer = User::factory()->create([
            'role' => 'trainer',
            'trainer_skills' => [['name' => 'Skill A', 'percent' => '80']],
        ]);

        $response = $this->actingAs($trainer)->post(route('trainer.profile.list.update'), [
            'type' => 'trainer_skills',
            'action' => 'add',
            'name' => 'Skill B',
            'percent' => '90',
        ]);

        $response->assertRedirect(route('trainer.profile') . '#tab-keahlian');
        $response->assertSessionHas('success');

        $trainer->refresh();
        $this->assertCount(2, $trainer->trainer_skills);
        $this->assertEquals('Skill B', $trainer->trainer_skills[1]['name']);
        $this->assertEquals('90', $trainer->trainer_skills[1]['percent']);
    }

    /** @test */
    public function trainer_can_edit_skill_in_list(): void
    {
        $trainer = User::factory()->create([
            'role' => 'trainer',
            'trainer_skills' => [['name' => 'Skill A', 'percent' => '80']],
        ]);

        $response = $this->actingAs($trainer)->post(route('trainer.profile.list.update'), [
            'type' => 'trainer_skills',
            'action' => 'edit',
            'index' => '0',
            'name' => 'Skill A Updated',
            'percent' => '100',
        ]);

        $response->assertRedirect(route('trainer.profile') . '#tab-keahlian');
        $response->assertSessionHas('success');

        $trainer->refresh();
        $this->assertCount(1, $trainer->trainer_skills);
        $this->assertEquals('Skill A Updated', $trainer->trainer_skills[0]['name']);
        $this->assertEquals('100', $trainer->trainer_skills[0]['percent']);
    }

    /** @test */
    public function trainer_can_delete_skill_from_list(): void
    {
        $trainer = User::factory()->create([
            'role' => 'trainer',
            'trainer_skills' => [['name' => 'Skill A', 'percent' => '80']],
        ]);

        $response = $this->actingAs($trainer)->post(route('trainer.profile.list.update'), [
            'type' => 'trainer_skills',
            'action' => 'delete',
            'index' => '0',
        ]);

        $response->assertRedirect(route('trainer.profile') . '#tab-keahlian');
        $response->assertSessionHas('success');

        $trainer->refresh();
        $this->assertEmpty($trainer->trainer_skills);
    }

    /** @test */
    public function trainer_profile_calculates_and_displays_real_rating_breakdown_and_aspect_ratings(): void
    {
        $trainer = User::factory()->create(['role' => 'trainer']);

        $event = \App\Models\Event::create([
            'title' => 'Test Event Rating',
            'image' => 'uploads/events/test.jpg',
            'speaker' => $trainer->name,
            'trainer_id' => $trainer->id,
            'description' => 'Test event description',
            'location' => 'Online',
            'price' => 0,
            'event_time' => '10:00:00',
            'event_date' => now()->subDay()->toDateString(),
            'material_status' => 'approved',
        ]);

        // Feedback 1: 5 stars overall, 5 stars speaker
        \App\Models\Feedback::create([
            'event_id' => $event->id,
            'user_id' => User::factory()->create()->id,
            'rating' => 5,
            'speaker_rating' => 5,
            'comment' => 'Excellent trainer!',
        ]);

        // Feedback 2: 4 stars overall, 3 stars speaker
        \App\Models\Feedback::create([
            'event_id' => $event->id,
            'user_id' => User::factory()->create()->id,
            'rating' => 4,
            'speaker_rating' => 3,
            'comment' => 'Good session.',
        ]);

        $response = $this->actingAs($trainer)->get(route('trainer.profile'));

        $response->assertOk();

        // Check if correct data was passed to the view
        $response->assertViewHas('ratingCounts', function ($ratingCounts) {
            return $ratingCounts[5] === 1 && $ratingCounts[3] === 1 && $ratingCounts[4] === 0;
        });

        $response->assertViewHas('ratingPercentages', function ($ratingPercentages) {
            return $ratingPercentages[5] === 50 && $ratingPercentages[3] === 50 && $ratingPercentages[4] === 0;
        });

        // Avg speaker_rating is (5+3)/2 = 4.0
        // Avg overall rating is (5+4)/2 = 4.5
        $response->assertViewHas('aspectRatings', function ($aspectRatings) {
            return $aspectRatings['penyampaian_materi'] === 4.0
                && $aspectRatings['penguasaan_materi'] === 4.1 // 4.0 + 0.1
                && $aspectRatings['interaktivitas'] === 3.9      // 4.0 - 0.1
                && $aspectRatings['manfaat_aplikasi'] === 4.5;
        });

        // Assert dynamic HTML structure is rendered in response
        $response->assertSee('50%'); // Percentage text in rating bar
        $response->assertSee('width: 50%;'); // Width in style of bar-fill
        $response->assertSee('4.0'); // Aspect rating text for Penyampaian Materi
        $response->assertSee('4.1'); // Aspect rating text for Penguasaan Materi
        $response->assertSee('3.9'); // Aspect rating text for Interaktivitas
        $response->assertSee('4.5'); // Aspect rating text for Manfaat & Aplikasi
    }
}

