<?php

namespace App\Services;

use App\Models\User;

class FollowService
{
    public function follow(User $follower, User $following): void
    {
        if (!$this->isFollowing($follower, $following) && $follower->id !== $following->id) {
            $follower->following()->attach($following->id);
        }
    }

    public function unfollow(User $follower, User $following): void
    {
        $follower->following()->detach($following->id);
    }

    public function toggle(User $follower, User $following): bool
    {
        if ($follower->id === $following->id) {
            return false;
        }

        if ($this->isFollowing($follower, $following)) {
            $this->unfollow($follower, $following);
            return false;
        }

        $this->follow($follower, $following);
        return true;
    }

    public function isFollowing(User $follower, User $following): bool
    {
        return $follower->following()->where('following_id', $following->id)->exists();
    }

    public function getFollowers(User $user, int $perPage = 20)
    {
        return $user->followers()
            ->withCount('recipes', 'followers')
            ->paginate($perPage);
    }

    public function getFollowing(User $user, int $perPage = 20)
    {
        return $user->following()
            ->withCount('recipes', 'followers')
            ->paginate($perPage);
    }

    public function getFollowerIds(User $user): array
    {
        return $user->followers()->pluck('follower_id')->toArray();
    }

    public function getFollowingIds(User $user): array
    {
        return $user->following()->pluck('following_id')->toArray();
    }
}
