<?php

namespace App\Traits;

trait HasMediaCleanup
{
    protected static function bootHasMediaCleanup()
    {
        static::deleting(function ($model) {
            if (method_exists($model, 'clearMediaCollection')) {
                $model->clearMediaCollection();
            }
        });
    }
}
