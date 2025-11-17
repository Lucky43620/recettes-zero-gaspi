<?php

namespace Tests\Unit\Services;

use App\Enums\Difficulty;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\RecipeStep;
use App\Models\Unit;
use App\Models\User;
use App\Services\RecipeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RecipeServiceTest extends TestCase
{
    use RefreshDatabase;

    private RecipeService $recipeService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->recipeService = app(RecipeService::class);
        Storage::fake('public');
    }

    public function test_create_recipe_with_ingredients_and_steps(): void
    {
        $user = User::factory()->create();
        $ingredient = Ingredient::factory()->create();
        $unit = Unit::firstOrCreate(['code' => 'g'], ['label' => 'gramme']);

        $data = [
            'title' => 'Test Recipe',
            'summary' => 'A delicious test recipe',
            'servings' => 4,
            'prep_minutes' => 15,
            'cook_minutes' => 30,
            'difficulty' => Difficulty::MEDIUM->value,
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
                ['text' => 'Third step'],
            ],
        ];

        $this->actingAs($user);
        $recipe = $this->recipeService->createRecipe($data);

        $this->assertInstanceOf(Recipe::class, $recipe);
        $this->assertEquals('Test Recipe', $recipe->title);
        $this->assertEquals($user->id, $recipe->author_id);
        $this->assertEquals(45, $recipe->prep_minutes + $recipe->cook_minutes);

        // Check ingredients were attached
        $this->assertCount(1, $recipe->ingredients);
        $this->assertEquals($ingredient->id, $recipe->ingredients->first()->id);
        $this->assertEquals(200, $recipe->ingredients->first()->pivot->quantity);

        // Check steps were created
        $this->assertCount(3, $recipe->steps);
        $this->assertEquals('First step', $recipe->steps[0]->content);
        $this->assertEquals(1, $recipe->steps[0]->position);
        $this->assertEquals('Second step', $recipe->steps[1]->content);
        $this->assertEquals(2, $recipe->steps[1]->position);
    }

    public function test_create_recipe_with_image(): void
    {
        $user = User::factory()->create();

        $data = [
            'title' => 'Recipe with Image',
            'summary' => 'Test summary',
            'servings' => 2,
            'prep_minutes' => 10,
            'cook_minutes' => 20,
            'difficulty' => Difficulty::EASY->value,
            'is_public' => true,
            'image' => UploadedFile::fake()->image('recipe.jpg'),
            'ingredients' => [],
            'steps' => [],
        ];

        $this->actingAs($user);
        $recipe = $this->recipeService->createRecipe($data);

        $this->assertCount(1, $recipe->getMedia('images'));
    }

    public function test_update_recipe_with_new_ingredients(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user, 'author')->create();

        $ingredient1 = Ingredient::factory()->create();
        $ingredient2 = Ingredient::factory()->create();
        $unit = Unit::firstOrCreate(['code' => 'g'], ['label' => 'gramme']);

        $data = [
            'title' => 'Updated Recipe',
            'summary' => $recipe->summary,
            'servings' => $recipe->servings,
            'prep_minutes' => $recipe->prep_minutes,
            'cook_minutes' => $recipe->cook_minutes,
            'difficulty' => $recipe->difficulty->value,
            'is_public' => $recipe->is_public,
            'ingredients' => [
                [
                    'ingredient_id' => $ingredient1->id,
                    'quantity' => 100,
                    'unit_code' => $unit->code,
                ],
                [
                    'ingredient_id' => $ingredient2->id,
                    'quantity' => 200,
                    'unit_code' => $unit->code,
                ],
            ],
            'steps' => [],
        ];

        $this->actingAs($user);
        $updatedRecipe = $this->recipeService->updateRecipe($recipe, $data);

        $this->assertEquals('Updated Recipe', $updatedRecipe->title);
        $this->assertCount(2, $updatedRecipe->fresh()->ingredients);
    }

    public function test_update_recipe_steps_updates_positions(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user, 'author')->create();

        // Create initial steps
        RecipeStep::create(['recipe_id' => $recipe->id, 'content' => 'Old step', 'position' => 1]);

        $data = [
            'title' => $recipe->title,
            'summary' => $recipe->summary,
            'servings' => $recipe->servings,
            'prep_minutes' => $recipe->prep_minutes,
            'cook_minutes' => $recipe->cook_minutes,
            'difficulty' => $recipe->difficulty->value,
            'is_public' => $recipe->is_public,
            'ingredients' => [],
            'steps' => [
                ['text' => 'New first step'],
                ['text' => 'New second step'],
            ],
        ];

        $this->actingAs($user);
        $updatedRecipe = $this->recipeService->updateRecipe($recipe, $data);

        $steps = $updatedRecipe->fresh()->steps()->orderBy('position')->get();
        $this->assertCount(2, $steps);
        $this->assertEquals('New first step', $steps[0]->content);
        $this->assertEquals(1, $steps[0]->position);
        $this->assertEquals('New second step', $steps[1]->content);
        $this->assertEquals(2, $steps[1]->position);
    }

    public function test_apply_filters_by_difficulty(): void
    {
        Recipe::factory()->create(['difficulty' => Difficulty::EASY, 'is_public' => true]);
        Recipe::factory()->create(['difficulty' => Difficulty::HARD, 'is_public' => true]);

        $query = Recipe::query()->where('is_public', true);
        $request = new class {
            public function has($key) {
                return $key === 'difficulty';
            }
            public function get($key) {
                return $key === 'difficulty' ? Difficulty::EASY->value : null;
            }
        };

        $filteredQuery = $this->recipeService->applyFilters($query, $request);
        $results = $filteredQuery->get();

        $this->assertCount(1, $results);
        $this->assertEquals(Difficulty::EASY, $results->first()->difficulty);
    }

    public function test_apply_filters_by_max_time(): void
    {
        Recipe::factory()->create(['prep_minutes' => 10, 'cook_minutes' => 20, 'is_public' => true]); // 30 total
        Recipe::factory()->create(['prep_minutes' => 30, 'cook_minutes' => 40, 'is_public' => true]); // 70 total

        $query = Recipe::query()->where('is_public', true);
        $request = new class {
            public function has($key) {
                return $key === 'max_time';
            }
            public function get($key) {
                return $key === 'max_time' ? 45 : null;
            }
        };

        $filteredQuery = $this->recipeService->applyFilters($query, $request);
        $results = $filteredQuery->get();

        $this->assertCount(1, $results);
        $this->assertEquals(30, $results->first()->prep_minutes + $results->first()->cook_minutes);
    }

    public function test_delete_recipe_removes_related_data(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user, 'author')->create();

        RecipeStep::create(['recipe_id' => $recipe->id, 'content' => 'Step 1', 'position' => 1]);

        $ingredient = Ingredient::factory()->create();
        $recipe->ingredients()->attach($ingredient->id, [
            'quantity' => 100,
            'unit_code' => 'g',
            'position' => 1,
        ]);

        $recipeId = $recipe->id;

        $this->actingAs($user);
        $recipe->delete();

        $this->assertDatabaseMissing('recipes', ['id' => $recipeId]);
        $this->assertDatabaseMissing('recipe_steps', ['recipe_id' => $recipeId]);
        $this->assertDatabaseMissing('recipe_ingredients', ['recipe_id' => $recipeId]);
    }
}
