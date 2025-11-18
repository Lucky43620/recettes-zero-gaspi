<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const { t } = useI18n();
const showMobileMenu = ref(false);

const navigation = computed(() => [
    { name: t('admin.dashboard'), href: '/admin/dashboard', icon: '📊' },
    { name: t('admin.users'), href: '/admin/users', icon: '👥' },
    { name: t('admin.reports'), href: '/admin/reports', icon: '🚨' },
    { name: t('admin.badges'), href: '/admin/badges', icon: '🏆' },
    { name: t('admin.settings'), href: '/admin/settings', icon: '⚙️' },
]);

const isActive = (href) => {
    return page.url.startsWith(href);
};

const closeMobileMenu = () => {
    showMobileMenu.value = false;
};
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-gray-50">
            <button
                @click="showMobileMenu = true"
                class="lg:hidden fixed top-20 left-4 z-40 p-3 bg-white rounded-lg shadow-lg text-gray-600 hover:text-gray-900"
                aria-label="Ouvrir le menu admin"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div
                v-if="showMobileMenu"
                class="lg:hidden fixed inset-0 z-50 bg-black bg-opacity-50"
                @click="closeMobileMenu"
            ></div>

            <div class="flex">
                <aside class="hidden lg:block w-64 bg-white shadow-sm min-h-screen sticky top-0">
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

                <aside
                    :class="[
                        'lg:hidden fixed top-0 left-0 z-50 w-64 bg-white shadow-lg min-h-screen transition-transform duration-300',
                        showMobileMenu ? 'translate-x-0' : '-translate-x-full'
                    ]"
                >
                    <div class="p-6 border-b flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900">{{ t('admin.title') }}</h2>
                        <button
                            @click="closeMobileMenu"
                            class="p-2 text-gray-400 hover:text-gray-600"
                            aria-label="Fermer le menu"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <nav class="p-4 space-y-2">
                        <Link
                            v-for="item in navigation"
                            :key="item.name"
                            :href="item.href"
                            @click="closeMobileMenu"
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
                        <Link
                            href="/"
                            @click="closeMobileMenu"
                            class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition"
                        >
                            <span class="text-xl">⬅️</span>
                            <span>{{ t('admin.back_to_site') }}</span>
                        </Link>
                    </div>
                </aside>

                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    <slot />
                </main>
            </div>
        </div>
    </AppLayout>
</template>
