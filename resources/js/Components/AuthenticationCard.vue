<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-green-50 via-orange-50 to-green-50">
        <div class="mb-6">
            <slot name="logo" />
        </div>

        <div class="w-full sm:max-w-md px-8 py-8 bg-white shadow-xl overflow-hidden sm:rounded-2xl border border-green-100">
            <slot />
        </div>
    </div>
</template>
