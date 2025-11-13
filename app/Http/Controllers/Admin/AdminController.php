<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Cooksnap;
use App\Models\Event;
use App\Models\Recipe;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'users' => [
                'total' => User::count(),
                'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
                'active_this_week' => User::where('updated_at', '>=', now()->subWeek())->count(),
            ],
            'recipes' => [
                'total' => Recipe::count(),
                'public' => Recipe::where('is_public', true)->count(),
                'private' => Recipe::where('is_public', false)->count(),
                'new_this_month' => Recipe::whereMonth('created_at', now()->month)->count(),
            ],
            'engagement' => [
                'comments' => Comment::count(),
                'cooksnaps' => Cooksnap::count(),
                'total_ratings' => DB::table('ratings')->count(),
                'avg_rating' => round(DB::table('ratings')->avg('rating'), 2),
            ],
            'moderation' => [
                'pending_reports' => Report::pending()->count(),
                'reviewing_reports' => Report::reviewing()->count(),
                'total_reports' => Report::count(),
            ],
            'events' => [
                'active' => Event::active()->count(),
                'upcoming' => Event::upcoming()->count(),
                'total' => Event::count(),
            ],
        ];

        $recentReports = Report::with(['reporter', 'reportable'])
            ->latest()
            ->limit(10)
            ->get();

        $topRecipes = Recipe::where('is_public', true)
            ->orderByDesc('rating_avg')
            ->orderByDesc('rating_count')
            ->with('author')
            ->limit(10)
            ->get();

        $recentUsers = User::latest()->limit(10)->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentReports' => $recentReports,
            'topRecipes' => $topRecipes,
            'recentUsers' => $recentUsers,
        ]);
    }
}
