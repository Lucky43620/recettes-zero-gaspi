<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    route: {
        type: String,
        required: true,
    },
    method: {
        type: String,
        default: 'post',
        validator: (value) => ['post', 'delete'].includes(value),
    },
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'primary', 'danger'].includes(value),
    },
    icon: {
        type: String,
        default: null,
    },
    isActive: {
        type: Boolean,
        default: false,
    },
});

const form = useForm({});

const handleClick = () => {
    form[props.method](props.route, {
        preserveScroll: true,
    });
};

const buttonClasses = computed(() => {
    const base = 'flex items-center gap-2 px-4 py-2 rounded-md font-medium transition disabled:opacity-50';

    const variants = {
        default: 'border border-gray-300 hover:bg-gray-50',
        primary: props.isActive
            ? 'bg-gray-200 text-gray-700 hover:bg-gray-300'
            : 'bg-green-600 text-white hover:bg-green-700',
        danger: 'bg-red-600 text-white hover:bg-red-700',
    };

    return `${base} ${variants[props.variant]}`;
});
</script>

<template>
    <button
        @click="handleClick"
        :disabled="form.processing"
        :class="buttonClasses"
    >
        <svg v-if="icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path v-html="icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
        </svg>
        <slot />
    </button>
</template>
