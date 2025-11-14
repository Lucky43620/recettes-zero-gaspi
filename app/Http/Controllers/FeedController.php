<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FeedController extends Controller
{
    public function index()
    {
        $feed = Recipe::with(['author', 'media'])
            ->where('is_public', true)
            ->whereHas('author', function ($query) {
                $query->whereIn('id', function ($subQuery) {
                    $subQuery->select('following_id')
                        ->from('followers')
                        ->where('follower_id', Auth::id());
                });
            })
            ->latest()
            ->paginate(12);

        return Inertia::render('Feed/Index', [
            'feed' => $feed,
        ]);
    }
}
