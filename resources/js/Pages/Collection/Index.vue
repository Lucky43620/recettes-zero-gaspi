<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    collections: Array,
});

const showForm = ref(false);
const form = useForm({
    name: '',
    description: '',
    is_public: false,
});

function submitCollection() {
    form.post(route('collections.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            showForm.value = false;
        },
    });
}
</script>

<template>
    <AppLayout title="Mes collections">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Mes collections
                </h2>
                <button
                    @click="showForm = !showForm"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                >
                    {{ showForm ? 'Annuler' : 'Nouvelle collection' }}
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div v-if="showForm" class="bg-white rounded-lg shadow p-6">
                    <form @submit.prevent="submitCollection" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nom de la collection *
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="3"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"
                            ></textarea>
                        </div>

                        <div class="flex items-center">
                            <input
                                v-model="form.is_public"
                                type="checkbox"
                                class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                            />
                            <label class="ml-2 text-sm text-gray-700">
                                Collection publique
                            </label>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
                        >
                            Créer
                        </button>
                    </form>
                </div>

                <div v-if="collections.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link
                        v-for="collection in collections"
                        :key="collection.id"
                        :href="route('collections.show', collection.id)"
                        class="bg-white rounded-lg shadow hover:shadow-lg transition p-6"
                    >
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            {{ collection.name }}
                        </h3>
                        <p v-if="collection.description" class="text-gray-600 text-sm mb-4">
                            {{ collection.description }}
                        </p>
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <span>{{ collection.recipes_count }} recettes</span>
                            <span v-if="collection.is_public" class="text-green-600">Publique</span>
                            <span v-else class="text-gray-600">Privée</span>
                        </div>
                    </Link>
                </div>

                <div v-else class="bg-white rounded-lg shadow p-8 text-center">
                    <p class="text-gray-600">
                        Vous n'avez pas encore de collection
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
