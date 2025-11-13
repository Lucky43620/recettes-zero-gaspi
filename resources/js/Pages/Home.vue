<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import RecipeGrid from '@/Components/Recipe/RecipeGrid.vue';
import { SparklesIcon, HeartIcon, ClockIcon, UserGroupIcon } from '@heroicons/vue/24/outline';

const { t } = useI18n();

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    featuredRecipes: Array,
});

const features = computed(() => [
    {
        icon: HeartIcon,
        title: t('home.feature_zero_waste_title'),
        description: t('home.feature_zero_waste_desc')
    },
    {
        icon: ClockIcon,
        title: t('home.feature_easy_planning_title'),
        description: t('home.feature_easy_planning_desc')
    },
    {
        icon: UserGroupIcon,
        title: t('home.feature_active_community_title'),
        description: t('home.feature_active_community_desc')
    },
    {
        icon: SparklesIcon,
        title: t('home.feature_personalized_title'),
        description: t('home.feature_personalized_desc')
    }
]);
</script>

<template>
    <PublicLayout title="Accueil">
        <div class="relative overflow-hidden bg-gradient-to-br from-green-600 via-green-700 to-orange-600 py-24 md:py-32">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmZmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDE0YzYuMDc1IDAgMTEgNC45MjUgMTEgMTEtMCA2LjA3NS00LjkyNSAxMS0xMSAxMS02LjA3NSAwLTExLTQuOTI1LTExLTExIDAtNi4wNzUgNC45MjUtMTEgMTEtMTF6Ii8+PC9nPjwvZz48L3N2Zz4=')] opacity-20"></div>

            <div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8 relative">
                <div class="text-center">
                    <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-white mb-6 leading-tight">
                        {{ $t('home.hero_title') }}
                        <span class="block text-orange-300">{{ $t('home.hero_title_highlight') }}</span>
                    </h1>
                    <p class="text-xl md:text-2xl text-white/95 mb-10 max-w-3xl mx-auto leading-relaxed">
                        {{ $t('home.hero_description') }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <Link
                            :href="route('recipes.index')"
                            class="inline-flex items-center px-8 py-4 bg-white text-green-700 font-bold text-lg rounded-xl hover:bg-green-50 transition-all shadow-2xl hover:shadow-xl hover:scale-105 transform"
                        >
                            <SparklesIcon class="h-6 w-6 mr-2" />
                            {{ $t('home.explore_recipes') }}
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="inline-flex items-center px-8 py-4 bg-green-800/30 backdrop-blur-sm text-white font-bold text-lg rounded-xl hover:bg-green-800/50 transition-all border-2 border-white/30"
                        >
                            {{ $t('home.join_community') }}
                        </Link>
                    </div>
                </div>

                <div class="mt-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div
                        v-for="feature in features"
                        :key="feature.title"
                        class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all"
                    >
                        <component :is="feature.icon" class="h-10 w-10 text-white mb-4" />
                        <h3 class="text-lg font-bold text-white mb-2">{{ feature.title }}</h3>
                        <p class="text-white/80 text-sm leading-relaxed">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 py-16">
            <div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        {{ $t('home.popular_recipes') }}
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        {{ $t('home.popular_recipes_desc') }}
                    </p>
                </div>

                <RecipeGrid
                    :recipes="featuredRecipes"
                    :empty-message="$t('home.no_recipes')"
                />

                <div v-if="featuredRecipes && featuredRecipes.length > 0" class="text-center mt-12">
                    <Link
                        :href="route('recipes.index')"
                        class="inline-block px-8 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors shadow-md"
                    >
                        {{ $t('home.view_all_recipes') }}
                    </Link>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-600 to-green-700 py-16">
            <div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                    {{ $t('home.ready_title') }}
                </h2>
                <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                    {{ $t('home.ready_desc') }}
                </p>
                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="inline-block px-10 py-4 bg-white text-green-700 font-bold text-lg rounded-xl hover:bg-green-50 transition-all shadow-lg"
                >
                    {{ $t('home.create_account') }}
                </Link>
                <Link
                    v-else-if="canLogin"
                    :href="route('login')"
                    class="inline-block px-10 py-4 bg-white text-green-700 font-bold text-lg rounded-xl hover:bg-green-50 transition-all shadow-lg"
                >
                    {{ $t('auth.login') }}
                </Link>
            </div>
        </div>
    </PublicLayout>
</template>
