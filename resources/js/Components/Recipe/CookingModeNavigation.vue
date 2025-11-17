<template>
    <div class="bg-gray-50 border-t p-3 md:p-4 flex items-center justify-between gap-2">
        <button
            @click="$emit('previous')"
            :disabled="isFirstStep"
            class="flex items-center px-4 py-3 md:px-6 md:py-3 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 active:bg-gray-100 transition disabled:opacity-50 disabled:cursor-not-allowed text-sm md:text-lg font-medium"
        >
            <ChevronLeftIcon class="w-5 h-5 md:w-6 md:h-6 mr-1 md:mr-2" />
            <span class="hidden sm:inline">{{ previousLabel }}</span>
            <span class="sm:hidden">{{ previousLabelShort }}</span>
        </button>

        <div class="text-center hidden md:block">
            <p class="text-xs md:text-sm text-gray-500">{{ t('cook.completed_steps', { completed: completedCount, total: totalSteps }) }}</p>
        </div>

        <button
            v-if="!isLastStep"
            @click="$emit('next')"
            class="flex items-center px-4 py-3 md:px-6 md:py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 active:bg-green-800 transition text-sm md:text-lg font-medium"
        >
            <span class="hidden sm:inline">{{ nextLabel }}</span>
            <span class="sm:hidden">{{ nextLabelShort }}</span>
            <ChevronRightIcon class="w-5 h-5 md:w-6 md:h-6 ml-1 md:ml-2" />
        </button>
        <button
            v-else
            @click="$emit('finish')"
            class="px-4 py-3 md:px-6 md:py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 active:bg-green-800 transition text-sm md:text-lg font-medium"
        >
            {{ t('cook.finish') }}
        </button>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline';

const { t } = useI18n();

const props = defineProps({
    currentStep: {
        type: Number,
        required: true
    },
    totalSteps: {
        type: Number,
        required: true
    },
    completedCount: {
        type: Number,
        required: true
    },
    showingIngredients: {
        type: Boolean,
        default: false
    }
});

defineEmits(['previous', 'next', 'finish']);

const isFirstStep = computed(() => props.showingIngredients);
const isLastStep = computed(() => props.currentStep >= props.totalSteps - 1 && !props.showingIngredients);

const previousLabel = computed(() => {
    if (props.showingIngredients) return t('cook.close');
    if (props.currentStep === 0) return t('cook.ingredients');
    return t('cook.previous');
});

const previousLabelShort = computed(() => {
    if (props.showingIngredients) return t('cook.close');
    return t('cook.previous');
});

const nextLabel = computed(() => {
    if (props.showingIngredients) return t('cook.start');
    return t('cook.next');
});

const nextLabelShort = computed(() => {
    if (props.showingIngredients) return t('cook.start');
    return t('cook.next');
});
</script>
