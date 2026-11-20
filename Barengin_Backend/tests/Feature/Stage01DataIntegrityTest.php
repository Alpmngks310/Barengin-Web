<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\ActivityParticipant;
use App\Models\Category;
use App\Models\JoinRequest;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class Stage01DataIntegrityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Build testing schema in SQLite in-memory database
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamp('adult_confirmed_at')->nullable();
            $table->timestamp('terms_accepted_at')->nullable();
            $table->timestamp('privacy_accepted_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();
            $table->string('display_name');
            $table->text('bio')->nullable();
            $table->string('avatar_path')->nullable();
            $table->string('area')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->char('country_code', 2)->default('ID');
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon_key')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('host_id');
            $table->unsignedBigInteger('category_id');
            $table->string('title');
            $table->text('description');
            $table->string('participant_level')->default('all_levels');
            $table->datetime('starts_at');
            $table->datetime('ends_at');
            $table->string('timezone')->default('Asia/Jakarta');
            $table->string('venue_name');
            $table->string('address');
            $table->string('area')->nullable();
            $table->string('city');
            $table->string('province')->nullable();
            $table->char('country_code', 2)->default('ID');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedTinyInteger('capacity');
            $table->unsignedBigInteger('budget_amount')->default(0);
            $table->char('currency', 3)->default('IDR');
            $table->string('cover_image_path')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('join_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_id');
            $table->unsignedBigInteger('user_id');
            $table->string('message')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('decision_note')->nullable();
            $table->timestamps();
            $table->index(
                ['activity_id', 'user_id'],
                'idx_join_requests_activity_user'
            );
        });

        Schema::create('activity_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role')->default('member');
            $table->string('status')->default('joined');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('left_at')->nullable();
            $table->unsignedBigInteger('removed_by')->nullable();
            $table->string('removal_note')->nullable();
            $table->timestamps();
            $table->unique(['activity_id', 'user_id']);
        });

        Schema::create('user_blocks', function (Blueprint $table) {
            $table->unsignedBigInteger('blocker_id');
            $table->unsignedBigInteger('blocked_user_id');
            $table->timestamp('created_at')->nullable();
            $table->primary(['blocker_id', 'blocked_user_id']);
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    private function createUser(string $name, string $email): User
    {
        $user = User::create([
            'name' => $name,
            'username' => strtolower(str_replace(' ', '', $name)),
            'email' => $email,
            'password' => Hash::make('password123'),
            'adult_confirmed_at' => now(),
            'terms_accepted_at' => now(),
            'privacy_accepted_at' => now(),
            'is_active' => true,
        ]);

        $user->profile()->create([
            'display_name' => $name,
            'country_code' => 'ID',
        ]);

        return $user;
    }

    private function createCategory(): Category
    {
        return Category::create([
            'name' => 'Olahraga',
            'slug' => 'olahraga',
            'is_active' => true,
        ]);
    }

    private function createActivity(User $host, Category $category, int $capacity = 5): Activity
    {
        $activity = Activity::create([
            'host_id' => $host->id,
            'category_id' => $category->id,
            'title' => 'Badminton Santai',
            'description' => 'Main bareng mencari keringat sore-sore.',
            'participant_level' => 'all_levels',
            'starts_at' => now()->addDays(2),
            'ends_at' => now()->addDays(2)->addHours(2),
            'venue_name' => 'GOR Dago',
            'address' => 'Jl. Ir. H. Djuanda No. 10',
            'city' => 'Bandung',
            'capacity' => $capacity,
            'status' => 'published',
            'published_at' => now(),
        ]);

        $activity->participants()->create([
            'user_id' => $host->id,
            'role' => 'host',
            'status' => 'joined',
            'joined_at' => now(),
        ]);

        return $activity;
    }

    /**
     * Test A — Cancel Join Request
     */
    public function test_a_participant_can_cancel_pending_request_and_repeat_cancellation_safely(): void
    {
        $host = $this->createUser('Host User', 'host@test.com');
        $member = $this->createUser('Member User', 'member@test.com');
        $category = $this->createCategory();
        $activity = $this->createActivity($host, $category);

        $request = JoinRequest::create([
            'activity_id' => $activity->id,
            'user_id' => $member->id,
            'message' => 'Mau ikutan',
            'status' => 'pending',
        ]);

        // First cancellation
        $token = $member->createToken('test')->plainTextToken;
        $response = $this->withToken($token)->deleteJson("/api/my/join-requests/{$request->id}");
        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Permintaan bergabung berhasil dibatalkan.');

        $request->refresh();
        $this->assertEquals('cancelled', $request->status);

        // Repeat cancellation (idempotent, safe)
        $repeatResponse = $this->withToken($token)->deleteJson("/api/my/join-requests/{$request->id}");
        $repeatResponse->assertOk()
            ->assertJsonPath('success', true);

        // Verify other user cannot cancel
        $stranger = $this->createUser('Stranger User', 'stranger@test.com');
        $strangerToken = $stranger->createToken('test')->plainTextToken;
        $otherRequest = JoinRequest::create([
            'activity_id' => $activity->id,
            'user_id' => 999, // dummy user
            'message' => 'Lain',
            'status' => 'pending',
        ]);
        // stranger is not the owner
        $forbiddenResponse = $this->withToken($strangerToken)->deleteJson("/api/my/join-requests/{$otherRequest->id}");
        $forbiddenResponse->assertForbidden();
    }

    /**
     * Test B — Host Removes Participant
     */
    public function test_b_host_can_remove_participant_and_repeat_removal_is_handled_safely(): void
    {
        $host = $this->createUser('Host B', 'host_b@test.com');
        $member = $this->createUser('Member B', 'member_b@test.com');
        $category = $this->createCategory();
        $activity = $this->createActivity($host, $category);

        $participant = $activity->participants()->create([
            'user_id' => $member->id,
            'role' => 'member',
            'status' => 'joined',
            'joined_at' => now(),
        ]);

        $joinReq = JoinRequest::create([
            'activity_id' => $activity->id,
            'user_id' => $member->id,
            'status' => 'accepted',
        ]);

        $token = $host->createToken('test')->plainTextToken;

        // Host removes member
        $response = $this->withToken($token)->deleteJson("/api/activities/{$activity->id}/participants/{$member->id}", [
            'removal_note' => 'Tidak hadir tanpa kabar',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Peserta berhasil dikeluarkan dari aktivitas.');

        $participant->refresh();
        $this->assertEquals('removed', $participant->status);
        $this->assertEquals($host->id, $participant->removed_by);
        $this->assertNotNull($participant->left_at);
        $this->assertEquals('Tidak hadir tanpa kabar', $participant->removal_note);

        // JoinRequest historical decision must remain accepted.
        $joinReq->refresh();

        $this->assertEquals('accepted', $joinReq->status);
        $this->assertEquals('Tidak hadir tanpa kabar', $joinReq->decision_note);

        // Repeated removal must return HTTP 409.
        $repeatResponse = $this->withToken($token)->deleteJson(
            "/api/activities/{$activity->id}/participants/{$member->id}"
        );

        $repeatResponse->assertStatus(409)
            ->assertJsonPath('success', false)
            ->assertJsonPath(
                'message',
                'Peserta sudah tidak aktif dalam aktivitas ini.'
            );
    }
    public function test_host_cannot_remove_participant_that_does_not_exist(): void
    {
        $host = $this->createUser('Host Missing', 'host_missing@test.com');
        $member = $this->createUser('Member Missing', 'member_missing@test.com');
        $category = $this->createCategory();
        $activity = $this->createActivity($host, $category);

        $token = $host->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->deleteJson(
            "/api/activities/{$activity->id}/participants/{$member->id}"
        );

        $response->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    public function test_host_cannot_remove_participant_who_already_left(): void
    {
        $host = $this->createUser('Host Left', 'host_left@test.com');
        $member = $this->createUser('Member Left', 'member_left@test.com');
        $category = $this->createCategory();
        $activity = $this->createActivity($host, $category);

        $activity->participants()->create([
            'user_id' => $member->id,
            'role' => 'member',
            'status' => 'left',
            'joined_at' => now()->subDay(),
            'left_at' => now(),
        ]);

        $token = $host->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->deleteJson(
            "/api/activities/{$activity->id}/participants/{$member->id}"
        );

        $response->assertStatus(409)
            ->assertJsonPath('success', false);
    }

    /**
     * Test C — Removed Participant Reapplies (Rejected)
     */
    public function test_c_removed_participant_cannot_reapply_for_same_activity(): void
    {
        $host = $this->createUser('Host C', 'host_c@test.com');
        $member = $this->createUser('Member C', 'member_c@test.com');
        $category = $this->createCategory();
        $activity = $this->createActivity($host, $category);

        // Participant was removed by host
        $activity->participants()->create([
            'user_id' => $member->id,
            'role' => 'member',
            'status' => 'removed',
            'left_at' => now(),
            'removed_by' => $host->id,
        ]);

        $token = $member->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->postJson("/api/activities/{$activity->id}/join-requests", [
            'message' => 'Boleh ikutan lagi?',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['errors' => ['activity_id']]);

        $this->assertStringContainsString(
            'dikeluarkan dari aktivitas ini oleh Host',
            $response->json('errors.activity_id.0')
        );

        // Verify no pending request was created
        $this->assertDatabaseMissing('join_requests', [
            'activity_id' => $activity->id,
            'user_id' => $member->id,
            'status' => 'pending',
        ]);
    }

    /**
     * Test D — Rejected Participant Reapplies (Rejected)
     */
    public function test_d_rejected_participant_cannot_reapply_and_rejected_status_preserved(): void
    {
        $host = $this->createUser('Host D', 'host_d@test.com');
        $member = $this->createUser('Member D', 'member_d@test.com');
        $category = $this->createCategory();
        $activity = $this->createActivity($host, $category);

        // Existing rejected join request
        JoinRequest::create([
            'activity_id' => $activity->id,
            'user_id' => $member->id,
            'message' => 'Halo',
            'status' => 'rejected',
            'reviewed_by' => $host->id,
            'decision_note' => 'Maaf penuh',
        ]);

        $token = $member->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->postJson("/api/activities/{$activity->id}/join-requests", [
            'message' => 'Coba lagi dong',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['errors' => ['activity_id']]);

        $this->assertStringContainsString(
            'sebelumnya telah ditolak',
            $response->json('errors.activity_id.0')
        );

        // Verify rejected record remains unchanged
        $this->assertDatabaseHas('join_requests', [
            'activity_id' => $activity->id,
            'user_id' => $member->id,
            'status' => 'rejected',
            'decision_note' => 'Maaf penuh',
        ]);
    }


    /**
     * Test E — Participant Leaves
     */
    public function test_e_participant_can_leave_but_cannot_reapply(): void
    {
        $host = $this->createUser('Host E', 'host_e@test.com');
        $member = $this->createUser('Member E', 'member_e@test.com');
        $category = $this->createCategory();
        $activity = $this->createActivity($host, $category);

        $participant = $activity->participants()->create([
            'user_id' => $member->id,
            'role' => 'member',
            'status' => 'joined',
            'joined_at' => now(),
        ]);

        $joinReq = JoinRequest::create([
            'activity_id' => $activity->id,
            'user_id' => $member->id,
            'status' => 'accepted',
        ]);

        $token = $member->createToken('test')->plainTextToken;

        // Member leaves
        $leaveResponse = $this->withToken($token)
            ->postJson("/api/activities/{$activity->id}/leave");

        $leaveResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Kamu sudah keluar dari aktivitas.');

        $participant->refresh();

        $this->assertEquals('left', $participant->status);
        $this->assertNotNull($participant->left_at);

        // Repeat leave remains safe
        $repeatLeave = $this->withToken($token)
            ->postJson("/api/activities/{$activity->id}/leave");

        $repeatLeave->assertOk()
            ->assertJsonPath('success', true);

        // Participant cannot reapply after leaving
        $reapplyResponse = $this->withToken($token)
            ->postJson("/api/activities/{$activity->id}/join-requests", [
                'message' => 'Mau gabung kembali',
            ]);

        $reapplyResponse->assertUnprocessable()
            ->assertJsonPath('success', false);

        // Original request history must remain accepted
        $joinReq->refresh();
        $this->assertEquals('accepted', $joinReq->status);

        // No new Join Request should be created
        $this->assertSame(
            1,
            JoinRequest::where('activity_id', $activity->id)
                ->where('user_id', $member->id)
                ->count()
        );

        // Participant remains left
        $participant->refresh();
        $this->assertEquals('left', $participant->status);
    }


    /**
     * Test F — Authorization Protections
     */

    public function test_f_authorization_restrictions(): void
    {
        $host = $this->createUser('Host F', 'host_f@test.com');
        $member1 = $this->createUser('Member 1', 'm1@test.com');
        $member2 = $this->createUser('Member 2', 'm2@test.com');

        $category = $this->createCategory();
        $activity = $this->createActivity($host, $category);

        $activity->participants()->create([
            'user_id' => $member1->id,
            'role' => 'member',
            'status' => 'joined',
        ]);

        // 1. Member biasa tidak boleh mengeluarkan peserta lain.
        $m2Token = $member2->createToken('test')->plainTextToken;

        $res1 = $this->withToken($m2Token)->deleteJson(
            "/api/activities/{$activity->id}/participants/{$member1->id}"
        );

        $res1->assertForbidden();

        // 2. Host tidak boleh mengeluarkan dirinya sendiri.
        $hostToken = $host->createToken('test')->plainTextToken;

        // Reset auth guard agar identitas request sebelumnya tidak terbawa.
        $this->app['auth']->forgetGuards();

        $res2 = $this->withToken($hostToken)->deleteJson(
            "/api/activities/{$activity->id}/participants/{$host->id}"
        );

        $res2->assertStatus(422)
            ->assertJsonPath('success', false);

        // 3. Host tidak boleh keluar dari aktivitas miliknya sendiri.
        $this->app['auth']->forgetGuards();

        $res3 = $this->withToken($hostToken)->postJson(
            "/api/activities/{$activity->id}/leave"
        );

        $res3->assertStatus(422)
            ->assertJsonPath('success', false);
    }


    /**
     * Test G — Clean Error Response (No ModelNotFoundException leakage)
     */
    public function test_g_clean_json_error_responses_when_model_not_found(): void
    {
        $user = $this->createUser('User G', 'user_g@test.com');
        $token = $user->createToken('test')->plainTextToken;

        // Missing activity
        $response = $this->withToken($token)->getJson('/api/activities/999999');
        $response->assertStatus(404)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Data tidak ditemukan atau sudah tidak tersedia.');

        $this->assertStringNotContainsString('No query results for model', $response->json('message'));
        $this->assertStringNotContainsString('App\\Models', $response->json('message'));
    }
}
