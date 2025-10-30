<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link :href="route('home')">
        <img src="/logo.png" alt="Recettes Zéro Gaspi" class="h-20 w-auto" />
    </Link>
</template>
