<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from '@/Components/Common/PrimaryButton.vue';
import StatCard from '@/Components/Admin/StatCard.vue';
import { useDateFormat } from '@/composables/useDateFormat';

const props = defineProps({
    user: Object,
    stats: Object,
});

const confirmingDeletion = ref(false);
const deleteForm = useForm({});

const confirmDelete = () => {
    confirmingDeletion.value = true;
};

const deleteUser = () => {
    deleteForm.delete(route('admin.users.destroy', props.user.id), {
        onSuccess: () => {
            confirmingDeletion.value = false;
        },
    });
};

const { formatDate, formatRelativeTime } = useDateFormat();
</script>

<template>
    <Head :title="`Administration - ${user.name}`" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <Link href="/admin/users" class="text-gray-600 hover:text-gray-900">
                        ← Retour
                    </Link>
                    <img :src="user.profile_photo_url" :alt="user.name" class="w-16 h-16 rounded-full" />
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ user.name }}</h1>
                        <p class="text-gray-600">{{ user.email }}</p>
                    </div>
                </div>
                <button
                    @click="confirmDelete"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                >
                    Supprimer
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <StatCard
                    title="Recettes"
                    :value="stats.recipes_count"
                    icon="📖"
                    color="blue"
                    :subtitle="`${stats.public_recipes} publiques`"
                />
                <StatCard
                    title="Commentaires"
                    :value="stats.comments_count"
                    icon="💬"
                    color="purple"
                />
                <StatCard
                    title="Notes données"
                    :value="stats.ratings_count"
                    icon="⭐"
                    color="orange"
                />
                <StatCard
                    title="Followers"
                    :value="stats.followers_count"
                    icon="👥"
                    color="green"
                    :subtitle="`Suit ${stats.following_count} personnes`"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <StatCard
                    title="Collections"
                    :value="stats.collections_count"
                    icon="📚"
                    color="blue"
                />
                <StatCard
                    title="Inscrit le"
                    :value="formatDate(user.created_at)"
                    icon="📅"
                    color="green"
                />
                <StatCard
                    title="Dernière activité"
                    :value="formatRelativeTime(user.updated_at)"
                    icon="⚡"
                    color="purple"
                />
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-semibold text-gray-900">Recettes récentes</h2>
                </div>
                <div v-if="user.recipes && user.recipes.length > 0" class="divide-y">
                    <div
                        v-for="recipe in user.recipes.slice(0, 5)"
                        :key="recipe.id"
                        class="p-4 hover:bg-gray-50"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <Link :href="`/recipes/${recipe.slug}`" class="font-medium text-gray-900 hover:text-green-600">
                                    {{ recipe.title }}
                                </Link>
                                <div class="flex items-center gap-4 mt-1 text-sm text-gray-500">
                                    <span>{{ recipe.is_public ? '🌐 Public' : '🔒 Privé' }}</span>
                                    <span>{{ formatRelativeTime(recipe.created_at) }}</span>
                                </div>
                            </div>
                            <div v-if="recipe.rating_avg" class="flex items-center gap-1">
                                <span class="text-yellow-500">⭐</span>
                                <span class="font-medium">{{ recipe.rating_avg }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="p-8 text-center text-gray-500">
                    Aucune recette
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-semibold text-gray-900">Commentaires récents</h2>
                </div>
                <div v-if="user.comments && user.comments.length > 0" class="divide-y">
                    <div
                        v-for="comment in user.comments.slice(0, 5)"
                        :key="comment.id"
                        class="p-4 hover:bg-gray-50"
                    >
                        <p class="text-gray-900">{{ comment.content }}</p>
                        <div class="mt-2 flex items-center gap-4 text-sm text-gray-500">
                            <Link v-if="comment.recipe" :href="`/recipes/${comment.recipe.slug}`" class="hover:text-green-600">
                                Sur: {{ comment.recipe.title }}
                            </Link>
                            <span>{{ formatRelativeTime(comment.created_at) }}</span>
                        </div>
                    </div>
                </div>
                <div v-else class="p-8 text-center text-gray-500">
                    Aucun commentaire
                </div>
            </div>
        </div>

        <ConfirmationModal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <template #title>
                Supprimer l'utilisateur
            </template>

            <template #content>
                Êtes-vous sûr de vouloir supprimer cet utilisateur ? Toutes ses recettes et données seront supprimées définitivement.
            </template>

            <template #footer>
                <PrimaryButton variant="secondary" @click="confirmingDeletion = false">
                    Annuler
                </PrimaryButton>

                <PrimaryButton
                    variant="danger"
                    class="ms-3"
                    @click="deleteUser"
                    :loading="deleteForm.processing"
                >
                    Supprimer
                </PrimaryButton>
            </template>
        </ConfirmationModal>
    </AdminLayout>
</template>
