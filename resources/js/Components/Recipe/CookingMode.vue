<script setup>
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { XMarkIcon } from '@heroicons/vue/24/outline';
import CookingModeIngredients from './CookingModeIngredients.vue';
import CookingModeProgress from './CookingModeProgress.vue';
import CookingModeStep from './CookingModeStep.vue';
import CookingModeNavigation from './CookingModeNavigation.vue';

const { t } = useI18n();

const props = defineProps({
    recipe: Object,
});

const emit = defineEmits(['close']);

const currentStep = ref(-1);
const completedSteps = ref([]);
const stepKey = ref(0);

const steps = computed(() => props.recipe.steps || []);
const ingredients = computed(() => props.recipe.ingredients || []);
const showingIngredients = computed(() => currentStep.value === -1);

const timerActive = ref(false);
const timerRemaining = ref(0);
let timerInterval = null;

const extractDuration = (step) => {
    if (step.timer_minutes) {
        return Math.ceil(step.timer_minutes);
    }

    const match = step.content?.match(/(\d+)\s*(min|minute|minutes|h|heure|heures)/i);
    if (match) {
        const value = parseInt(match[1]);
        const unit = match[2].toLowerCase();
        return unit.startsWith('h') ? value * 60 : value;
    }

    return null;
};

const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
};

const stopTimer = () => {
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }
    timerActive.value = false;
    timerRemaining.value = 0;
};

const startTimer = () => {
    stopTimer();

    const step = steps.value[currentStep.value];
    const duration = extractDuration(step);

    if (duration) {
        timerActive.value = true;
        timerRemaining.value = duration * 60;
        const endTime = Date.now() + duration * 60 * 1000;

        timerInterval = setInterval(() => {
            const remaining = Math.max(0, Math.floor((endTime - Date.now()) / 1000));
            timerRemaining.value = remaining;

            if (remaining === 0) {
                stopTimer();
                if (Notification.permission === 'granted') {
                    new Notification(t('app.name'), {
                        body: t('cook.step_completed', { step: currentStep.value + 1 }),
                        icon: '/favicon.ico',
                    });
                }
            }
        }, 1000);
    }
};

watch(currentStep, () => {
    stopTimer();
    timerActive.value = false;
    timerRemaining.value = 0;
    stepKey.value++;
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

const currentStepData = computed(() => {
    if (currentStep.value >= 0 && steps.value[currentStep.value]) {
        return steps.value[currentStep.value];
    }
    return null;
});

const currentDuration = computed(() => {
    if (currentStepData.value) {
        return extractDuration(currentStepData.value);
    }
    return null;
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
                        v-if="currentStepData"
                        :key="stepKey"
                        :step-number="currentStep + 1"
                        :content="currentStepData.content"
                        :is-completed="isStepCompleted(currentStep)"
                        :duration="currentDuration"
                        :timer-active="timerActive"
                        :formatted-time="formatTime(timerRemaining)"
                        @toggle-completion="toggleStepCompletion(currentStep)"
                        @start-timer="startTimer"
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
