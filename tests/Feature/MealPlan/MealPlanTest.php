<?php

namespace Tests\Feature\MealPlan;

use App\Models\MealPlan;
use App\Models\MealPlanRecipe;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MealPlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_meal_plans(): void
    {
        $response = $this->get('/meal-plans');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_view_meal_plans(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/meal-plans');
        $response->assertStatus(200);
    }

    public function test_meal_plan_is_automatically_created_for_current_week(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/meal-plans');

        $startOfWeek = now()->startOfWeek();
        $this->assertDatabaseHas('meal_plans', [
            'user_id' => $user->id,
            'week_start_date' => $startOfWeek->format('Y-m-d'),
        ]);
    }

    public function test_users_can_add_recipes_to_meal_plan(): void
    {
        $user = User::factory()->create();
        $mealPlan = MealPlan::factory()->for($user)->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $response = $this->actingAs($user)->post("/meal-plans/{$mealPlan->id}/recipes", [
            'recipe_id' => $recipe->id,
            'meal_type' => 'lunch',
            'planned_date' => now()->format('Y-m-d'),
            'servings' => 4,
        ]);

        $this->assertDatabaseHas('meal_plan_recipes', [
            'meal_plan_id' => $mealPlan->id,
            'recipe_id' => $recipe->id,
            'meal_type' => 'lunch',
        ]);
    }

    public function test_users_can_update_meal_plan_recipes(): void
    {
        $user = User::factory()->create();
        $mealPlan = MealPlan::factory()->for($user)->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $mealPlan->recipes()->attach($recipe->id, [
            'meal_type' => 'lunch',
            'planned_date' => now(),
            'servings' => 2,
        ]);

        $mealPlanRecipe = MealPlanRecipe::where('meal_plan_id', $mealPlan->id)
            ->where('recipe_id', $recipe->id)
            ->first();

        $response = $this->actingAs($user)->put("/meal-plan-recipes/{$mealPlanRecipe->id}", [
            'meal_type' => 'dinner',
            'planned_date' => now()->format('Y-m-d'),
            'servings' => 4,
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('meal_plan_recipes', [
            'id' => $mealPlanRecipe->id,
            'meal_type' => 'dinner',
            'servings' => 4,
        ]);
    }

    public function test_users_can_remove_recipes_from_meal_plan(): void
    {
        $user = User::factory()->create();
        $mealPlan = MealPlan::factory()->for($user)->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $mealPlan->recipes()->attach($recipe->id, [
            'meal_type' => 'lunch',
            'planned_date' => now(),
            'servings' => 2,
        ]);

        $mealPlanRecipeId = $mealPlan->recipes()->first()->pivot->id;

        $response = $this->actingAs($user)->delete("/meal-plan-recipes/{$mealPlanRecipeId}");

        $this->assertDatabaseMissing('meal_plan_recipes', ['id' => $mealPlanRecipeId]);
    }

    public function test_users_cannot_modify_other_users_meal_plans(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $mealPlan = MealPlan::factory()->for($owner)->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $response = $this->actingAs($otherUser)->post("/meal-plans/{$mealPlan->id}/recipes", [
            'recipe_id' => $recipe->id,
            'meal_type' => 'lunch',
            'planned_date' => now()->format('Y-m-d'),
            'servings' => 4,
        ]);

        $response->assertStatus(403);
    }

    public function test_users_can_duplicate_meal_plan(): void
    {
        $user = User::factory()->create();
        $mealPlan = MealPlan::factory()->for($user)->create();
        $recipe1 = Recipe::factory()->create(['is_public' => true]);
        $recipe2 = Recipe::factory()->create(['is_public' => true]);

        $mealPlan->recipes()->attach($recipe1->id, [
            'meal_type' => 'lunch',
            'planned_date' => now(),
            'servings' => 2,
        ]);
        $mealPlan->recipes()->attach($recipe2->id, [
            'meal_type' => 'dinner',
            'planned_date' => now(),
            'servings' => 4,
        ]);

        $response = $this->actingAs($user)->post("/meal-plans/{$mealPlan->id}/duplicate", [
            'week_start_date' => now()->addWeek()->startOfWeek()->format('Y-m-d'),
        ]);

        $this->assertEquals(2, MealPlan::where('user_id', $user->id)->count());

        $newMealPlan = MealPlan::where('user_id', $user->id)
            ->where('week_start_date', now()->addWeek()->startOfWeek()->format('Y-m-d'))
            ->first();

        $this->assertEquals(2, $newMealPlan->recipes()->count());
    }

    public function test_meal_types_are_validated(): void
    {
        $user = User::factory()->create();
        $mealPlan = MealPlan::factory()->for($user)->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $response = $this->actingAs($user)->post("/meal-plans/{$mealPlan->id}/recipes", [
            'recipe_id' => $recipe->id,
            'meal_type' => 'invalid_meal_type',
            'planned_date' => now()->format('Y-m-d'),
            'servings' => 4,
        ]);

        $response->assertSessionHasErrors('meal_type');
    }
}
