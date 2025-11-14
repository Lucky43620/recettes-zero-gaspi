<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        $user->loadCount(['recipes', 'followers', 'following']);

        $topRecipes = $user->recipes()
            ->where('is_public', true)
            ->whereNotNull('rating_avg')
            ->with('media')
            ->select('id', 'author_id', 'title', 'slug', 'rating_avg', 'rating_count', 'created_at')
            ->orderByDesc('rating_avg')
            ->orderByDesc('rating_count')
            ->limit(3)
            ->get();

        $topRecipeIds = $topRecipes->pluck('id');

        $recentRecipes = $user->recipes()
            ->where('is_public', true)
            ->whereNotIn('id', $topRecipeIds)
            ->with('media')
            ->select('id', 'author_id', 'title', 'slug', 'rating_avg', 'rating_count', 'created_at')
            ->latest()
            ->limit(6)
            ->get();

        $averageRating = $user->recipes()
            ->where('is_public', true)
            ->whereNotNull('rating_avg')
            ->avg('rating_avg');

        $isFollowing = false;
        $isOwnProfile = false;

        if (Auth::check()) {
            $authUser = Auth::user();
            $isFollowing = $authUser->following()->where('following_id', $user->id)->exists();
            $isOwnProfile = $authUser->id === $user->id;
        }

        return Inertia::render('Profile/PublicProfile', [
            'profileUser' => $user,
            'topRecipes' => $topRecipes,
            'recentRecipes' => $recentRecipes,
            'averageRating' => $averageRating ? round($averageRating, 1) : null,
            'isFollowing' => $isFollowing,
            'isOwnProfile' => $isOwnProfile,
        ]);
    }

    public function followers(User $user)
    {
        $followers = $user->followers()
            ->withCount('recipes', 'followers')
            ->paginate(20);

        return Inertia::render('Profile/Followers', [
            'profileUser' => $user,
            'followers' => $followers,
        ]);
    }

    public function following(User $user)
    {
        $following = $user->following()
            ->withCount('recipes', 'followers')
            ->paginate(20);

        return Inertia::render('Profile/Following', [
            'profileUser' => $user,
            'following' => $following,
        ]);
    }
}
