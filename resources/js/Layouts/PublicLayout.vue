<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LanguageSwitcher from '@/Components/Common/LanguageSwitcher.vue';

defineProps({
    title: String,
});

const { t } = useI18n();
const showMobileMenu = ref(false);

const closeMobileMenu = () => {
    showMobileMenu.value = false;
};
</script>

<template>
    <div>
        <Head :title="title" />

        <div class="min-h-screen bg-gray-50 flex flex-col">
            <nav class="bg-white shadow-sm">
                <div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <div class="flex items-center">
                            <Link :href="route('home')" class="flex items-center">
                                <ApplicationLogo size="sm" />
                            </Link>
                        </div>

                        <div class="hidden md:flex items-center gap-6">
                            <Link
                                :href="route('home')"
                                :class="[
                                    'text-gray-700 hover:text-gray-900 transition',
                                    route().current('home') ? 'font-semibold text-gray-900' : ''
                                ]"
                            >
                                {{ t('nav.home') }}
                            </Link>
                            <Link
                                :href="route('recipes.index')"
                                :class="[
                                    'text-gray-700 hover:text-gray-900 transition',
                                    route().current('recipes.*') ? 'font-semibold text-gray-900' : ''
                                ]"
                            >
                                {{ t('nav.recipes') }}
                            </Link>
                            <Link
                                :href="route('products.index')"
                                :class="[
                                    'text-gray-700 hover:text-gray-900 transition',
                                    route().current('products.*') ? 'font-semibold text-gray-900' : ''
                                ]"
                            >
                                {{ t('nav.products') }}
                            </Link>

                            <div class="flex items-center gap-4">
                                <LanguageSwitcher />

                                <template v-if="$page.props.auth.user">
                                    <Link
                                        :href="route('dashboard')"
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition"
                                    >
                                        {{ t('nav.dashboard') }}
                                    </Link>
                                </template>
                                <template v-else>
                                    <Link
                                        :href="route('login')"
                                        class="text-gray-700 hover:text-gray-900 transition"
                                    >
                                        {{ t('auth.login') }}
                                    </Link>
                                    <Link
                                        :href="route('register')"
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition"
                                    >
                                        {{ t('auth.register') }}
                                    </Link>
                                </template>
                            </div>
                        </div>

                        <div class="md:hidden flex items-center gap-3">
                            <LanguageSwitcher />
                            <button
                                @click="showMobileMenu = true"
                                class="p-2 text-gray-600 hover:text-gray-900"
                                aria-label="Ouvrir le menu"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </nav>

            <div
                v-if="showMobileMenu"
                class="md:hidden fixed inset-0 z-50 bg-black bg-opacity-50"
                @click="closeMobileMenu"
            ></div>

            <div
                :class="[
                    'md:hidden fixed top-0 right-0 z-50 w-64 bg-white shadow-lg h-full transition-transform duration-300',
                    showMobileMenu ? 'translate-x-0' : 'translate-x-full'
                ]"
            >
                <div class="p-6 border-b flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">{{ t('common.menu') }}</h2>
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
                        :href="route('home')"
                        @click="closeMobileMenu"
                        :class="[
                            'block px-4 py-3 rounded-lg transition',
                            route().current('home')
                                ? 'bg-green-50 text-green-700 font-medium'
                                : 'text-gray-700 hover:bg-gray-50'
                        ]"
                    >
                        {{ t('nav.home') }}
                    </Link>
                    <Link
                        :href="route('recipes.index')"
                        @click="closeMobileMenu"
                        :class="[
                            'block px-4 py-3 rounded-lg transition',
                            route().current('recipes.*')
                                ? 'bg-green-50 text-green-700 font-medium'
                                : 'text-gray-700 hover:bg-gray-50'
                        ]"
                    >
                        {{ t('nav.recipes') }}
                    </Link>
                    <Link
                        :href="route('products.index')"
                        @click="closeMobileMenu"
                        :class="[
                            'block px-4 py-3 rounded-lg transition',
                            route().current('products.*')
                                ? 'bg-green-50 text-green-700 font-medium'
                                : 'text-gray-700 hover:bg-gray-50'
                        ]"
                    >
                        {{ t('nav.products') }}
                    </Link>

                    <div class="border-t pt-4 mt-4 space-y-2">
                        <template v-if="$page.props.auth.user">
                            <Link
                                :href="route('dashboard')"
                                @click="closeMobileMenu"
                                class="block w-full px-4 py-3 bg-green-600 text-white text-center rounded-lg hover:bg-green-700 transition"
                            >
                                {{ t('nav.dashboard') }}
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                @click="closeMobileMenu"
                                class="block px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition"
                            >
                                {{ t('auth.login') }}
                            </Link>
                            <Link
                                :href="route('register')"
                                @click="closeMobileMenu"
                                class="block w-full px-4 py-3 bg-green-600 text-white text-center rounded-lg hover:bg-green-700 transition"
                            >
                                {{ t('auth.register') }}
                            </Link>
                        </template>
                    </div>
                </nav>
            </div>

            <main class="flex-1">
                <slot />
            </main>

            <footer class="bg-white border-t border-gray-200 py-8 mt-12">
                <div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-600">
                    <p>© 2025 {{ t('app.name') }}. {{ t('common.all_rights_reserved') }}</p>
                </div>
            </footer>
        </div>
    </div>
</template>
