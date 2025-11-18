<template>
    <AdminLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Paramètres Système</h1>
                <p class="mt-2 text-gray-600">Gérez tous les paramètres de l'application</p>
            </div>

            <!-- Success/Error Alerts -->
            <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-start gap-3">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-medium text-green-800">{{ $page.props.flash.success }}</p>
                </div>
                <button @click="$page.props.flash.success = null" class="text-green-600 hover:text-green-800">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>

            <div v-if="$page.props.flash?.error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-medium text-red-800">{{ $page.props.flash.error }}</p>
                </div>
                <button @click="$page.props.flash.error = null" class="text-red-600 hover:text-red-800">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        <button
                            v-for="tab in tabs"
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            :class="[
                                activeTab === tab.id
                                    ? 'border-orange-500 text-orange-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                            ]"
                        >
                            {{ tab.name }}
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <div v-show="activeTab === 'general'" class="space-y-6">
                        <SettingsSection
                            title="Paramètres Généraux"
                            description="Configuration générale de l'application"
                        >
                            <form @submit.prevent="updateGeneral" class="space-y-4">
                                <SettingsInput
                                    v-model="forms.general.site_name"
                                    label="Nom du site"
                                    :required="true"
                                />
                                <SettingsInput
                                    v-model="forms.general.site_description"
                                    label="Description"
                                    type="textarea"
                                />
                                <SettingsInput
                                    v-model="forms.general.contact_email"
                                    label="Email de contact"
                                    type="email"
                                    :required="true"
                                />
                                <SettingsToggle
                                    v-model="forms.general.maintenance_mode"
                                    label="Mode maintenance"
                                    description="Activer le mode maintenance (site inaccessible)"
                                />

                                <div class="pt-4 flex justify-end">
                                    <button
                                        type="submit"
                                        class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition"
                                    >
                                        Enregistrer
                                    </button>
                                </div>
                            </form>
                        </SettingsSection>
                    </div>

                    <div v-show="activeTab === 'stripe'" class="space-y-6">
                        <SettingsSection
                            title="Configuration Stripe"
                            description="Paramètres de paiement et abonnement"
                        >
                            <form @submit.prevent="updateStripe" class="space-y-4">
                                <SettingsToggle
                                    v-model="forms.stripe.stripe_enabled"
                                    label="Activer Stripe"
                                    description="Activer les paiements Stripe"
                                />
                                <SettingsToggle
                                    v-model="forms.stripe.stripe_test_mode"
                                    label="Mode test"
                                    description="Utiliser les clés de test Stripe"
                                />
                                <SettingsInput
                                    v-model="forms.stripe.stripe_key"
                                    label="Clé publique Stripe"
                                    placeholder="pk_test_..."
                                />
                                <SettingsInput
                                    v-model="forms.stripe.stripe_secret"
                                    label="Clé secrète Stripe"
                                    placeholder="sk_test_..."
                                    type="password"
                                />
                                <SettingsInput
                                    v-model="forms.stripe.stripe_webhook_secret"
                                    label="Secret Webhook"
                                    placeholder="whsec_..."
                                />
                                <SettingsInput
                                    v-model="forms.stripe.stripe_price_monthly"
                                    label="ID Prix mensuel"
                                    placeholder="price_..."
                                />
                                <SettingsInput
                                    v-model="forms.stripe.stripe_price_yearly"
                                    label="ID Prix annuel"
                                    placeholder="price_..."
                                />
                                <SettingsInput
                                    v-model.number="forms.stripe.trial_days"
                                    label="Jours d'essai gratuit"
                                    type="number"
                                    min="0"
                                />

                                <div class="pt-4 flex justify-end gap-3">
                                    <button
                                        type="button"
                                        @click="testStripe"
                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                                    >
                                        Tester connexion
                                    </button>
                                    <button
                                        type="submit"
                                        class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition"
                                    >
                                        Enregistrer
                                    </button>
                                </div>
                            </form>
                        </SettingsSection>
                    </div>

                    <div v-show="activeTab === 'features'" class="space-y-6">
                        <SettingsSection
                            title="Fonctionnalités"
                            description="Activer ou désactiver des fonctionnalités"
                        >
                            <form @submit.prevent="updateFeatures" class="space-y-4">
                                <SettingsToggle
                                    v-model="forms.features.enable_ai_suggestions"
                                    label="Suggestions IA"
                                    description="Suggestions de recettes par IA"
                                />
                                <SettingsToggle
                                    v-model="forms.features.enable_barcode_scan"
                                    label="Scan code-barres"
                                    description="Scanner les codes-barres des produits"
                                />
                                <SettingsToggle
                                    v-model="forms.features.enable_events"
                                    label="Événements"
                                    description="Système d'événements communautaires"
                                />
                                <SettingsToggle
                                    v-model="forms.features.enable_badges"
                                    label="Badges"
                                    description="Système de badges et gamification"
                                />
                                <SettingsToggle
                                    v-model="forms.features.enable_cooksnaps"
                                    label="Cooksnaps"
                                    description="Photos de réalisations"
                                />
                                <SettingsToggle
                                    v-model="forms.features.enable_comments"
                                    label="Commentaires"
                                    description="Système de commentaires"
                                />

                                <div class="pt-4 flex justify-end">
                                    <button
                                        type="submit"
                                        class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition"
                                    >
                                        Enregistrer
                                    </button>
                                </div>
                            </form>
                        </SettingsSection>
                    </div>

                    <div v-show="activeTab === 'limits'" class="space-y-6">
                        <SettingsSection
                            title="Limites Utilisateurs"
                            description="Limites pour les comptes gratuits"
                        >
                            <form @submit.prevent="updateLimits" class="space-y-4">
                                <SettingsInput
                                    v-model.number="forms.limits.free_pantry_limit"
                                    label="Garde-manger (gratuit)"
                                    type="number"
                                    min="0"
                                    help="Nombre de produits maximum"
                                />
                                <SettingsInput
                                    v-model.number="forms.limits.free_meal_plan_limit"
                                    label="Planification repas (gratuit)"
                                    type="number"
                                    min="0"
                                    help="Recettes par semaine"
                                />
                                <SettingsInput
                                    v-model.number="forms.limits.free_collections_limit"
                                    label="Collections (gratuit)"
                                    type="number"
                                    min="0"
                                />
                                <SettingsInput
                                    v-model.number="forms.limits.free_shopping_lists_limit"
                                    label="Listes courses (gratuit)"
                                    type="number"
                                    min="0"
                                />

                                <div class="pt-4 flex justify-end">
                                    <button
                                        type="submit"
                                        class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition"
                                    >
                                        Enregistrer
                                    </button>
                                </div>
                            </form>
                        </SettingsSection>
                    </div>

                    <div v-show="activeTab === 'gdpr'" class="space-y-6">
                        <SettingsSection
                            title="RGPD & Confidentialité"
                            description="Paramètres de conformité RGPD"
                        >
                            <form @submit.prevent="updateGdpr" class="space-y-4">
                                <SettingsInput
                                    v-model.number="forms.gdpr.data_retention_days"
                                    label="Durée rétention données"
                                    type="number"
                                    min="1"
                                    help="Nombre de jours de conservation des données supprimées"
                                />
                                <SettingsInput
                                    v-model="forms.gdpr.dpo_email"
                                    label="Email DPO"
                                    type="email"
                                    :required="true"
                                />
                                <SettingsInput
                                    v-model="forms.gdpr.terms_version"
                                    label="Version CGU"
                                    :required="true"
                                />
                                <SettingsInput
                                    v-model="forms.gdpr.privacy_version"
                                    label="Version Politique Confidentialité"
                                    :required="true"
                                />

                                <div class="pt-4 flex justify-end">
                                    <button
                                        type="submit"
                                        class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition"
                                    >
                                        Enregistrer
                                    </button>
                                </div>
                            </form>
                        </SettingsSection>
                    </div>

                    <div v-show="activeTab === 'performance'" class="space-y-6">
                        <SettingsSection
                            title="Performance & Cache"
                            description="Outils de gestion du cache"
                        >
                            <div class="space-y-4">
                                <p class="text-sm text-gray-600">
                                    Vider le cache peut améliorer les performances après des modifications importantes.
                                </p>

                                <div class="grid grid-cols-2 gap-4">
                                    <button
                                        type="button"
                                        @click="clearCache('config')"
                                        class="px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition"
                                    >
                                        Vider cache config
                                    </button>
                                    <button
                                        type="button"
                                        @click="clearCache('route')"
                                        class="px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition"
                                    >
                                        Vider cache routes
                                    </button>
                                    <button
                                        type="button"
                                        @click="clearCache('view')"
                                        class="px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition"
                                    >
                                        Vider cache vues
                                    </button>
                                    <button
                                        type="button"
                                        @click="clearCache('cache')"
                                        class="px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition"
                                    >
                                        Vider cache application
                                    </button>
                                </div>

                                <button
                                    type="button"
                                    @click="clearCache('all')"
                                    class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                                >
                                    Vider tout le cache
                                </button>
                            </div>
                        </SettingsSection>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import SettingsSection from '@/Components/Admin/SettingsSection.vue'
