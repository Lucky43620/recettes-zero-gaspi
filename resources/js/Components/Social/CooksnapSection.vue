<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from '@/Components/Common/PrimaryButton.vue';
import { useDateFormat } from '@/composables/useDateFormat';

const props = defineProps({
    recipe: Object,
    cooksnaps: Array,
});

const form = useForm({
    comment: '',
    photos: [],
});

const photoPreviews = ref([]);
const confirmingDeletion = ref(false);
const cooksnapToDelete = ref(null);
const selectedImage = ref(null);

function handlePhotoSelect(event) {
    const files = Array.from(event.target.files);
    if (files.length + form.photos.length > 5) {
        alert('Maximum 5 photos autorisées');
        return;
    }

    form.photos.push(...files);

    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreviews.value.push(e.target.result);
        };
        reader.readAsDataURL(file);
    });
}

function removePhoto(index) {
    form.photos.splice(index, 1);
    photoPreviews.value.splice(index, 1);
}

function submitCooksnap() {
    form.post(route('cooksnaps.store', props.recipe.slug), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            photoPreviews.value = [];
        },
    });
}

function confirmDeleteCooksnap(cooksnapId) {
    cooksnapToDelete.value = cooksnapId;
    confirmingDeletion.value = true;
}

function deleteCooksnap() {
    useForm({}).delete(route('cooksnaps.destroy', cooksnapToDelete.value), {
        preserveScroll: true,
        onSuccess: () => {
            confirmingDeletion.value = false;
            cooksnapToDelete.value = null;
        },
    });
}

function openImageModal(imageUrl) {
    selectedImage.value = imageUrl;
}

function closeImageModal() {
    selectedImage.value = null;
}

const { formatRelativeTime } = useDateFormat();
</script>

<template>
    <div class="space-y-6">
        <h3 class="text-xl font-semibold text-gray-900">
            Cooksnaps ({{ cooksnaps?.length || 0 }})
        </h3>
        <p class="text-gray-600 text-sm">Partagez vos réalisations de cette recette !</p>

        <div v-if="!$page.props.auth.user" class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
            <p class="text-gray-700 mb-4">Connectez-vous pour partager votre réalisation</p>
            <Link :href="route('login')">
                <PrimaryButton>
                    Se connecter
                </PrimaryButton>
            </Link>
        </div>

        <form v-else @submit.prevent="submitCooksnap" class="space-y-4 bg-gray-50 rounded-lg p-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Photos (1-5) *
                </label>
                <input
                    type="file"
                    @change="handlePhotoSelect"
                    accept="image/*"
                    multiple
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"
                    :disabled="form.photos.length >= 5"
                />
                <div v-if="photoPreviews.length" class="grid grid-cols-3 gap-2 mt-4">
                    <div v-for="(preview, index) in photoPreviews" :key="index" class="relative">
                        <img :src="preview" class="w-full h-24 object-cover rounded-lg" />
                        <button
                            type="button"
                            @click="removePhoto(index)"
                            class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-700"
                        >
                            ×
                        </button>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Commentaire (optionnel)
                </label>
                <textarea
                    v-model="form.comment"
                    rows="3"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"
                    placeholder="Décrivez votre expérience avec cette recette..."
                ></textarea>
            </div>

            <PrimaryButton type="submit" :loading="form.processing" :disabled="form.photos.length === 0">
                Publier mon cooksnap
            </PrimaryButton>
        </form>

        <div v-if="cooksnaps?.length" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div
                v-for="cooksnap in cooksnaps"
                :key="cooksnap.id"
                class="bg-white border border-gray-200 rounded-lg overflow-hidden"
            >
                <div class="relative aspect-square">
                    <img
                        v-if="cooksnap.media?.[0]"
                        :src="cooksnap.media[0].original_url"
                        :alt="`Cooksnap par ${cooksnap.user.name}`"
                        class="w-full h-full object-cover cursor-pointer hover:opacity-90 transition"
                        @click="openImageModal(cooksnap.media[0].original_url)"
                    />
                    <div v-if="cooksnap.media?.length > 1" class="absolute bottom-2 right-2 bg-black bg-opacity-60 text-white px-2 py-1 rounded text-sm">
                        +{{ cooksnap.media.length - 1 }}
                    </div>
                </div>

                <div class="p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <img
                            :src="cooksnap.user.profile_photo_url"
                            :alt="cooksnap.user.name"
                            class="w-8 h-8 rounded-full"
                        />
                        <div class="flex-1">
                            <span class="font-medium text-gray-900 text-sm">{{ cooksnap.user.name }}</span>
                            <p class="text-xs text-gray-500">{{ formatRelativeTime(cooksnap.created_at) }}</p>
                        </div>
                        <button
                            v-if="$page.props.auth.user?.id === cooksnap.user_id"
                            @click="confirmDeleteCooksnap(cooksnap.id)"
                            class="text-red-600 hover:text-red-800 text-sm"
                        >
                            Supprimer
                        </button>
                    </div>
                    <p v-if="cooksnap.comment" class="text-gray-700 text-sm">{{ cooksnap.comment }}</p>
                </div>
            </div>
        </div>

        <div v-if="selectedImage" class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" @click="closeImageModal">
            <img :src="selectedImage" class="max-w-full max-h-full object-contain" @click.stop />
            <button
                @click="closeImageModal"
                class="absolute top-4 right-4 text-white text-4xl hover:text-gray-300"
            >
                ×
            </button>
        </div>

        <ConfirmationModal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <template #title>
                Supprimer le cooksnap
            </template>

            <template #content>
                Êtes-vous sûr de vouloir supprimer ce cooksnap ? Cette action est irréversible.
            </template>

            <template #footer>
                <PrimaryButton variant="secondary" @click="confirmingDeletion = false">
                    Annuler
                </PrimaryButton>

                <PrimaryButton
                    variant="danger"
                    class="ms-3"
                    @click="deleteCooksnap"
                >
                    Supprimer
                </PrimaryButton>
            </template>
        </ConfirmationModal>
    </div>
</template>
