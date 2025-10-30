<?php

use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RecipeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Routes Publiques (accessible sans authentification)
|--------------------------------------------------------------------------
*/

// Page d'accueil
Route::get('/', function () {
    $featuredRecipes = \App\Models\Recipe::where('is_public', true)
        ->with(['author', 'media'])
        ->orderBy('rating_avg', 'desc')
        ->orderBy('rating_count', 'desc')
        ->limit(6)
        ->get();

    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'featuredRecipes' => $featuredRecipes,
    ]);
})->name('home');

// Recettes publiques
Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');

// Profils publics
Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/profile/{user}/followers', [ProfileController::class, 'followers'])->name('profile.followers');
Route::get('/profile/{user}/following', [ProfileController::class, 'following'])->name('profile.following');

/*
|--------------------------------------------------------------------------
| Routes Authentifiées (nécessite connexion)
|--------------------------------------------------------------------------
*/

// Routes GET pour recettes (doivent être avant la route show publique)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/recipes/create', [RecipeController::class, 'create'])->name('recipes.create');
    Route::get('/recipes/{recipe:slug}/edit', [RecipeController::class, 'edit'])->name('recipes.edit');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        $user = Auth::user();

        $stats = [
            'totalRecipes' => $user->recipes()->count(),
            'publicRecipes' => $user->recipes()->where('is_public', true)->count(),
            'privateRecipes' => $user->recipes()->where('is_public', false)->count(),
            'totalRatings' => $user->recipes()->withCount('ratings')->get()->sum('ratings_count'),
            'averageRating' => $user->recipes()->whereNotNull('rating_avg')->avg('rating_avg'),
            'totalComments' => $user->recipes()->withCount('comments')->get()->sum('comments_count'),
            'recentRecipes' => $user->recipes()->with(['media'])->latest()->limit(3)->get(),
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats
        ]);
    })->name('dashboard');

    // Mes recettes
    Route::get('/my-recipes', [RecipeController::class, 'myRecipes'])->name('recipes.my');

    // Gestion des recettes (CRUD)
    Route::post('/recipes', [RecipeController::class, 'store'])->name('recipes.store');
    Route::put('/recipes/{recipe:slug}', [RecipeController::class, 'update'])->name('recipes.update');
    Route::delete('/recipes/{recipe:slug}', [RecipeController::class, 'destroy'])->name('recipes.destroy');

    // Feed personnalisé
    Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');

    // Interactions sociales
    Route::post('/users/{user}/follow', [FollowController::class, 'follow'])->name('users.follow');
    Route::delete('/users/{user}/unfollow', [FollowController::class, 'unfollow'])->name('users.unfollow');

    // Ratings
    Route::post('/recipes/{recipe:slug}/ratings', [RatingController::class, 'store'])->name('ratings.store');
    Route::delete('/recipes/{recipe:slug}/ratings', [RatingController::class, 'destroy'])->name('ratings.destroy');

    // Commentaires
    Route::post('/recipes/{recipe:slug}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{comment}/vote/{type}', [CommentController::class, 'vote'])->name('comments.vote');

    // Favoris
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/recipes/{recipe:slug}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // Collections
    Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');
    Route::post('/collections', [CollectionController::class, 'store'])->name('collections.store');
    Route::get('/collections/{collection}', [CollectionController::class, 'show'])->name('collections.show');
    Route::put('/collections/{collection}', [CollectionController::class, 'update'])->name('collections.update');
    Route::delete('/collections/{collection}', [CollectionController::class, 'destroy'])->name('collections.destroy');
    Route::post('/collections/{collection}/recipes/{recipe:slug}', [CollectionController::class, 'addRecipe'])->name('collections.recipes.add');
    Route::delete('/collections/{collection}/recipes/{recipe:slug}', [CollectionController::class, 'removeRecipe'])->name('collections.recipes.remove');
    Route::post('/collections/{collection}/reorder', [CollectionController::class, 'reorder'])->name('collections.reorder');
});

// Route publique pour afficher une recette (doit être après les routes CRUD)
Route::get('/recipes/{recipe:slug}', [RecipeController::class, 'show'])->name('recipes.show');
