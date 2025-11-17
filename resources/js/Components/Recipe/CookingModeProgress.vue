<template>
    <div class="text-center mb-4 md:mb-8">
        <p class="text-gray-500 text-sm md:text-lg mb-2">{{ t('cook.step_on_total', { current: currentStep + 1, total: totalSteps }) }}</p>
        <div class="w-full bg-gray-200 rounded-full h-2 md:h-3">
            <div
                class="bg-green-600 h-2 md:h-3 rounded-full transition-all duration-300"
                :style="{ width: `${progress}%` }"
            ></div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    currentStep: {
        type: Number,
        required: true
    },
    totalSteps: {
        type: Number,
        required: true
    }
});

const progress = computed(() => ((props.currentStep + 1) / props.totalSteps) * 100);
</script>
