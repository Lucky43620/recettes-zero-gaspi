<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = str_replace(['%', '_'], ['\%', '\_'], $request->search);
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->withCount(['recipes', 'comments', 'followers'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only('search'),
        ]);
    }

    public function show(User $user)
    {
        $user->loadCount(['recipes', 'comments', 'ratings', 'collections', 'followers', 'following']);
        $user->load(['recipes' => fn($q) => $q->select('id', 'author_id', 'is_public')]);

        $stats = [
            'recipes_count' => $user->recipes_count,
            'public_recipes' => $user->recipes->where('is_public', true)->count(),
            'comments_count' => $user->comments_count,
            'ratings_count' => $user->ratings_count,
            'followers_count' => $user->followers_count,
            'following_count' => $user->following_count,
            'collections_count' => $user->collections_count,
        ];

        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    public function destroy(User $user)
    {
        \DB::transaction(function () use ($user) {
            $recipes = $user->recipes()->with('media')->get();

            foreach ($recipes as $recipe) {
                $recipe->clearMediaCollection('images');
                $recipe->delete();
            }

            $user->delete();
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé');
    }
}
