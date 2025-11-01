<?php

namespace Tests\Feature\MealPlan;

use App\Models\Ingredient;
use App\Models\MealPlan;
use App\Models\Recipe;
use App\Models\ShoppingList;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShoppingListTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_shopping_lists(): void
    {
        $response = $this->get('/shopping-lists');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_view_shopping_lists(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/shopping-lists');
        $response->assertStatus(200);
    }

    public function test_users_can_create_shopping_lists(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/shopping-lists', [
            'name' => 'Weekly Shopping',
        ]);

        $this->assertDatabaseHas('shopping_lists', [
            'user_id' => $user->id,
            'name' => 'Weekly Shopping',
        ]);
    }

    public function test_shopping_list_requires_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/shopping-lists', [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_users_can_delete_shopping_lists(): void
    {
        $user = User::factory()->create();
        $shoppingList = ShoppingList::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete("/shopping-lists/{$shoppingList->id}");

        $this->assertDatabaseMissing('shopping_lists', ['id' => $shoppingList->id]);
    }

    public function test_users_can_generate_shopping_list_from_meal_plan(): void
    {
        $user = User::factory()->create();
        $mealPlan = MealPlan::factory()->for($user)->create();

        $ingredient1 = Ingredient::factory()->create();
        $ingredient2 = Ingredient::factory()->create();
        $unitG = Unit::firstOrCreate(['code' => 'g'], ['label' => 'gramme']);
        $unitPiece = Unit::firstOrCreate(['code' => 'piece'], ['label' => 'pièce']);

        $recipe1 = Recipe::factory()->create(['is_public' => true]);
        $recipe1->ingredients()->attach($ingredient1->id, ['quantity' => 200, 'unit_code' => $unitG->code]);
        $recipe1->ingredients()->attach($ingredient2->id, ['quantity' => 1, 'unit_code' => $unitPiece->code]);

        $recipe2 = Recipe::factory()->create(['is_public' => true]);
        $recipe2->ingredients()->attach($ingredient1->id, ['quantity' => 300, 'unit_code' => $unitG->code]);

        $mealPlan->recipes()->attach($recipe1->id, [
            'meal_type' => 'lunch',
            'planned_date' => now(),
            'servings' => 2,
        ]);
        $mealPlan->recipes()->attach($recipe2->id, [
            'meal_type' => 'dinner',
            'planned_date' => now(),
            'servings' => 2,
        ]);

        $response = $this->actingAs($user)->post("/meal-plans/{$mealPlan->id}/generate-shopping-list");

        $response->assertStatus(302);

        $this->assertDatabaseHas('shopping_lists', [
            'user_id' => $user->id,
        ]);

        $shoppingList = ShoppingList::where('user_id', $user->id)->latest()->first();
        $this->assertGreaterThan(0, $shoppingList->items()->count());
    }

    public function test_users_can_add_items_to_shopping_list(): void
    {
        $user = User::factory()->create();
        $shoppingList = ShoppingList::factory()->for($user)->create();
        $ingredient = Ingredient::factory()->create();
        $unit = Unit::firstOrCreate(['code' => 'g'], ['label' => 'gramme']);

        $response = $this->actingAs($user)->post("/shopping-lists/{$shoppingList->id}/items", [
            'ingredient_id' => $ingredient->id,
            'quantity' => 500,
            'unit_code' => $unit->code,
        ]);

        $this->assertDatabaseHas('shopping_list_items', [
            'shopping_list_id' => $shoppingList->id,
            'ingredient_id' => $ingredient->id,
            'quantity' => 500,
        ]);
    }

    public function test_users_can_update_shopping_list_items(): void
    {
        $user = User::factory()->create();
        $shoppingList = ShoppingList::factory()->for($user)->create();
        $ingredient = Ingredient::factory()->create();
        $unit = Unit::firstOrCreate(['code' => 'g'], ['label' => 'gramme']);

        $shoppingList->items()->create([
            'ingredient_id' => $ingredient->id,
            'quantity' => 500,
            'unit_code' => $unit->code,
            'is_checked' => false,
        ]);

        $item = $shoppingList->items()->first();

        $response = $this->actingAs($user)->put("/shopping-list-items/{$item->id}", [
            'quantity' => 1000,
            'unit_code' => 'g',
            'is_checked' => true,
        ]);

        $this->assertDatabaseHas('shopping_list_items', [
            'id' => $item->id,
            'quantity' => 1000,
            'is_checked' => true,
        ]);
    }

    public function test_users_can_check_off_items(): void
    {
        $user = User::factory()->create();
        $shoppingList = ShoppingList::factory()->for($user)->create();
        $ingredient = Ingredient::factory()->create();
        $unit = Unit::firstOrCreate(['code' => 'g'], ['label' => 'gramme']);

        $item = $shoppingList->items()->create([
            'ingredient_id' => $ingredient->id,
            'quantity' => 500,
            'unit_code' => $unit->code,
            'is_checked' => false,
        ]);

        $response = $this->actingAs($user)->put("/shopping-list-items/{$item->id}", [
            'quantity' => $item->quantity,
            'unit_code' => $item->unit_code,
            'is_checked' => true,
        ]);

        $this->assertDatabaseHas('shopping_list_items', [
            'id' => $item->id,
            'is_checked' => true,
        ]);
    }

    public function test_users_can_remove_items_from_shopping_list(): void
    {
        $user = User::factory()->create();
        $shoppingList = ShoppingList::factory()->for($user)->create();
        $ingredient = Ingredient::factory()->create();
        $unit = Unit::firstOrCreate(['code' => 'g'], ['label' => 'gramme']);

        $item = $shoppingList->items()->create([
            'ingredient_id' => $ingredient->id,
            'quantity' => 500,
            'unit_code' => $unit->code,
        ]);

        $response = $this->actingAs($user)->delete("/shopping-list-items/{$item->id}");

        $this->assertDatabaseMissing('shopping_list_items', ['id' => $item->id]);
    }

    public function test_users_cannot_modify_other_users_shopping_lists(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $shoppingList = ShoppingList::factory()->for($owner)->create();

        $response = $this->actingAs($otherUser)->delete("/shopping-lists/{$shoppingList->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('shopping_lists', ['id' => $shoppingList->id]);
    }

    public function test_shopping_list_aggregates_same_ingredients(): void
    {
        $user = User::factory()->create();
        $mealPlan = MealPlan::factory()->for($user)->create();

        $ingredient = Ingredient::factory()->create(['name' => 'Tomato']);
        $unit = Unit::firstOrCreate(['code' => 'g'], ['label' => 'gramme']);

        $recipe1 = Recipe::factory()->create(['is_public' => true]);
        $recipe1->ingredients()->attach($ingredient->id, ['quantity' => 200, 'unit_code' => $unit->code]);

        $recipe2 = Recipe::factory()->create(['is_public' => true]);
        $recipe2->ingredients()->attach($ingredient->id, ['quantity' => 300, 'unit_code' => $unit->code]);

        $mealPlan->recipes()->attach($recipe1->id, [
            'meal_type' => 'lunch',
            'planned_date' => now(),
            'servings' => 2,
        ]);
        $mealPlan->recipes()->attach($recipe2->id, [
            'meal_type' => 'dinner',
            'planned_date' => now(),
            'servings' => 2,
        ]);

        $this->actingAs($user)->post("/meal-plans/{$mealPlan->id}/generate-shopping-list");

        $shoppingList = ShoppingList::where('user_id', $user->id)->latest()->first();
        $tomatoItem = $shoppingList->items()->where('ingredient_id', $ingredient->id)->first();

        $this->assertNotNull($tomatoItem);
        $this->assertEquals(1, $shoppingList->items()->where('ingredient_id', $ingredient->id)->count());
    }
}
