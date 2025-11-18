<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const { t } = useI18n();

const props = defineProps({
    plans: Object,
    currentPlan: String,
    isSubscribed: Boolean,
    subscription: Object,
    stripeKey: String,
});

const selectedPlan = ref('monthly');
const isProcessing = ref(false);
const errorMessage = ref(null);

const subscribe = (plan) => {
    if (isProcessing.value) {
        return;
    }

    errorMessage.value = null;

    try {
        const routeUrl = route('subscription.checkout');
        isProcessing.value = true;

        router.post(routeUrl, {
            plan: plan,
        }, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                isProcessing.value = false;
            },
            onError: (errors) => {
                isProcessing.value = false;

                if (errors.plan) {
                    errorMessage.value = errors.plan;
                } else if (typeof errors === 'string') {
                    errorMessage.value = errors;
                } else {
                    errorMessage.value = t('common.error_occurred');
                }
            },
        });
    } catch (error) {
        isProcessing.value = false;
        errorMessage.value = error.message;
    }
};

const getPlanFeatures = (features) => {
    return features.map(feature => t(`subscription.features.${feature}`));
};
</script>

<template>
    <AppLayout :title="t('subscription.title')">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ t('subscription.title') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Current Plan Info -->
                <div v-if="isSubscribed" class="mb-8 bg-green-50 border border-green-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-green-900 mb-2">
                        {{ t('subscription.current_plan') }}: {{ t(`subscription.plans.${currentPlan}`) }}
                    </h3>
                    <p class="text-green-700">
                        {{ t('subscription.active_subscription') }}
                    </p>
                    <div class="mt-4">
                        <a :href="route('subscription.manage')" class="text-green-700 hover:text-green-900 font-medium underline">
                            {{ t('subscription.manage_subscription') }}
                        </a>
                    </div>
                </div>

                <!-- Error Message -->
                <div v-if="errorMessage" class="mb-8 bg-red-50 border border-red-200 rounded-lg p-4 md:p-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-red-500 mr-2 md:mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base md:text-lg font-semibold text-red-900 mb-1">
                                {{ t('common.error') }}
                            </h3>
                            <p class="text-sm md:text-base text-red-700">
                                {{ errorMessage }}
                            </p>
                        </div>
                        <button @click="errorMessage = null" class="ml-2 md:ml-auto text-red-500 hover:text-red-700 flex-shrink-0">
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Header Section -->
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Choisissez votre formule
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Profitez de toutes les fonctionnalités Premium pour gérer votre cuisine de manière intelligente et réduire le gaspillage alimentaire
                    </p>
                </div>

                <!-- Pricing Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    <!-- Free Plan -->
                    <div class="bg-white rounded-2xl shadow-lg p-8 flex flex-col border border-gray-200">
                        <div class="mb-6">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">
                                {{ t('subscription.plans.free') }}
                            </h3>
                            <div class="flex items-baseline gap-1">
                                <span class="text-5xl font-bold text-gray-900">0€</span>
                            </div>
                        </div>
                        <ul class="space-y-4 mb-8 flex-grow">
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">Accès aux recettes publiques</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">Créer vos propres recettes</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">Garde-manger basique (10 articles max)</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">Planning de repas basique</span>
                            </li>
                        </ul>
                        <button v-if="currentPlan === 'free'" disabled class="w-full py-3 px-6 rounded-lg bg-gray-200 text-gray-600 font-semibold cursor-not-allowed">
                            Plan actuel
                        </button>
                    </div>

                    <!-- Monthly Plan - Popular -->
                    <div class="bg-gradient-to-b from-green-50 to-white rounded-2xl shadow-2xl p-8 flex flex-col border-2 border-green-500 relative transform md:scale-105">
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                            <span class="bg-green-500 text-white px-6 py-2 rounded-full text-sm font-bold shadow-lg">
                                {{ t('subscription.popular') }}
                            </span>
                        </div>
                        <div class="mb-6">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">
                                {{ t('subscription.plans.monthly') }}
                            </h3>
                            <div class="flex items-baseline gap-1">
                                <span class="text-5xl font-bold text-green-600">{{ plans.monthly.price }}€</span>
                                <span class="text-gray-600">/ {{ t('subscription.month') }}</span>
                            </div>
                        </div>
                        <ul class="space-y-4 mb-8 flex-grow">
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-900 font-medium">Toutes les fonctionnalités gratuites</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-900 font-medium">Garde-manger illimité avec scanner code-barres</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-900 font-medium">Alertes de péremption intelligentes</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-900 font-medium">Recettes anti-gaspi personnalisées</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-900 font-medium">Plans de repas illimités</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-900 font-medium">Sans publicité</span>
                            </li>
                        </ul>
                        <PrimaryButton
                            v-if="currentPlan !== 'monthly'"
                            type="button"
                            @click="subscribe('monthly')"
                            :disabled="isProcessing"
                            class="w-full justify-center py-4 text-lg shadow-lg"
                        >
                            <span v-if="isProcessing">{{ t('common.loading') }}...</span>
                            <span v-else>{{ t('subscription.subscribe') }}</span>
                        </PrimaryButton>
                        <button v-else disabled class="w-full py-4 px-6 rounded-lg bg-gray-200 text-gray-600 font-semibold cursor-not-allowed">
                            Plan actuel
                        </button>
                    </div>

                    <!-- Yearly Plan -->
                    <div class="bg-white rounded-2xl shadow-lg p-8 flex flex-col border border-gray-200 relative">
                        <div class="absolute -top-4 right-4">
                            <span class="bg-yellow-500 text-white px-4 py-2 rounded-full text-sm font-bold shadow-md">
                                {{ plans.yearly.savings }}
                            </span>
                        </div>
                        <div class="mb-6">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">
                                {{ t('subscription.plans.yearly') }}
                            </h3>
                            <div class="flex items-baseline gap-1">
                                <span class="text-5xl font-bold text-gray-900">{{ plans.yearly.price }}€</span>
                                <span class="text-gray-600">/ {{ t('subscription.year') }}</span>
                            </div>
                            <p class="text-sm text-green-600 font-semibold mt-2">
                                Soit {{ (plans.yearly.price / 12).toFixed(2) }}€/mois
                            </p>
                        </div>
                        <ul class="space-y-4 mb-8 flex-grow">
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-900 font-medium">Toutes les fonctionnalités mensuelles</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-yellow-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="text-gray-900 font-medium">Économisez 2 mois sur l'année</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-yellow-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="text-gray-900 font-medium">Meilleur rapport qualité-prix</span>
                            </li>
                        </ul>
                        <PrimaryButton
                            v-if="currentPlan !== 'yearly'"
                            type="button"
                            @click="subscribe('yearly')"
                            :disabled="isProcessing"
                            class="w-full justify-center py-4 text-lg"
                        >
                            <span v-if="isProcessing">{{ t('common.loading') }}...</span>
                            <span v-else>{{ t('subscription.subscribe') }}</span>
                        </PrimaryButton>
                        <button v-else disabled class="w-full py-4 px-6 rounded-lg bg-gray-200 text-gray-600 font-semibold cursor-not-allowed">
                            Plan actuel
                        </button>
                    </div>
                </div>

                <!-- Trust indicators -->
                <div class="flex flex-wrap justify-center items-center gap-8 mb-12 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Paiement sécurisé avec Stripe</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Annulation à tout moment</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span>Support client réactif</span>
                    </div>
                </div>

                <!-- FAQ Section -->
                <div class="mt-16">
                    <h3 class="text-2xl font-bold text-gray-900 mb-8 text-center">
                        {{ t('subscription.faq_title') }}
                    </h3>
                    <div class="max-w-3xl mx-auto space-y-6">
                        <div class="bg-white rounded-lg shadow p-6">
                            <h4 class="font-semibold text-gray-900 mb-2">
                                {{ t('subscription.faq_1_question') }}
                            </h4>
                            <p class="text-gray-700">
                                {{ t('subscription.faq_1_answer') }}
                            </p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <h4 class="font-semibold text-gray-900 mb-2">
                                {{ t('subscription.faq_2_question') }}
                            </h4>
                            <p class="text-gray-700">
                                {{ t('subscription.faq_2_answer') }}
                            </p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <h4 class="font-semibold text-gray-900 mb-2">
                                {{ t('subscription.faq_3_question') }}
                            </h4>
                            <p class="text-gray-700">
                                {{ t('subscription.faq_3_answer') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
