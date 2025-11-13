<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PrimaryButton from '@/Components/Common/PrimaryButton.vue';

const props = defineProps({
    status: Number,
});

const { t } = useI18n();

const title = computed(() => {
    return {
        503: '503: Service Indisponible',
        500: '500: Erreur Serveur',
        404: '404: Page Introuvable',
        403: '403: Accès Interdit',
    }[props.status] || 'Erreur';
});

const description = computed(() => {
    return {
        503: 'Désolé, nous effectuons une maintenance. Merci de réessayer dans quelques instants.',
        500: 'Oups ! Une erreur est survenue sur nos serveurs. Nous travaillons pour la résoudre.',
        404: 'Désolé, la page que vous recherchez n\'existe pas ou a été déplacée.',
        403: 'Désolé, vous n\'avez pas l\'autorisation d\'accéder à cette page.',
    }[props.status] || 'Une erreur inattendue est survenue.';
});

const illustration = computed(() => {
    return {
        503: {
            icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
            color: 'text-yellow-500',
        },
        500: {
            icon: 'M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z',
            color: 'text-red-500',
        },
        404: {
            icon: 'M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z',
            color: 'text-blue-500',
        },
        403: {
            icon: 'M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z',
            color: 'text-orange-500',
        },
    }[props.status] || {
        icon: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        color: 'text-gray-500',
    };
});
</script>

<template>
    <div>
        <Head :title="title" />

        <PublicLayout>
            <div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
                <div class="max-w-lg w-full text-center">
                    <!-- Error Icon -->
                    <div class="mb-8">
                        <div :class="['inline-flex items-center justify-center w-24 h-24 rounded-full bg-gray-100', illustration.color]">
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" :d="illustration.icon" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>

                    <!-- Error Code -->
                    <div class="mb-4">
                        <h1 class="text-6xl font-bold text-gray-900 mb-2">
                            {{ status }}
                        </h1>
                        <h2 class="text-2xl font-semibold text-gray-700">
                            {{ title.split(': ')[1] }}
                        </h2>
                    </div>

                    <!-- Description -->
                    <p class="text-gray-600 mb-8 text-lg">
                        {{ description }}
                    </p>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <Link :href="route('home')">
                            <PrimaryButton class="w-full sm:w-auto">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                {{ t('common.back') }} Accueil
                            </PrimaryButton>
                        </Link>

                        <button
                            @click="$inertia.visit(window.history.back())"
                            class="px-6 py-3 text-gray-700 hover:text-gray-900 font-medium transition-colors"
                        >
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Page précédente
                        </button>
                    </div>

                    <!-- Help Section -->
                    <div v-if="status === 404" class="mt-12 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Peut-être cherchez-vous...
                        </h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <Link :href="route('recipes.index')" class="text-green-600 hover:text-green-700 font-medium">
                                Parcourir les recettes
                            </Link>
                            <Link :href="route('dashboard')" class="text-green-600 hover:text-green-700 font-medium">
                                Mon tableau de bord
                            </Link>
                            <Link :href="route('meal-plans.index')" class="text-green-600 hover:text-green-700 font-medium">
                                Planning de repas
                            </Link>
                            <Link :href="route('pantry.index')" class="text-green-600 hover:text-green-700 font-medium">
                                Mon garde-manger
                            </Link>
                        </div>
                    </div>

                    <!-- Contact Support -->
                    <div v-if="status === 500 || status === 503" class="mt-8 p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">
                            Le problème persiste ?
                            <a href="mailto:support@recettes-zero-gaspi.com" class="text-green-600 hover:text-green-700 font-medium">
                                Contactez notre support
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </PublicLayout>
    </div>
</template>
