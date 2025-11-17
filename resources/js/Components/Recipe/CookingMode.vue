<script setup>
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { XMarkIcon, ChevronLeftIcon, ChevronRightIcon, ClockIcon, CheckIcon } from '@heroicons/vue/24/outline';

const { t } = useI18n();

const props = defineProps({
    recipe: Object,
});

const emit = defineEmits(['close']);

const currentStep = ref(0);
const completedSteps = ref([]);

const steps = computed(() => props.recipe.steps || []);
const ingredients = computed(() => props.recipe.ingredients || []);

const isStepCompleted = (index) => completedSteps.value.includes(index);

const toggleStepCompletion = (index) => {
    if (isStepCompleted(index)) {
        completedSteps.value = completedSteps.value.filter(i => i !== index);
    } else {
        completedSteps.value.push(index);
    }
};

const nextStep = () => {
    if (currentStep.value < steps.value.length - 1) {
        currentStep.value++;
    }
};

const previousStep = () => {
    if (currentStep.value > 0) {
        currentStep.value--;
    }
};

const extractDuration = (content) => {
    const match = content.match(/(\d+)\s*(min|minute|minutes|h|heure|heures)/i);
    if (match) {
        const value = parseInt(match[1]);
        const unit = match[2].toLowerCase();
        return unit.startsWith('h') ? value * 60 : value;
    }
    return null;
};

const timers = ref({});

const startTimer = (stepIndex) => {
    const step = steps.value[stepIndex];
    const duration = extractDuration(step.content);

    if (duration) {
        const endTime = Date.now() + duration * 60 * 1000;
        timers.value[stepIndex] = {
            endTime,
            remaining: duration * 60,
        };

        const interval = setInterval(() => {
            const remaining = Math.max(0, Math.floor((endTime - Date.now()) / 1000));
            if (timers.value[stepIndex]) {
                timers.value[stepIndex].remaining = remaining;
            }

            if (remaining === 0) {
                clearInterval(interval);
                if (Notification.permission === 'granted') {
                    new Notification(t('app.name'), {
                        body: t('cook.step_completed', { step: stepIndex + 1 }),
                        icon: '/favicon.ico',
                    });
                }
            }
        }, 1000);
    }
};

