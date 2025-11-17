<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Cooksnap;
use App\Models\Event;
use App\Models\Rating;
use App\Models\Recipe;
use App\Models\Report;
use App\Models\User;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        $userStats = User::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN 1 ELSE 0 END) as new_this_month,
            SUM(CASE WHEN updated_at >= ? THEN 1 ELSE 0 END) as active_this_week
        ', [now()->month, now()->year, now()->subWeek()])->first();

        $recipeStats = Recipe::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN is_public = 1 THEN 1 ELSE 0 END) as public,
            SUM(CASE WHEN is_public = 0 THEN 1 ELSE 0 END) as private,
            SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN 1 ELSE 0 END) as new_this_month
        ', [now()->month, now()->year])->first();

        $engagementStats = (object) [
            'comments' => Comment::count(),
            'cooksnaps' => Cooksnap::count(),
            'total_ratings' => Rating::count(),
            'avg_rating' => round(Rating::avg('rating') ?? 0, 2),
        ];

        $reportStats = Report::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as reviewing
        ', ['pending', 'reviewing'])->first();

        $eventStats = Event::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN start_date <= ? AND end_date >= ? THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN start_date > ? THEN 1 ELSE 0 END) as upcoming
        ', [now(), now(), now()])->first();

        $stats = [
            'users' => [
                'total' => $userStats->total,
                'new_this_month' => $userStats->new_this_month,
                'active_this_week' => $userStats->active_this_week,
            ],
            'recipes' => [
                'total' => $recipeStats->total,
                'public' => $recipeStats->public,
                'private' => $recipeStats->private,
                'new_this_month' => $recipeStats->new_this_month,
            ],
            'engagement' => [
                'comments' => $engagementStats->comments,
                'cooksnaps' => $engagementStats->cooksnaps,
                'total_ratings' => $engagementStats->total_ratings,
                'avg_rating' => $engagementStats->avg_rating,
            ],
            'moderation' => [
                'pending_reports' => $reportStats->pending,
                'reviewing_reports' => $reportStats->reviewing,
                'total_reports' => $reportStats->total,
            ],
            'events' => [
                'active' => $eventStats->active,
                'upcoming' => $eventStats->upcoming,
                'total' => $eventStats->total,
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

        $recentUsers = User::select('id', 'name', 'email', 'created_at')
            ->latest()
            ->limit(10)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentReports' => $recentReports,
            'topRecipes' => $topRecipes,
            'recentUsers' => $recentUsers,
        ]);
    }
}
