<template>
    <AppLayout title="Mon Garde-Manger">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Mon Garde-Manger
                </h2>
                <PrimaryButton @click="openAddModal" type="button">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajouter un article
                </PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-[1920px] mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Total d'articles</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3">
                                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Expire bientôt</p>
                                <p class="text-2xl font-bold text-yellow-900">{{ stats.expiring_soon }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-100 rounded-full p-3">
                                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Expiré</p>
                                <p class="text-2xl font-bold text-red-900">{{ stats.expired }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Filtrer par statut</label>
                                <select
                                    v-model="filters.status"
                                    @change="applyFilters"
                                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                                >
                                    <option value="">Tous les articles</option>
                                    <option value="expiring">Expire bientôt</option>
                                    <option value="expired">Expiré</option>
                                </select>
                            </div>

                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Filtrer par emplacement</label>
                                <select
                                    v-model="filters.storage"
                                    @change="applyFilters"
                                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                                >
                                    <option value="">Tous les emplacements</option>
                                    <option v-for="location in storageLocations" :key="location" :value="location">
                                        {{ location }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="items.length === 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                    <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun article dans votre garde-manger</h3>
                    <p class="text-gray-500 mb-4">Commencez à ajouter des ingrédients pour suivre vos stocks et dates de péremption.</p>
                    <PrimaryButton @click="openAddModal" type="button">
                        Ajouter mon premier article
                    </PrimaryButton>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <PantryItemCard
                        v-for="item in items"
                        :key="item.id"
                        :item="item"
                        @edit="openEditModal"
                        @delete="deleteItem"
                    />
                </div>
            </div>
        </div>

        <AddPantryItemModal
            v-if="showAddModal"
            :units="units"
            @close="closeAddModal"
        />

        <EditPantryItemModal
            v-if="showEditModal && editingItem"
            :item="editingItem"
            :units="units"
            @close="closeEditModal"
        />

        <ConfirmationModal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <template #title>
                Supprimer l'article
            </template>

            <template #content>
                Êtes-vous sûr de vouloir supprimer cet article de votre garde-manger ? Cette action est irréversible.
            </template>

            <template #footer>
                <PrimaryButton variant="secondary" @click="confirmingDeletion = false">
                    Annuler
                </PrimaryButton>

                <PrimaryButton
                    variant="danger"
                    class="ms-3"
                    @click="confirmDelete"
                >
                    Supprimer
                </PrimaryButton>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PantryItemCard from '@/Components/Pantry/PantryItemCard.vue';
import AddPantryItemModal from '@/Components/Pantry/AddPantryItemModal.vue';
import EditPantryItemModal from '@/Components/Pantry/EditPantryItemModal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from '@/Components/Common/PrimaryButton.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    items: Array,
    stats: Object,
    storageLocations: Array,
    units: Array,
});

const showAddModal = ref(false);
const showEditModal = ref(false);
const editingItem = ref(null);
const confirmingDeletion = ref(false);
const itemToDelete = ref(null);

const filters = ref({
    status: '',
    storage: '',
});

const openAddModal = () => {
    showAddModal.value = true;
};

const closeAddModal = () => {
    showAddModal.value = false;
};

const openEditModal = (item) => {
    editingItem.value = item;
    showEditModal.value = true;
};

const closeEditModal = () => {
    editingItem.value = null;
    showEditModal.value = false;
};

const applyFilters = () => {
    router.get(route('pantry.index'), {
        filter: filters.value.status || undefined,
        storage: filters.value.storage || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const deleteItem = (item) => {
    itemToDelete.value = item;
    confirmingDeletion.value = true;
};

const confirmDelete = () => {
    router.delete(route('pantry.destroy', itemToDelete.value.id), {
        preserveScroll: true,
        onFinish: () => {
            confirmingDeletion.value = false;
            itemToDelete.value = null;
        },
    });
};
</script>
