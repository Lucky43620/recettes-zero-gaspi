<?php

use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\RecipeSearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('ingredients')->group(function () {
    Route::get('/search', [IngredientController::class, 'search']);
    Route::post('/barcode', [IngredientController::class, 'findByBarcode']);
    Route::post('/find-or-create', [IngredientController::class, 'findOrCreate']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/recipes/search-with-pantry', [RecipeSearchController::class, 'searchWithPantryIngredients']);
});
