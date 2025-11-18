<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventService
{
    /**
     * Get all events with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllEvents(int $perPage = 15): LengthAwarePaginator
    {
        return Event::withCount('participants')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get active events
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getActiveEvents(int $perPage = 15): LengthAwarePaginator
    {
        return Event::active()
            ->withCount('participants')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get upcoming events
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUpcomingEvents(int $perPage = 15): LengthAwarePaginator
    {
        return Event::upcoming()
            ->withCount('participants')
            ->orderBy('start_date')
            ->paginate($perPage);
    }

    /**
     * Get ended events
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getEndedEvents(int $perPage = 15): LengthAwarePaginator
    {
        return Event::ended()
            ->withCount('participants')
            ->latest('end_date')
            ->paginate($perPage);
    }

    /**
     * Get event by ID
     *
     * @param int $id
     * @return Event|null
     */
    public function getEventById(int $id): ?Event
    {
        return Event::withCount('participants')->find($id);
    }

    /**
     * Get event by slug
     *
     * @param string $slug
     * @return Event|null
     */
    public function getEventBySlug(string $slug): ?Event
    {
        return Event::withCount('participants')
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Create a new event
     *
     * @param array $data
     * @return Event|null
     */
    public function createEvent(array $data): ?Event
    {
        try {
            $event = Event::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'rules' => $data['rules'] ?? null,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'is_active' => $data['is_active'] ?? true,
            ]);

            Log::info('Event created', [
                'event_id' => $event->id,
                'title' => $event->title,
            ]);

            return $event;
        } catch (\Exception $e) {
            Log::error('Failed to create event', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return null;
        }
    }

    /**
     * Update an event
     *
     * @param Event $event
     * @param array $data
     * @return bool
     */
    public function updateEvent(Event $event, array $data): bool
    {
        try {
            $event->update([
                'title' => $data['title'] ?? $event->title,
                'description' => $data['description'] ?? $event->description,
                'rules' => $data['rules'] ?? $event->rules,
                'start_date' => $data['start_date'] ?? $event->start_date,
                'end_date' => $data['end_date'] ?? $event->end_date,
                'is_active' => $data['is_active'] ?? $event->is_active,
            ]);

            Log::info('Event updated', [
                'event_id' => $event->id,
                'title' => $event->title,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update event', [
                'event_id' => $event->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Delete an event
     *
     * @param Event $event
     * @return bool
     */
    public function deleteEvent(Event $event): bool
    {
        try {
            $eventId = $event->id;
            $event->participants()->detach();
            $event->delete();

            Log::info('Event deleted', [
                'event_id' => $eventId,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete event', [
                'event_id' => $event->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Activate an event
     *
     * @param Event $event
     * @return bool
     */
    public function activateEvent(Event $event): bool
    {
        try {
            $event->update(['is_active' => true]);

            Log::info('Event activated', [
                'event_id' => $event->id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to activate event', [
                'event_id' => $event->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Deactivate an event
     *
     * @param Event $event
     * @return bool
     */
    public function deactivateEvent(Event $event): bool
    {
        try {
            $event->update(['is_active' => false]);

            Log::info('Event deactivated', [
                'event_id' => $event->id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to deactivate event', [
                'event_id' => $event->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get leaderboard for an event
     *
     * @param Event $event
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getLeaderboard(Event $event, int $limit = 10): \Illuminate\Support\Collection
    {
        return DB::table('event_participants')
            ->join('users', 'event_participants.user_id', '=', 'users.id')
            ->leftJoin('recipes', 'event_participants.recipe_id', '=', 'recipes.id')
            ->where('event_participants.event_id', $event->id)
            ->select(
                'users.id',
                'users.name',
                'users.profile_photo_path',
                'event_participants.score',
                'recipes.title as recipe_title',
                'recipes.slug as recipe_slug'
            )
            ->orderByDesc('event_participants.score')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user participation in an event
     *
     * @param Event $event
     * @param int $userId
     * @return object|null
     */
    public function getUserParticipation(Event $event, int $userId): ?object
    {
        return DB::table('event_participants')
            ->where('event_id', $event->id)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Add participant to event
     *
     * @param Event $event
     * @param int $userId
     * @param int|null $recipeId
     * @param int $score
     * @return bool
     */
    public function addParticipant(Event $event, int $userId, ?int $recipeId = null, int $score = 0): bool
    {
        try {
            $event->participants()->attach($userId, [
                'recipe_id' => $recipeId,
                'score' => $score,
            ]);

            Log::info('Participant added to event', [
                'event_id' => $event->id,
                'user_id' => $userId,
                'recipe_id' => $recipeId,
                'score' => $score,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to add participant to event', [
                'event_id' => $event->id,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Remove participant from event
     *
     * @param Event $event
     * @param int $userId
     * @return bool
     */
    public function removeParticipant(Event $event, int $userId): bool
    {
        try {
            $event->participants()->detach($userId);

            Log::info('Participant removed from event', [
                'event_id' => $event->id,
                'user_id' => $userId,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to remove participant from event', [
                'event_id' => $event->id,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Update participant score
     *
     * @param Event $event
     * @param int $userId
     * @param int $score
     * @return bool
     */
    public function updateParticipantScore(Event $event, int $userId, int $score): bool
    {
        try {
            $event->participants()->updateExistingPivot($userId, [
                'score' => $score,
            ]);

            Log::info('Participant score updated', [
                'event_id' => $event->id,
                'user_id' => $userId,
                'score' => $score,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update participant score', [
                'event_id' => $event->id,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Check if user is participating in event
     *
     * @param Event $event
     * @param int $userId
     * @return bool
     */
    public function isUserParticipating(Event $event, int $userId): bool
    {
        return $event->participants()->where('user_id', $userId)->exists();
    }

    /**
     * Get all participants of an event
     *
     * @param Event $event
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getParticipants(Event $event, int $perPage = 20): LengthAwarePaginator
    {
        return $event->participants()
            ->withPivot('recipe_id', 'score')
            ->paginate($perPage);
    }

    /**
     * Get event statistics
     *
     * @param Event $event
     * @return array
     */
    public function getEventStats(Event $event): array
    {
        $participants = DB::table('event_participants')
            ->where('event_id', $event->id)
            ->get();

        return [
            'total_participants' => $participants->count(),
            'average_score' => $participants->avg('score'),
            'highest_score' => $participants->max('score'),
            'lowest_score' => $participants->min('score'),
            'total_score' => $participants->sum('score'),
        ];
    }

    /**
     * Check if event is currently active
     *
     * @param Event $event
     * @return bool
     */
    public function isEventActive(Event $event): bool
    {
        return $event->is_active
            && $event->start_date <= now()
            && $event->end_date >= now();
    }

    /**
     * Check if event has ended
     *
     * @param Event $event
     * @return bool
     */
    public function hasEventEnded(Event $event): bool
    {
        return $event->end_date < now();
    }

    /**
     * Check if event is upcoming
     *
     * @param Event $event
     * @return bool
     */
    public function isEventUpcoming(Event $event): bool
    {
        return $event->is_active && $event->start_date > now();
    }
}
