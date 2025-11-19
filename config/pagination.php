<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Pagination Settings
    |--------------------------------------------------------------------------
    |
    | These values define the default pagination settings used across the
    | application. You can override these on a per-model or per-page basis.
    |
    */

    'per_page' => 15,
    'max_per_page' => 100,
    'default' => 15,

    /*
    |--------------------------------------------------------------------------
    | Model-Specific Pagination Settings
    |--------------------------------------------------------------------------
    |
    | Configure pagination for specific models and views throughout the app.
    |
    */

    'recipes' => [
        'per_page' => 12,
        'my_recipes' => 12,
    ],

    'products' => [
        'per_page' => 20,
    ],

    'profile' => [
        'per_page' => 20,
        'top_recipes_limit' => 3,
        'recent_recipes_limit' => 6,
    ],

    'feed' => [
        'per_page' => 15,
    ],

    'events' => [
        'per_page' => 15,
    ],

    'reports' => [
        'per_page' => 20,
    ],

    'notifications' => [
        'per_page' => 20,
    ],

    'admin' => [
        'per_page' => 25,
    ],
];
