<?php

namespace Tests\Feature\Social;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_favorite_recipes(): void
    {
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $response = $this->post("/recipes/{$recipe->slug}/favorite");
        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_favorite_recipes(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $response = $this->actingAs($user)->post("/recipes/{$recipe->slug}/favorite");

        $this->assertTrue($user->favorites->contains($recipe));
    }

    public function test_users_can_unfavorite_recipes(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $user->favorites()->attach($recipe->id);

        $response = $this->actingAs($user)->post("/recipes/{$recipe->slug}/favorite");

        $this->assertFalse($user->fresh()->favorites->contains($recipe));
    }

    public function test_favoriting_same_recipe_twice_toggles_it(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $this->actingAs($user)->post("/recipes/{$recipe->slug}/favorite");
        $this->assertTrue($user->fresh()->favorites->contains($recipe));

        $this->actingAs($user)->post("/recipes/{$recipe->slug}/favorite");
        $this->assertFalse($user->fresh()->favorites->contains($recipe));
    }

    public function test_users_can_view_their_favorites(): void
    {
        $user = User::factory()->create();
        $favoriteRecipe = Recipe::factory()->create(['is_public' => true, 'title' => 'Favorite Recipe']);
        $otherRecipe = Recipe::factory()->create(['is_public' => true, 'title' => 'Other Recipe']);

        $user->favorites()->attach($favoriteRecipe->id);

        $response = $this->actingAs($user)->get('/favorites');

        $response->assertStatus(200);
        $response->assertSee('Favorite Recipe');
        $response->assertDontSee('Other Recipe');
    }

    public function test_favorites_page_requires_authentication(): void
    {
        $response = $this->get('/favorites');
        $response->assertRedirect('/login');
    }

    public function test_favorite_count_is_accurate(): void
    {
        $user = User::factory()->create();
        $recipe1 = Recipe::factory()->create(['is_public' => true]);
        $recipe2 = Recipe::factory()->create(['is_public' => true]);
        $recipe3 = Recipe::factory()->create(['is_public' => true]);

        $user->favorites()->attach([$recipe1->id, $recipe2->id, $recipe3->id]);

        $this->assertEquals(3, $user->favorites()->count());
    }
}
