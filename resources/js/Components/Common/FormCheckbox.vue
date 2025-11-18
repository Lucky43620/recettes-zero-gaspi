<template>
    <div class="flex items-center">
        <input
            :id="id"
            type="checkbox"
            v-model="proxyChecked"
            :value="value"
            :disabled="disabled"
            :class="[
                'rounded border-gray-300 text-green-600 focus:ring-green-500',
                disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
            ]"
        />
        <label v-if="label || $slots.label" :for="id" :class="['ml-2 text-sm', disabled ? 'text-gray-400' : 'text-gray-700', disabled ? 'cursor-not-allowed' : 'cursor-pointer']">
            <slot name="label">
                {{ label }}
            </slot>
        </label>
    </div>
    <p v-if="error" class="mt-2 text-sm text-red-600">{{ error }}</p>
    <p v-if="hint && !error" class="mt-2 text-sm text-gray-500">{{ hint }}</p>
</template>

<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    id: String,
    label: String,
    modelValue: {
        type: [Array, Boolean],
        default: false
    },
    value: {
        type: String,
        default: null
    },
    disabled: Boolean,
    error: String,
    hint: String,
});

const proxyChecked = computed({
    get() {
        return props.modelValue;
    },
    set(val) {
        emit('update:modelValue', val);
    }
});
</script>
