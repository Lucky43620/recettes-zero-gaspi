<script setup>
defineProps({
    title: String,
    subtitle: String
});
</script>

<template>
    <div class="mb-6 md:mb-8">
        <h1 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 mb-2">{{ title }}</h1>
        <p v-if="subtitle" class="text-sm md:text-base text-gray-600">{{ subtitle }}</p>
    </div>
</template>
