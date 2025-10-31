<template>
    <DialogModal :show="true" @close="$emit('close')">
        <template #title>
            Ajouter un article au garde-manger
        </template>

        <template #content>
            <form @submit.prevent="submit">
                <div class="space-y-4">
                    <div>
                        <IngredientSearchInput
                            label="Ingrédient"
                            placeholder="Rechercher ou scanner un code-barres..."
                            :show-barcode-button="true"
                            @select="selectIngredient"
                            @scan="openBarcodeScanner"
                        />

                        <div v-if="form.ingredient_id && selectedIngredient" class="mt-2 p-3 bg-green-50 border border-green-200 rounded-md flex items-center gap-3">
                            <img
                                v-if="selectedIngredient.image_url"
                                :src="selectedIngredient.image_url"
                                :alt="selectedIngredient.name"
                                class="w-12 h-12 object-cover rounded"
                            >
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ selectedIngredient.name }}</p>
                            </div>
                            <button
                                type="button"
                                @click="clearSelection"
                                class="text-gray-400 hover:text-gray-600"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <InputError :message="form.errors.ingredient_id" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Quantité
                            </label>
                            <input
                                v-model="form.quantity"
                                type="number"
                                step="0.01"
                                min="0.01"
                                required
                                class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                            >
                            <InputError :message="form.errors.quantity" class="mt-2" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Unité
                            </label>
                            <select
                                v-model="form.unit_code"
                                required
                                class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                            >
                                <option value="">Sélectionner</option>
                                <option v-for="unit in units" :key="unit.code" :value="unit.code">
                                    {{ unit.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.unit_code" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date de péremption
                        </label>
                        <input
                            v-model="form.expiration_date"
                            type="date"
                            :min="today"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                        >
                        <InputError :message="form.errors.expiration_date" class="mt-2" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Emplacement de stockage
                        </label>
                        <select
                            v-model="form.storage_location"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                        >
                            <option value="">Sélectionner un emplacement</option>
                            <option value="Réfrigérateur">Réfrigérateur</option>
                            <option value="Congélateur">Congélateur</option>
                            <option value="Placard">Placard</option>
                            <option value="Cave">Cave</option>
                            <option value="Garde-manger">Garde-manger</option>
                        </select>
                        <InputError :message="form.errors.storage_location" class="mt-2" />
                    </div>

                    <div class="flex items-center">
                        <input
                            v-model="form.opened"
                            type="checkbox"
                            id="opened"
                            class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500"
                        >
                        <label for="opened" class="ml-2 text-sm text-gray-700">
                            Article déjà ouvert
                        </label>
                    </div>
                </div>
            </form>
        </template>

        <template #footer>
            <button
                type="button"
                @click="$emit('close')"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
            >
                Annuler
            </button>

            <button
                @click="submit"
                :disabled="form.processing || !form.ingredient_id"
                class="ml-3 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
            >
                <span v-if="form.processing">Ajout en cours...</span>
                <span v-else>Ajouter</span>
            </button>
        </template>
    </DialogModal>
</template>

<script setup>
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import IngredientSearchInput from '@/Components/Ingredients/IngredientSearchInput.vue';
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    units: Array,
});

const emit = defineEmits(['close']);

const form = useForm({
    ingredient_id: null,
    quantity: 1,
    unit_code: '',
    expiration_date: '',
    storage_location: '',
    opened: false,
});

const selectedIngredient = ref(null);

const today = computed(() => {
    return new Date().toISOString().split('T')[0];
});

const selectIngredient = (ingredient) => {
    form.ingredient_id = ingredient.id;
    selectedIngredient.value = ingredient;
};

const clearSelection = () => {
    form.ingredient_id = null;
    selectedIngredient.value = null;
};

const openBarcodeScanner = () => {
    alert('Scanner de code-barres à implémenter');
};

const submit = () => {
    form.post(route('pantry.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit('close');
        },
    });
};
</script>
