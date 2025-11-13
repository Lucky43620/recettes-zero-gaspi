<?php

namespace App\Policies;

use App\Models\Cooksnap;
use App\Models\User;

class CooksnapPolicy
{
    public function delete(User $user, Cooksnap $cooksnap): bool
    {
        $cooksnap->loadMissing('recipe');

        return $user->id === $cooksnap->user_id || $user->id === $cooksnap->recipe->author_id;
    }
}
