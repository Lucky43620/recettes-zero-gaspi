<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import LanguageSwitcher from '@/Components/Common/LanguageSwitcher.vue';

defineProps({
    title: String,
});

const { t } = useI18n();
const showingNavigationDropdown = ref(false);

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('home')">
                                    <ApplicationLogo size="sm" />
                                </Link>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    {{ t('nav.dashboard') }}
                                </NavLink>
                                <NavLink :href="route('feed.index')" :active="route().current('feed.index')">
                                    {{ t('nav.feed') }}
                                </NavLink>
                                <NavLink :href="route('recipes.my')" :active="route().current('recipes.my')">
                                    {{ t('nav.my_recipes') }}
                                </NavLink>
                                <NavLink :href="route('pantry.index')" :active="route().current('pantry.*')">
                                    {{ t('nav.pantry') }}
                                </NavLink>
                                <NavLink :href="route('anti-waste.index')" :active="route().current('anti-waste.*')">
                                    {{ t('nav.anti_waste') }}
                                </NavLink>
                                <NavLink :href="route('meal-plans.index')" :active="route().current('meal-plans.*')">
                                    {{ t('nav.meal_plans') }}
                                </NavLink>
                                <NavLink :href="route('shopping-lists.index')" :active="route().current('shopping-lists.*')">
                                    {{ t('nav.shopping') }}
                                </NavLink>
                                <NavLink :href="route('favorites.index')" :active="route().current('favorites.index')">
                                    {{ t('nav.favorites') }}
                                </NavLink>
                                <NavLink :href="route('collections.index')" :active="route().current('collections.*')">
                                    {{ t('nav.collections') }}
                                </NavLink>
                            </div>
                        </div>

                        <div v-if="$page.props.auth.user" class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                            <!-- Language Switcher -->
                            <LanguageSwitcher />

                            <!-- Settings Dropdown -->
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="size-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                                        </button>

                                        <span v-else-if="$page.props.auth.user" class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.name }}

                                                <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('user.profile')">
                                            {{ t('profile.title') }}
                                        </DropdownLink>

                                        <DropdownLink :href="route('user.public-profile', $page.props.auth.user)">
                                            {{ t('profile.view_public') }}
                                        </DropdownLink>

                                        <template v-if="$page.props.auth.user.is_admin">
                                            <div class="border-t border-gray-200" />

                                            <DropdownLink :href="route('admin.dashboard')">
                                                {{ t('admin.panel') }}
                                            </DropdownLink>
                                        </template>

                                        <div class="border-t border-gray-200" />

                                        <DropdownLink :href="route('subscription.index')">
                                            {{ t('subscription.title') }}
                                        </DropdownLink>

                                        <div class="border-t border-gray-200" />

                                        <form @submit.prevent="logout">
                                            <DropdownLink as="button">
                                                {{ t('auth.logout') }}
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out" @click="showingNavigationDropdown = ! showingNavigationDropdown">
                                <svg
                                    class="size-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            {{ t('nav.dashboard') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('feed.index')" :active="route().current('feed.index')">
                            {{ t('nav.feed') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('recipes.my')" :active="route().current('recipes.my')">
                            {{ t('nav.my_recipes') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('pantry.index')" :active="route().current('pantry.*')">
                            {{ t('nav.pantry') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('anti-waste.index')" :active="route().current('anti-waste.*')">
                            {{ t('nav.anti_waste') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('meal-plans.index')" :active="route().current('meal-plans.*')">
                            {{ t('nav.meal_plans') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('shopping-lists.index')" :active="route().current('shopping-lists.*')">
                            {{ t('nav.shopping') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('favorites.index')" :active="route().current('favorites.index')">
                            {{ t('nav.favorites') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('collections.index')" :active="route().current('collections.*')">
                            {{ t('nav.collections') }}
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4 py-2">
                            <LanguageSwitcher />
                        </div>

                        <div class="flex items-center px-4">
                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 me-3">
                                <img class="size-10 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                            </div>

                            <div v-if="$page.props.auth.user">
                                <div class="font-medium text-base text-gray-800">
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <div class="font-medium text-sm text-gray-500">
                                    {{ $page.props.auth.user.email }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('user.profile')" :active="route().current('user.profile')">
                                {{ t('profile.title') }}
                            </ResponsiveNavLink>

                            <ResponsiveNavLink :href="route('user.public-profile', $page.props.auth.user)">
                                {{ t('profile.view_public') }}
                            </ResponsiveNavLink>

                            <ResponsiveNavLink v-if="$page.props.auth.user.is_admin" :href="route('admin.dashboard')" :active="route().current('admin.*')">
                                {{ t('admin.panel') }}
                            </ResponsiveNavLink>

                            <ResponsiveNavLink :href="route('subscription.index')" :active="route().current('subscription.*')">
                                {{ t('subscription.title') }}
                            </ResponsiveNavLink>

                            <form method="POST" @submit.prevent="logout">
                                <ResponsiveNavLink as="button">
                                    {{ t('auth.logout') }}
                                </ResponsiveNavLink>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white shadow">
                <div class="max-w-[1920px] mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 py-8 mt-12">
                <div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-gray-600 text-sm">© 2025 {{ t('app.name') }}. {{ t('common.all_rights_reserved') }}</p>
                        <div class="flex gap-6 text-sm">
                            <Link :href="route('terms.show')" class="text-gray-600 hover:text-gray-900 transition">
                                {{ t('auth.terms_of_service') }}
                            </Link>
                            <Link :href="route('policy.show')" class="text-gray-600 hover:text-gray-900 transition">
                                {{ t('auth.privacy_policy') }}
                            </Link>
                            <Link :href="route('rgpd')" class="text-gray-600 hover:text-gray-900 transition">
                                RGPD
                            </Link>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>
