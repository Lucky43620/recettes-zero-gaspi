<?php

namespace Tests\Feature\Social;

use App\Models\Collection;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionReorderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_reorder_recipes_in_their_collection(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->for($user)->create();

        $recipe1 = Recipe::factory()->create(['is_public' => true]);
        $recipe2 = Recipe::factory()->create(['is_public' => true]);
        $recipe3 = Recipe::factory()->create(['is_public' => true]);

        // Attach recipes with initial positions
        $collection->recipes()->attach($recipe1->id, ['position' => 1, 'added_at' => now()]);
        $collection->recipes()->attach($recipe2->id, ['position' => 2, 'added_at' => now()]);
        $collection->recipes()->attach($recipe3->id, ['position' => 3, 'added_at' => now()]);

        // Reorder: recipe3, recipe1, recipe2
        $response = $this->actingAs($user)->post("/collections/{$collection->id}/reorder", [
            'recipe_ids' => [$recipe3->id, $recipe1->id, $recipe2->id],
        ]);

        $response->assertRedirect();

        // Verify new positions
        $this->assertEquals(1, $collection->recipes()->find($recipe3->id)->pivot->position);
        $this->assertEquals(2, $collection->recipes()->find($recipe1->id)->pivot->position);
        $this->assertEquals(3, $collection->recipes()->find($recipe2->id)->pivot->position);
    }

    public function test_user_cannot_reorder_recipes_in_others_collection(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $collection = Collection::factory()->for($owner)->create();

        $recipe1 = Recipe::factory()->create(['is_public' => true]);
        $recipe2 = Recipe::factory()->create(['is_public' => true]);

        $collection->recipes()->attach($recipe1->id, ['position' => 1, 'added_at' => now()]);
        $collection->recipes()->attach($recipe2->id, ['position' => 2, 'added_at' => now()]);

        $response = $this->actingAs($otherUser)->post("/collections/{$collection->id}/reorder", [
            'recipe_ids' => [$recipe2->id, $recipe1->id],
        ]);

        $response->assertStatus(403);

        // Verify positions haven't changed
        $this->assertEquals(1, $collection->recipes()->find($recipe1->id)->pivot->position);
        $this->assertEquals(2, $collection->recipes()->find($recipe2->id)->pivot->position);
    }

    public function test_reorder_requires_valid_recipe_ids(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->for($user)->create();

        $recipe = Recipe::factory()->create(['is_public' => true]);
        $collection->recipes()->attach($recipe->id, ['position' => 1, 'added_at' => now()]);

        // Try to reorder with non-existent recipe ID
        $response = $this->actingAs($user)->post("/collections/{$collection->id}/reorder", [
            'recipe_ids' => [99999, $recipe->id],
        ]);

        $response->assertSessionHasErrors('recipe_ids.0');
    }

    public function test_reorder_requires_recipe_ids_array(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->for($user)->create();

        $response = $this->actingAs($user)->post("/collections/{$collection->id}/reorder", [
            'recipe_ids' => 'not-an-array',
        ]);

        $response->assertSessionHasErrors('recipe_ids');
    }

    public function test_guests_cannot_reorder_recipes(): void
    {
        $collection = Collection::factory()->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $collection->recipes()->attach($recipe->id, ['position' => 1, 'added_at' => now()]);

        $response = $this->post("/collections/{$collection->id}/reorder", [
            'recipe_ids' => [$recipe->id],
        ]);

        $response->assertRedirect('/login');
    }
}
