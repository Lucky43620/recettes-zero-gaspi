<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    recipe: Object,
    isFavorited: Boolean,
});

const form = useForm({});

function toggleFavorite() {
    form.post(route('favorites.toggle', props.recipe.slug), {
        preserveScroll: true,
    });
}
</script>

<template>
    <button
        @click="toggleFavorite"
        :disabled="form.processing"
        class="flex items-center gap-2 px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-50 transition disabled:opacity-50"
    >
        <svg
            class="w-5 h-5"
            :class="isFavorited ? 'text-red-500 fill-current' : 'text-gray-400'"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
            />
        </svg>
        <span>{{ isFavorited ? 'Retirer des favoris' : 'Ajouter aux favoris' }}</span>
    </button>
</template>
