<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
            <p v-if="description" class="text-sm text-gray-500 mt-1">{{ description }}</p>
        </div>
        <div class="px-6 py-4">
            <slot />
        </div>
        <div v-if="$slots.actions" class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <slot name="actions" />
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: String,
    description: String
})
</script>
