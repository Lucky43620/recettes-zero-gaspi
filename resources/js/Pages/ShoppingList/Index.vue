<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

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
    <AppLayout :title="t('shopping_list.title')">
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ t('shopping_list.my_lists') }}
                </h2>
                <PrimaryButton @click="showCreateModal = true" class="w-full sm:w-auto">
                    {{ t('shopping_list.new_list') }}
                </PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">
                <div v-if="shoppingLists && shoppingLists.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                            <p>{{ list.items.length }} {{ t('shopping_list.items_count', list.items.length) }}</p>
                            <p>{{ list.items.filter(item => item.is_checked).length }} {{ t('shopping_list.checked_count', list.items.filter(item => item.is_checked).length) }}</p>
                        </div>
                        <Link :href="route('shopping-lists.show', list.id)" class="block">
                            <PrimaryButton class="w-full">
                                {{ t('shopping_list.view_list') }}
                            </PrimaryButton>
                        </Link>
                    </div>
                </div>
                <div v-else class="text-center py-12">
                    <p class="text-gray-500 mb-4">{{ t('shopping_list.no_lists') }}</p>
                    <PrimaryButton @click="showCreateModal = true">
                        {{ t('shopping_list.create_first_list') }}
                    </PrimaryButton>
                </div>
            </div>
        </div>

        <div
            v-if="showCreateModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            @click.self="showCreateModal = false"
        >
            <div class="bg-white rounded-lg p-4 md:p-6 max-w-md w-full">
                <h3 class="text-base md:text-lg font-semibold mb-4">{{ t('shopping_list.new_list') }}</h3>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ t('shopping_list.list_name') }}
                    </label>
                    <input
                        v-model="newListName"
                        type="text"
                        class="w-full px-3 py-3 border rounded-md text-base"
                        :placeholder="t('shopping_list.list_name_placeholder')"
                        @keyup.enter="createList"
                    />
                </div>
                <div class="flex flex-col sm:flex-row gap-3 justify-end">
                    <PrimaryButton variant="secondary" @click="showCreateModal = false" class="w-full sm:w-auto">
                        {{ t('common.cancel') }}
                    </PrimaryButton>
                    <PrimaryButton @click="createList" class="w-full sm:w-auto">
                        {{ t('common.create') }}
                    </PrimaryButton>
                </div>
            </div>
        </div>

        <ConfirmationModal :show="confirmingListDeletion" @close="closeDeleteModal">
            <template #title>
                {{ t('shopping_list.delete_list') }}
            </template>

            <template #content>
                {{ t('shopping_list.delete_confirmation') }}
            </template>

            <template #footer>
                <PrimaryButton variant="secondary" @click="closeDeleteModal">
                    {{ t('common.cancel') }}
                </PrimaryButton>

                <PrimaryButton
                    variant="danger"
                    class="ms-3"
                    @click="deleteList"
                >
                    {{ t('common.delete') }}
                </PrimaryButton>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>
