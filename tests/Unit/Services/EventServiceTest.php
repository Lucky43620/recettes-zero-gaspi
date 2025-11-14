<?php

namespace Tests\Unit\Services;

use App\Models\Event;
use App\Models\Recipe;
use App\Models\User;
use App\Services\EventService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EventServiceTest extends TestCase
{
    use RefreshDatabase;

    private EventService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(EventService::class);
    }

    public function test_get_leaderboard_returns_top_participants()
    {
        $event = Event::factory()->create();
        $user1 = User::factory()->create(['name' => 'User 1']);
        $user2 = User::factory()->create(['name' => 'User 2']);
        $user3 = User::factory()->create(['name' => 'User 3']);
        $recipe = Recipe::factory()->create();

        DB::table('event_participants')->insert([
            ['event_id' => $event->id, 'user_id' => $user1->id, 'recipe_id' => $recipe->id, 'score' => 100],
            ['event_id' => $event->id, 'user_id' => $user2->id, 'recipe_id' => $recipe->id, 'score' => 50],
            ['event_id' => $event->id, 'user_id' => $user3->id, 'recipe_id' => $recipe->id, 'score' => 75],
        ]);

        $leaderboard = $this->service->getLeaderboard($event, 10);

        $this->assertCount(3, $leaderboard);
        $this->assertEquals($user1->id, $leaderboard[0]->id);
        $this->assertEquals(100, $leaderboard[0]->score);
        $this->assertEquals($user3->id, $leaderboard[1]->id);
        $this->assertEquals($user2->id, $leaderboard[2]->id);
    }

    public function test_get_leaderboard_respects_limit()
    {
        $event = Event::factory()->create();

        for ($i = 0; $i < 15; $i++) {
            $user = User::factory()->create();
            $recipe = Recipe::factory()->create();
            DB::table('event_participants')->insert([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'recipe_id' => $recipe->id,
                'score' => rand(1, 100),
            ]);
        }

        $leaderboard = $this->service->getLeaderboard($event, 10);

        $this->assertCount(10, $leaderboard);
    }

    public function test_get_user_participation_returns_data()
    {
        $event = Event::factory()->create();
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();

        DB::table('event_participants')->insert([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'score' => 85,
        ]);

        $participation = $this->service->getUserParticipation($event, $user->id);

        $this->assertNotNull($participation);
        $this->assertEquals(85, $participation->score);
        $this->assertEquals($user->id, $participation->user_id);
    }

    public function test_get_user_participation_returns_null_if_not_participating()
    {
        $event = Event::factory()->create();
        $user = User::factory()->create();

        $participation = $this->service->getUserParticipation($event, $user->id);

        $this->assertNull($participation);
    }

    public function test_leaderboard_includes_recipe_information()
    {
        $event = Event::factory()->create();
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['title' => 'Test Recipe', 'slug' => 'test-recipe']);

        DB::table('event_participants')->insert([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'score' => 100,
        ]);

        $leaderboard = $this->service->getLeaderboard($event, 10);

        $this->assertEquals('Test Recipe', $leaderboard[0]->recipe_title);
        $this->assertEquals('test-recipe', $leaderboard[0]->recipe_slug);
    }
}
