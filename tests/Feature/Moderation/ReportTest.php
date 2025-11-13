<?php

namespace Tests\Feature\Moderation;

use App\Models\Comment;
use App\Models\Recipe;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_report_content(): void
    {
        $recipe = Recipe::factory()->create();

        $response = $this->post('/reports', [
            'reportable_type' => 'App\Models\Recipe',
            'reportable_id' => $recipe->id,
            'reason' => 'spam',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_report_recipes(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();

        $response = $this->actingAs($user)->post('/reports', [
            'reportable_type' => 'App\Models\Recipe',
            'reportable_id' => $recipe->id,
            'reason' => 'spam',
            'description' => 'This is spam content',
        ]);

        $this->assertDatabaseHas('reports', [
            'reporter_id' => $user->id,
            'reportable_type' => 'App\Models\Recipe',
            'reportable_id' => $recipe->id,
            'reason' => 'spam',
        ]);
    }

    public function test_authenticated_users_can_report_comments(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();
        $comment = Comment::create([
            'recipe_id' => $recipe->id,
            'user_id' => User::factory()->create()->id,
            'content' => 'Test comment',
        ]);

        $response = $this->actingAs($user)->post('/reports', [
            'reportable_type' => 'App\Models\Comment',
            'reportable_id' => $comment->id,
            'reason' => 'offensive',
        ]);

        $this->assertDatabaseHas('reports', [
            'reportable_type' => 'App\Models\Comment',
            'reportable_id' => $comment->id,
        ]);
    }

    public function test_report_requires_reason(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();

        $response = $this->actingAs($user)->post('/reports', [
            'reportable_type' => 'App\Models\Recipe',
            'reportable_id' => $recipe->id,
        ]);

        $response->assertSessionHasErrors('reason');
    }

    public function test_report_requires_valid_reportable_type(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/reports', [
            'reportable_type' => 'App\Models\InvalidModel',
            'reportable_id' => 1,
            'reason' => 'spam',
        ]);

        $response->assertSessionHasErrors('reportable_type');
    }

    public function test_reports_can_be_updated(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();
        $recipe = Recipe::factory()->create();

        $report = Report::create([
            'reporter_id' => $user->id,
            'reportable_type' => 'App\Models\Recipe',
            'reportable_id' => $recipe->id,
            'reason' => 'spam',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->put("/reports/{$report->id}", [
            'status' => 'resolved',
            'resolution_note' => 'Handled appropriately',
        ]);

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => 'resolved',
            'reviewed_by' => $admin->id,
        ]);
    }

    public function test_report_scopes_work_correctly(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();

        Report::create([
            'reporter_id' => $user->id,
            'reportable_type' => 'App\Models\Recipe',
            'reportable_id' => $recipe->id,
            'reason' => 'spam',
            'status' => 'pending',
        ]);

        Report::create([
            'reporter_id' => $user->id,
            'reportable_type' => 'App\Models\Recipe',
            'reportable_id' => $recipe->id,
            'reason' => 'offensive',
            'status' => 'reviewing',
        ]);

        Report::create([
            'reporter_id' => $user->id,
            'reportable_type' => 'App\Models\Recipe',
            'reportable_id' => $recipe->id,
            'reason' => 'spam',
            'status' => 'resolved',
        ]);

        $this->assertEquals(1, Report::pending()->count());
        $this->assertEquals(1, Report::reviewing()->count());
        $this->assertEquals(1, Report::resolved()->count());
    }
}
