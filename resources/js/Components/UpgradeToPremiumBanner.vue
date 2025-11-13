<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import PremiumBadge from './PremiumBadge.vue';

defineProps({
    feature: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
});

const { t } = useI18n();
</script>

<template>
    <div class="bg-gradient-to-r from-yellow-50 via-amber-50 to-orange-50 border-2 border-yellow-300 rounded-xl p-8 text-center shadow-lg">
        <div class="flex justify-center mb-4">
            <div class="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full p-4">
                <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            </div>
        </div>

        <div class="mb-2">
            <PremiumBadge size="lg" />
        </div>

        <h3 class="text-2xl font-bold text-gray-900 mb-3">
            {{ feature }}
        </h3>

        <p v-if="description" class="text-gray-600 mb-6 max-w-2xl mx-auto">
            {{ description }}
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center items-center">
            <Link
                :href="route('subscription.index')"
                class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-semibold rounded-lg shadow-md hover:shadow-xl transition-all duration-200 transform hover:scale-105"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                {{ t('subscription.subscribe') }}
            </Link>

            <Link
                :href="route('dashboard')"
                class="text-gray-600 hover:text-gray-900 font-medium underline"
            >
                {{ t('common.back') }}
            </Link>
        </div>

        <div class="mt-6 flex flex-wrap gap-4 justify-center text-sm text-gray-600">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ t('subscription.features.ai_menu_generator') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ t('subscription.features.advanced_pantry') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ t('subscription.features.no_ads') }}</span>
            </div>
        </div>
    </div>
</template>
