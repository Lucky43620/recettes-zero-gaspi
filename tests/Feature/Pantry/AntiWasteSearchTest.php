<?php

namespace Tests\Feature\Pantry;

use App\Models\Ingredient;
use App\Models\PantryItem;
use App\Models\Recipe;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AntiWasteSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_anti_waste_search(): void
    {
        $response = $this->get('/anti-waste');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_view_anti_waste_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/anti-waste');
        $response->assertStatus(200);
    }

    public function test_api_returns_empty_when_pantry_is_empty(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/recipes/search-with-pantry');

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [],
            'pantry_ingredients_count' => 0,
        ]);
        $response->assertJsonStructure([
            'message',
        ]);
    }

    public function test_api_returns_matching_recipes(): void
    {
        $user = User::factory()->create();

        $ingredient1 = Ingredient::factory()->create(['name' => 'Tomate']);
        $ingredient2 = Ingredient::factory()->create(['name' => 'Oignon']);
        $ingredient3 = Ingredient::factory()->create(['name' => 'Ail']);
        $unit = Unit::firstOrCreate(['code' => 'piece'], ['label' => 'pièce']);

        PantryItem::factory()->for($user)->create(['ingredient_id' => $ingredient1->id]);
        PantryItem::factory()->for($user)->create(['ingredient_id' => $ingredient2->id]);

        $recipe = Recipe::factory()->create(['is_public' => true, 'title' => 'Sauce Tomate']);
        $recipe->ingredients()->attach([
            $ingredient1->id => ['quantity' => 2, 'unit_code' => $unit->code],
            $ingredient2->id => ['quantity' => 1, 'unit_code' => $unit->code],
            $ingredient3->id => ['quantity' => 1, 'unit_code' => $unit->code],
        ]);

        $response = $this->actingAs($user)->getJson('/api/recipes/search-with-pantry');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'slug',
                    'title',
                    'matching_ingredients_count',
                    'missing_ingredients_count',
                    'match_percentage',
                    'matching_ingredients',
                    'missing_ingredients',
                    'can_make_with_pantry',
                ]
            ],
            'pantry_ingredients_count',
        ]);

        $data = $response->json('data.0');
        $this->assertEquals(2, $data['matching_ingredients_count']);
        $this->assertEquals(1, $data['missing_ingredients_count']);
        $this->assertEquals(67, $data['match_percentage']);
        $this->assertFalse($data['can_make_with_pantry']);
    }

    public function test_api_identifies_completable_recipes(): void
    {
        $user = User::factory()->create();

        $ingredient1 = Ingredient::factory()->create(['name' => 'Tomate']);
        $ingredient2 = Ingredient::factory()->create(['name' => 'Oignon']);
        $unit = Unit::firstOrCreate(['code' => 'piece'], ['label' => 'pièce']);

        PantryItem::factory()->for($user)->create(['ingredient_id' => $ingredient1->id]);
        PantryItem::factory()->for($user)->create(['ingredient_id' => $ingredient2->id]);

        $recipe = Recipe::factory()->create(['is_public' => true, 'title' => 'Tomates Oignons']);
        $recipe->ingredients()->attach([
            $ingredient1->id => ['quantity' => 2, 'unit_code' => $unit->code],
            $ingredient2->id => ['quantity' => 1, 'unit_code' => $unit->code],
        ]);

        $response = $this->actingAs($user)->getJson('/api/recipes/search-with-pantry');

        $response->assertStatus(200);

        $data = $response->json('data.0');
        $this->assertEquals(2, $data['matching_ingredients_count']);
        $this->assertEquals(0, $data['missing_ingredients_count']);
        $this->assertEquals(100, $data['match_percentage']);
        $this->assertTrue($data['can_make_with_pantry']);
    }

    public function test_api_only_returns_public_recipes(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $ingredient = Ingredient::factory()->create();
        $unit = Unit::firstOrCreate(['code' => 'piece'], ['label' => 'pièce']);
        PantryItem::factory()->for($user)->create(['ingredient_id' => $ingredient->id]);

        $publicRecipe = Recipe::factory()->for($otherUser, 'author')->create([
            'is_public' => true,
            'title' => 'Public Recipe',
        ]);
        $publicRecipe->ingredients()->attach($ingredient->id, ['quantity' => 1, 'unit_code' => $unit->code]);

        $privateRecipe = Recipe::factory()->for($otherUser, 'author')->create([
            'is_public' => false,
            'title' => 'Private Recipe',
        ]);
        $privateRecipe->ingredients()->attach($ingredient->id, ['quantity' => 1, 'unit_code' => $unit->code]);

        $response = $this->actingAs($user)->getJson('/api/recipes/search-with-pantry');

        $response->assertStatus(200);

        $titles = collect($response->json('data'))->pluck('title')->toArray();
        $this->assertContains('Public Recipe', $titles);
        $this->assertNotContains('Private Recipe', $titles);
    }

    public function test_api_sorts_recipes_by_match_percentage(): void
    {
        $user = User::factory()->create();

        $ing1 = Ingredient::factory()->create();
        $ing2 = Ingredient::factory()->create();
        $ing3 = Ingredient::factory()->create();
        $ing4 = Ingredient::factory()->create();
        $unit = Unit::firstOrCreate(['code' => 'piece'], ['label' => 'pièce']);

        PantryItem::factory()->for($user)->create(['ingredient_id' => $ing1->id]);
        PantryItem::factory()->for($user)->create(['ingredient_id' => $ing2->id]);

        $recipe1 = Recipe::factory()->create(['is_public' => true, 'title' => '50% Match']);
        $recipe1->ingredients()->attach([
            $ing1->id => ['quantity' => 1, 'unit_code' => $unit->code],
            $ing3->id => ['quantity' => 1, 'unit_code' => $unit->code],
        ]);

        $recipe2 = Recipe::factory()->create(['is_public' => true, 'title' => '100% Match']);
        $recipe2->ingredients()->attach([
            $ing1->id => ['quantity' => 1, 'unit_code' => $unit->code],
            $ing2->id => ['quantity' => 1, 'unit_code' => $unit->code],
        ]);

        $response = $this->actingAs($user)->getJson('/api/recipes/search-with-pantry');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertEquals('100% Match', $data[0]['title']);
        $this->assertEquals('50% Match', $data[1]['title']);
    }
}
