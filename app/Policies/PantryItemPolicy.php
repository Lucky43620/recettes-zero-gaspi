<?php

namespace App\Policies;

use App\Models\PantryItem;
use App\Models\User;

class PantryItemPolicy
{
    public function update(User $user, PantryItem $pantryItem): bool
    {
        return $user->id === $pantryItem->user_id;
    }

    public function delete(User $user, PantryItem $pantryItem): bool
    {
        return $user->id === $pantryItem->user_id;
    }
}
