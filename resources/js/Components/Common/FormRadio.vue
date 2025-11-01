<template>
    <div>
        <label v-if="groupLabel" class="block text-sm font-medium text-gray-700 mb-2">
            {{ groupLabel }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        <div :class="inline ? 'flex items-center gap-4' : 'space-y-2'">
            <div v-for="option in options" :key="option.value" class="flex items-center">
                <input
                    :id="`${name}-${option.value}`"
                    type="radio"
                    :name="name"
                    :value="option.value"
                    :checked="modelValue === option.value"
                    @change="$emit('update:modelValue', option.value)"
                    :disabled="disabled"
                    :class="[
                        'border-gray-300 text-green-600 focus:ring-green-500',
                        disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                    ]"
                />
                <label
                    :for="`${name}-${option.value}`"
                    :class="['ml-2 text-sm', disabled ? 'text-gray-400' : 'text-gray-700', disabled ? 'cursor-not-allowed' : 'cursor-pointer']"
                >
                    {{ option.label }}
                </label>
            </div>
        </div>
        <p v-if="error" class="mt-2 text-sm text-red-600">{{ error }}</p>
        <p v-if="hint && !error" class="mt-2 text-sm text-gray-500">{{ hint }}</p>
    </div>
</template>

<script setup>
defineProps({
    name: {
        type: String,
        required: true
    },
    groupLabel: String,
    options: {
        type: Array,
        required: true
    },
    modelValue: [String, Number, Boolean],
    inline: {
        type: Boolean,
        default: false
    },
    required: Boolean,
    disabled: Boolean,
    error: String,
    hint: String,
});

defineEmits(['update:modelValue']);
</script>
