<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import FormInput from '@/Components/Common/FormInput.vue';
import FormTextarea from '@/Components/Common/FormTextarea.vue';
import FormCheckbox from '@/Components/Common/FormCheckbox.vue';
import PrimaryButton from '@/Components/Common/PrimaryButton.vue';

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
                <PrimaryButton
                    @click="showForm = !showForm"
                    :variant="showForm ? 'secondary' : 'primary'"
                >
                    {{ showForm ? 'Annuler' : 'Nouvelle collection' }}
                </PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-[1920px] mx-auto sm:px-6 lg:px-8 space-y-6">
                <div v-if="showForm" class="bg-white rounded-lg shadow p-6">
                    <form @submit.prevent="submitCollection" class="space-y-4">
                        <FormInput
                            v-model="form.name"
                            label="Nom de la collection"
                            type="text"
                            required
                            :error="form.errors.name"
                        />

                        <FormTextarea
                            v-model="form.description"
                            label="Description"
                            rows="3"
                            :error="form.errors.description"
                        />

                        <FormCheckbox
                            v-model="form.is_public"
                            id="is_public"
                            label="Collection publique"
                        />

                        <PrimaryButton
                            type="submit"
                            :loading="form.processing"
                        >
                            Créer
                        </PrimaryButton>
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
