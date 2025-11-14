<script setup>
import { useI18n } from 'vue-i18n';
import { useDateFormat } from '@/composables/useDateFormat';

const { t } = useI18n();
const { formatRelativeTime } = useDateFormat();

defineProps({
    cooksnap: Object,
    canDelete: Boolean,
});

const emit = defineEmits(['openImage', 'delete']);
</script>

<template>
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="relative aspect-square">
            <img
                v-if="cooksnap.media?.[0]"
                :src="cooksnap.media[0].original_url"
                :alt="`Cooksnap par ${cooksnap.user.name}`"
                class="w-full h-full object-cover cursor-pointer hover:opacity-90 transition"
                @click="emit('openImage', cooksnap.media[0].original_url)"
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
                    v-if="canDelete"
                    @click="emit('delete', cooksnap.id)"
                    class="text-red-600 hover:text-red-800 text-sm"
                >
                    {{ t('common.delete') }}
                </button>
            </div>
            <p v-if="cooksnap.comment" class="text-gray-700 text-sm">{{ cooksnap.comment }}</p>
        </div>
    </div>
</template>