const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
};
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
                <div v-if="currentStep === -1" class="space-y-4 md:space-y-6">
                    <div>
                        <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-3 md:mb-4">{{ t('cook.ingredients_needed') }}</h3>
                        <div class="bg-gray-50 rounded-lg p-4 md:p-6">
                            <p class="text-base md:text-lg text-gray-600 mb-3 md:mb-4">{{ t('cook.for_servings', { servings: recipe.servings }) }}</p>
                            <ul class="space-y-2 md:space-y-3">
                                <li v-for="(ingredient, index) in ingredients" :key="index" class="flex items-center text-sm md:text-lg">
                                    <div class="w-2 h-2 bg-green-600 rounded-full mr-2 md:mr-3 flex-shrink-0"></div>
                                    <span class="font-medium mr-2">{{ ingredient.quantity }} {{ ingredient.unit }}</span>
                                    <span>{{ ingredient.name }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 md:p-6 rounded-r-lg">
                        <p class="text-sm md:text-lg text-blue-900">
                            ⏱️ {{ t('cook.preparation_time', { time: recipe.prep_time }) }}
                        </p>
                        <p class="text-sm md:text-lg text-blue-900 mt-2">
                            🔥 {{ t('cook.cooking_time', { time: recipe.cook_time }) }}
                        </p>
                    </div>
                </div>

                <div v-else class="space-y-4 md:space-y-6">
                    <div class="text-center mb-4 md:mb-8">
                        <p class="text-gray-500 text-sm md:text-lg mb-2">{{ t('cook.step_on_total', { current: currentStep + 1, total: steps.length }) }}</p>
                        <div class="w-full bg-gray-200 rounded-full h-2 md:h-3">
                            <div
                                class="bg-green-600 h-2 md:h-3 rounded-full transition-all duration-300"
                                :style="{ width: `${((currentStep + 1) / steps.length) * 100}%` }"
                            ></div>
                        </div>
                    </div>

                    <div v-if="steps[currentStep]" class="bg-white rounded-xl shadow-lg p-4 md:p-8">
                        <div class="flex items-start justify-between mb-4 md:mb-6">
                            <h3 class="text-xl md:text-3xl font-bold text-gray-900">{{ t('cook.step') }} {{ currentStep + 1 }}</h3>
                            <button
                                @click="toggleStepCompletion(currentStep)"
                                :class="[
                                    'p-3 md:p-4 rounded-full transition flex-shrink-0 ml-2 active:scale-95',
                                    isStepCompleted(currentStep)
                                        ? 'bg-green-600 text-white active:bg-green-700'
                                        : 'bg-gray-200 text-gray-600 hover:bg-gray-300 active:bg-gray-400'
                                ]"
                                :aria-label="t('cook.mark_completed')"
                            >
                                <CheckIcon class="w-6 h-6 md:w-8 md:h-8" />
                            </button>
                        </div>

                        <p class="text-base md:text-2xl text-gray-800 leading-relaxed mb-4 md:mb-6" style="line-height: 1.6;">
                            {{ steps[currentStep].content }}
                        </p>

                        <div v-if="extractDuration(steps[currentStep].content)" class="mt-4 md:mt-6 bg-orange-50 rounded-lg p-4 md:p-6">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                <div class="flex items-center">
                                    <ClockIcon class="w-6 h-6 md:w-8 md:h-8 text-orange-600 mr-2 md:mr-3 flex-shrink-0" />
                                    <span class="text-base md:text-xl font-semibold text-orange-900">
                                        {{ extractDuration(steps[currentStep].content) }} {{ t('cook.minutes') }}
                                    </span>
                                </div>
                                <button
                                    v-if="!timers[currentStep]"
                                    @click="startTimer(currentStep)"
                                    class="w-full sm:w-auto px-5 py-3 md:px-6 md:py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 active:bg-orange-800 transition text-base md:text-lg font-medium"
                                >
                                    {{ t('cook.launch_timer') }}
                                </button>
                                <div v-else class="text-2xl md:text-3xl font-bold text-orange-600">
                                    {{ formatTime(timers[currentStep].remaining) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 border-t p-3 md:p-4 flex items-center justify-between gap-2">
            <button
                @click="currentStep === -1 ? emit('close') : (currentStep === 0 ? currentStep = -1 : previousStep())"
                :disabled="currentStep === -1"
                class="flex items-center px-4 py-3 md:px-6 md:py-3 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 active:bg-gray-100 transition disabled:opacity-50 disabled:cursor-not-allowed text-sm md:text-lg font-medium"
            >
                <ChevronLeftIcon class="w-5 h-5 md:w-6 md:h-6 mr-1 md:mr-2" />
                <span class="hidden sm:inline">{{ currentStep === -1 ? t('cook.close') : currentStep === 0 ? t('cook.ingredients') : t('cook.previous') }}</span>
                <span class="sm:hidden">{{ currentStep === -1 ? t('cook.close') : t('cook.previous') }}</span>
            </button>

            <div class="text-center hidden md:block">
                <p class="text-xs md:text-sm text-gray-500">{{ t('cook.completed_steps', { completed: completedSteps.length, total: steps.length }) }}</p>
            </div>

            <button
                v-if="currentStep < steps.length - 1 || currentStep === -1"
                @click="currentStep === -1 ? currentStep = 0 : nextStep()"
                class="flex items-center px-4 py-3 md:px-6 md:py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 active:bg-green-800 transition text-sm md:text-lg font-medium"
            >
                <span class="hidden sm:inline">{{ currentStep === -1 ? t('cook.start') : t('cook.next') }}</span>
                <span class="sm:hidden">{{ currentStep === -1 ? t('cook.start') : t('cook.next') }}</span>
                <ChevronRightIcon class="w-5 h-5 md:w-6 md:h-6 ml-1 md:ml-2" />
            </button>
            <button
                v-else
                @click="emit('close')"
                class="px-4 py-3 md:px-6 md:py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 active:bg-green-800 transition text-sm md:text-lg font-medium"
            >
                {{ t('cook.finish') }}
            </button>
        </div>
    </div>
</template>
