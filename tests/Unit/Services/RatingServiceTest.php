<?php

namespace Tests\Unit\Services;

use App\Models\Rating;
use App\Models\Recipe;
use App\Models\User;
use App\Services\RatingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingServiceTest extends TestCase
{
    use RefreshDatabase;

    private RatingService $ratingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ratingService = app(RatingService::class);
    }

    public function test_add_new_rating()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();

        $this->ratingService->addOrUpdateRating($user, $recipe, 5);

        $this->assertDatabaseHas('ratings', [
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'rating' => 5,
        ]);

        $recipe->refresh();
        $this->assertEquals(5, $recipe->rating_avg);
        $this->assertEquals(1, $recipe->rating_count);
    }

    public function test_update_existing_rating()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();

        $this->ratingService->addOrUpdateRating($user, $recipe, 3);
        $this->ratingService->addOrUpdateRating($user, $recipe, 5);

        $this->assertEquals(1, Rating::where('user_id', $user->id)->count());

        $recipe->refresh();
        $this->assertEquals(5, $recipe->rating_avg);
        $this->assertEquals(1, $recipe->rating_count);
    }

    public function test_remove_rating()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();

        $this->ratingService->addOrUpdateRating($user, $recipe, 4);
        $recipe->refresh();
        $this->assertEquals(4, $recipe->rating_avg);

        $this->ratingService->removeRating($user, $recipe);

        $this->assertDatabaseMissing('ratings', [
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
        ]);

        $recipe->refresh();
        $this->assertEquals(0, $recipe->rating_avg);
        $this->assertEquals(0, $recipe->rating_count);
    }

    public function test_multiple_ratings_calculation()
    {
        $recipe = Recipe::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        $this->ratingService->addOrUpdateRating($user1, $recipe, 5);
        $this->ratingService->addOrUpdateRating($user2, $recipe, 3);
        $this->ratingService->addOrUpdateRating($user3, $recipe, 4);

        $recipe->refresh();
        $this->assertEquals(4.0, $recipe->rating_avg);
        $this->assertEquals(3, $recipe->rating_count);
    }

    public function test_remove_nonexistent_rating()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();

        $this->ratingService->removeRating($user, $recipe);

        $recipe->refresh();
        $this->assertEquals(0, $recipe->rating_avg);
        $this->assertEquals(0, $recipe->rating_count);
    }
}
