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

        $allPublicRecipes = $user->recipes()
            ->where('is_public', true)
            ->with('media')
            ->select('id', 'author_id', 'title', 'slug', 'rating_avg', 'rating_count', 'created_at')
            ->get();

        $topRecipes = $allPublicRecipes
            ->filter(fn($r) => $r->rating_avg !== null)
            ->sortByDesc('rating_avg')
            ->sortByDesc('rating_count')
            ->take(3)
            ->values();

        $recentRecipes = $allPublicRecipes
            ->whereNotIn('id', $topRecipes->pluck('id'))
            ->sortByDesc('created_at')
            ->take(6)
            ->values();

        $averageRating = $allPublicRecipes
            ->filter(fn($r) => $r->rating_avg !== null)
            ->avg('rating_avg');

        $isFollowing = Auth::check() ? Auth::user()->isFollowing($user) : false;
        $isOwnProfile = Auth::check() ? Auth::id() === $user->id : false;

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
