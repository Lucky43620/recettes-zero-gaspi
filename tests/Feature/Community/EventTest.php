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

    public function test_guests_can_view_events_list(): void
    {
        Event::create([
            'title' => 'Test Event',
            'description' => 'Test Description',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(7),
        ]);

        $response = $this->get('/events');

        $response->assertOk();
    }

    public function test_guests_can_view_event_details(): void
    {
        $event = Event::create([
            'title' => 'Test Event',
            'description' => 'Test Description',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(7),
        ]);

        $response = $this->get("/events/{$event->slug}");

        $response->assertOk();
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
