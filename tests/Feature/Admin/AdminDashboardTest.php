<?php

namespace Tests\Feature\Admin;

use App\Models\Comment;
use App\Models\Cooksnap;
use App\Models\Rating;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_dashboard_displays_correct_user_statistics(): void
    {
        // Create test data
        User::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Index')
            ->has('stats')
            ->where('stats.totalUsers', 6) // 5 + admin
        );
    }

    public function test_dashboard_displays_correct_recipe_statistics(): void
    {
        Recipe::factory()->count(10)->create(['is_public' => true]);
        Recipe::factory()->count(3)->create(['is_public' => false]);

        $response = $this->actingAs($this->admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('stats.totalRecipes', 13)
            ->where('stats.publicRecipes', 10)
        );
    }

    public function test_dashboard_displays_correct_engagement_statistics(): void
    {
        $user = User::factory()->create();
        $recipe1 = Recipe::factory()->create();
        $recipe2 = Recipe::factory()->create();

        // Create engagement data
        Comment::create(['user_id' => $user->id, 'recipe_id' => $recipe1->id, 'content' => 'Great!']);
        Comment::create(['user_id' => $user->id, 'recipe_id' => $recipe2->id, 'content' => 'Nice!']);

        Cooksnap::create(['user_id' => $user->id, 'recipe_id' => $recipe1->id]);
        Cooksnap::create(['user_id' => $user->id, 'recipe_id' => $recipe2->id]);
        Cooksnap::create(['user_id' => $user->id, 'recipe_id' => $recipe1->id]);

        Rating::create(['user_id' => $user->id, 'recipe_id' => $recipe1->id, 'rating' => 5]);
        Rating::create(['user_id' => $user->id, 'recipe_id' => $recipe2->id, 'rating' => 4]);

        $response = $this->actingAs($this->admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('engagementStats.comments', 2)
            ->where('engagementStats.cooksnaps', 3)
            ->where('engagementStats.total_ratings', 2)
            ->where('engagementStats.avg_rating', 4.5)
        );
    }

    public function test_dashboard_calculates_average_rating_correctly(): void
    {
        $user = User::factory()->create();
        $recipe1 = Recipe::factory()->create();
        $recipe2 = Recipe::factory()->create();
        $recipe3 = Recipe::factory()->create();

        Rating::create(['user_id' => $user->id, 'recipe_id' => $recipe1->id, 'rating' => 5]);
        Rating::create(['user_id' => $user->id, 'recipe_id' => $recipe2->id, 'rating' => 3]);
        Rating::create(['user_id' => $user->id, 'recipe_id' => $recipe3->id, 'rating' => 4]);

        $response = $this->actingAs($this->admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('engagementStats.avg_rating', 4.0)
        );
    }

    public function test_dashboard_handles_no_ratings(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('engagementStats.total_ratings', 0)
            ->where('engagementStats.avg_rating', 0.0)
        );
    }

    public function test_dashboard_shows_top_recipes(): void
    {
        $recipe1 = Recipe::factory()->create([
            'title' => 'Most Popular',
            'rating_avg' => 5.0,
            'rating_count' => 100,
            'is_public' => true,
        ]);

        $recipe2 = Recipe::factory()->create([
            'title' => 'Second Popular',
            'rating_avg' => 4.5,
            'rating_count' => 50,
            'is_public' => true,
        ]);

        $recipe3 = Recipe::factory()->create([
            'title' => 'Less Popular',
            'rating_avg' => 3.0,
            'rating_count' => 10,
            'is_public' => true,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('topRecipes', 3)
            ->where('topRecipes.0.title', 'Most Popular')
            ->where('topRecipes.1.title', 'Second Popular')
        );
    }

    public function test_dashboard_excludes_private_recipes_from_top_recipes(): void
    {
        Recipe::factory()->create([
            'rating_avg' => 5.0,
            'rating_count' => 100,
            'is_public' => false, // Private recipe
        ]);

        Recipe::factory()->create([
            'title' => 'Public Recipe',
            'rating_avg' => 4.0,
            'rating_count' => 50,
            'is_public' => true,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('topRecipes', 1)
            ->where('topRecipes.0.title', 'Public Recipe')
        );
    }

    public function test_dashboard_limits_top_recipes_to_ten(): void
    {
        Recipe::factory()->count(15)->create([
            'is_public' => true,
            'rating_avg' => 4.0,
            'rating_count' => 10,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('topRecipes', 10)
        );
    }
}
