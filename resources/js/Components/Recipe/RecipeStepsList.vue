<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps({
    steps: Array,
});
</script>

<template>
    <div class="mb-6">
        <h3 class="text-lg md:text-xl font-semibold text-gray-900 mb-3 md:mb-4">{{ t('recipe.steps') }}</h3>
        <ol class="space-y-3 md:space-y-4">
            <li
                v-for="(step, index) in steps"
                :key="step.id"
                class="flex gap-3 md:gap-4 p-3 md:p-4 bg-gray-50 rounded-lg"
            >
                <span class="flex-shrink-0 w-7 h-7 md:w-8 md:h-8 bg-green-600 text-white rounded-full flex items-center justify-center font-semibold text-sm md:text-base">
                    {{ index + 1 }}
                </span>
                <div class="flex-1">
                    <p class="text-gray-800 text-sm md:text-base">{{ step.text }}</p>
                    <p v-if="step.timer_minutes" class="text-xs md:text-sm text-green-600 mt-2">
                        ⏱️ {{ step.timer_minutes }} min
                    </p>
                </div>
            </li>
        </ol>
    </div>
</template>
