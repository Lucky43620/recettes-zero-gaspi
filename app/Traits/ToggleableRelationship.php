<?php

namespace App\Traits;

trait ToggleableRelationship
{
    public function toggle($relation, $id)
    {
        if ($this->{$relation}()->where('id', $id)->exists()) {
            $this->{$relation}()->detach($id);
            return false;
        }

        $this->{$relation}()->attach($id);
        return true;
    }
}
