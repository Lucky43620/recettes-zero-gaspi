<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const { t } = useI18n();

const props = defineProps({
    badges: Object,
});

const showCreateModal = ref(false);
const editingBadge = ref(null);

const form = useForm({
    key: '',
    name: '',
    description: '',
    icon: '',
    required_count: 1,
});

const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    editingBadge.value = null;
    showCreateModal.value = true;
};

const openEditModal = (badge) => {
    form.key = badge.key;
    form.name = badge.name;
    form.description = badge.description;
    form.icon = badge.icon || '';
    form.required_count = badge.required_count;
    editingBadge.value = badge;
    showCreateModal.value = true;
};

const submit = () => {
    if (editingBadge.value) {
        form.put(route('admin.badges.update', editingBadge.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                showCreateModal.value = false;
                form.reset();
            },
        });
    } else {
        form.post(route('admin.badges.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showCreateModal.value = false;
                form.reset();
            },
        });
    }
};

const deleteBadge = (badge) => {
    if (confirm(t('admin.delete_badge_confirm', { name: badge.name }))) {
        form.delete(route('admin.badges.destroy', badge.id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head :title="t('admin.badges_title')" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ t('admin.badges') }}</h1>
                    <p class="mt-2 text-gray-600">{{ t('admin.manage_badges_description') }}</p>
                </div>
                <button
                    @click="openCreateModal"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
                >
                    {{ t('admin.new_badge') }}
                </button>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('admin.badge_column') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('admin.key_column') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('admin.description_column') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('admin.required_column') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('admin.users_column') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ t('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="badge in badges.data" :key="badge.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <span class="text-2xl mr-3">{{ badge.icon || '🏆' }}</span>
                                        <span class="text-sm font-medium text-gray-900">{{ badge.name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <code class="px-2 py-1 bg-gray-100 rounded text-xs">{{ badge.key }}</code>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                    {{ badge.description }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ badge.required_count }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                        {{ badge.users_count || 0 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                                    <button
                                        @click="openEditModal(badge)"
                                        class="text-blue-600 hover:text-blue-700"
                                    >
                                        {{ t('common.edit') }}
                                    </button>
                                    <button
                                        @click="deleteBadge(badge)"
                                        class="text-red-600 hover:text-red-700"
                                    >
                                        {{ t('common.delete') }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="badges.links" class="px-6 py-4 border-t flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        {{ badges.from }} - {{ badges.to }} {{ t('admin.pagination_of') }} {{ badges.total }} {{ t('admin.badges_count') }}
                    </div>
                    <div class="flex gap-2">
                        <Link
                            v-for="(link, index) in badges.links"
                            :key="index"
                            :href="link.url"
                            :class="[
                                'px-3 py-2 rounded-lg text-sm transition',
                                link.active
                                    ? 'bg-green-600 text-white'
                                    : link.url
                                    ? 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                    : 'bg-gray-50 text-gray-400 cursor-not-allowed'
                            ]"
                        >
                            <span v-if="link.label === 'pagination.previous'">{{ t('common.previous') }}</span>
                            <span v-else-if="link.label === 'pagination.next'">{{ t('common.next') }}</span>
                            <span v-else v-html="link.label"></span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="showCreateModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @click.self="showCreateModal = false"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">
                        {{ editingBadge ? t('admin.edit_badge') : t('admin.new_badge') }}
                    </h2>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('admin.unique_key') }}</label>
                            <input
                                v-model="form.key"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                :placeholder="t('admin.unique_key_placeholder')"
                                required
                            />
                            <p v-if="form.errors.key" class="mt-1 text-sm text-red-600">{{ form.errors.key }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('admin.badge_name') }}</label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                :placeholder="t('admin.name_placeholder')"
                                required
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('admin.badge_description') }}</label>
                            <textarea
                                v-model="form.description"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                :placeholder="t('admin.description_placeholder')"
                                required
                            ></textarea>
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('admin.icon_emoji') }}</label>
                            <input
                                v-model="form.icon"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                :placeholder="t('admin.icon_emoji_placeholder')"
                            />
                            <p v-if="form.errors.icon" class="mt-1 text-sm text-red-600">{{ form.errors.icon }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('admin.required_count') }}</label>
                            <input
                                v-model.number="form.required_count"
                                type="number"
                                min="1"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                required
                            />
                            <p v-if="form.errors.required_count" class="mt-1 text-sm text-red-600">{{ form.errors.required_count }}</p>
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button
                                type="button"
                                @click="showCreateModal = false"
                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition"
                                :disabled="form.processing"
                            >
                                {{ t('common.cancel') }}
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition disabled:opacity-50"
                                :disabled="form.processing"
                            >
                                {{ editingBadge ? t('common.update') : t('common.create') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
