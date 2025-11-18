<?php

namespace App\Traits;

trait HandlesCheckboxValidation
{
    protected function validateCheckbox($field)
    {
        return $this->validate([$field => 'accepted']);
    }

    protected function checkboxToBoolean($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
