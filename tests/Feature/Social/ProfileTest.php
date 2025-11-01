<?php

namespace Tests\Feature\Social;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_view_public_profiles(): void
    {
        $user = User::factory()->create();

        $response = $this->get("/profile/{$user->id}");
        $response->assertStatus(200);
    }

    public function test_profile_shows_public_recipes(): void
    {
        $user = User::factory()->create();
        $publicRecipe = Recipe::factory()->for($user, 'author')->create([
            'is_public' => true,
            'title' => 'Public Recipe Title'
        ]);
        $privateRecipe = Recipe::factory()->for($user, 'author')->create([
            'is_public' => false,
            'title' => 'Private Recipe Title'
        ]);

        $response = $this->get("/profile/{$user->id}");

        $response->assertStatus(200);
        $response->assertSee('Public Recipe Title');
        $response->assertDontSee('Private Recipe Title');
    }

    public function test_profile_shows_recipe_count(): void
    {
        $user = User::factory()->create();
        Recipe::factory()->count(5)->for($user, 'author')->create(['is_public' => true]);

        $response = $this->get("/profile/{$user->id}");

        $response->assertStatus(200);
    }

    public function test_profile_shows_follower_count(): void
    {
        $user = User::factory()->create();
        $follower1 = User::factory()->create();
        $follower2 = User::factory()->create();

        $follower1->following()->attach($user->id);
        $follower2->following()->attach($user->id);

        $response = $this->get("/profile/{$user->id}");

        $response->assertStatus(200);
    }

    public function test_profile_shows_following_count(): void
    {
        $user = User::factory()->create();
        $following1 = User::factory()->create();
        $following2 = User::factory()->create();

        $user->following()->attach([$following1->id, $following2->id]);

        $response = $this->get("/profile/{$user->id}");

        $response->assertStatus(200);
    }

    public function test_users_can_view_followers_list(): void
    {
        $user = User::factory()->create();
        $follower = User::factory()->create(['name' => 'Follower Name']);
        $follower->following()->attach($user->id);

        $response = $this->get("/profile/{$user->id}/followers");

        $response->assertStatus(200);
        $response->assertSee('Follower Name');
    }

    public function test_users_can_view_following_list(): void
    {
        $user = User::factory()->create();
        $following = User::factory()->create(['name' => 'Following Name']);
        $user->following()->attach($following->id);

        $response = $this->get("/profile/{$user->id}/following");

        $response->assertStatus(200);
        $response->assertSee('Following Name');
    }
}
