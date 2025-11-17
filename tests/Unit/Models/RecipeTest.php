<?php

namespace Tests\Unit\Models;

use App\Enums\Difficulty;
use App\Models\Collection;
use App\Models\Comment;
use App\Models\Cooksnap;
use App\Models\Ingredient;
use App\Models\Rating;
use App\Models\Recipe;
use App\Models\RecipeStep;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeTest extends TestCase
{
    use RefreshDatabase;

    public function test_recipe_belongs_to_author(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user, 'author')->create();

        $this->assertInstanceOf(User::class, $recipe->author);
        $this->assertEquals($user->id, $recipe->author->id);
    }

    public function test_recipe_has_many_steps(): void
    {
        $recipe = Recipe::factory()->create();

        RecipeStep::create(['recipe_id' => $recipe->id, 'content' => 'Step 1', 'position' => 1]);
        RecipeStep::create(['recipe_id' => $recipe->id, 'content' => 'Step 2', 'position' => 2]);

        $this->assertCount(2, $recipe->steps);
        $this->assertEquals('Step 1', $recipe->steps->first()->content);
    }

    public function test_recipe_steps_ordered_by_position(): void
    {
        $recipe = Recipe::factory()->create();

        RecipeStep::create(['recipe_id' => $recipe->id, 'content' => 'Third', 'position' => 3]);
        RecipeStep::create(['recipe_id' => $recipe->id, 'content' => 'First', 'position' => 1]);
        RecipeStep::create(['recipe_id' => $recipe->id, 'content' => 'Second', 'position' => 2]);

        $steps = $recipe->fresh()->steps;

        $this->assertEquals('First', $steps[0]->content);
        $this->assertEquals('Second', $steps[1]->content);
        $this->assertEquals('Third', $steps[2]->content);
    }

    public function test_recipe_has_many_ratings(): void
    {
        $recipe = Recipe::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Rating::create(['user_id' => $user1->id, 'recipe_id' => $recipe->id, 'rating' => 5]);
        Rating::create(['user_id' => $user2->id, 'recipe_id' => $recipe->id, 'rating' => 4]);

        $this->assertCount(2, $recipe->ratings);
    }

    public function test_recipe_has_many_comments(): void
    {
        $recipe = Recipe::factory()->create();
        $user = User::factory()->create();

        Comment::create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'content' => 'Great recipe!',
        ]);

        $this->assertCount(1, $recipe->comments);
    }

    public function test_recipe_belongs_to_many_collections(): void
    {
        $recipe = Recipe::factory()->create(['is_public' => true]);
        $collection1 = Collection::factory()->create();
        $collection2 = Collection::factory()->create();

        $collection1->recipes()->attach($recipe->id, ['position' => 1, 'added_at' => now()]);
        $collection2->recipes()->attach($recipe->id, ['position' => 1, 'added_at' => now()]);

        $this->assertCount(2, $recipe->collections);
    }

    public function test_recipe_belongs_to_many_ingredients(): void
    {
        $recipe = Recipe::factory()->create();
        $ingredient1 = Ingredient::factory()->create();
        $ingredient2 = Ingredient::factory()->create();

        $recipe->ingredients()->attach($ingredient1->id, [
            'quantity' => 100,
            'unit_code' => 'g',
            'position' => 1,
        ]);

        $recipe->ingredients()->attach($ingredient2->id, [
            'quantity' => 200,
            'unit_code' => 'ml',
            'position' => 2,
        ]);

        $this->assertCount(2, $recipe->fresh()->ingredients);
        $this->assertEquals(100, $recipe->ingredients->first()->pivot->quantity);
    }

    public function test_recipe_ingredients_ordered_by_position(): void
    {
        $recipe = Recipe::factory()->create();
        $ingredient1 = Ingredient::factory()->create(['name' => 'First']);
        $ingredient2 = Ingredient::factory()->create(['name' => 'Second']);
        $ingredient3 = Ingredient::factory()->create(['name' => 'Third']);

        $recipe->ingredients()->attach($ingredient2->id, ['quantity' => 100, 'unit_code' => 'g', 'position' => 2]);
        $recipe->ingredients()->attach($ingredient3->id, ['quantity' => 100, 'unit_code' => 'g', 'position' => 3]);
        $recipe->ingredients()->attach($ingredient1->id, ['quantity' => 100, 'unit_code' => 'g', 'position' => 1]);

        $ingredients = $recipe->fresh()->ingredients;

        $this->assertEquals('First', $ingredients[0]->name);
        $this->assertEquals('Second', $ingredients[1]->name);
        $this->assertEquals('Third', $ingredients[2]->name);
    }

    public function test_recipe_has_many_cooksnaps(): void
    {
        $recipe = Recipe::factory()->create();
        $user = User::factory()->create();

        Cooksnap::create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'comment' => 'Delicious!',
        ]);

        $this->assertCount(1, $recipe->cooksnaps);
    }

    public function test_scope_public_filters_public_recipes(): void
    {
        Recipe::factory()->create(['is_public' => true]);
        Recipe::factory()->create(['is_public' => false]);
        Recipe::factory()->create(['is_public' => true]);

        $publicRecipes = Recipe::public()->get();

        $this->assertCount(2, $publicRecipes);
        $publicRecipes->each(function ($recipe) {
            $this->assertTrue($recipe->is_public);
        });
    }

    public function test_scope_by_difficulty_filters_recipes(): void
    {
        Recipe::factory()->create(['difficulty' => Difficulty::EASY]);
        Recipe::factory()->create(['difficulty' => Difficulty::MEDIUM]);
        Recipe::factory()->create(['difficulty' => Difficulty::EASY]);

        $easyRecipes = Recipe::byDifficulty(Difficulty::EASY)->get();

        $this->assertCount(2, $easyRecipes);
        $easyRecipes->each(function ($recipe) {
            $this->assertEquals(Difficulty::EASY, $recipe->difficulty);
        });
    }

    public function test_scope_popular_returns_recipes_with_high_ratings(): void
    {
        $recipe1 = Recipe::factory()->create(['rating_avg' => 4.5, 'rating_count' => 10]);
        $recipe2 = Recipe::factory()->create(['rating_avg' => 3.0, 'rating_count' => 5]);
        $recipe3 = Recipe::factory()->create(['rating_avg' => 5.0, 'rating_count' => 20]);
        $recipeNoRatings = Recipe::factory()->create(['rating_avg' => 0, 'rating_count' => 0]);

        $popularRecipes = Recipe::popular()->get();

        $this->assertCount(3, $popularRecipes);
        $this->assertEquals($recipe3->id, $popularRecipes[0]->id);
        $this->assertEquals($recipe1->id, $popularRecipes[1]->id);
        $this->assertEquals($recipe2->id, $popularRecipes[2]->id);
    }

    public function test_scope_recent_returns_latest_recipes(): void
    {
        $old = Recipe::factory()->create(['created_at' => now()->subDays(5)]);
        $recent = Recipe::factory()->create(['created_at' => now()->subDay()]);
        $newest = Recipe::factory()->create(['created_at' => now()]);

        $recentRecipes = Recipe::recent()->get();

        $this->assertEquals($newest->id, $recentRecipes[0]->id);
        $this->assertEquals($recent->id, $recentRecipes[1]->id);
        $this->assertEquals($old->id, $recentRecipes[2]->id);
    }

    public function test_scope_with_metadata_eager_loads_author_and_media(): void
    {
        $user = User::factory()->create();
        Recipe::factory()->for($user, 'author')->create();

        $recipes = Recipe::withMetadata()->get();

        $this->assertTrue($recipes->first()->relationLoaded('author'));
        $this->assertTrue($recipes->first()->relationLoaded('media'));
    }

    public function test_scope_with_full_data_eager_loads_all_relations(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user, 'author')->create();

        Rating::create(['user_id' => $user->id, 'recipe_id' => $recipe->id, 'rating' => 5]);
        Comment::create(['user_id' => $user->id, 'recipe_id' => $recipe->id, 'content' => 'Great!']);

        $recipes = Recipe::withFullData()->get();

        $this->assertTrue($recipes->first()->relationLoaded('author'));
        $this->assertTrue($recipes->first()->relationLoaded('media'));
        $this->assertTrue($recipes->first()->relationLoaded('ratings'));
        $this->assertTrue($recipes->first()->relationLoaded('comments'));
    }

    public function test_recipe_slug_is_generated_from_title(): void
    {
        $recipe = Recipe::factory()->create(['title' => 'My Delicious Recipe']);

        $this->assertNotNull($recipe->slug);
        $this->assertStringContainsString('my-delicious-recipe', $recipe->slug);
    }

    public function test_recipe_casts_difficulty_to_enum(): void
    {
        $recipe = Recipe::factory()->create(['difficulty' => Difficulty::MEDIUM->value]);

        $this->assertInstanceOf(Difficulty::class, $recipe->difficulty);
        $this->assertEquals(Difficulty::MEDIUM, $recipe->difficulty);
    }

    public function test_recipe_casts_is_public_to_boolean(): void
    {
        $recipe = Recipe::factory()->create(['is_public' => 1]);

        $this->assertIsBool($recipe->is_public);
        $this->assertTrue($recipe->is_public);
    }

    public function test_recipe_casts_nutrients_to_array(): void
    {
        $nutrients = ['protein' => 10, 'carbs' => 20, 'fat' => 5];
        $recipe = Recipe::factory()->create(['nutrients' => $nutrients]);

        $this->assertIsArray($recipe->nutrients);
        $this->assertEquals($nutrients, $recipe->nutrients);
    }
}
