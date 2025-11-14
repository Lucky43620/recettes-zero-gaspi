<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps({
    currentCount: Number,
    limit: Number,
});
</script>

<template>
    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-lg shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <div>
                    <p class="text-sm font-semibold text-gray-900">
                        {{ t('meal_plan.free_limit', { used: currentCount, limit: limit }) }}
                    </p>
                    <p class="text-xs text-gray-600 mt-0.5">
                        {{ t('meal_plan.upgrade_unlimited_message') }}
                    </p>
                </div>
            </div>
            <Link :href="route('subscription.index')" class="ml-4 px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white text-sm font-semibold rounded-lg transition-all">
                {{ t('pantry.upgrade_to_premium') }}
            </Link>
        </div>
    </div>
</template>
