<template>
    <div class="flex items-center justify-between py-3">
        <div class="flex-1">
            <label class="text-sm font-medium text-gray-700">{{ label }}</label>
            <p v-if="description" class="text-sm text-gray-500 mt-1">{{ description }}</p>
        </div>
        <button
            type="button"
            @click="toggle"
            :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2',
                modelValue ? 'bg-orange-600' : 'bg-gray-200'
            ]"
        >
            <span
                :class="[
                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                    modelValue ? 'translate-x-5' : 'translate-x-0'
                ]"
            />
        </button>
    </div>
</template>

<script setup>
const props = defineProps({
    label: String,
    description: String,
    modelValue: Boolean
})

const emit = defineEmits(['update:modelValue'])

const toggle = () => {
    emit('update:modelValue', !props.modelValue)
}
</script>
