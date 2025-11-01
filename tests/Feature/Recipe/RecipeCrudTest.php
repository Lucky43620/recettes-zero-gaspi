<?php

namespace Tests\Feature\Recipe;

use App\Enums\Difficulty;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_create_recipes(): void
    {
        $response = $this->get('/recipes/create');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_view_create_recipe_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/recipes/create');
        $response->assertStatus(200);
    }

    public function test_authenticated_users_can_create_recipe(): void
    {
        $user = User::factory()->create();
        $ingredient = Ingredient::factory()->create();
        $unit = Unit::firstOrCreate(['code' => 'g'], ['label' => 'gramme']);

        $response = $this->actingAs($user)->post('/recipes', [
            'title' => 'Test Recipe',
            'summary' => 'A test recipe summary',
            'servings' => 4,
            'prep_minutes' => 15,
            'cook_minutes' => 30,
            'difficulty' => Difficulty::EASY->value,
            'is_public' => true,
            'ingredients' => [
                [
                    'ingredient_id' => $ingredient->id,
                    'quantity' => 200,
                    'unit_code' => $unit->code,
                ]
            ],
            'steps' => [
                ['text' => 'First step'],
                ['text' => 'Second step'],
            ],
        ]);

        $this->assertDatabaseHas('recipes', [
            'title' => 'Test Recipe',
            'author_id' => $user->id,
        ]);

        $recipe = Recipe::where('title', 'Test Recipe')->first();
        $response->assertRedirect("/recipes/{$recipe->slug}?from=my");
    }

    public function test_recipe_requires_title(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/recipes', [
            'title' => '',
            'summary' => 'A test recipe',
            'servings' => 4,
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_users_can_view_their_own_recipes(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user, 'author')->create(['is_public' => false]);

        $response = $this->actingAs($user)->get("/recipes/{$recipe->slug}");
        $response->assertStatus(200);
    }

    public function test_users_can_view_public_recipes(): void
    {
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $response = $this->get("/recipes/{$recipe->slug}");
        $response->assertStatus(200);
    }

    public function test_users_cannot_view_others_private_recipes(): void
    {
        $author = User::factory()->create();
        $otherUser = User::factory()->create();
        $recipe = Recipe::factory()->for($author, 'author')->create(['is_public' => false]);

        $response = $this->actingAs($otherUser)->get("/recipes/{$recipe->slug}");
        $response->assertStatus(403);
    }

    public function test_users_can_edit_their_own_recipes(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user, 'author')->create();

        $response = $this->actingAs($user)->get("/recipes/{$recipe->slug}/edit");
        $response->assertStatus(200);
    }

    public function test_users_cannot_edit_others_recipes(): void
    {
        $author = User::factory()->create();
        $otherUser = User::factory()->create();
        $recipe = Recipe::factory()->for($author, 'author')->create();

        $response = $this->actingAs($otherUser)->get("/recipes/{$recipe->slug}/edit");
        $response->assertStatus(403);
    }

    public function test_users_can_update_their_own_recipes(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user, 'author')->create(['title' => 'Original Title']);

        $response = $this->actingAs($user)->put("/recipes/{$recipe->slug}", [
            'title' => 'Updated Title',
            'summary' => $recipe->summary,
            'servings' => $recipe->servings,
            'prep_minutes' => $recipe->prep_minutes,
            'cook_minutes' => $recipe->cook_minutes,
            'difficulty' => $recipe->difficulty->value,
            'is_public' => $recipe->is_public,
            'ingredients' => [],
            'steps' => [],
        ]);

        $this->assertDatabaseHas('recipes', [
            'id' => $recipe->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_users_can_delete_their_own_recipes(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user, 'author')->create();

        $response = $this->actingAs($user)->delete("/recipes/{$recipe->slug}");

        $this->assertDatabaseMissing('recipes', ['id' => $recipe->id]);
        $response->assertRedirect('/my-recipes');
    }

    public function test_users_cannot_delete_others_recipes(): void
    {
        $author = User::factory()->create();
        $otherUser = User::factory()->create();
        $recipe = Recipe::factory()->for($author, 'author')->create();

        $response = $this->actingAs($otherUser)->delete("/recipes/{$recipe->slug}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('recipes', ['id' => $recipe->id]);
    }

    public function test_recipe_list_shows_only_public_recipes(): void
    {
        Recipe::factory()->create(['is_public' => true, 'title' => 'Public Recipe']);
        Recipe::factory()->create(['is_public' => false, 'title' => 'Private Recipe']);

        $response = $this->get('/recipes');

        $response->assertStatus(200);
        $response->assertSee('Public Recipe');
        $response->assertDontSee('Private Recipe');
    }
}
