<?php

namespace Tests\Feature\Social;

use App\Models\Comment;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_comment_on_recipes(): void
    {
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $response = $this->post("/recipes/{$recipe->slug}/comments", [
            'content' => 'Test comment',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_comment_on_recipes(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $response = $this->actingAs($user)->post("/recipes/{$recipe->slug}/comments", [
            'content' => 'Great recipe!',
        ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'content' => 'Great recipe!',
        ]);
    }

    public function test_comment_requires_content(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);

        $response = $this->actingAs($user)->post("/recipes/{$recipe->slug}/comments", [
            'content' => '',
        ]);

        $response->assertSessionHasErrors('content');
    }

    public function test_users_can_delete_their_own_comments(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);
        $comment = Comment::create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'content' => 'Test comment',
        ]);

        $response = $this->actingAs($user)->delete("/comments/{$comment->id}");

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_users_cannot_delete_others_comments(): void
    {
        $author = User::factory()->create();
        $otherUser = User::factory()->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);
        $comment = Comment::create([
            'user_id' => $author->id,
            'recipe_id' => $recipe->id,
            'content' => 'Test comment',
        ]);

        $response = $this->actingAs($otherUser)->delete("/comments/{$comment->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }

    public function test_recipe_author_can_delete_any_comment_on_their_recipe(): void
    {
        $recipeAuthor = User::factory()->create();
        $commenter = User::factory()->create();
        $recipe = Recipe::factory()->for($recipeAuthor, 'author')->create(['is_public' => true]);
        $comment = Comment::create([
            'user_id' => $commenter->id,
            'recipe_id' => $recipe->id,
            'content' => 'Test comment',
        ]);

        $response = $this->actingAs($recipeAuthor)->delete("/comments/{$comment->id}");

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_users_can_upvote_comments(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);
        $comment = Comment::create([
            'user_id' => User::factory()->create()->id,
            'recipe_id' => $recipe->id,
            'content' => 'Test comment',
        ]);

        $response = $this->actingAs($user)->post("/comments/{$comment->id}/vote/up");

        $this->assertDatabaseHas('comment_votes', [
            'user_id' => $user->id,
            'comment_id' => $comment->id,
            'vote_type' => 'up',
        ]);
    }

    public function test_users_can_downvote_comments(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);
        $comment = Comment::create([
            'user_id' => User::factory()->create()->id,
            'recipe_id' => $recipe->id,
            'content' => 'Test comment',
        ]);

        $response = $this->actingAs($user)->post("/comments/{$comment->id}/vote/down");

        $this->assertDatabaseHas('comment_votes', [
            'user_id' => $user->id,
            'comment_id' => $comment->id,
            'vote_type' => 'down',
        ]);
    }

    public function test_users_can_change_their_vote(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['is_public' => true]);
        $comment = Comment::create([
            'user_id' => User::factory()->create()->id,
            'recipe_id' => $recipe->id,
            'content' => 'Test comment',
        ]);

        $this->actingAs($user)->post("/comments/{$comment->id}/vote/up");
        $this->actingAs($user)->post("/comments/{$comment->id}/vote/down");

        $this->assertDatabaseHas('comment_votes', [
            'user_id' => $user->id,
            'comment_id' => $comment->id,
            'vote_type' => 'down',
        ]);

        $this->assertEquals(1, $comment->votes()->count());
    }

    public function test_comment_score_is_calculated_correctly(): void
    {
        $recipe = Recipe::factory()->create(['is_public' => true]);
        $comment = Comment::create([
            'user_id' => User::factory()->create()->id,
            'recipe_id' => $recipe->id,
            'content' => 'Test comment',
        ]);

        $upvoter1 = User::factory()->create();
        $upvoter2 = User::factory()->create();
        $downvoter = User::factory()->create();

        $this->actingAs($upvoter1)->post("/comments/{$comment->id}/vote/up");
        $this->actingAs($upvoter2)->post("/comments/{$comment->id}/vote/up");
        $this->actingAs($downvoter)->post("/comments/{$comment->id}/vote/down");

        $upvotes = $comment->votes()->where('vote_type', 'up')->count();
        $downvotes = $comment->votes()->where('vote_type', 'down')->count();

        $this->assertEquals(2, $upvotes);
        $this->assertEquals(1, $downvotes);
    }
}
