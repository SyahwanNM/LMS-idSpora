<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Team;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LombaTeamRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_user_can_create_team_with_correct_data()
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $event = Event::create([
            'title' => 'Lomba Hackathon Nasional',
            'image' => 'lomba.png',
            'speaker' => 'Speaker A',
            'description' => 'Description Lomba',
            'location' => 'Online',
            'price' => 150000.00,
            'event_time' => '09:00:00',
            'event_date' => now()->addDays(5)->format('Y-m-d'),
            'jenis' => 'Lomba',
            'lomba_kategori' => 'team',
            'max_team_members' => 3,
            'is_published' => 1,
        ]);

        $response = $this->actingAs($user)
            ->post(route('events.create-team', $event), [
                'team_name' => 'Gryffindor Devs',
                'full_name' => 'John Doe',
                'university_origin' => 'Harvard University',
                'institution_location' => 'Cambridge, MA',
                'whatsapp_number' => '081234567890',
                'info_source' => 'Website',
                'educational_background' => "Bachelor's Degree",
            ]);

        $response->assertRedirect(route('events.registered.detail', $event));
        $this->assertDatabaseHas('teams', [
            'event_id' => $event->id,
            'name' => 'Gryffindor Devs',
            'leader_id' => $user->id,
            'status' => 'pending',
        ]);

        $team = Team::where('event_id', $event->id)->where('leader_id', $user->id)->first();
        $this->assertNotNull($team);
        $this->assertEquals(6, strlen($team->code));

        $this->assertDatabaseHas('event_registrations', [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'team_id' => $team->id,
            'is_team_leader' => 1,
            'status' => 'pending',
        ]);
    }

    public function test_user_cannot_create_team_if_already_registered()
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $event = Event::create([
            'title' => 'Lomba Hackathon Nasional',
            'image' => 'lomba.png',
            'speaker' => 'Speaker A',
            'description' => 'Description Lomba',
            'location' => 'Online',
            'price' => 150000.00,
            'event_time' => '09:00:00',
            'event_date' => now()->addDays(5)->format('Y-m-d'),
            'jenis' => 'Lomba',
            'lomba_kategori' => 'team',
            'max_team_members' => 3,
            'is_published' => 1,
        ]);

        // Pre-register user
        EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => 'active',
            'registration_code' => 'EVT1-ABCDEF',
        ]);

        $response = $this->actingAs($user)
            ->from(route('events.registered.detail', $event))
            ->post(route('events.create-team', $event), [
                'team_name' => 'Slytherin Devs',
                'full_name' => 'John Doe',
                'university_origin' => 'Harvard University',
                'institution_location' => 'Cambridge, MA',
                'whatsapp_number' => '081234567890',
                'info_source' => 'Website',
                'educational_background' => "Bachelor's Degree",
            ]);

        $response->assertRedirect(route('events.registered.detail', $event));
        $this->assertDatabaseMissing('teams', [
            'name' => 'Slytherin Devs',
        ]);
    }

    public function test_user_can_join_team_using_valid_code()
    {
        $leader = User::factory()->create(['role' => 'user']);
        $member = User::factory()->create(['role' => 'user']);

        $event = Event::create([
            'title' => 'Lomba Hackathon Nasional',
            'image' => 'lomba.png',
            'speaker' => 'Speaker A',
            'description' => 'Description Lomba',
            'location' => 'Online',
            'price' => 150000.00,
            'event_time' => '09:00:00',
            'event_date' => now()->addDays(5)->format('Y-m-d'),
            'jenis' => 'Lomba',
            'lomba_kategori' => 'team',
            'max_team_members' => 3,
            'is_published' => 1,
        ]);

        // Leader creates a team
        $team = Team::create([
            'event_id' => $event->id,
            'name' => 'Ravenclaw Devs',
            'code' => 'RVNCLW',
            'leader_id' => $leader->id,
            'status' => 'pending',
        ]);

        EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $leader->id,
            'team_id' => $team->id,
            'is_team_leader' => true,
            'status' => 'pending',
            'registration_code' => 'EVT1-LEADER',
        ]);

        // Member joins the team
        $response = $this->actingAs($member)
            ->post(route('events.join-team', $event), [
                'team_code' => 'RVNCLW',
                'full_name' => 'Jane Doe',
                'university_origin' => 'MIT',
                'institution_location' => 'Boston, MA',
                'whatsapp_number' => '081298765432',
                'info_source' => 'Website',
                'educational_background' => 'Diploma',
            ]);

        $response->assertRedirect(route('events.registered.detail', $event));
        $this->assertDatabaseHas('event_registrations', [
            'event_id' => $event->id,
            'user_id' => $member->id,
            'team_id' => $team->id,
            'is_team_leader' => 0,
            'status' => 'pending',
        ]);
    }

    public function test_user_cannot_join_full_team()
    {
        $leader = User::factory()->create(['role' => 'user']);
        $member1 = User::factory()->create(['role' => 'user']);
        $member2 = User::factory()->create(['role' => 'user']);

        $event = Event::create([
            'title' => 'Lomba Hackathon Nasional',
            'image' => 'lomba.png',
            'speaker' => 'Speaker A',
            'description' => 'Description Lomba',
            'location' => 'Online',
            'price' => 150000.00,
            'event_time' => '09:00:00',
            'event_date' => now()->addDays(5)->format('Y-m-d'),
            'jenis' => 'Lomba',
            'lomba_kategori' => 'team',
            'max_team_members' => 2, // Max size is 2 (Leader + 1 Member)
            'is_published' => 1,
        ]);

        $team = Team::create([
            'event_id' => $event->id,
            'name' => 'Duo Devs',
            'code' => 'DUODEV',
            'leader_id' => $leader->id,
            'status' => 'pending',
        ]);

        EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $leader->id,
            'team_id' => $team->id,
            'is_team_leader' => true,
            'status' => 'pending',
            'registration_code' => 'EVT1-LEADER',
        ]);

        // First member joins -> team is now full
        EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $member1->id,
            'team_id' => $team->id,
            'is_team_leader' => false,
            'status' => 'pending',
            'registration_code' => 'EVT1-MEMBER1',
        ]);

        // Second member tries to join -> should be rejected
        $response = $this->actingAs($member2)
            ->from(route('events.registered.detail', $event))
            ->post(route('events.join-team', $event), [
                'team_code' => 'DUODEV',
                'full_name' => 'Jane Doe 2',
                'university_origin' => 'MIT',
                'institution_location' => 'Boston, MA',
                'whatsapp_number' => '081298765432',
                'info_source' => 'Website',
                'educational_background' => 'Diploma',
            ]);

        $response->assertRedirect(route('events.registered.detail', $event));
        $this->assertDatabaseMissing('event_registrations', [
            'event_id' => $event->id,
            'user_id' => $member2->id,
            'team_id' => $team->id,
        ]);
    }

    public function test_checkout_gate_blocks_non_leader_members()
    {
        $leader = User::factory()->create(['role' => 'user']);
        $member = User::factory()->create(['role' => 'user']);

        $event = Event::create([
            'title' => 'Lomba Hackathon Nasional',
            'image' => 'lomba.png',
            'speaker' => 'Speaker A',
            'description' => 'Description Lomba',
            'location' => 'Online',
            'price' => 150000.00,
            'event_time' => '09:00:00',
            'event_date' => now()->addDays(5)->format('Y-m-d'),
            'jenis' => 'Lomba',
            'lomba_kategori' => 'team',
            'max_team_members' => 3,
            'is_published' => 1,
        ]);

        $team = Team::create([
            'event_id' => $event->id,
            'name' => 'Trial Team',
            'code' => 'TRT123',
            'leader_id' => $leader->id,
            'status' => 'pending',
        ]);

        $leaderReg = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $leader->id,
            'team_id' => $team->id,
            'is_team_leader' => true,
            'status' => 'pending',
            'registration_code' => 'EVT1-LEADER',
        ]);

        $memberReg = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $member->id,
            'team_id' => $team->id,
            'is_team_leader' => false,
            'status' => 'pending',
            'registration_code' => 'EVT1-MEMBER',
        ]);

        // Leader can access checkout page
        $responseLeader = $this->actingAs($leader)
            ->get(route('payment', $event));
        $responseLeader->assertStatus(200);

        // Member is blocked and redirected
        $responseMember = $this->actingAs($member)
            ->get(route('payment', $event));
        $responseMember->assertRedirect(route('events.registered.detail', $event));
    }

    public function test_status_propagation_to_team_members_on_leader_activation()
    {
        $leader = User::factory()->create(['role' => 'user']);
        $member = User::factory()->create(['role' => 'user']);

        $event = Event::create([
            'title' => 'Lomba Hackathon',
            'image' => 'lomba.png',
            'speaker' => 'Test Speaker',
            'description' => 'Test Description',
            'location' => 'Online',
            'price' => 150000.00,
            'event_time' => '09:00:00',
            'event_date' => now()->addDays(5)->format('Y-m-d'),
            'jenis' => 'Lomba',
            'lomba_kategori' => 'team',
            'max_team_members' => 2,
            'is_published' => 1,
        ]);

        $team = Team::create([
            'event_id' => $event->id,
            'name' => 'Dynamic Devs',
            'code' => 'DYNDEV',
            'leader_id' => $leader->id,
            'status' => 'pending',
        ]);

        $leaderReg = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $leader->id,
            'team_id' => $team->id,
            'is_team_leader' => true,
            'status' => 'pending',
            'registration_code' => 'EVT1-LEADER',
        ]);

        $memberReg = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $member->id,
            'team_id' => $team->id,
            'is_team_leader' => false,
            'status' => 'pending',
            'registration_code' => 'EVT1-MEMBER',
        ]);

        // Update leader status to active
        $leaderReg->status = 'active';
        $leaderReg->save();

        // Verify member registration and team statuses propagate to active
        $memberReg->refresh();
        $team->refresh();

        $this->assertEquals('active', $memberReg->status);
        $this->assertEquals('active', $team->status);
    }

    public function test_team_submissions_are_restricted_to_leader_and_propagate_to_members()
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $leader = User::factory()->create(['role' => 'user']);
        $member = User::factory()->create(['role' => 'user']);

        $event = Event::create([
            'title' => 'Lomba Hackathon',
            'image' => 'lomba.png',
            'speaker' => 'Test Speaker',
            'description' => 'Test Description',
            'location' => 'Online',
            'price' => 0,
            'event_time' => '09:00:00',
            'event_date' => now()->addDays(5)->format('Y-m-d'),
            'start_submission' => now()->subDay(),
            'until_submission' => now()->addDay(),
            'announcement_date' => now()->subDay(),
            'until_submission_2' => now()->addDay(),
            'jenis' => 'Lomba',
            'lomba_kategori' => 'team',
            'max_team_members' => 2,
            'is_published' => 1,
        ]);

        $team = Team::create([
            'event_id' => $event->id,
            'name' => 'Submission Team',
            'code' => 'SUBMIT',
            'leader_id' => $leader->id,
            'status' => 'active',
        ]);

        $leaderReg = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $leader->id,
            'team_id' => $team->id,
            'is_team_leader' => true,
            'status' => 'active',
            'registration_code' => 'EVT1-LEADER',
        ]);

        $memberReg = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $member->id,
            'team_id' => $team->id,
            'is_team_leader' => false,
            'status' => 'active',
            'registration_code' => 'EVT1-MEMBER',
        ]);

        // 1. Verify Member (Non-Leader) cannot upload initial submission
        $file = \Illuminate\Http\UploadedFile::fake()->create('initial.pdf', 500, 'application/pdf');
        $responseMember = $this->actingAs($member)
            ->post(route('events.submit.initial', $event), [
                'submission_file' => $file,
            ]);
        $responseMember->assertSessionHas('error', 'Hanya Ketua Tim yang dapat mengunggah submission.');

        // 2. Verify Leader can upload initial submission and it propagates to member
        $responseLeader = $this->actingAs($leader)
            ->post(route('events.submit.initial', $event), [
                'submission_file' => $file,
            ]);
        $responseLeader->assertSessionHas('success');

        $leaderReg->refresh();
        $memberReg->refresh();

        $this->assertNotNull($leaderReg->submission_path);
        $this->assertEquals($leaderReg->submission_path, $memberReg->submission_path);
        $this->assertEquals('pending', $memberReg->submission_status);

        // 3. Verify Member (Non-Leader) cannot upload Stage 2 final submission
        $file2 = \Illuminate\Http\UploadedFile::fake()->create('final.pdf', 500, 'application/pdf');
        $responseMember2 = $this->actingAs($member)
            ->post(route('events.submit.second', $event), [
                'submission_file_2' => $file2,
            ]);
        $responseMember2->assertSessionHas('error', 'Hanya Ketua Tim yang dapat mengunggah submission.');

        // Set status to lolos first so stage 2 is allowed
        $leaderReg->submission_status = 'lolos';
        $leaderReg->save();
        $memberReg->submission_status = 'lolos';
        $memberReg->save();

        // 4. Verify Leader can upload Stage 2 final submission and it propagates to member
        $responseLeader2 = $this->actingAs($leader)
            ->post(route('events.submit.second', $event), [
                'submission_file_2' => $file2,
            ]);
        $responseLeader2->assertSessionHas('success');

        $leaderReg->refresh();
        $memberReg->refresh();

        $this->assertNotNull($leaderReg->submission_path_2);
        $this->assertEquals($leaderReg->submission_path_2, $memberReg->submission_path_2);
    }

    public function test_submission_status_and_payment_propagation_to_team_members()
    {
        $leader = User::factory()->create(['role' => 'user']);
        $member = User::factory()->create(['role' => 'user']);

        $event = Event::create([
            'title' => 'Lomba Hackathon',
            'image' => 'lomba.png',
            'speaker' => 'Test Speaker',
            'description' => 'Test Description',
            'location' => 'Online',
            'price' => 150000.00,
            'price_stage2' => 200000.00,
            'event_time' => '09:00:00',
            'event_date' => now()->addDays(5)->format('Y-m-d'),
            'jenis' => 'Lomba',
            'lomba_kategori' => 'team',
            'max_team_members' => 2,
            'is_published' => 1,
        ]);

        $team = Team::create([
            'event_id' => $event->id,
            'name' => 'Propagation Team',
            'code' => 'PROPAG',
            'leader_id' => $leader->id,
            'status' => 'active',
        ]);

        $leaderReg = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $leader->id,
            'team_id' => $team->id,
            'is_team_leader' => true,
            'status' => 'active',
            'registration_code' => 'EVT1-LEADER',
            'submission_status' => 'pending',
        ]);

        $memberReg = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $member->id,
            'team_id' => $team->id,
            'is_team_leader' => false,
            'status' => 'active',
            'registration_code' => 'EVT1-MEMBER',
            'submission_status' => 'pending',
        ]);

        // Act as admin to review submission
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->post(route('admin.events.submissions.review', [$event, $leaderReg]), [
                'status' => 'lolos',
                'submission_notes' => 'Luar biasa, tingkatkan di tahap 2!',
            ]);

        $response->assertRedirect();
        
        $leaderReg->refresh();
        $memberReg->refresh();

        // Assert leader's registration status propagated
        $this->assertEquals('lolos', $leaderReg->submission_status);
        $this->assertEquals('Luar biasa, tingkatkan di tahap 2!', $leaderReg->submission_notes);
        $this->assertEquals('pending', $leaderReg->stage2_payment_status);

        // Assert member's registration updated via propagation
        $this->assertEquals('lolos', $memberReg->submission_status);
        $this->assertEquals('Luar biasa, tingkatkan di tahap 2!', $memberReg->submission_notes);
        $this->assertEquals('pending', $memberReg->stage2_payment_status);

        // Simulate payment settled on leader
        $leaderReg->stage2_payment_status = 'settled';
        $leaderReg->stage2_payment_at = now();
        $leaderReg->save();

        $memberReg->refresh();
        $this->assertEquals('settled', $memberReg->stage2_payment_status);
        $this->assertNotNull($memberReg->stage2_payment_at);
    }

    public function test_joining_member_inherits_leader_submission_and_status()
    {
        $leader = User::factory()->create(['role' => 'user']);
        $member = User::factory()->create(['role' => 'user']);

        $event = Event::create([
            'title' => 'Lomba Hackathon',
            'image' => 'lomba.png',
            'speaker' => 'Test Speaker',
            'description' => 'Test Description',
            'location' => 'Online',
            'price' => 150000.00,
            'price_stage2' => 200000.00,
            'event_time' => '09:00:00',
            'event_date' => now()->addDays(5)->format('Y-m-d'),
            'jenis' => 'Lomba',
            'lomba_kategori' => 'team',
            'max_team_members' => 3,
            'is_published' => 1,
        ]);

        $team = Team::create([
            'event_id' => $event->id,
            'name' => 'Join Inheritance Team',
            'code' => 'JINHER',
            'leader_id' => $leader->id,
            'status' => 'pending',
        ]);

        // Leader registers
        $leaderReg = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $leader->id,
            'team_id' => $team->id,
            'is_team_leader' => true,
            'status' => 'pending',
            'registration_code' => 'EVT1-LEADER',
        ]);

        // Leader uploads initial submission
        $leaderReg->submission_path = 'submissions/test_initial.pdf';
        $leaderReg->submission_uploaded_at = now();
        $leaderReg->submission_status = 'lolos';
        $leaderReg->submission_notes = 'Sangat Bagus!';
        $leaderReg->stage2_payment_status = 'pending';
        $leaderReg->save();

        // Now, member joins team via joinTeam route
        $response = $this->actingAs($member)
            ->post(route('events.join-team', $event), [
                'team_code' => 'JINHER',
                'full_name' => 'Jane Doe 3',
                'university_origin' => 'MIT',
                'institution_location' => 'Boston, MA',
                'whatsapp_number' => '081298765432',
                'info_source' => 'Website',
                'educational_background' => 'Diploma',
            ]);

        $response->assertRedirect(route('events.registered.detail', $event));

        // Find member registration and assert inheritance
        $memberReg = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $member->id)
            ->first();

        $this->assertNotNull($memberReg);
        $this->assertEquals($team->id, $memberReg->team_id);
        $this->assertFalse((bool) $memberReg->is_team_leader);
        
        // Assert fields are inherited from leader
        $this->assertEquals('pending', $memberReg->status); // matches leader status
        $this->assertEquals('submissions/test_initial.pdf', $memberReg->submission_path);
        $this->assertEquals('lolos', $memberReg->submission_status);
        $this->assertEquals('Sangat Bagus!', $memberReg->submission_notes);
        $this->assertEquals('pending', $memberReg->stage2_payment_status);
    }

    public function test_member_view_falls_back_to_leader_submission()
    {
        $leader = User::factory()->create(['role' => 'user']);
        $member = User::factory()->create(['role' => 'user']);

        $event = Event::create([
            'title' => 'Lomba Hackathon',
            'image' => 'lomba.png',
            'speaker' => 'Test Speaker',
            'description' => 'Test Description',
            'location' => 'Online',
            'price' => 0, // free stage 1
            'price_stage2' => 200000.00,
            'event_time' => '09:00:00',
            'event_date' => now()->addDays(5)->format('Y-m-d'),
            'start_submission' => now()->subDays(2),
            'until_submission' => now()->addDays(2),
            'announcement_date' => now()->addDays(3),
            'until_submission_2' => now()->addDays(5),
            'jenis' => 'Lomba',
            'lomba_kategori' => 'team',
            'max_team_members' => 3,
            'is_published' => 1,
        ]);

        $team = Team::create([
            'event_id' => $event->id,
            'name' => 'Fallback View Team',
            'code' => 'FVVIEW',
            'leader_id' => $leader->id,
            'status' => 'active',
        ]);

        // Leader registers
        $leaderReg = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $leader->id,
            'team_id' => $team->id,
            'is_team_leader' => true,
            'status' => 'active',
            'registration_code' => 'EVT1-LEADER',
        ]);

        // Leader uploads initial submission and is qualified
        $leaderReg->submission_path = 'submissions/test_initial.pdf';
        $leaderReg->submission_uploaded_at = now();
        $leaderReg->submission_status = 'lolos';
        $leaderReg->submission_notes = 'Sangat Bagus!';
        $leaderReg->stage2_payment_status = 'pending';
        $leaderReg->save();

        // Member registers
        $memberReg = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $member->id,
            'team_id' => $team->id,
            'is_team_leader' => false,
            'status' => 'active',
            'registration_code' => 'EVT1-MEMBER',
        ]);

        // Direct update to DB to bypass creating observer and clear out the fields
        DB::table('event_registrations')->where('id', $memberReg->id)->update([
            'submission_path' => null,
            'submission_status' => 'pending',
            'submission_notes' => null,
            'stage2_payment_status' => 'not_required',
        ]);

        // Access the detail page as the member
        $response = $this->actingAs($member)
            ->get(route('events.registered.detail', $event));

        $response->assertStatus(200);
        
        // Assert that the page renders the leader's submission data as fallback
        $response->assertSee('Qualified to Next Stage');
        $response->assertSee('Sangat Bagus!');
        $response->assertSee('test_initial.pdf');
    }
}
