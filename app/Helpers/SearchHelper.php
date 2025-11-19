<?php

namespace App\Helpers;

class SearchHelper
{
    public static function sanitize(string $query): string
    {
        return trim(strip_tags(htmlspecialchars($query)));
    }

    public static function escape(string $query): string
    {
        return addslashes($query);
    }
}
