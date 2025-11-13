<?php

namespace Tests\Feature\Community;

use App\Models\Event;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_events_can_be_retrieved(): void
    {
        $event = Event::create([
            'title' => 'Test Event',
            'description' => 'Test Description',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(7),
        ]);

        $this->assertDatabaseHas('events', [
            'title' => 'Test Event',
            'slug' => $event->slug,
        ]);
    }

    public function test_event_scopes_work_correctly(): void
    {
        Event::create([
            'title' => 'Active Event',
            'description' => 'Test',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
        ]);

        Event::create([
            'title' => 'Upcoming Event',
            'description' => 'Test',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(7),
        ]);

        Event::create([
            'title' => 'Ended Event',
            'description' => 'Test',
            'start_date' => now()->subDays(7),
            'end_date' => now()->subDay(),
        ]);

        $this->assertEquals(1, Event::active()->count());
        $this->assertEquals(1, Event::upcoming()->count());
        $this->assertEquals(1, Event::ended()->count());
    }

    public function test_guests_cannot_join_events(): void
    {
        $event = Event::create([
            'title' => 'Test Event',
            'description' => 'Test Description',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(7),
        ]);
        $recipe = Recipe::factory()->create();

        $response = $this->post("/events/{$event->slug}/join", [
            'recipe_id' => $recipe->id,
        ]);

        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_join_events(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            'title' => 'Test Event',
            'description' => 'Test Description',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(7),
        ]);
        $recipe = Recipe::factory()->create(['author_id' => $user->id]);

        $response = $this->actingAs($user)->post("/events/{$event->slug}/join", [
            'recipe_id' => $recipe->id,
        ]);

        $this->assertDatabaseHas('event_participants', [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
        ]);
    }

    public function test_users_can_leave_events(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            'title' => 'Test Event',
            'description' => 'Test Description',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(7),
        ]);
        $recipe = Recipe::factory()->create(['author_id' => $user->id]);

        $event->participants()->attach($user->id, ['recipe_id' => $recipe->id]);

        $response = $this->actingAs($user)->delete("/events/{$event->slug}/leave");

        $this->assertDatabaseMissing('event_participants', [
            'event_id' => $event->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_authenticated_users_can_create_events(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/events', [
            'title' => 'New Event',
            'description' => 'Event Description',
            'start_date' => now()->addDay()->toDateTimeString(),
            'end_date' => now()->addDays(7)->toDateTimeString(),
        ]);

        $this->assertDatabaseHas('events', [
            'title' => 'New Event',
        ]);
    }

    public function test_event_requires_title(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/events', [
            'description' => 'Event Description',
            'start_date' => now()->addDay()->toDateTimeString(),
            'end_date' => now()->addDays(7)->toDateTimeString(),
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_event_end_date_must_be_after_start_date(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/events', [
            'title' => 'Test Event',
            'description' => 'Event Description',
            'start_date' => now()->addDays(7)->toDateTimeString(),
            'end_date' => now()->addDay()->toDateTimeString(),
        ]);

        $response->assertSessionHasErrors('end_date');
    }
}
