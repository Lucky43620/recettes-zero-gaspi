<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    shoppingLists: Array,
});

const showCreateModal = ref(false);
const newListName = ref('');
const confirmingListDeletion = ref(false);
const listToDelete = ref(null);

const createList = () => {
    router.post(route('shopping-lists.store'), {
        name: newListName.value,
    }, {
        onSuccess: () => {
            showCreateModal.value = false;
            newListName.value = '';
        },
    });
};

const confirmDelete = (listId) => {
    listToDelete.value = listId;
    confirmingListDeletion.value = true;
};

const deleteList = () => {
    router.delete(route('shopping-lists.destroy', listToDelete.value), {
        onFinish: () => {
            confirmingListDeletion.value = false;
            listToDelete.value = null;
        },
    });
};

const closeDeleteModal = () => {
    confirmingListDeletion.value = false;
    listToDelete.value = null;
};
</script>

<template>
    <AppLayout title="Listes de courses">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Mes listes de courses
                </h2>
                <button
                    @click="showCreateModal = true"
                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700"
                >
                    Nouvelle liste
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="shoppingLists.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="list in shoppingLists"
                        :key="list.id"
                        class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition"
                    >
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-semibold">{{ list.name }}</h3>
                            <button
                                @click="confirmDelete(list.id)"
                                class="text-red-500 hover:text-red-700"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                        <div class="text-sm text-gray-600 mb-4">
                            <p>{{ list.items.length }} article{{ list.items.length > 1 ? 's' : '' }}</p>
                            <p>{{ list.items.filter(item => item.is_checked).length }} coché{{ list.items.filter(item => item.is_checked).length > 1 ? 's' : '' }}</p>
                        </div>
                        <Link
                            :href="route('shopping-lists.show', list.id)"
                            class="block w-full text-center bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700"
                        >
                            Voir la liste
                        </Link>
                    </div>
                </div>
                <div v-else class="text-center py-12">
                    <p class="text-gray-500 mb-4">Aucune liste de courses</p>
                    <button
                        @click="showCreateModal = true"
                        class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700"
                    >
                        Créer ma première liste
                    </button>
                </div>
            </div>
        </div>

        <div
            v-if="showCreateModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @click.self="showCreateModal = false"
        >
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">Nouvelle liste de courses</h3>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nom de la liste
                    </label>
                    <input
                        v-model="newListName"
                        type="text"
                        class="w-full px-3 py-2 border rounded-md"
                        placeholder="Ma liste de courses"
                        @keyup.enter="createList"
                    />
                </div>
                <div class="flex gap-3 justify-end">
                    <button
                        @click="showCreateModal = false"
                        class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md"
                    >
                        Annuler
                    </button>
                    <button
                        @click="createList"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                    >
                        Créer
                    </button>
                </div>
            </div>
        </div>

        <ConfirmationModal :show="confirmingListDeletion" @close="closeDeleteModal">
            <template #title>
                Supprimer la liste de courses
            </template>

            <template #content>
                Êtes-vous sûr de vouloir supprimer cette liste ? Cette action est irréversible.
            </template>

            <template #footer>
                <SecondaryButton @click="closeDeleteModal">
                    Annuler
                </SecondaryButton>

                <DangerButton
                    class="ms-3"
                    @click="deleteList"
                >
                    Supprimer
                </DangerButton>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>
