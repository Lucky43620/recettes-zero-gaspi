<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps({
    comment: Object,
    userVote: {
        type: Number,
        default: 0,
    },
    isAuthenticated: Boolean,
});

const emit = defineEmits(['vote']);
</script>

<template>
    <div v-if="isAuthenticated" class="flex items-center gap-2">
        <button
            @click="emit('vote', 'up')"
            :class="[
                'hover:text-green-600 transition',
                userVote === 1 ? 'text-green-600' : 'text-gray-600'
            ]"
        >
            ▲
        </button>
        <span class="font-medium" :class="comment.score > 0 ? 'text-green-600' : comment.score < 0 ? 'text-red-600' : 'text-gray-600'">
            {{ comment.score }}
        </span>
        <button
            @click="emit('vote', 'down')"
            :class="[
                'hover:text-red-600 transition',
                userVote === -1 ? 'text-red-600' : 'text-gray-600'
            ]"
        >
            ▼
        </button>
    </div>
    <span v-else class="font-medium text-gray-600">
        {{ comment.score }} {{ t('profile.points') }}
    </span>
</template>
