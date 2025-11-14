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
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ t('admin.badges') }}</h1>
                    <p class="mt-2 text-sm md:text-base text-gray-600">{{ t('admin.manage_badges_description') }}</p>
                </div>
                <button
                    @click="openCreateModal"
                    class="w-full sm:w-auto px-5 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 active:bg-green-800 transition font-medium"
                >
                    {{ t('admin.new_badge') }}
                </button>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="hidden md:block overflow-x-auto">
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

                <div class="md:hidden divide-y divide-gray-200">
                    <div v-for="badge in badges.data" :key="badge.id" class="p-4 hover:bg-gray-50">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center flex-1 min-w-0">
                                <span class="text-3xl mr-3 flex-shrink-0">{{ badge.icon || '🏆' }}</span>
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-medium text-gray-900">{{ badge.name }}</div>
                                    <code class="text-xs bg-gray-100 px-2 py-0.5 rounded inline-block mt-1">{{ badge.key }}</code>
                                </div>
                            </div>
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium ml-2 whitespace-nowrap">
                                {{ badge.users_count || 0 }} {{ t('admin.users_column') }}
                            </span>
                        </div>

                        <div class="space-y-2 mb-3">
                            <div class="text-sm text-gray-700">{{ badge.description }}</div>
                            <div class="flex items-center justify-between text-sm pt-2 border-t">
                                <span class="text-gray-500">{{ t('admin.required_column') }}:</span>
                                <span class="text-gray-900 font-medium">{{ badge.required_count }}</span>
                            </div>
                        </div>

                        <div class="flex gap-2 pt-3 border-t">
                            <button
                                @click="openEditModal(badge)"
                                class="flex-1 px-4 py-3 text-sm text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 active:bg-blue-200 transition font-medium"
                            >
                                {{ t('common.edit') }}
                            </button>
                            <button
                                @click="deleteBadge(badge)"
                                class="flex-1 px-4 py-3 text-sm text-red-600 bg-red-50 rounded-lg hover:bg-red-100 active:bg-red-200 transition font-medium"
                            >
                                {{ t('common.delete') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="badges.links" class="px-4 md:px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="text-xs sm:text-sm text-gray-500">
                        {{ badges.from }} - {{ badges.to }} {{ t('admin.pagination_of') }} {{ badges.total }} {{ t('admin.badges_count') }}
                    </div>
                    <div class="flex flex-wrap gap-1 sm:gap-2 justify-center">
                        <Link
                            v-for="(link, index) in badges.links"
                            :key="index"
                            :href="link.url"
                            :class="[
                                'px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm transition',
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
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            @click.self="showCreateModal = false"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-4 md:p-6">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-4 md:mb-6">
                        {{ editingBadge ? t('admin.edit_badge') : t('admin.new_badge') }}
                    </h2>

                    <form @submit.prevent="submit" class="space-y-4 md:space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('admin.unique_key') }}</label>
                            <input
                                v-model="form.key"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-base"
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-base"
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-base"
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-base"
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-base"
                                required
                            />
                            <p v-if="form.errors.required_count" class="mt-1 text-sm text-red-600">{{ form.errors.required_count }}</p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 justify-end pt-2">
                            <button
                                type="button"
                                @click="showCreateModal = false"
                                class="w-full sm:w-auto px-5 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 active:bg-gray-300 transition font-medium"
                                :disabled="form.processing"
                            >
                                {{ t('common.cancel') }}
                            </button>
                            <button
                                type="submit"
                                class="w-full sm:w-auto px-5 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 active:bg-green-800 transition disabled:opacity-50 font-medium"
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
