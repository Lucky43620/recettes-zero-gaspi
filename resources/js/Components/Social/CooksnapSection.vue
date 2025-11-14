<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from '@/Components/Common/PrimaryButton.vue';
import CooksnapForm from './CooksnapForm.vue';
import CooksnapCard from './CooksnapCard.vue';

const { t } = useI18n();
const page = usePage();

const props = defineProps({
    recipe: Object,
    cooksnaps: Array,
});

const form = useForm({
    comment: '',
    photos: [],
});

const cooksnapFormRef = ref(null);
const confirmingDeletion = ref(false);
const cooksnapToDelete = ref(null);
const selectedImage = ref(null);

const isAuthenticated = computed(() => !!page.props.auth.user);
const currentUserId = computed(() => page.props.auth.user?.id);

function submitCooksnap() {
    form.post(route('cooksnaps.store', props.recipe.slug), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            cooksnapFormRef.value?.resetPreviews();
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
</script>

<template>
    <div class="space-y-6">
        <h3 class="text-xl font-semibold text-gray-900">
            {{ t('cooksnaps.title') }} ({{ cooksnaps?.length || 0 }})
        </h3>
        <p class="text-gray-600 text-sm">{{ t('cooksnaps.share_your_creation') }}</p>

        <CooksnapForm
            ref="cooksnapFormRef"
            :form="form"
            :is-authenticated="isAuthenticated"
            @submit="submitCooksnap"
        />

        <div v-if="cooksnaps?.length" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <CooksnapCard
                v-for="cooksnap in cooksnaps"
                :key="cooksnap.id"
                :cooksnap="cooksnap"
                :can-delete="currentUserId === cooksnap.user_id"
                @open-image="openImageModal"
                @delete="confirmDeleteCooksnap"
            />
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
                {{ t('cooksnaps.delete_cooksnap') }}
            </template>

            <template #content>
                {{ t('cooksnaps.delete_confirmation') }}
            </template>

            <template #footer>
                <PrimaryButton variant="secondary" @click="confirmingDeletion = false">
                    {{ t('common.cancel') }}
                </PrimaryButton>

                <PrimaryButton
                    variant="danger"
                    class="ms-3"
                    @click="deleteCooksnap"
                >
                    {{ t('common.delete') }}
                </PrimaryButton>
            </template>
        </ConfirmationModal>
    </div>
</template>
