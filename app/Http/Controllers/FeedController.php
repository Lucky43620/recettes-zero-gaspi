<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FeedController extends Controller
{
    public function index()
    {
        $followingIds = Auth::user()->following()->pluck('users.id');

        $feed = Recipe::with(['author', 'media'])
            ->where('is_public', true)
            ->whereIn('author_id', $followingIds)
            ->latest()
            ->paginate(12);

        return Inertia::render('Feed/Index', [
            'feed' => $feed,
        ]);
    }
}
