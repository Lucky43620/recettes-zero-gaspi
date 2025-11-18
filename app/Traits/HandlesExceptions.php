<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait HandlesExceptions
{
    protected function handleException(\Exception $e, string $context = '')
    {
        Log::error($context . ': ' . $e->getMessage(), [
            'exception' => get_class($e),
            'trace' => $e->getTraceAsString()
        ]);

        return back()->with('error', __('common.error_occurred'));
    }
}
