<?php

namespace Tests\Feature\Social;

use App\Models\Rating;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_rate_recipes(): void
    {
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $response = $this->post("/recipes/{$recipe->slug}/ratings", [
            'rating' => 5,
        ]);

        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_rate_recipes(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $response = $this->actingAs($user)->post("/recipes/{$recipe->slug}/ratings", [
            'rating' => 5,
        ]);

        $this->assertDatabaseHas('ratings', [
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'rating' => 5,
        ]);
    }

    public function test_rating_requires_score_between_1_and_5(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $response = $this->actingAs($user)->post("/recipes/{$recipe->slug}/ratings", [
            'rating' => 6,
        ]);

        $response->assertSessionHasErrors('rating');

        $response = $this->actingAs($user)->post("/recipes/{$recipe->slug}/ratings", [
            'rating' => 0,
        ]);

        $response->assertSessionHasErrors('rating');
    }

    public function test_users_can_update_their_rating(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $this->actingAs($user)->post("/recipes/{$recipe->slug}/ratings", ['rating' => 3]);

        $this->actingAs($user)->post("/recipes/{$recipe->slug}/ratings", ['rating' => 5]);

        $this->assertDatabaseHas('ratings', [
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'rating' => 5,
        ]);

        $this->assertEquals(1, $recipe->ratings()->count());
    }

    public function test_users_can_delete_their_rating(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $this->actingAs($user)->post("/recipes/{$recipe->slug}/ratings", ['rating' => 5]);

        $response = $this->actingAs($user)->delete("/recipes/{$recipe->slug}/ratings");

        $this->assertDatabaseMissing('ratings', [
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
        ]);
    }

    public function test_users_cannot_delete_others_ratings(): void
    {
        $rater = User::factory()->create();
        $otherUser = User::factory()->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $this->actingAs($rater)->post("/recipes/{$recipe->slug}/ratings", ['rating' => 5]);

        $response = $this->actingAs($otherUser)->delete("/recipes/{$recipe->slug}/ratings");

        $this->assertDatabaseHas('ratings', [
            'user_id' => $rater->id,
            'recipe_id' => $recipe->id,
        ]);
    }

    public function test_recipe_average_rating_is_calculated_correctly(): void
    {
        $recipe = Recipe::factory()->create(['is_public' => true]);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        Rating::create(['user_id' => $user1->id, 'recipe_id' => $recipe->id, 'rating' => 5]);
        Rating::create(['user_id' => $user2->id, 'recipe_id' => $recipe->id, 'rating' => 4]);
        Rating::create(['user_id' => $user3->id, 'recipe_id' => $recipe->id, 'rating' => 3]);

        $recipe->refresh();

        $this->assertEquals(4.0, $recipe->rating_avg);
        $this->assertEquals(3, $recipe->rating_count);
    }

    public function test_deleting_rating_updates_recipe_averages(): void
    {
        $recipe = Recipe::factory()->create(['is_public' => true]);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Rating::create(['user_id' => $user1->id, 'recipe_id' => $recipe->id, 'rating' => 5]);
        Rating::create(['user_id' => $user2->id, 'recipe_id' => $recipe->id, 'rating' => 3]);

        $this->actingAs($user1)->delete("/recipes/{$recipe->slug}/ratings");

        $recipe->refresh();

        $this->assertEquals(3.0, $recipe->rating_avg);
        $this->assertEquals(1, $recipe->rating_count);
    }
}
