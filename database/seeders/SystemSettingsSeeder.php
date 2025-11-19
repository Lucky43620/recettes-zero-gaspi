<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'Recettes Zéro Gaspi',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Nom du site',
            ],
            [
                'key' => 'site_description',
                'value' => 'Application web communautaire de recettes anti-gaspillage',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Description du site',
            ],
            [
                'key' => 'contact_email',
                'value' => 'noreply@recettes-zero-gaspi.fr',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Email de contact',
            ],
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'group' => 'general',
                'type' => 'boolean',
                'description' => 'Mode maintenance activé',
            ],
            [
                'key' => 'stripe_enabled',
                'value' => 'true',
                'group' => 'stripe',
                'type' => 'boolean',
                'description' => 'Stripe activé',
            ],
            [
                'key' => 'stripe_test_mode',
                'value' => 'true',
                'group' => 'stripe',
                'type' => 'boolean',
                'description' => 'Mode test Stripe',
            ],
            [
                'key' => 'stripe_key',
                'value' => env('STRIPE_KEY', ''),
                'group' => 'stripe',
                'type' => 'string',
                'description' => 'Clé publique Stripe',
            ],
            [
                'key' => 'stripe_secret',
                'value' => env('STRIPE_SECRET', ''),
                'group' => 'stripe',
                'type' => 'string',
                'description' => 'Clé secrète Stripe',
            ],
            [
                'key' => 'stripe_webhook_secret',
                'value' => env('STRIPE_WEBHOOK_SECRET', ''),
                'group' => 'stripe',
                'type' => 'string',
                'description' => 'Secret webhook Stripe',
            ],
            [
                'key' => 'stripe_price_monthly',
                'value' => env('STRIPE_PRICE_MONTHLY', ''),
                'group' => 'stripe',
                'type' => 'string',
                'description' => 'ID prix mensuel Stripe',
            ],
            [
                'key' => 'stripe_price_yearly',
                'value' => env('STRIPE_PRICE_YEARLY', ''),
                'group' => 'stripe',
                'type' => 'string',
                'description' => 'ID prix annuel Stripe',
            ],
            [
                'key' => 'trial_days',
                'value' => '0',
                'group' => 'stripe',
                'type' => 'integer',
                'description' => 'Jours essai gratuit',
            ],
            [
                'key' => 'stripe_calculate_taxes',
                'value' => 'false',
                'group' => 'stripe',
                'type' => 'boolean',
                'description' => 'Calcul automatique taxes Stripe Tax',
            ],
            [
                'key' => 'monthly_price',
                'value' => '4.99',
                'group' => 'stripe',
                'type' => 'decimal',
                'description' => 'Prix mensuel affiché',
            ],
            [
                'key' => 'yearly_price',
                'value' => '49.90',
                'group' => 'stripe',
                'type' => 'decimal',
                'description' => 'Prix annuel affiché',
            ],
            [
                'key' => 'monthly_plan_name',
                'value' => 'Premium Mensuel',
                'group' => 'stripe',
                'type' => 'string',
                'description' => 'Nom du plan mensuel',
            ],
            [
                'key' => 'yearly_plan_name',
                'value' => 'Premium Annuel',
                'group' => 'stripe',
                'type' => 'string',
                'description' => 'Nom du plan annuel',
            ],
            [
                'key' => 'yearly_savings_message',
                'value' => 'Économisez 2 mois',
                'group' => 'stripe',
                'type' => 'string',
                'description' => 'Message économies plan annuel',
            ],
            [
                'key' => 'enable_ai_suggestions',
                'value' => 'false',
                'group' => 'features',
                'type' => 'boolean',
                'description' => 'Suggestions IA activées',
            ],
            [
                'key' => 'enable_barcode_scan',
                'value' => 'true',
                'group' => 'features',
                'type' => 'boolean',
                'description' => 'Scan code-barres activé',
            ],
            [
                'key' => 'enable_events',
                'value' => 'true',
                'group' => 'features',
                'type' => 'boolean',
                'description' => 'Événements activés',
            ],
            [
                'key' => 'enable_badges',
                'value' => 'true',
                'group' => 'features',
                'type' => 'boolean',
                'description' => 'Badges activés',
            ],
            [
                'key' => 'enable_cooksnaps',
                'value' => 'true',
                'group' => 'features',
                'type' => 'boolean',
                'description' => 'Cooksnaps activés',
            ],
            [
                'key' => 'enable_comments',
                'value' => 'true',
                'group' => 'features',
                'type' => 'boolean',
                'description' => 'Commentaires activés',
            ],
            [
                'key' => 'free_pantry_limit',
                'value' => '10',
                'group' => 'limits',
                'type' => 'integer',
                'description' => 'Limite produits garde-manger gratuit',
            ],
            [
                'key' => 'free_meal_plan_limit',
                'value' => '3',
                'group' => 'limits',
                'type' => 'integer',
                'description' => 'Limite recettes planification gratuit',
            ],
            [
                'key' => 'free_collections_limit',
                'value' => '3',
                'group' => 'limits',
                'type' => 'integer',
                'description' => 'Limite collections gratuites',
            ],
            [
                'key' => 'free_shopping_lists_limit',
                'value' => '2',
                'group' => 'limits',
                'type' => 'integer',
                'description' => 'Limite listes courses gratuites',
            ],
            [
                'key' => 'data_retention_days',
                'value' => '3650',
                'group' => 'gdpr',
                'type' => 'integer',
                'description' => 'Durée rétention données (jours)',
            ],
            [
                'key' => 'dpo_email',
                'value' => 'dpo@recettes-zero-gaspi.fr',
                'group' => 'gdpr',
                'type' => 'string',
                'description' => 'Email DPO',
            ],
            [
                'key' => 'terms_version',
                'value' => '1.0',
                'group' => 'gdpr',
                'type' => 'string',
                'description' => 'Version CGU',
            ],
            [
                'key' => 'privacy_version',
                'value' => '1.0',
                'group' => 'gdpr',
                'type' => 'string',
                'description' => 'Version politique confidentialité',
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
