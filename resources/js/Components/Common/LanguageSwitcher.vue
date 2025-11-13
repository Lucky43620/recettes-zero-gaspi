<script setup>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { locale } = useI18n();
const showDropdown = ref(false);

const languages = [
    { code: 'fr', name: 'Français', flag: '🇫🇷' },
    { code: 'en', name: 'English', flag: '🇬🇧' },
];

const currentLanguage = ref(languages.find(lang => lang.code === locale.value) || languages[0]);

const changeLanguage = (lang) => {
    locale.value = lang.code;
    currentLanguage.value = lang;
    localStorage.setItem('locale', lang.code);
    showDropdown.value = false;
};
</script>

<template>
    <div class="relative">
        <button
            @click="showDropdown = !showDropdown"
            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition"
        >
            <span class="text-2xl">{{ currentLanguage.flag }}</span>
            <span class="text-sm font-medium text-gray-700">{{ currentLanguage.code.toUpperCase() }}</span>
        </button>

        <div
            v-if="showDropdown"
            @click.away="showDropdown = false"
            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
        >
            <button
                v-for="lang in languages"
                :key="lang.code"
                @click="changeLanguage(lang)"
                :class="[
                    'w-full px-4 py-2 text-left hover:bg-gray-50 transition flex items-center gap-3',
                    locale === lang.code ? 'bg-green-50 text-green-700' : 'text-gray-700'
                ]"
            >
                <span class="text-2xl">{{ lang.flag }}</span>
                <span class="text-sm font-medium">{{ lang.name }}</span>
            </button>
        </div>
    </div>
</template>
