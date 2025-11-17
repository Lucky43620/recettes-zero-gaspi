<script setup>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const { t } = useI18n();

const props = defineProps({
    form: Object,
    isAuthenticated: Boolean,
});

const emit = defineEmits(['submit']);

const photoPreviews = ref([]);

function handlePhotoSelect(event) {
    const files = Array.from(event.target.files);
    if (files.length + props.form.photos.length > 5) {
        alert(t('cooksnaps.max_photos_alert'));
        return;
    }

    props.form.photos.push(...files);

    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreviews.value.push(e.target.result);
        };
        reader.readAsDataURL(file);
    });
}

function removePhoto(index) {
    props.form.photos.splice(index, 1);
    photoPreviews.value.splice(index, 1);
}

defineExpose({
    resetPreviews: () => {
        photoPreviews.value = [];
    }
});
</script>

<template>
    <div v-if="!isAuthenticated" class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
        <p class="text-gray-700 mb-4">{{ t('cooksnaps.login_to_share') }}</p>
        <Link :href="route('login')">
            <PrimaryButton>
                {{ t('auth.login') }}
            </PrimaryButton>
        </Link>
    </div>

    <form v-else @submit.prevent="emit('submit')" class="space-y-4 bg-gray-50 rounded-lg p-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ t('cooksnaps.photos_1_5') }}
            </label>
            <div class="mb-4">
                <input
                    type="file"
                    @change="handlePhotoSelect"
                    accept="image/*"
                    multiple
                    id="cooksnap-photo-input"
                    class="hidden"
                    :disabled="form.photos.length >= 5"
                />
                <label
                    for="cooksnap-photo-input"
                    :class="[
                        'inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 transition ease-in-out duration-150 cursor-pointer',
                        form.photos.length >= 5 ? 'opacity-50 cursor-not-allowed' : ''
                    ]"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ form.photos.length > 0 ? t('cooksnaps.add_more_photos') : t('cooksnaps.select_photos') }}
                </label>
            </div>
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
                {{ t('cooksnaps.comment_optional') }}
            </label>
            <textarea
                v-model="form.comment"
                rows="3"
                class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"
                :placeholder="t('cooksnaps.describe_experience')"
            ></textarea>
        </div>

        <PrimaryButton type="submit" :loading="form.processing" :disabled="form.photos.length === 0">
            {{ t('cooksnaps.publish_cooksnap') }}
        </PrimaryButton>
    </form>
</template>
