<?php

namespace Tests\Feature\Social;

use App\Models\Collection;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_collections(): void
    {
        $response = $this->get('/collections');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_view_collections(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/collections');
        $response->assertStatus(200);
    }

    public function test_users_can_create_collections(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/collections', [
            'name' => 'My Collection',
            'description' => 'A test collection',
            'is_public' => true,
        ]);

        $this->assertDatabaseHas('collections', [
            'user_id' => $user->id,
            'name' => 'My Collection',
        ]);
    }

    public function test_collection_requires_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/collections', [
            'name' => '',
            'description' => 'Test',
            'is_public' => true,
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_users_can_update_their_collections(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->for($user)->create(['name' => 'Old Name']);

        $response = $this->actingAs($user)->put("/collections/{$collection->id}", [
            'name' => 'New Name',
            'description' => $collection->description,
            'is_public' => $collection->is_public,
        ]);

        $this->assertDatabaseHas('collections', [
            'id' => $collection->id,
            'name' => 'New Name',
        ]);
    }

    public function test_users_cannot_update_others_collections(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $collection = Collection::factory()->for($owner)->create();

        $response = $this->actingAs($otherUser)->put("/collections/{$collection->id}", [
            'name' => 'Hacked Name',
            'description' => 'Test',
            'is_public' => true,
        ]);

        $response->assertStatus(403);
    }

    public function test_users_can_delete_their_collections(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete("/collections/{$collection->id}");

        $this->assertDatabaseMissing('collections', ['id' => $collection->id]);
    }

    public function test_users_can_add_recipes_to_collections(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->for($user)->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $response = $this->actingAs($user)->post("/collections/{$collection->id}/recipes/{$recipe->id}");

        $response->assertRedirect();

        $collection->refresh();
        $this->assertTrue($collection->recipes->contains($recipe));
    }

    public function test_users_can_remove_recipes_from_collections(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->for($user)->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $collection->recipes()->attach($recipe->id);

        $response = $this->actingAs($user)->delete("/collections/{$collection->id}/recipes/{$recipe->slug}");

        $this->assertFalse($collection->fresh()->recipes->contains($recipe));
    }

    public function test_users_can_reorder_recipes_in_collections(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->for($user)->create();
        $recipe1 = Recipe::factory()->create(['is_public' => true]);
        $recipe2 = Recipe::factory()->create(['is_public' => true]);

        $collection->recipes()->attach($recipe1->id, ['position' => 1]);
        $collection->recipes()->attach($recipe2->id, ['position' => 2]);

        $response = $this->actingAs($user)->post("/collections/{$collection->id}/reorder", [
            'recipe_ids' => [$recipe2->id, $recipe1->id],
        ]);

        $this->assertEquals(1, $collection->recipes()->where('recipe_id', $recipe2->id)->first()->pivot->position);
        $this->assertEquals(2, $collection->recipes()->where('recipe_id', $recipe1->id)->first()->pivot->position);
    }

    public function test_public_collections_are_viewable_by_everyone(): void
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $collection = Collection::factory()->for($owner)->create(['is_public' => true]);

        $response = $this->actingAs($guest)->get("/collections/{$collection->id}");
        $response->assertStatus(200);
    }

    public function test_private_collections_are_not_viewable_by_others(): void
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $collection = Collection::factory()->for($owner)->create(['is_public' => false]);

        $response = $this->actingAs($guest)->get("/collections/{$collection->id}");
        $response->assertStatus(403);
    }
}
