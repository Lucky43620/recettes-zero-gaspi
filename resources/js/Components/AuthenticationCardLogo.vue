<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
    <Link :href="route('home')">
        <img src="/logo.png" :alt="t('app.name')" class="h-20 w-auto" />
    </Link>
</template>
