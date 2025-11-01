<?php

namespace Tests\Feature\Pantry;

use App\Models\Ingredient;
use App\Models\PantryItem;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PantryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_pantry(): void
    {
        $response = $this->get('/pantry');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_view_pantry(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/pantry');
        $response->assertStatus(200);
    }

    public function test_users_can_add_items_to_pantry(): void
    {
        $user = User::factory()->create();
        $ingredient = Ingredient::factory()->create();
        $unit = Unit::firstOrCreate(['code' => 'g'], ['label' => 'gramme']);

        $response = $this->actingAs($user)->post('/pantry', [
            'ingredient_id' => $ingredient->id,
            'quantity' => 500,
            'unit_code' => $unit->code,
            'expiration_date' => now()->addDays(7)->format('Y-m-d'),
            'storage_location' => 'Frigo',
        ]);

        $this->assertDatabaseHas('pantry_items', [
            'user_id' => $user->id,
            'ingredient_id' => $ingredient->id,
            'quantity' => 500,
        ]);
    }

    public function test_users_can_update_pantry_items(): void
    {
        $user = User::factory()->create();
        $pantryItem = PantryItem::factory()->for($user)->create(['quantity' => 100]);

        $response = $this->actingAs($user)->put("/pantry/{$pantryItem->id}", [
            'ingredient_id' => $pantryItem->ingredient_id,
            'quantity' => 200,
            'unit_code' => $pantryItem->unit_code,
            'expiration_date' => $pantryItem->expiration_date?->format('Y-m-d'),
            'storage_location' => $pantryItem->storage_location,
        ]);

        $this->assertDatabaseHas('pantry_items', [
            'id' => $pantryItem->id,
            'quantity' => 200,
        ]);
    }

    public function test_users_can_delete_pantry_items(): void
    {
        $user = User::factory()->create();
        $pantryItem = PantryItem::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete("/pantry/{$pantryItem->id}");

        $this->assertDatabaseMissing('pantry_items', ['id' => $pantryItem->id]);
    }

    public function test_users_cannot_delete_other_users_pantry_items(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $pantryItem = PantryItem::factory()->for($owner)->create();

        $response = $this->actingAs($otherUser)->delete("/pantry/{$pantryItem->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('pantry_items', ['id' => $pantryItem->id]);
    }

    public function test_pantry_items_expiring_soon_are_identified(): void
    {
        $user = User::factory()->create();

        $expiringItem = PantryItem::factory()->for($user)->create([
            'expiration_date' => now()->addDays(3),
        ]);

        $freshItem = PantryItem::factory()->for($user)->create([
            'expiration_date' => now()->addDays(30),
        ]);

        $expiringItems = $user->pantryItems()->expiringSoon()->get();

        $this->assertTrue($expiringItems->contains($expiringItem));
        $this->assertFalse($expiringItems->contains($freshItem));
    }

    public function test_users_only_see_their_own_pantry_items(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $item1 = PantryItem::factory()->for($user1)->create();
        $item2 = PantryItem::factory()->for($user2)->create();

        $response = $this->actingAs($user1)->get('/pantry');

        $response->assertStatus(200);
        $this->assertEquals(1, $user1->pantryItems()->count());
        $this->assertEquals(1, $user2->pantryItems()->count());
    }
}
