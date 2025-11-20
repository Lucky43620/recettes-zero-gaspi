<script setup>
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { XMarkIcon } from '@heroicons/vue/24/outline';
import CookingModeIngredients from './CookingModeIngredients.vue';
import CookingModeProgress from './CookingModeProgress.vue';
import CookingModeStep from './CookingModeStep.vue';
import CookingModeNavigation from './CookingModeNavigation.vue';
import { useCookingTimer } from '@/composables/useCookingTimer';

const { t } = useI18n();

const props = defineProps({
    recipe: Object,
});

const emit = defineEmits(['close']);

const currentStep = ref(-1);
const completedSteps = ref([]);

const steps = computed(() => props.recipe.steps || []);
const ingredients = computed(() => props.recipe.ingredients || []);
const showingIngredients = computed(() => currentStep.value === -1);

const { activeTimer, extractDuration, formatTime, startTimer, stopTimer } = useCookingTimer();

watch(currentStep, () => {
    stopTimer();
});

const isStepCompleted = (index) => completedSteps.value.includes(index);

const toggleStepCompletion = (index) => {
    if (isStepCompleted(index)) {
        completedSteps.value = completedSteps.value.filter(i => i !== index);
    } else {
        completedSteps.value.push(index);
    }
};

const nextStep = () => {
    if (currentStep.value === -1) {
        currentStep.value = 0;
    } else if (currentStep.value < steps.value.length - 1) {
        currentStep.value++;
    }
};

const previousStep = () => {
    if (currentStep.value === 0) {
        currentStep.value = -1;
    } else if (currentStep.value > 0) {
        currentStep.value--;
    }
};

const handleStartTimer = (stepIndex) => {
    const step = steps.value[stepIndex];
    startTimer(stepIndex, step.content);
};

const isTimerActive = computed(() => {
    return activeTimer.value !== null && activeTimer.value.stepIndex === currentStep.value;
});

const formattedTime = computed(() => {
    return isTimerActive.value ? formatTime(activeTimer.value.remaining) : '';
});
</script>

<template>
    <div class="fixed inset-0 bg-white z-50 flex flex-col">
        <div class="bg-green-600 text-white p-3 md:p-4 flex items-center justify-between">
            <h2 class="text-base md:text-xl font-bold truncate mr-2">{{ recipe.title }}</h2>
            <button @click="emit('close')" class="p-3 hover:bg-green-700 rounded-lg transition flex-shrink-0 active:bg-green-800" :aria-label="t('cook.close')">
                <XMarkIcon class="w-6 h-6" />
            </button>
        </div>

        <div class="flex-1 overflow-y-auto">
            <div class="max-w-4xl mx-auto p-4 md:p-6">
                <CookingModeIngredients
                    v-if="showingIngredients"
                    :recipe="recipe"
                    :ingredients="ingredients"
                />

                <div v-else class="space-y-4 md:space-y-6">
                    <CookingModeProgress
                        :current-step="currentStep"
                        :total-steps="steps.length"
                    />

                    <CookingModeStep
                        v-if="steps[currentStep]"
                        :step-number="currentStep + 1"
                        :content="steps[currentStep].content"
                        :is-completed="isStepCompleted(currentStep)"
                        :duration="extractDuration(steps[currentStep].content)"
                        :timer-active="isTimerActive"
                        :formatted-time="formattedTime"
                        @toggle-completion="toggleStepCompletion(currentStep)"
                        @start-timer="handleStartTimer(currentStep)"
                    />
                </div>
            </div>
        </div>

        <CookingModeNavigation
            :current-step="currentStep"
            :total-steps="steps.length"
            :completed-count="completedSteps.length"
            :showing-ingredients="showingIngredients"
            @previous="currentStep === -1 ? emit('close') : previousStep()"
            @next="nextStep"
            @finish="emit('close')"
        />
    </div>
</template>
