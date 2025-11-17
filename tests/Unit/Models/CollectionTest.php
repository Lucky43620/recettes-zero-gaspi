<?php

namespace Tests\Unit\Models;

use App\Models\Collection;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_collection_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->for($user)->create();

        $this->assertInstanceOf(User::class, $collection->user);
        $this->assertEquals($user->id, $collection->user->id);
    }

    public function test_collection_belongs_to_many_recipes(): void
    {
        $collection = Collection::factory()->create();
        $recipe1 = Recipe::factory()->create();
        $recipe2 = Recipe::factory()->create();

        $collection->recipes()->attach($recipe1->id, ['position' => 1, 'added_at' => now()]);
        $collection->recipes()->attach($recipe2->id, ['position' => 2, 'added_at' => now()]);

        $this->assertCount(2, $collection->recipes);
    }

    public function test_collection_recipes_have_position_pivot(): void
    {
        $collection = Collection::factory()->create();
        $recipe = Recipe::factory()->create();

        $collection->recipes()->attach($recipe->id, ['position' => 5, 'added_at' => now()]);

        $attachedRecipe = $collection->recipes()->first();

        $this->assertEquals(5, $attachedRecipe->pivot->position);
    }

    public function test_collection_recipes_have_added_at_pivot(): void
    {
        $collection = Collection::factory()->create();
        $recipe = Recipe::factory()->create();
        $addedAt = now()->subDays(3);

        $collection->recipes()->attach($recipe->id, ['position' => 1, 'added_at' => $addedAt]);

        $attachedRecipe = $collection->recipes()->first();

        $this->assertEquals($addedAt->toDateTimeString(), $attachedRecipe->pivot->added_at->toDateTimeString());
    }

    public function test_collection_can_be_public_or_private(): void
    {
        $publicCollection = Collection::factory()->create(['is_public' => true]);
        $privateCollection = Collection::factory()->create(['is_public' => false]);

        $this->assertTrue($publicCollection->is_public);
        $this->assertFalse($privateCollection->is_public);
    }

    public function test_collection_name_is_required(): void
    {
        $user = User::factory()->create();

        $this->expectException(\Illuminate\Database\QueryException::class);

        Collection::create([
            'user_id' => $user->id,
            'is_public' => true,
        ]);
    }

    public function test_collection_user_id_is_required(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Collection::create([
            'name' => 'Test Collection',
            'is_public' => true,
        ]);
    }

    public function test_user_can_have_multiple_collections(): void
    {
        $user = User::factory()->create();

        Collection::factory()->for($user)->create(['name' => 'Desserts']);
        Collection::factory()->for($user)->create(['name' => 'Quick Meals']);
        Collection::factory()->for($user)->create(['name' => 'Vegetarian']);

        $this->assertCount(3, $user->collections);
    }

    public function test_collection_can_have_description(): void
    {
        $collection = Collection::factory()->create([
            'description' => 'My favorite dessert recipes',
        ]);

        $this->assertEquals('My favorite dessert recipes', $collection->description);
    }

    public function test_collection_description_can_be_null(): void
    {
        $collection = Collection::factory()->create(['description' => null]);

        $this->assertNull($collection->description);
    }

    public function test_recipe_can_belong_to_multiple_collections(): void
    {
        $recipe = Recipe::factory()->create();
        $collection1 = Collection::factory()->create(['name' => 'Favorites']);
        $collection2 = Collection::factory()->create(['name' => 'Quick Meals']);
        $collection3 = Collection::factory()->create(['name' => 'Vegetarian']);

        $collection1->recipes()->attach($recipe->id, ['position' => 1, 'added_at' => now()]);
        $collection2->recipes()->attach($recipe->id, ['position' => 1, 'added_at' => now()]);
        $collection3->recipes()->attach($recipe->id, ['position' => 1, 'added_at' => now()]);

        $this->assertCount(3, $recipe->fresh()->collections);
    }

    public function test_removing_collection_does_not_delete_recipes(): void
    {
        $collection = Collection::factory()->create();
        $recipe = Recipe::factory()->create();

        $collection->recipes()->attach($recipe->id, ['position' => 1, 'added_at' => now()]);
        $recipeId = $recipe->id;

        $collection->delete();

        $this->assertDatabaseHas('recipes', ['id' => $recipeId]);
    }

    public function test_removing_recipe_from_collection_keeps_recipe(): void
    {
        $collection = Collection::factory()->create();
        $recipe = Recipe::factory()->create();

        $collection->recipes()->attach($recipe->id, ['position' => 1, 'added_at' => now()]);
        $recipeId = $recipe->id;

        $collection->recipes()->detach($recipe->id);

        $this->assertDatabaseHas('recipes', ['id' => $recipeId]);
        $this->assertCount(0, $collection->fresh()->recipes);
    }

    public function test_collection_recipes_can_be_reordered(): void
    {
        $collection = Collection::factory()->create();
        $recipe1 = Recipe::factory()->create();
        $recipe2 = Recipe::factory()->create();
        $recipe3 = Recipe::factory()->create();

        $collection->recipes()->attach($recipe1->id, ['position' => 1, 'added_at' => now()]);
        $collection->recipes()->attach($recipe2->id, ['position' => 2, 'added_at' => now()]);
        $collection->recipes()->attach($recipe3->id, ['position' => 3, 'added_at' => now()]);

        // Reorder using updateExistingPivot
        $collection->recipes()->updateExistingPivot($recipe1->id, ['position' => 3]);
        $collection->recipes()->updateExistingPivot($recipe2->id, ['position' => 1]);
        $collection->recipes()->updateExistingPivot($recipe3->id, ['position' => 2]);

        $collection->refresh();

        $this->assertEquals(3, $collection->recipes()->find($recipe1->id)->pivot->position);
        $this->assertEquals(1, $collection->recipes()->find($recipe2->id)->pivot->position);
        $this->assertEquals(2, $collection->recipes()->find($recipe3->id)->pivot->position);
    }
}
