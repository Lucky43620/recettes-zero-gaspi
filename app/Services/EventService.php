<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\DB;

class EventService
{
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

    public function getUserParticipation(Event $event, int $userId): ?object
    {
        return DB::table('event_participants')
            ->where('event_id', $event->id)
            ->where('user_id', $userId)
            ->first();
    }
}
