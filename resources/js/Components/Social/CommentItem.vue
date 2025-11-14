<script setup>
import { useI18n } from 'vue-i18n';
import { useDateFormat } from '@/composables/useDateFormat';
import CommentVoting from './CommentVoting.vue';
import CommentReply from './CommentReply.vue';
import CommentForm from './CommentForm.vue';

const { t } = useI18n();
const { formatRelativeTime } = useDateFormat();

defineProps({
    comment: Object,
    userVote: Number,
    isAuthenticated: Boolean,
    currentUserId: Number,
    isReplyingTo: Boolean,
    replyForm: Object,
});

const emit = defineEmits(['vote', 'startReply', 'submitReply', 'cancelReply', 'delete']);
</script>

<template>
    <div class="border-l-2 border-gray-200 pl-4">
        <div class="flex items-start gap-3">
            <img
                :src="comment.user.profile_photo_url"
                :alt="comment.user.name"
                class="w-10 h-10 rounded-full"
            />
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-medium text-gray-900">{{ comment.user.name }}</span>
                    <span class="text-sm text-gray-500">
                        {{ formatRelativeTime(comment.created_at) }}
                    </span>
                </div>
                <p class="text-gray-700 mb-2">{{ comment.content }}</p>

                <div class="flex items-center gap-4 text-sm">
                    <CommentVoting
                        :comment="comment"
                        :user-vote="userVote"
                        :is-authenticated="isAuthenticated"
                        @vote="(type) => emit('vote', comment.id, type)"
                    />
                    <button
                        v-if="isAuthenticated"
                        @click="emit('startReply', comment)"
                        class="text-green-600 hover:text-green-800"
                    >
                        {{ t('profile.reply') }}
                    </button>
                    <button
                        v-if="currentUserId === comment.user_id"
                        @click="emit('delete', comment.id)"
                        class="text-red-600 hover:text-red-800"
                    >
                        {{ t('common.delete') }}
                    </button>
                </div>

                <div v-if="isReplyingTo" class="mt-4">
                    <CommentForm
                        :form="replyForm"
                        :is-authenticated="isAuthenticated"
                        :is-reply="true"
                        @submit="emit('submitReply')"
                        @cancel="emit('cancelReply')"
                    />
                </div>

                <div v-if="comment.replies?.length" class="mt-4 space-y-4">
                    <CommentReply
                        v-for="reply in comment.replies"
                        :key="reply.id"
                        :reply="reply"
                        :can-delete="currentUserId === reply.user_id"
                        @delete="(id) => emit('delete', id)"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
