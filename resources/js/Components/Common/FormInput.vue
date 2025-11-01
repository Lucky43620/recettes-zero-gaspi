<template>
    <div>
        <label v-if="label || $slots.label" :for="id" class="block text-sm font-medium text-gray-700 mb-2">
            <slot name="label">
                {{ label }}
                <span v-if="required" class="text-red-500">*</span>
            </slot>
        </label>
        <input
            :id="id"
            :type="type"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="placeholder"
            :required="required"
            :disabled="disabled"
            :min="min"
            :max="max"
            :step="step"
            :class="[
                'w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm',
                error ? 'border-red-500' : '',
                disabled ? 'bg-gray-100 cursor-not-allowed' : ''
            ]"
        >
        <p v-if="error" class="mt-2 text-sm text-red-600">{{ error }}</p>
        <p v-if="hint && !error" class="mt-2 text-sm text-gray-500">{{ hint }}</p>
    </div>
</template>

<script setup>
defineProps({
    id: String,
    label: String,
    type: {
        type: String,
        default: 'text'
    },
    modelValue: [String, Number],
    placeholder: String,
    required: Boolean,
    disabled: Boolean,
    error: String,
    hint: String,
    min: [String, Number],
    max: [String, Number],
    step: [String, Number],
});

defineEmits(['update:modelValue']);
</script>
