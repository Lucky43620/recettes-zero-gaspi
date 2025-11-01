<template>
    <div>
        <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-2">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        <textarea
            :id="id"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="placeholder"
            :required="required"
            :disabled="disabled"
            :rows="rows"
            :class="[
                'w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm',
                error ? 'border-red-500' : '',
                disabled ? 'bg-gray-100 cursor-not-allowed' : ''
            ]"
        ></textarea>
        <p v-if="error" class="mt-2 text-sm text-red-600">{{ error }}</p>
        <p v-if="hint && !error" class="mt-2 text-sm text-gray-500">{{ hint }}</p>
    </div>
</template>

<script setup>
defineProps({
    id: String,
    label: String,
    modelValue: String,
    placeholder: String,
    required: Boolean,
    disabled: Boolean,
    error: String,
    hint: String,
    rows: {
        type: Number,
        default: 3
    },
});

defineEmits(['update:modelValue']);
</script>
