<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from '@/Components/Common/PrimaryButton.vue';
import CommentForm from './CommentForm.vue';
import CommentItem from './CommentItem.vue';

const { t } = useI18n();
const page = usePage();

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

const isAuthenticated = computed(() => !!page.props.auth.user);
const currentUserId = computed(() => page.props.auth.user?.id);

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
</script>

<template>
    <div class="space-y-6">
        <h3 class="text-xl font-semibold text-gray-900">
            {{ t('profile.comments_count', { count: comments?.length || 0 }) }}
        </h3>

        <CommentForm
            :form="form"
            :is-authenticated="isAuthenticated"
            @submit="submitComment"
            @cancel="cancelReply"
        />

        <div class="space-y-6">
            <CommentItem
                v-for="comment in comments"
                :key="comment.id"
                :comment="comment"
                :user-vote="getUserVote(comment.id)"
                :is-authenticated="isAuthenticated"
                :current-user-id="currentUserId"
                :is-replying-to="replyingTo === comment.id"
                :reply-form="form"
                @vote="vote"
                @start-reply="startReply"
                @submit-reply="submitComment"
                @cancel-reply="cancelReply"
                @delete="confirmDeleteComment"
            />
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
