<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useDateFormat } from '@/composables/useDateFormat';

const props = defineProps({
    users: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

const searchUsers = () => {
    router.get('/admin/users', { search: search.value }, {
        preserveState: true,
        replace: true,
    });
};

const { formatRelativeTime } = useDateFormat();
</script>

<template>
    <Head title="Administration - Utilisateurs" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Utilisateurs</h1>
                    <p class="mt-2 text-gray-600">Gérer les utilisateurs de la plateforme</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <div class="flex items-center gap-4">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Rechercher par nom ou email..."
                            class="flex-1 border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                            @keyup.enter="searchUsers"
                        />
                        <button
                            @click="searchUsers"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
                        >
                            Rechercher
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recettes</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commentaires</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Followers</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inscrit</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img :src="user.profile_photo_url" :alt="user.name" class="w-10 h-10 rounded-full" />
                                        <span class="ml-3 text-sm font-medium text-gray-900">{{ user.name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ user.email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ user.recipes_count || 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ user.comments_count || 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ user.followers_count || 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatRelativeTime(user.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <Link :href="`/admin/users/${user.id}`" class="text-green-600 hover:text-green-700 font-medium">
                                        Voir détails
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="users.links" class="px-6 py-4 border-t flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        {{ users.from }} - {{ users.to }} sur {{ users.total }} utilisateurs
                    </div>
                    <div class="flex gap-2">
                        <Link
                            v-for="link in users.links"
                            :key="link.label"
                            :href="link.url"
                            :class="[
                                'px-3 py-2 rounded-lg text-sm transition',
                                link.active
                                    ? 'bg-green-600 text-white'
                                    : link.url
                                    ? 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                    : 'bg-gray-50 text-gray-400 cursor-not-allowed'
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>