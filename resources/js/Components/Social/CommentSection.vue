<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

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
</script>

<template>
    <div class="space-y-6">
        <h3 class="text-xl font-semibold text-gray-900">
            Commentaires ({{ comments?.length || 0 }})
        </h3>

        <div v-if="!$page.props.auth.user" class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
            <p class="text-gray-700 mb-4">Connectez-vous pour laisser un commentaire</p>
            <Link
                :href="route('login')"
                class="inline-block px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
            >
                Se connecter
            </Link>
        </div>

        <form v-else @submit.prevent="submitComment" class="space-y-4">
            <div>
                <textarea
                    v-model="form.content"
                    rows="3"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"
                    placeholder="Partagez votre expérience ou posez une question..."
                    required
                ></textarea>
            </div>
            <div class="flex gap-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
                >
                    Publier
                </button>
                <button
                    v-if="replyingTo"
                    type="button"
                    @click="cancelReply"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                >
                    Annuler
                </button>
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
                                {{ new Date(comment.created_at).toLocaleDateString('fr-FR') }}
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
                                {{ comment.score }} points
                            </span>
                            <button
                                v-if="$page.props.auth.user"
                                @click="startReply(comment)"
                                class="text-green-600 hover:text-green-800"
                            >
                                Répondre
                            </button>
                            <button
                                v-if="$page.props.auth.user?.id === comment.user_id"
                                @click="confirmDeleteComment(comment.id)"
                                class="text-red-600 hover:text-red-800"
                            >
                                Supprimer
                            </button>
                        </div>

                        <div v-if="replyingTo === comment.id" class="mt-4">
                            <form @submit.prevent="submitComment" class="space-y-2">
                                <textarea
                                    v-model="form.content"
                                    rows="2"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"
                                    placeholder="Votre réponse..."
                                    required
                                ></textarea>
                                <div class="flex gap-2">
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="px-3 py-1 bg-green-600 text-white text-sm rounded-md hover:bg-green-700"
                                    >
                                        Répondre
                                    </button>
                                    <button
                                        type="button"
                                        @click="cancelReply"
                                        class="px-3 py-1 bg-gray-200 text-gray-700 text-sm rounded-md hover:bg-gray-300"
                                    >
                                        Annuler
                                    </button>
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
                                            {{ new Date(reply.created_at).toLocaleDateString('fr-FR') }}
                                        </span>
                                    </div>
                                    <p class="text-gray-700 text-sm">{{ reply.content }}</p>

                                    <button
                                        v-if="$page.props.auth.user?.id === reply.user_id"
                                        @click="confirmDeleteComment(reply.id)"
                                        class="text-sm text-red-600 hover:text-red-800 mt-1"
                                    >
                                        Supprimer
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
                Supprimer le commentaire
            </template>

            <template #content>
                Êtes-vous sûr de vouloir supprimer ce commentaire ? Cette action est irréversible.
            </template>

            <template #footer>
                <SecondaryButton @click="confirmingDeletion = false">
                    Annuler
                </SecondaryButton>

                <DangerButton
                    class="ms-3"
                    @click="deleteComment"
                >
                    Supprimer
                </DangerButton>
            </template>
        </ConfirmationModal>
    </div>
</template>
