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

        $response->assertRedirect(route('trainer.profile'));
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

        $response->assertRedirect(route('trainer.profile'));
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
}
