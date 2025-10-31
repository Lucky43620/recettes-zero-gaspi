<script setup>
import { computed } from 'vue';
import { TrashIcon, PlusIcon, ClockIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    modelValue: Array,
    errors: Object,
});

const emit = defineEmits(['update:modelValue']);

const steps = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

function addStep() {
    steps.value.push({ text: '', timer_minutes: null });
}

function removeStep(index) {
    if (steps.value.length > 1) {
        steps.value.splice(index, 1);
    }
}

function updateStepText(index, text) {
    steps.value[index].text = text;
}

function updateStepTimer(index, minutes, seconds) {
    const totalMinutes = (parseInt(minutes) || 0) + (parseInt(seconds) || 0) / 60;
    steps.value[index].timer_minutes = totalMinutes > 0 ? parseFloat(totalMinutes.toFixed(2)) : null;
}

function getMinutes(timerMinutes) {
    if (!timerMinutes) return '';
    return Math.floor(timerMinutes);
}

function getSeconds(timerMinutes) {
    if (!timerMinutes) return '';
    const seconds = Math.round((timerMinutes % 1) * 60);
    return seconds;
}
</script>

<template>
    <div class="space-y-4">
        <label class="block text-sm font-medium text-gray-700">
            Étapes de préparation *
        </label>

        <div class="space-y-4">
            <div
                v-for="(step, index) in steps"
                :key="index"
                class="border-2 border-gray-200 rounded-lg p-4 hover:border-green-300 transition-colors"
            >
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center text-green-700 font-semibold text-sm mt-1">
                        {{ index + 1 }}
                    </div>

                    <div class="flex-1 space-y-3">
                        <textarea
                            :value="step.text"
                            @input="updateStepText(index, $event.target.value)"
                            :placeholder="`Décrivez l'étape ${index + 1}...`"
                            required
                            rows="3"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 resize-none"
                        />

                        <div class="flex items-center gap-2">
                            <ClockIcon class="h-5 w-5 text-gray-400" />
                            <span class="text-sm text-gray-600 font-medium">Minuteur (optionnel):</span>
                            <div class="flex items-center gap-2">
                                <input
                                    :value="getMinutes(step.timer_minutes)"
                                    @input="updateStepTimer(index, $event.target.value, getSeconds(step.timer_minutes))"
                                    type="number"
                                    placeholder="Min"
                                    min="0"
                                    max="999"
                                    class="w-20 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-center"
                                />
                                <span class="text-gray-500 font-medium">:</span>
                                <input
                                    :value="getSeconds(step.timer_minutes)"
                                    @input="updateStepTimer(index, getMinutes(step.timer_minutes), $event.target.value)"
                                    type="number"
                                    placeholder="Sec"
                                    min="0"
                                    max="59"
                                    class="w-20 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-center"
                                />
                            </div>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="removeStep(index)"
                        :disabled="steps.length === 1"
                        :class="[
                            'flex-shrink-0 p-2 rounded-lg transition-all',
                            steps.length === 1
                                ? 'text-gray-300 cursor-not-allowed'
                                : 'text-red-600 hover:bg-red-50 hover:text-red-700'
                        ]"
                        :title="steps.length === 1 ? 'Au moins une étape est requise' : 'Supprimer cette étape'"
                    >
                        <TrashIcon class="h-5 w-5" />
                    </button>
                </div>
            </div>
        </div>

        <button
            type="button"
            @click="addStep"
            class="w-full py-3 px-4 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-green-400 hover:text-green-600 hover:bg-green-50 transition-all font-medium flex items-center justify-center gap-2"
        >
            <PlusIcon class="h-5 w-5" />
            Ajouter une étape
        </button>

        <div v-if="errors?.steps" class="text-red-600 text-sm">
            {{ errors.steps }}
        </div>
    </div>
</template>