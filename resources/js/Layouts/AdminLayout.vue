<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const { t } = useI18n();

const navigation = computed(() => [
    { name: t('admin.dashboard'), href: '/admin/dashboard', icon: '📊' },
    { name: t('admin.users'), href: '/admin/users', icon: '👥' },
    { name: t('admin.reports'), href: '/admin/reports', icon: '🚨' },
    { name: t('admin.badges'), href: '/admin/badges', icon: '🏆' },
]);

const isActive = (href) => {
    return page.url.startsWith(href);
};
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-gray-50">
            <div class="flex">
                <aside class="w-64 bg-white shadow-sm min-h-screen sticky top-0">
                    <div class="p-6 border-b">
                        <h2 class="text-xl font-bold text-gray-900">{{ t('admin.title') }}</h2>
                    </div>

                    <nav class="p-4 space-y-2">
                        <Link
                            v-for="item in navigation"
                            :key="item.name"
                            :href="item.href"
                            :class="[
                                'flex items-center gap-3 px-4 py-3 rounded-lg transition',
                                isActive(item.href)
                                    ? 'bg-green-50 text-green-700 font-medium'
                                    : 'text-gray-600 hover:bg-gray-50'
                            ]"
                        >
                            <span class="text-xl">{{ item.icon }}</span>
                            <span>{{ item.name }}</span>
                        </Link>
                    </nav>

                    <div class="p-4 mt-auto border-t">
                        <Link href="/" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition">
                            <span class="text-xl">⬅️</span>
                            <span>{{ t('admin.back_to_site') }}</span>
                        </Link>
                    </div>
                </aside>

                <main class="flex-1 p-8">
                    <slot />
                </main>
            </div>
        </div>
    </AppLayout>
</template>
