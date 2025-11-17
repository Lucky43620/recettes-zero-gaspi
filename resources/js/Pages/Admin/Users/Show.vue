<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import StatCard from '@/Components/Admin/StatCard.vue';
import { useDateFormat } from '@/composables/useDateFormat';

const { t } = useI18n();

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
    <Head :title="`${t('admin.title')} - ${user.name}`" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <Link href="/admin/users" class="text-gray-600 hover:text-gray-900">
                        ← {{ t('common.back') }}
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
                    {{ t('common.delete') }}
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <StatCard
                    :title="t('admin.recipes')"
                    :value="stats.recipes_count"
                    icon="📖"
                    color="blue"
                    :subtitle="t('admin.public_count', { count: stats.public_recipes })"
                />
                <StatCard
                    :title="t('admin.comments')"
                    :value="stats.comments_count"
                    icon="💬"
                    color="purple"
                />
                <StatCard
                    :title="t('admin.ratings_given')"
                    :value="stats.ratings_count"
                    icon="⭐"
                    color="orange"
                />
                <StatCard
                    :title="t('admin.followers_column')"
                    :value="stats.followers_count"
                    icon="👥"
                    color="green"
                    :subtitle="t('admin.follows_count', { count: stats.following_count })"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <StatCard
                    :title="t('admin.collections')"
                    :value="stats.collections_count"
                    icon="📚"
                    color="blue"
                />
                <StatCard
                    :title="t('admin.registered_on_date')"
                    :value="formatDate(user.created_at)"
                    icon="📅"
                    color="green"
                />
                <StatCard
                    :title="t('admin.last_activity')"
                    :value="formatRelativeTime(user.updated_at)"
                    icon="⚡"
                    color="purple"
                />
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-semibold text-gray-900">{{ t('admin.recent_recipes') }}</h2>
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
                                    <span>{{ recipe.is_public ? t('admin.public_badge') : t('admin.private_badge') }}</span>
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
                    {{ t('admin.no_recipes') }}
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-semibold text-gray-900">{{ t('admin.recent_comments') }}</h2>
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
                                {{ t('admin.on_recipe', { title: comment.recipe.title }) }}
                            </Link>
                            <span>{{ formatRelativeTime(comment.created_at) }}</span>
                        </div>
                    </div>
                </div>
                <div v-else class="p-8 text-center text-gray-500">
                    {{ t('admin.no_comments') }}
                </div>
            </div>
        </div>

        <ConfirmationModal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <template #title>
                {{ t('admin.delete_user') }}
            </template>

            <template #content>
                {{ t('admin.delete_user_confirm') }}
            </template>

            <template #footer>
                <PrimaryButton variant="secondary" @click="confirmingDeletion = false">
                    {{ t('common.cancel') }}
                </PrimaryButton>

                <PrimaryButton
                    variant="danger"
                    class="ms-3"
                    @click="deleteUser"
                    :loading="deleteForm.processing"
                >
                    {{ t('common.delete') }}
                </PrimaryButton>
            </template>
        </ConfirmationModal>
    </AdminLayout>
</template>
