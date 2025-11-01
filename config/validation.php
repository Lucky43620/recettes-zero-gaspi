<?php

return [
    'max_lengths' => [
        'title' => 255,
        'name' => 255,
        'description' => 1000,
        'storage_location' => 255,
    ],

    'image' => [
        'max_size_kb' => 10240,
        'allowed_mimes' => 'image/jpeg,image/png,image/jpg,image/gif,image/webp',
    ],

    'recipe' => [
        'min_prep_time' => 1,
        'min_cook_time' => 0,
        'min_servings' => 1,
        'max_servings' => 100,
    ],

    'pantry' => [
        'min_quantity' => 0.01,
    ],
];
