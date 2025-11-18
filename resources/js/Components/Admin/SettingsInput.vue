<template>
    <div class="py-3">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        <p v-if="help" class="text-sm text-gray-500 mb-2">{{ help }}</p>

        <input
            v-if="type !== 'textarea'"
            :type="type"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="placeholder"
            :required="required"
            :min="min"
            :max="max"
            :step="step"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
        />

        <textarea
            v-else
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="placeholder"
            :required="required"
            :rows="rows || 3"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
        />
    </div>
</template>

<script setup>
const props = withDefaults(defineProps<{
    label?: string
    help?: string
    type?: string
    modelValue?: string | number
    placeholder?: string
    required?: boolean
    min?: string | number
    max?: string | number
    step?: string | number
    rows?: number
}>(), {
    type: 'text',
    required: false,
    rows: 3
})

defineEmits(['update:modelValue'])
</script>
