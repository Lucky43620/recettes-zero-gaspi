<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from '@/Components/Common/PrimaryButton.vue';
import { useDateFormat } from '@/composables/useDateFormat';

const { t } = useI18n();

const props = defineProps({
    recipe: Object,
    comments: Array,
    commentVotes: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    content: '',
    parent_id: null,
});

const replyingTo = ref(null);
const confirmingDeletion = ref(false);
const commentToDelete = ref(null);

function submitComment() {
    form.post(route('comments.store', props.recipe.slug), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            replyingTo.value = null;
        },
    });
}

function startReply(comment) {
    replyingTo.value = comment.id;
    form.parent_id = comment.id;
}

function cancelReply() {
    replyingTo.value = null;
    form.parent_id = null;
    form.content = '';
}

function confirmDeleteComment(commentId) {
    commentToDelete.value = commentId;
    confirmingDeletion.value = true;
}

function deleteComment() {
    useForm({}).delete(route('comments.destroy', commentToDelete.value), {
        preserveScroll: true,
        onSuccess: () => {
            confirmingDeletion.value = false;
            commentToDelete.value = null;
        },
    });
}

function vote(commentId, type) {
    useForm({}).post(route('comments.vote', { comment: commentId, type }), {
        preserveScroll: true,
    });
}

function getUserVote(commentId) {
    return props.commentVotes[commentId] || 0;
}

const { formatRelativeTime } = useDateFormat();
</script>

<template>
    <div class="space-y-6">
        <h3 class="text-xl font-semibold text-gray-900">
            {{ t('profile.comments_count', { count: comments?.length || 0 }) }}
        </h3>

        <div v-if="!$page.props.auth.user" class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
            <p class="text-gray-700 mb-4">{{ t('profile.login_to_comment') }}</p>
            <Link :href="route('login')">
                <PrimaryButton>
                    {{ t('auth.login') }}
                </PrimaryButton>
            </Link>
        </div>

        <form v-else @submit.prevent="submitComment" class="space-y-4">
            <div>
                <textarea
                    v-model="form.content"
                    rows="3"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"
                    :placeholder="t('profile.share_experience')"
                    required
                ></textarea>
            </div>
            <div class="flex gap-2">
                <PrimaryButton type="submit" :loading="form.processing">
                    {{ t('profile.publish') }}
                </PrimaryButton>
                <PrimaryButton
                    v-if="replyingTo"
                    type="button"
                    @click="cancelReply"
                    variant="secondary"
                >
                    {{ t('common.cancel') }}
                </PrimaryButton>
            </div>
        </form>

        <div class="space-y-6">
            <div
                v-for="comment in comments"
                :key="comment.id"
                class="border-l-2 border-gray-200 pl-4"
            >
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
                            <div v-if="$page.props.auth.user" class="flex items-center gap-2">
                                <button
                                    @click="vote(comment.id, 'up')"
                                    :class="[
                                        'hover:text-green-600 transition',
                                        getUserVote(comment.id) === 1 ? 'text-green-600' : 'text-gray-600'
                                    ]"
                                >
                                    ▲
                                </button>
                                <span class="font-medium" :class="comment.score > 0 ? 'text-green-600' : comment.score < 0 ? 'text-red-600' : 'text-gray-600'">
                                    {{ comment.score }}
                                </span>
                                <button
                                    @click="vote(comment.id, 'down')"
                                    :class="[
                                        'hover:text-red-600 transition',
                                        getUserVote(comment.id) === -1 ? 'text-red-600' : 'text-gray-600'
                                    ]"
                                >
                                    ▼
                                </button>
                            </div>
                            <span v-else class="font-medium text-gray-600">
                                {{ comment.score }} {{ t('profile.points') }}
                            </span>
                            <button
                                v-if="$page.props.auth.user"
                                @click="startReply(comment)"
                                class="text-green-600 hover:text-green-800"
                            >
                                {{ t('profile.reply') }}
                            </button>
                            <button
                                v-if="$page.props.auth.user?.id === comment.user_id"
                                @click="confirmDeleteComment(comment.id)"
                                class="text-red-600 hover:text-red-800"
                            >
                                {{ t('common.delete') }}
                            </button>
                        </div>

                        <div v-if="replyingTo === comment.id" class="mt-4">
                            <form @submit.prevent="submitComment" class="space-y-2">
                                <textarea
                                    v-model="form.content"
                                    rows="2"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"
                                    :placeholder="t('profile.your_reply')"
                                    required
                                ></textarea>
                                <div class="flex gap-2">
                                    <PrimaryButton type="submit" :loading="form.processing" size="sm">
                                        {{ t('profile.reply') }}
                                    </PrimaryButton>
                                    <PrimaryButton
                                        type="button"
                                        @click="cancelReply"
                                        variant="secondary"
                                        size="sm"
                                    >
                                        {{ t('common.cancel') }}
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>

                        <div v-if="comment.replies?.length" class="mt-4 space-y-4">
                            <div
                                v-for="reply in comment.replies"
                                :key="reply.id"
                                class="flex items-start gap-3"
                            >
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
                                        v-if="$page.props.auth.user?.id === reply.user_id"
                                        @click="confirmDeleteComment(reply.id)"
                                        class="text-sm text-red-600 hover:text-red-800 mt-1"
                                    >
                                        {{ t('common.delete') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmationModal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <template #title>
                {{ t('profile.delete_comment') }}
            </template>

            <template #content>
                {{ t('profile.delete_comment_confirmation') }}
            </template>

            <template #footer>
                <PrimaryButton variant="secondary" @click="confirmingDeletion = false">
                    {{ t('common.cancel') }}
                </PrimaryButton>

                <PrimaryButton
                    variant="danger"
                    class="ms-3"
                    @click="deleteComment"
                >
                    {{ t('common.delete') }}
                </PrimaryButton>
            </template>
        </ConfirmationModal>
    </div>
</template>
