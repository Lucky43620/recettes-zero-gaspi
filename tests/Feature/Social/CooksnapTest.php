<?php

namespace Tests\Feature\Social;

use App\Models\Cooksnap;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CooksnapTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_post_cooksnaps(): void
    {
        Storage::fake('public');
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $response = $this->post("/recipes/{$recipe->slug}/cooksnaps", [
            'photos' => [UploadedFile::fake()->image('test.jpg')],
            'comment' => 'Test cooksnap',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_post_cooksnaps(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $response = $this->actingAs($user)->post("/recipes/{$recipe->slug}/cooksnaps", [
            'photos' => [UploadedFile::fake()->image('test.jpg')],
            'comment' => 'My version of this recipe!',
        ]);

        $this->assertDatabaseHas('cooksnaps', [
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'comment' => 'My version of this recipe!',
        ]);
    }

    public function test_cooksnap_requires_at_least_one_photo(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $response = $this->actingAs($user)->post("/recipes/{$recipe->slug}/cooksnaps", [
            'photos' => [],
            'comment' => 'Test cooksnap',
        ]);

        $response->assertSessionHasErrors('photos');
    }

    public function test_cooksnap_comment_is_optional(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $response = $this->actingAs($user)->post("/recipes/{$recipe->slug}/cooksnaps", [
            'photos' => [UploadedFile::fake()->image('test.jpg')],
        ]);

        $this->assertDatabaseHas('cooksnaps', [
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'comment' => null,
        ]);
    }

    public function test_users_can_delete_their_own_cooksnaps(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);
        $cooksnap = Cooksnap::create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'comment' => 'Test cooksnap',
        ]);

        $response = $this->actingAs($user)->delete("/cooksnaps/{$cooksnap->id}");

        $this->assertDatabaseMissing('cooksnaps', ['id' => $cooksnap->id]);
    }

    public function test_users_cannot_delete_others_cooksnaps(): void
    {
        $author = User::factory()->create();
        $otherUser = User::factory()->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);
        $cooksnap = Cooksnap::create([
            'user_id' => $author->id,
            'recipe_id' => $recipe->id,
            'comment' => 'Test cooksnap',
        ]);

        $response = $this->actingAs($otherUser)->delete("/cooksnaps/{$cooksnap->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('cooksnaps', ['id' => $cooksnap->id]);
    }

    public function test_recipe_author_can_delete_any_cooksnap_on_their_recipe(): void
    {
        $recipeAuthor = User::factory()->create();
        $otherUser = User::factory()->create();
        $recipe = Recipe::factory()->create([
            'author_id' => $recipeAuthor->id,
            'is_public' => true
        ]);
        $cooksnap = Cooksnap::create([
            'user_id' => $otherUser->id,
            'recipe_id' => $recipe->id,
            'comment' => 'Test cooksnap',
        ]);

        $response = $this->actingAs($recipeAuthor)->delete("/cooksnaps/{$cooksnap->id}");

        $this->assertDatabaseMissing('cooksnaps', ['id' => $cooksnap->id]);
    }

    public function test_cooksnap_photos_are_stored(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $this->actingAs($user)->post("/recipes/{$recipe->slug}/cooksnaps", [
            'photos' => [
                UploadedFile::fake()->image('photo1.jpg'),
                UploadedFile::fake()->image('photo2.jpg'),
            ],
            'comment' => 'Multiple photos',
        ]);

        $cooksnap = Cooksnap::where('user_id', $user->id)->first();
        $this->assertCount(2, $cooksnap->getMedia('photos'));
    }
}
