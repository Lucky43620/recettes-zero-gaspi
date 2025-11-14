<?php

namespace Tests\Unit\Services;

use App\Models\MealPlan;
use App\Models\Recipe;
use App\Models\User;
use App\Services\MealPlanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MealPlanServiceTest extends TestCase
{
    use RefreshDatabase;

    private MealPlanService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(MealPlanService::class);
    }

    public function test_duplicate_meal_plan_to_new_week()
    {
        $user = User::factory()->create();
        $recipe1 = Recipe::factory()->create();
        $recipe2 = Recipe::factory()->create();

        $sourcePlan = $user->mealPlans()->create([
            'week_start_date' => '2024-01-01',
        ]);

        $sourcePlan->mealPlanRecipes()->create([
            'recipe_id' => $recipe1->id,
            'planned_date' => '2024-01-01',
            'meal_type' => 'lunch',
            'servings' => 2,
        ]);

        $sourcePlan->mealPlanRecipes()->create([
            'recipe_id' => $recipe2->id,
            'planned_date' => '2024-01-02',
            'meal_type' => 'dinner',
            'servings' => 4,
            'notes' => 'Test notes',
        ]);

        $newPlan = $this->service->duplicateMealPlan($user, $sourcePlan, '2024-01-08');

        $this->assertEquals('2024-01-08', $newPlan->week_start_date);
        $this->assertEquals(2, $newPlan->mealPlanRecipes()->count());

        $recipes = $newPlan->mealPlanRecipes;
        $this->assertEquals($recipe1->id, $recipes[0]->recipe_id);
        $this->assertEquals('lunch', $recipes[0]->meal_type);
        $this->assertEquals(2, $recipes[0]->servings);
    }

    public function test_duplicate_overwrites_existing_week()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();

        $sourcePlan = $user->mealPlans()->create([
            'week_start_date' => '2024-01-01',
        ]);

        $sourcePlan->mealPlanRecipes()->create([
            'recipe_id' => $recipe->id,
            'planned_date' => '2024-01-01',
            'meal_type' => 'lunch',
            'servings' => 2,
        ]);

        $existingPlan = $user->mealPlans()->create([
            'week_start_date' => '2024-01-08',
        ]);

        $existingPlan->mealPlanRecipes()->create([
            'recipe_id' => $recipe->id,
            'planned_date' => '2024-01-08',
            'meal_type' => 'dinner',
            'servings' => 1,
        ]);

        $newPlan = $this->service->duplicateMealPlan($user, $sourcePlan, '2024-01-08');

        $this->assertEquals($existingPlan->id, $newPlan->id);
        $this->assertEquals(1, $newPlan->mealPlanRecipes()->count());
        $this->assertEquals('lunch', $newPlan->mealPlanRecipes->first()->meal_type);
    }

    public function test_duplicate_empty_meal_plan()
    {
        $user = User::factory()->create();

        $sourcePlan = $user->mealPlans()->create([
            'week_start_date' => '2024-01-01',
        ]);

        $newPlan = $this->service->duplicateMealPlan($user, $sourcePlan, '2024-01-08');

        $this->assertEquals('2024-01-08', $newPlan->week_start_date);
        $this->assertEquals(0, $newPlan->mealPlanRecipes()->count());
    }
}
