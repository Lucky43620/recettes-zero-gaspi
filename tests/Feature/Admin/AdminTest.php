<?php

namespace Tests\Feature\Admin;

use App\Models\Badge;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_user_count_is_accurate(): void
    {
        User::factory()->count(10)->create();

        $this->assertEquals(10, User::count());
    }

    public function test_recipe_stats_are_accurate(): void
    {
        Recipe::factory()->count(5)->create(['is_public' => true]);
        Recipe::factory()->count(3)->create(['is_public' => false]);

        $this->assertEquals(5, Recipe::where('is_public', true)->count());
        $this->assertEquals(3, Recipe::where('is_public', false)->count());
    }

    public function test_admin_can_delete_users(): void
    {
        $admin = User::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/users/{$user->id}");

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_badges_can_be_retrieved(): void
    {
        Badge::create([
            'key' => 'first-recipe',
            'name' => 'Première recette',
            'description' => 'Créer votre première recette',
            'required_count' => 1,
        ]);

        $this->assertEquals(1, Badge::count());
    }

    public function test_admin_can_create_badges(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/badges', [
            'key' => 'master-chef',
            'name' => 'Master Chef',
            'description' => 'Créer 100 recettes',
            'icon' => '🏆',
            'required_count' => 100,
        ]);

        $this->assertDatabaseHas('badges', [
            'key' => 'master-chef',
            'name' => 'Master Chef',
        ]);
    }

    public function test_admin_can_update_badges(): void
    {
        $admin = User::factory()->create();
        $badge = Badge::create([
            'key' => 'test-badge',
            'name' => 'Test Badge',
            'description' => 'Test',
            'required_count' => 1,
        ]);

        $response = $this->actingAs($admin)->put("/admin/badges/{$badge->id}", [
            'key' => 'test-badge',
            'name' => 'Updated Badge',
            'description' => 'Updated description',
            'required_count' => 5,
        ]);

        $this->assertDatabaseHas('badges', [
            'id' => $badge->id,
            'name' => 'Updated Badge',
        ]);
    }

    public function test_admin_can_delete_badges(): void
    {
        $admin = User::factory()->create();
        $badge = Badge::create([
            'key' => 'test-badge',
            'name' => 'Test Badge',
            'description' => 'Test',
            'required_count' => 1,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/badges/{$badge->id}");

        $this->assertDatabaseMissing('badges', ['id' => $badge->id]);
    }
}
