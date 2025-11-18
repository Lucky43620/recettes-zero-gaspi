<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePremium
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->canAccessPremiumFeatures()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Cette fonctionnalité nécessite un abonnement Premium.',
                    'requires_premium' => true,
                ], 403);
            }

            return redirect()->route('subscription.index')
                ->with('info', 'Cette fonctionnalité nécessite un abonnement Premium.');
        }

        return $next($request);
    }
}
