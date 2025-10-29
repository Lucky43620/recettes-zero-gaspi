<?php

namespace App\Policies;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RecipePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Recipe $recipe): bool
    {
        return $recipe->is_public || ($user && $recipe->author_id === $user->id);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Recipe $recipe): bool
    {
        return $recipe->author_id === $user->id;
    }

    public function delete(User $user, Recipe $recipe): bool
    {
        return $recipe->author_id === $user->id;
    }

    public function restore(User $user, Recipe $recipe): bool
    {
        return $recipe->author_id === $user->id;
    }

    public function forceDelete(User $user, Recipe $recipe): bool
    {
        return $recipe->author_id === $user->id;
    }
}
