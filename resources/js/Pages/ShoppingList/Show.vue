<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

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
            <div class="flex justify-between items-center">
                <div>
                    <Link
                        :href="route('shopping-lists.index')"
                        class="text-sm text-gray-500 hover:text-gray-700 mb-2 inline-block"
                    >
                        ← Retour aux listes
                    </Link>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ shoppingList.name }}
                    </h2>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h3 class="font-semibold text-lg mb-4">Ajouter un article</h3>
                    <div class="flex gap-3">
                        <input
                            v-model="newItemName"
                            type="text"
                            placeholder="Nom de l'article"
                            class="flex-1 px-3 py-2 border rounded-md"
                            @keyup.enter="addItem"
                        />
                        <input
                            v-model="newItemQuantity"
                            type="text"
                            placeholder="Quantité (optionnel)"
                            class="w-40 px-3 py-2 border rounded-md"
                            @keyup.enter="addItem"
                        />
                        <button
                            @click="addItem"
                            class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700"
                        >
                            Ajouter
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow">
                    <div v-if="uncheckedItems.length" class="p-6 border-b">
                        <h3 class="font-semibold text-lg mb-4">À acheter ({{ uncheckedItems.length }})</h3>
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
                        <h3 class="font-semibold text-lg mb-4 text-gray-500">Acheté ({{ checkedItems.length }})</h3>
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
                        <p class="text-gray-500">Aucun article dans cette liste</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
