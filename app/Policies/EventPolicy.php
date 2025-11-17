<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Event $event): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user, Event $event): bool
    {
        return $user->is_admin;
    }

    public function delete(User $user, Event $event): bool
    {
        return $user->is_admin;
    }

    public function join(User $user, Event $event): bool
    {
        if (!$event->is_active) {
            return false;
        }

        if ($event->participants()->where('user_id', $user->id)->exists()) {
            return false;
        }

        return true;
    }

    public function leave(User $user, Event $event): bool
    {
        return $event->participants()->where('user_id', $user->id)->exists();
    }
}
