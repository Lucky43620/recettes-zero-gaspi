<script setup>
import { useI18n } from 'vue-i18n';
import { useDateFormat } from '@/composables/useDateFormat';

const { t } = useI18n();
const { formatRelativeTime } = useDateFormat();

defineProps({
    reply: Object,
    canDelete: Boolean,
});

const emit = defineEmits(['delete']);
</script>

<template>
    <div class="flex items-start gap-3">
        <img
            :src="reply.user.profile_photo_url"
            :alt="reply.user.name"
            class="w-8 h-8 rounded-full"
        />
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
                <span class="font-medium text-gray-900">{{ reply.user.name }}</span>
                <span class="text-sm text-gray-500">
                    {{ formatRelativeTime(reply.created_at) }}
                </span>
            </div>
            <p class="text-gray-700 text-sm">{{ reply.content }}</p>

            <button
                v-if="canDelete"
                @click="emit('delete', reply.id)"
                class="text-sm text-red-600 hover:text-red-800 mt-1"
            >
                {{ t('common.delete') }}
            </button>
        </div>
    </div>
</template>
