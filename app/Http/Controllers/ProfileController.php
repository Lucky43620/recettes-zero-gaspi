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

        // Top 3 recettes par note moyenne
        $topRecipes = $user->recipes()
            ->where('is_public', true)
            ->whereNotNull('rating_avg')
            ->orderBy('rating_avg', 'desc')
            ->orderBy('rating_count', 'desc')
            ->with('media')
            ->limit(3)
            ->get();

        // Recettes récentes (exclure le top 3)
        $recentRecipes = $user->recipes()
            ->where('is_public', true)
            ->whereNotIn('id', $topRecipes->pluck('id'))
            ->with('media')
            ->latest()
            ->limit(6)
            ->get();

        // Moyenne globale des notes de toutes les recettes
        $averageRating = $user->recipes()
            ->where('is_public', true)
            ->whereNotNull('rating_avg')
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
