<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    profileUser: Object,
    following: Object,
});
</script>

<template>
    <PublicLayout :title="`Abonnements de ${profileUser.name}`">
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link
                        :href="`/profile/${profileUser.id}`"
                        class="text-green-600 hover:text-green-800"
                    >
                        ← Retour au profil
                    </Link>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-8">
                    Abonnements de {{ profileUser.name }}
                </h1>

                <div v-if="following.data.length === 0" class="text-center py-12">
                    <p class="text-gray-600">Aucun abonnement pour le moment</p>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link
                        v-for="user in following.data"
                        :key="user.id"
                        :href="`/profile/${user.id}`"
                        class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition"
                    >
                        <div class="flex items-center gap-4">
                            <img
                                :src="user.profile_photo_url"
                                :alt="user.name"
                                class="w-16 h-16 rounded-full"
                            />
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ user.name }}</h3>
                                <p class="text-sm text-gray-600">
                                    {{ user.recipes_count }} recette{{ user.recipes_count > 1 ? 's' : '' }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    {{ user.followers_count }} abonné{{ user.followers_count > 1 ? 's' : '' }}
                                </p>
                            </div>
                        </div>
                    </Link>
                </div>

                <div v-if="following.links && following.links.length > 3" class="mt-8 flex justify-center gap-2">
                    <component
                        v-for="(link, index) in following.links"
                        :key="index"
                        :is="link.url ? Link : 'span'"
                        :href="link.url"
                        v-html="link.label"
                        :class="[
                            'px-3 py-2 border rounded',
                            link.active
                                ? 'bg-green-600 text-white border-green-600'
                                : link.url
                                    ? 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                    : 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                        ]"
                    />
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
