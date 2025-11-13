<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class GdprService
{
    public function exportUserData(User $user): string
    {
        $data = [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at->toDateTimeString(),
            ],
            'recipes' => $user->recipes()->with(['media', 'steps', 'ingredients'])->get()->toArray(),
            'comments' => $user->comments()->with('recipe')->get()->toArray(),
            'ratings' => $user->ratings()->with('recipe')->get()->toArray(),
            'favorites' => $user->favorites()->get()->toArray(),
            'collections' => $user->collections()->with('recipes')->get()->toArray(),
            'followers' => $user->followers()->select('id', 'name', 'email')->get()->toArray(),
            'following' => $user->following()->select('id', 'name', 'email')->get()->toArray(),
            'meal_plans' => $user->mealPlans()->with('recipes')->get()->toArray(),
            'shopping_lists' => $user->shoppingLists()->with('items')->get()->toArray(),
            'pantry' => $user->pantryItems()->with('ingredient')->get()->toArray(),
        ];

        $filename = 'user_data_' . $user->id . '_' . now()->timestamp . '.json';
        $path = 'gdpr-exports/' . $filename;

        Storage::put($path, json_encode($data, JSON_PRETTY_PRINT));

        return Storage::path($path);
    }

    public function deleteUserData(User $user): void
    {
        $user->recipes()->each(function ($recipe) {
            $recipe->clearMediaCollection('images');
            $recipe->delete();
        });

        $user->cooksnaps()->each(function ($cooksnap) {
            $cooksnap->clearMediaCollection('photos');
            $cooksnap->delete();
        });

        $user->comments()->delete();
        $user->ratings()->delete();
        $user->favorites()->detach();
        $user->collections()->delete();
        $user->followers()->detach();
        $user->following()->detach();
        $user->mealPlans()->delete();
        $user->shoppingLists()->delete();
        $user->pantryItems()->delete();
        $user->reports()->delete();
        $user->badges()->detach();
        $user->eventParticipations()->detach();

        $user->clearMediaCollection();

        $user->delete();
    }
}
