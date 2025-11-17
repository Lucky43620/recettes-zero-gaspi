<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\Report;
use App\Models\User;
use App\Services\AdminStatsService;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function __construct(
        private AdminStatsService $adminStatsService
    ) {}

    public function dashboard()
    {
        $stats = $this->adminStatsService->getDashboardStats();

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
