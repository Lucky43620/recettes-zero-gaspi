<template>
    <DialogModal :show="true" @close="$emit('close')">
        <template #title>
            Modifier l'article
        </template>

        <template #content>
            <form @submit.prevent="submit">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Ingrédient
                        </label>
                        <div class="p-3 bg-gray-50 border border-gray-200 rounded-md flex items-center gap-3">
                            <img
                                v-if="item.ingredient.image_url"
                                :src="item.ingredient.image_url"
                                :alt="item.ingredient.name"
                                class="w-12 h-12 object-cover rounded"
                            >
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ item.ingredient.name }}</p>
                            </div>
                        </div>
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
                            id="opened-edit"
                            class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500"
                        >
                        <label for="opened-edit" class="ml-2 text-sm text-gray-700">
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
                :disabled="form.processing"
                class="ml-3 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
            >
                <span v-if="form.processing">Enregistrement...</span>
                <span v-else>Enregistrer</span>
            </button>
        </template>
    </DialogModal>
</template>

<script setup>
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    item: Object,
    units: Array,
});

const emit = defineEmits(['close']);

const form = useForm({
    quantity: props.item.quantity,
    unit_code: props.item.unit.code,
    expiration_date: props.item.expiration_date || '',
    storage_location: props.item.storage_location || '',
    opened: props.item.opened,
});

const submit = () => {
    form.put(route('pantry.update', props.item.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit('close');
        },
    });
};
</script>
