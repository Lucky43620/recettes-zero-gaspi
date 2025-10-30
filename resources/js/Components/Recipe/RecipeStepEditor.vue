<script setup>
import { computed } from 'vue';

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

function updateStepTimer(index, timer) {
    steps.value[index].timer_minutes = timer;
}
</script>

<template>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Étapes *
        </label>
        <div
            v-for="(step, index) in steps"
            :key="index"
            class="flex gap-2 mb-3"
        >
            <div class="flex-1">
                <textarea
                    :value="step.text"
                    @input="updateStepText(index, $event.target.value)"
                    :placeholder="`Étape ${index + 1}`"
                    required
                    rows="2"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
            </div>
            <input
                :value="step.timer_minutes"
                @input="updateStepTimer(index, $event.target.value ? parseInt($event.target.value) : null)"
                type="number"
                placeholder="Timer (min)"
                min="0"
                class="w-32 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            />
            <button
                type="button"
                @click="removeStep(index)"
                :disabled="steps.length === 1"
                class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                ×
            </button>
        </div>
        <button
            type="button"
            @click="addStep"
            class="w-full py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
        >
            + Ajouter une étape
        </button>
        <div v-if="errors?.steps" class="text-red-600 text-sm mt-1">
            {{ errors.steps }}
        </div>
    </div>
</template>