import SettingsInput from '@/Components/Admin/SettingsInput.vue'
import SettingsToggle from '@/Components/Admin/SettingsToggle.vue'

const props = defineProps({
    settings: Object,
    groups: Array
})

const activeTab = ref('general')

const tabs = [
    { id: 'general', name: 'Général' },
    { id: 'stripe', name: 'Stripe' },
    { id: 'features', name: 'Fonctionnalités' },
    { id: 'limits', name: 'Limites' },
    { id: 'gdpr', name: 'RGPD' },
    { id: 'performance', name: 'Performance' }
]

const forms = reactive({
    general: {
        site_name: props.settings.general?.site_name?.value ?? '',
        site_description: props.settings.general?.site_description?.value ?? '',
        contact_email: props.settings.general?.contact_email?.value ?? '',
        maintenance_mode: props.settings.general?.maintenance_mode?.value ?? false
    },
    stripe: {
        stripe_enabled: props.settings.stripe?.stripe_enabled?.value ?? true,
        stripe_test_mode: props.settings.stripe?.stripe_test_mode?.value ?? true,
        stripe_key: props.settings.stripe?.stripe_key?.value ?? '',
        stripe_secret: props.settings.stripe?.stripe_secret?.value ?? '',
        stripe_webhook_secret: props.settings.stripe?.stripe_webhook_secret?.value ?? '',
        stripe_price_monthly: props.settings.stripe?.stripe_price_monthly?.value ?? '',
        stripe_price_yearly: props.settings.stripe?.stripe_price_yearly?.value ?? '',
        trial_days: props.settings.stripe?.trial_days?.value ?? 0
    },
    features: {
        enable_ai_suggestions: props.settings.features?.enable_ai_suggestions?.value ?? false,
        enable_barcode_scan: props.settings.features?.enable_barcode_scan?.value ?? true,
        enable_events: props.settings.features?.enable_events?.value ?? true,
        enable_badges: props.settings.features?.enable_badges?.value ?? true,
        enable_cooksnaps: props.settings.features?.enable_cooksnaps?.value ?? true,
        enable_comments: props.settings.features?.enable_comments?.value ?? true
    },
    limits: {
        free_pantry_limit: props.settings.limits?.free_pantry_limit?.value ?? 10,
        free_meal_plan_limit: props.settings.limits?.free_meal_plan_limit?.value ?? 3,
        free_collections_limit: props.settings.limits?.free_collections_limit?.value ?? 3,
        free_shopping_lists_limit: props.settings.limits?.free_shopping_lists_limit?.value ?? 2
    },
    gdpr: {
        data_retention_days: props.settings.gdpr?.data_retention_days?.value ?? 3650,
        dpo_email: props.settings.gdpr?.dpo_email?.value ?? '',
        terms_version: props.settings.gdpr?.terms_version?.value ?? '1.0',
        privacy_version: props.settings.gdpr?.privacy_version?.value ?? '1.0'
    }
})

const updateGeneral = () => {
    router.post('/admin/settings/general', forms.general)
}

const updateStripe = () => {
    router.post('/admin/settings/stripe', forms.stripe)
}

const updateFeatures = () => {
    router.post('/admin/settings/features', forms.features)
}

const updateLimits = () => {
    router.post('/admin/settings/limits', forms.limits)
}

const updateGdpr = () => {
    router.post('/admin/settings/gdpr', forms.gdpr)
}

const clearCache = (type) => {
    router.post('/admin/settings/clear-cache', { type })
}

const testStripe = async () => {
    try {
        const response = await fetch('/admin/settings/test-stripe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        const data = await response.json()

        if (data.success) {
            alert('Connexion Stripe réussie !\n\nCompte: ' + data.account.id)
        } else {
            alert('Erreur: ' + data.message)
        }
    } catch (error) {
        alert('Erreur de connexion à Stripe')
    }
}
</script>
