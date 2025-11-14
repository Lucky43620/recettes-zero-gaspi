<template>
    <AppLayout :title="t('pantry.title')">
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ t('pantry.title') }}
                </h2>
                <PrimaryButton @click="openAddModal" type="button" class="w-full sm:w-auto">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ t('pantry.add_item') }}
                </PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-[1920px] mx-auto sm:px-6 lg:px-8">
                <!-- Free user limit banner -->
                <div v-if="!isPremium && itemLimit" class="bg-gradient-to-r from-yellow-50 to-orange-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-lg shadow-sm">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="flex items-start sm:items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-3 flex-shrink-0 mt-0.5 sm:mt-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ t('pantry.free_limit_used', { used: stats.total, limit: itemLimit }) }}
                                </p>
                                <p class="text-xs text-gray-600 mt-0.5">
                                    {{ t('pantry.upgrade_unlimited_message') }}
                                </p>
                            </div>
                        </div>
                        <Link :href="route('subscription.index')" class="w-full sm:w-auto px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white text-sm font-semibold rounded-lg transition-all text-center whitespace-nowrap">
                            {{ t('pantry.upgrade_to_premium') }}
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">{{ t('pantry.total_items') }}</p>
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
                                <p class="text-sm text-gray-600">{{ t('pantry.expiring_soon') }}</p>
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
                                <p class="text-sm text-gray-600">{{ t('pantry.expired') }}</p>
                                <p class="text-2xl font-bold text-red-900">{{ stats.expired }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('pantry.filter_by_status') }}</label>
                                <select
                                    v-model="filters.status"
                                    @change="applyFilters"
                                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                                >
                                    <option value="">{{ t('pantry.all_items') }}</option>
                                    <option value="expiring">{{ t('pantry.expiring_soon') }}</option>
                                    <option value="expired">{{ t('pantry.expired') }}</option>
                                </select>
                            </div>

                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('pantry.filter_by_location') }}</label>
                                <select
                                    v-model="filters.storage"
                                    @change="applyFilters"
                                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                                >
                                    <option value="">{{ t('pantry.all_locations') }}</option>
                                    <option v-for="location in storageLocations" :key="location" :value="location">
                                        {{ location }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="!items || items.length === 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                    <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ t('pantry.no_items') }}</h3>
                    <p class="text-gray-500 mb-4">{{ t('pantry.empty_state') }}</p>
                    <PrimaryButton @click="openAddModal" type="button">
                        {{ t('pantry.add_first_item') }}
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
                {{ t('pantry.delete_item') }}
            </template>

            <template #content>
                {{ t('pantry.delete_confirmation') }}
            </template>

            <template #footer>
                <PrimaryButton variant="secondary" @click="confirmingDeletion = false">
                    {{ t('common.cancel') }}
                </PrimaryButton>

                <PrimaryButton
                    variant="danger"
                    class="ms-3"
                    @click="confirmDelete"
                >
                    {{ t('common.delete') }}
                </PrimaryButton>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>

<script setup>
import { useI18n } from 'vue-i18n';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PantryItemCard from '@/Components/Pantry/PantryItemCard.vue';
import AddPantryItemModal from '@/Components/Pantry/AddPantryItemModal.vue';
import EditPantryItemModal from '@/Components/Pantry/EditPantryItemModal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from '@/Components/Common/PrimaryButton.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const { t } = useI18n();

const props = defineProps({
    items: Array,
    stats: Object,
    storageLocations: Array,
    units: Array,
    isPremium: Boolean,
    itemLimit: Number,
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
