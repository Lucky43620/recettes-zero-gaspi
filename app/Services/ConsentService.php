<?php

namespace App\Services;

use App\Models\Consent;
use App\Models\User;

class ConsentService
{
    public function recordConsent(
        User $user,
        string $type,
        string $version,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): Consent {
        $existingConsent = Consent::where('user_id', $user->id)
            ->where('consent_type', $type)
            ->whereNull('revoked_at')
            ->first();

        if ($existingConsent) {
            $existingConsent->revoke();
        }

        return Consent::create([
            'user_id' => $user->id,
            'consent_type' => $type,
            'version' => $version,
            'consented_at' => now(),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    public function revokeConsent(User $user, string $type): bool
    {
        $consent = Consent::where('user_id', $user->id)
            ->where('consent_type', $type)
            ->whereNull('revoked_at')
            ->first();

        return $consent ? $consent->revoke() : false;
    }

    public function hasConsent(User $user, string $type, ?string $minVersion = null): bool
    {
        $query = Consent::where('user_id', $user->id)
            ->where('consent_type', $type)
            ->whereNull('revoked_at');

        if ($minVersion) {
            $query->where('version', '>=', $minVersion);
        }

        return $query->exists();
    }

    public function getConsentHistory(User $user): array
    {
        return Consent::where('user_id', $user->id)
            ->orderBy('consented_at', 'desc')
            ->get()
            ->map(function ($consent) {
                return [
                    'type' => $consent->consent_type,
                    'version' => $consent->version,
                    'consented_at' => $consent->consented_at,
                    'ip_address' => $consent->ip_address,
                    'revoked_at' => $consent->revoked_at,
                    'is_active' => $consent->isActive(),
                ];
            })
            ->toArray();
    }

    public function getCurrentConsents(User $user): array
    {
        return Consent::where('user_id', $user->id)
            ->whereNull('revoked_at')
            ->get()
            ->groupBy('consent_type')
            ->map(function ($consents) {
                return $consents->first();
            })
            ->mapWithKeys(function ($consent) {
                return [$consent->consent_type => [
                    'version' => $consent->version,
                    'consented_at' => $consent->consented_at,
                ]];
            })
            ->toArray();
    }

    public function recordRegistrationConsents(
        User $user,
        string $termsVersion,
        string $privacyVersion,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): void {
        $this->recordConsent($user, 'terms', $termsVersion, $ipAddress, $userAgent);
        $this->recordConsent($user, 'privacy', $privacyVersion, $ipAddress, $userAgent);
    }

    public function recordMarketingConsent(
        User $user,
        bool $accepted,
        string $version = '1.0',
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): void {
        if ($accepted) {
            $this->recordConsent($user, 'marketing', $version, $ipAddress, $userAgent);
        } else {
            $this->revokeConsent($user, 'marketing');
        }
    }

    public function hasMarketingConsent(User $user): bool
    {
        return $this->hasConsent($user, 'marketing');
    }

    public function needsConsentUpdate(User $user, string $currentTermsVersion, string $currentPrivacyVersion): array
    {
        $needsUpdate = [];

        if (!$this->hasConsent($user, 'terms', $currentTermsVersion)) {
            $needsUpdate[] = 'terms';
        }

        if (!$this->hasConsent($user, 'privacy', $currentPrivacyVersion)) {
            $needsUpdate[] = 'privacy';
        }

        return $needsUpdate;
    }
}
