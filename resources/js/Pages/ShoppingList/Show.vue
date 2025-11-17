<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import BackButton from '@/Components/Common/BackButton.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    shoppingList: Object,
});

const newItemName = ref('');
const newItemQuantity = ref('');

const uncheckedItems = computed(() =>
    props.shoppingList.items.filter(item => !item.is_checked)
);

const checkedItems = computed(() =>
    props.shoppingList.items.filter(item => item.is_checked)
);

const addItem = () => {
    if (!newItemName.value.trim()) return;

    router.post(route('shopping-lists.items.add', props.shoppingList.id), {
        name: newItemName.value,
        quantity: newItemQuantity.value || null,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            newItemName.value = '';
            newItemQuantity.value = '';
        },
    });
};

const toggleItem = (itemId, currentStatus) => {
    router.put(route('shopping-lists.items.update', itemId), {
        is_checked: !currentStatus,
    }, {
        preserveScroll: true,
    });
};

const removeItem = (itemId) => {
    router.delete(route('shopping-lists.items.remove', itemId), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :title="shoppingList.name">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ shoppingList.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <BackButton
                    :href="route('shopping-lists.index')"
                    :label="t('shopping_list.back_to_lists')"
                    class="mb-6"
                />
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h3 class="font-semibold text-lg mb-4">{{ t('shopping_list.add_item') }}</h3>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input
                            v-model="newItemName"
                            type="text"
                            :placeholder="t('shopping_list.item_name')"
                            class="flex-1 px-3 py-2 border rounded-md"
                            @keyup.enter="addItem"
                        />
                        <input
                            v-model="newItemQuantity"
                            type="text"
                            :placeholder="t('shopping_list.quantity_optional')"
                            class="w-full sm:w-40 px-3 py-2 border rounded-md"
                            @keyup.enter="addItem"
                        />
                        <button
                            @click="addItem"
                            class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 w-full sm:w-auto"
                        >
                            {{ t('shopping_list.add') }}
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow">
                    <div v-if="uncheckedItems.length" class="p-6 border-b">
                        <h3 class="font-semibold text-lg mb-4">{{ t('shopping_list.to_buy') }} ({{ uncheckedItems.length }})</h3>
                        <div class="space-y-2">
                            <div
                                v-for="item in uncheckedItems"
                                :key="item.id"
                                class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-md group"
                            >
                                <input
                                    type="checkbox"
                                    :checked="item.is_checked"
                                    @change="toggleItem(item.id, item.is_checked)"
                                    class="w-5 h-5 rounded border-gray-300"
                                />
                                <div class="flex-1">
                                    <p class="font-medium">{{ item.name }}</p>
                                    <p v-if="item.quantity" class="text-sm text-gray-500">
                                        {{ item.quantity }} {{ item.unit?.symbol }}
                                    </p>
                                </div>
                                <button
                                    @click="removeItem(item.id)"
                                    class="text-red-500 hover:text-red-700 opacity-0 group-hover:opacity-100 transition"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="checkedItems.length" class="p-6">
                        <h3 class="font-semibold text-lg mb-4 text-gray-500">{{ t('shopping_list.bought') }} ({{ checkedItems.length }})</h3>
                        <div class="space-y-2">
                            <div
                                v-for="item in checkedItems"
                                :key="item.id"
                                class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-md group opacity-60"
                            >
                                <input
                                    type="checkbox"
                                    :checked="item.is_checked"
                                    @change="toggleItem(item.id, item.is_checked)"
                                    class="w-5 h-5 rounded border-gray-300"
                                />
                                <div class="flex-1">
                                    <p class="font-medium line-through">{{ item.name }}</p>
                                    <p v-if="item.quantity" class="text-sm text-gray-500">
                                        {{ item.quantity }} {{ item.unit?.symbol }}
                                    </p>
                                </div>
                                <button
                                    @click="removeItem(item.id)"
                                    class="text-red-500 hover:text-red-700 opacity-0 group-hover:opacity-100 transition"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="!shoppingList.items.length" class="p-12 text-center">
                        <p class="text-gray-500">{{ t('shopping_list.no_items') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
