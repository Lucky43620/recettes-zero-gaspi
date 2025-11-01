<template>
    <div class="flex items-center">
        <input
            :id="id"
            type="checkbox"
            :checked="modelValue"
            @change="$emit('update:modelValue', $event.target.checked)"
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
defineProps({
    id: String,
    label: String,
    modelValue: {
        type: Boolean,
        default: false
    },
    disabled: Boolean,
    error: String,
    hint: String,
});

defineEmits(['update:modelValue']);
</script>
