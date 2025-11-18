<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class AdminSettingsController extends Controller
{
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function index()
    {
        $settings = SystemSetting::all()->groupBy('group');

        $groupedSettings = [];
        foreach ($settings as $group => $groupSettings) {
            $groupedSettings[$group] = $groupSettings->mapWithKeys(function ($setting) {
                return [$setting->key => [
                    'value' => $setting->getCastedValue(),
                    'type' => $setting->type,
                    'description' => $setting->description,
                ]];
            })->toArray();
        }

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $groupedSettings,
            'groups' => ['general', 'stripe', 'features', 'limits', 'gdpr'],
        ]);
    }

    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'contact_email' => 'required|email',
            'maintenance_mode' => 'boolean',
        ]);

        $this->settingsService->updateMultiple($validated, auth()->id());

        return redirect()->back()->with('success', 'Paramètres généraux mis à jour');
    }

    public function updateStripe(Request $request)
    {
        $validated = $request->validate([
            'stripe_enabled' => 'boolean',
            'stripe_test_mode' => 'boolean',
            'stripe_key' => 'nullable|string',
            'stripe_secret' => 'nullable|string',
            'stripe_webhook_secret' => 'nullable|string',
            'stripe_price_monthly' => 'nullable|string',
            'stripe_price_yearly' => 'nullable|string',
            'trial_days' => 'integer|min:0',
        ]);

        $this->settingsService->updateMultiple($validated, auth()->id());

        return redirect()->back()->with('success', 'Paramètres Stripe mis à jour');
    }

    public function updateFeatures(Request $request)
    {
        $validated = $request->validate([
            'enable_ai_suggestions' => 'boolean',
            'enable_barcode_scan' => 'boolean',
            'enable_events' => 'boolean',
            'enable_badges' => 'boolean',
            'enable_cooksnaps' => 'boolean',
            'enable_comments' => 'boolean',
        ]);

        $this->settingsService->updateMultiple($validated, auth()->id());

        return redirect()->back()->with('success', 'Fonctionnalités mises à jour');
    }

    public function updateLimits(Request $request)
    {
        $validated = $request->validate([
            'free_pantry_limit' => 'integer|min:0',
            'free_meal_plan_limit' => 'integer|min:0',
            'free_collections_limit' => 'integer|min:0',
            'free_shopping_lists_limit' => 'integer|min:0',
        ]);

        $this->settingsService->updateMultiple($validated, auth()->id());

        return redirect()->back()->with('success', 'Limites mises à jour');
    }

    public function updateGdpr(Request $request)
    {
        $validated = $request->validate([
            'data_retention_days' => 'integer|min:1',
            'dpo_email' => 'required|email',
            'terms_version' => 'required|string',
            'privacy_version' => 'required|string',
        ]);

        $this->settingsService->updateMultiple($validated, auth()->id());

        return redirect()->back()->with('success', 'Paramètres RGPD mis à jour');
    }

    public function clearCache(Request $request)
    {
        $type = $request->input('type', 'all');

        switch ($type) {
            case 'config':
                Artisan::call('config:clear');
                break;
            case 'route':
                Artisan::call('route:clear');
                break;
            case 'view':
                Artisan::call('view:clear');
                break;
            case 'cache':
                Artisan::call('cache:clear');
                break;
            case 'all':
                Artisan::call('config:clear');
                Artisan::call('route:clear');
                Artisan::call('view:clear');
                Artisan::call('cache:clear');
                Cache::flush();
                break;
        }

        $this->settingsService->clearCache();

        return redirect()->back()->with('success', 'Cache vidé avec succès');
    }

    public function testStripe()
    {
        try {
            $stripeKey = $this->settingsService->get('stripe_secret');

            if (!$stripeKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'Clé Stripe non configurée',
                ], 400);
            }

            \Stripe\Stripe::setApiKey($stripeKey);
            $account = \Stripe\Account::retrieve();

            return response()->json([
                'success' => true,
                'message' => 'Connexion Stripe réussie',
                'account' => [
                    'id' => $account->id,
                    'email' => $account->email,
                    'country' => $account->country,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur connexion Stripe: ' . $e->getMessage(),
            ], 500);
        }
    }
}
