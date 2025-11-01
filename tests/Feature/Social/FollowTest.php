<?php

namespace Tests\Feature\Social;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_follow_users(): void
    {
        $user = User::factory()->create();

        $response = $this->post("/users/{$user->id}/follow");
        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_follow_other_users(): void
    {
        $follower = User::factory()->create();
        $userToFollow = User::factory()->create();

        $response = $this->actingAs($follower)->post("/users/{$userToFollow->id}/follow");

        $response->assertStatus(302);
        $this->assertTrue($follower->following->contains($userToFollow));
    }

    public function test_users_cannot_follow_themselves(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post("/users/{$user->id}/follow");

        $response->assertStatus(302);
        $this->assertFalse($user->following->contains($user));
    }

    public function test_users_can_unfollow_other_users(): void
    {
        $follower = User::factory()->create();
        $userToUnfollow = User::factory()->create();

        $follower->following()->attach($userToUnfollow->id);

        $response = $this->actingAs($follower)->delete("/users/{$userToUnfollow->id}/unfollow");

        $response->assertStatus(302);
        $this->assertFalse($follower->fresh()->following->contains($userToUnfollow));
    }

    public function test_following_same_user_twice_does_not_duplicate(): void
    {
        $follower = User::factory()->create();
        $userToFollow = User::factory()->create();

        $this->actingAs($follower)->post("/users/{$userToFollow->id}/follow");
        $this->actingAs($follower)->post("/users/{$userToFollow->id}/follow");

        $this->assertEquals(1, $follower->fresh()->following()->count());
    }

    public function test_unfollowing_not_followed_user_does_not_error(): void
    {
        $follower = User::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($follower)->delete("/users/{$user->id}/unfollow");

        $response->assertStatus(302);
    }

    public function test_followers_count_is_accurate(): void
    {
        $user = User::factory()->create();
        $follower1 = User::factory()->create();
        $follower2 = User::factory()->create();
        $follower3 = User::factory()->create();

        $follower1->following()->attach($user->id);
        $follower2->following()->attach($user->id);
        $follower3->following()->attach($user->id);

        $this->assertEquals(3, $user->followers()->count());
    }

    public function test_following_count_is_accurate(): void
    {
        $user = User::factory()->create();
        $following1 = User::factory()->create();
        $following2 = User::factory()->create();

        $user->following()->attach([$following1->id, $following2->id]);

        $this->assertEquals(2, $user->following()->count());
    }
}
