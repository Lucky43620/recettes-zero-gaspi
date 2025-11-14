<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return Inertia::render('Profile/Show', [
            'confirmsTwoFactorAuthentication' => config('jetstream.confirms_two_factor_authentication'),
            'sessions' => $this->sessions($request)->all(),
        ]);
    }

    protected function sessions(Request $request)
    {
        if (config('session.driver') !== 'database') {
            return collect();
        }

        return collect(
            \DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                    ->where('user_id', $request->user()->getAuthIdentifier())
                    ->orderBy('last_activity', 'desc')
                    ->get()
        )->map(function ($session) use ($request) {
            return (object) [
                'agent' => (object) [
                    'is_desktop' => true,
                    'platform' => $this->extractPlatform($session->user_agent ?? ''),
                    'browser' => $this->extractBrowser($session->user_agent ?? ''),
                ],
                'ip_address' => $session->ip_address,
                'is_current_device' => $session->id === $request->session()->getId(),
                'last_active' => \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
            ];
        });
    }

    protected function extractPlatform(string $userAgent): string
    {
        if (stripos($userAgent, 'Windows') !== false) return 'Windows';
        if (stripos($userAgent, 'Mac') !== false) return 'Mac';
        if (stripos($userAgent, 'Linux') !== false) return 'Linux';
        if (stripos($userAgent, 'Android') !== false) return 'Android';
        if (stripos($userAgent, 'iOS') !== false || stripos($userAgent, 'iPhone') !== false) return 'iOS';
        return 'Unknown';
    }

    protected function extractBrowser(string $userAgent): string
    {
        if (stripos($userAgent, 'Firefox') !== false) return 'Firefox';
        if (stripos($userAgent, 'Chrome') !== false) return 'Chrome';
        if (stripos($userAgent, 'Safari') !== false) return 'Safari';
        if (stripos($userAgent, 'Edge') !== false) return 'Edge';
        return 'Unknown';
    }

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
            'profileUser' => array_merge($user->toArray(), [
                'is_premium' => $user->isPremium(),
            ]),
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
