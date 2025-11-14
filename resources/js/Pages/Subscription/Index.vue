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
    console.log('=== SUBSCRIBE FUNCTION CALLED ===');
    console.log('Plan:', plan);
    console.log('isProcessing:', isProcessing.value);

    if (isProcessing.value) {
        console.log('Already processing, returning...');
        return;
    }

    errorMessage.value = null;

    try {
        const routeUrl = route('subscription.checkout');
        console.log('Route URL:', routeUrl);

        isProcessing.value = true;
        console.log('Starting router.post...');

        router.post(routeUrl, {
            plan: plan,
        }, {
            preserveState: true,
            preserveScroll: true,
            onStart: () => {
                console.log('Request started');
            },
            onFinish: () => {
                console.log('Request finished');
                isProcessing.value = false;
            },
            onError: (errors) => {
                console.error('Request errors:', errors);
                isProcessing.value = false;

                if (errors.plan) {
                    errorMessage.value = errors.plan;
                } else if (typeof errors === 'string') {
                    errorMessage.value = errors;
                } else {
                    errorMessage.value = 'Une erreur est survenue. Veuillez réessayer.';
                }

                console.log('Error message set to:', errorMessage.value);
            },
            onSuccess: (page) => {
                console.log('Request successful', page);
            },
        });
    } catch (error) {
        console.error('Exception in subscribe:', error);
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
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                <div v-if="errorMessage" class="mb-8 bg-red-50 border border-red-200 rounded-lg p-6">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-red-900 mb-1">
                                Erreur
                            </h3>
                            <p class="text-red-700">
                                {{ errorMessage }}
                            </p>
                        </div>
                        <button @click="errorMessage = null" class="ml-auto text-red-500 hover:text-red-700">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Pricing Cards -->
                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Free Plan -->
                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">
                            {{ t('subscription.plans.free') }}
                        </h3>
                        <div class="mb-6">
                            <span class="text-4xl font-bold text-gray-900">{{ t('common.free') }}</span>
                        </div>
                        <ul class="space-y-3 mb-8">
                            <li v-for="feature in getPlanFeatures(plans.free.features)" :key="feature" class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">{{ feature }}</span>
                            </li>
                        </ul>
                        <button v-if="currentPlan === 'free'" disabled class="w-full py-3 px-6 rounded-lg bg-gray-300 text-gray-600 font-semibold cursor-not-allowed">
                            {{ t('subscription.current_plan') }}
                        </button>
                    </div>

                    <!-- Monthly Plan -->
                    <div class="bg-white rounded-lg shadow-lg p-8 border-2 border-green-500 relative">
                        <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                            <span class="bg-green-500 text-white px-4 py-1 rounded-full text-sm font-semibold">
                                {{ t('subscription.popular') }}
                            </span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">
                            {{ t('subscription.plans.monthly') }}
                        </h3>
                        <div class="mb-6">
                            <span class="text-4xl font-bold text-gray-900">{{ plans.monthly.price }}€</span>
                            <span class="text-gray-600">/ {{ t('subscription.month') }}</span>
                        </div>
                        <ul class="space-y-3 mb-8">
                            <li v-for="feature in getPlanFeatures(plans.monthly.features)" :key="feature" class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">{{ feature }}</span>
                            </li>
                        </ul>
                        <PrimaryButton
                            v-if="currentPlan !== 'monthly'"
                            type="button"
                            @click="subscribe('monthly')"
                            :disabled="isProcessing"
                            class="w-full justify-center py-3"
                        >
                            <span v-if="isProcessing">{{ t('common.loading') }}...</span>
                            <span v-else>{{ t('subscription.subscribe') }}</span>
                        </PrimaryButton>
                        <button v-else disabled class="w-full py-3 px-6 rounded-lg bg-gray-300 text-gray-600 font-semibold cursor-not-allowed">
                            {{ t('subscription.current_plan') }}
                        </button>
                    </div>

                    <!-- Yearly Plan -->
                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <div class="absolute top-0 right-8">
                            <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                {{ plans.yearly.savings }}
                            </span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">
                            {{ t('subscription.plans.yearly') }}
                        </h3>
                        <div class="mb-6">
                            <span class="text-4xl font-bold text-gray-900">{{ plans.yearly.price }}€</span>
                            <span class="text-gray-600">/ {{ t('subscription.year') }}</span>
                        </div>
                        <ul class="space-y-3 mb-8">
                            <li v-for="feature in getPlanFeatures(plans.yearly.features)" :key="feature" class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">{{ feature }}</span>
                            </li>
                        </ul>
                        <PrimaryButton
                            v-if="currentPlan !== 'yearly'"
                            type="button"
                            @click="subscribe('yearly')"
                            :disabled="isProcessing"
                            class="w-full justify-center py-3"
                        >
                            <span v-if="isProcessing">{{ t('common.loading') }}...</span>
                            <span v-else>{{ t('subscription.subscribe') }}</span>
                        </PrimaryButton>
                        <button v-else disabled class="w-full py-3 px-6 rounded-lg bg-gray-300 text-gray-600 font-semibold cursor-not-allowed">
                            {{ t('subscription.current_plan') }}
                        </button>
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
