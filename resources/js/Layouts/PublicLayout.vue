<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    title: String,
});
</script>

<template>
    <div>
        <Head :title="title" />

        <div class="min-h-screen bg-gray-50 flex flex-col">
            <nav class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <Link :href="route('home')" class="flex items-center">
                                <ApplicationLogo size="sm" />
                            </Link>
                        </div>

                        <div class="flex items-center gap-6">
                            <Link
                                :href="route('home')"
                                :class="[
                                    'text-gray-700 hover:text-gray-900 transition',
                                    route().current('home') ? 'font-semibold text-gray-900' : ''
                                ]"
                            >
                                Accueil
                            </Link>
                            <Link
                                :href="route('recipes.index')"
                                :class="[
                                    'text-gray-700 hover:text-gray-900 transition',
                                    route().current('recipes.*') ? 'font-semibold text-gray-900' : ''
                                ]"
                            >
                                Recettes
                            </Link>
                            <Link
                                :href="route('products.index')"
                                :class="[
                                    'text-gray-700 hover:text-gray-900 transition',
                                    route().current('products.*') ? 'font-semibold text-gray-900' : ''
                                ]"
                            >
                                Produits
                            </Link>

                            <div class="flex items-center gap-3">
                                <template v-if="$page.props.auth.user">
                                    <Link
                                        :href="route('dashboard')"
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition"
                                    >
                                        Mon espace
                                    </Link>
                                </template>
                                <template v-else>
                                    <Link
                                        :href="route('login')"
                                        class="text-gray-700 hover:text-gray-900 transition"
                                    >
                                        Connexion
                                    </Link>
                                    <Link
                                        :href="route('register')"
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition"
                                    >
                                        Inscription
                                    </Link>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <main class="flex-1">
                <slot />
            </main>

            <footer class="bg-white border-t border-gray-200 py-8 mt-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-600">
                    <p>© 2025 Recettes Zéro Gaspi. Tous droits réservés.</p>
                </div>
            </footer>
        </div>
    </div>
</template>
