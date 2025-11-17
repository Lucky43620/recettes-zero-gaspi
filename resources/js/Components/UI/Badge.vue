<template>
    <span
        :class="[
            'inline-flex items-center gap-1.5 font-medium rounded-full',
            sizeClasses,
            variantClasses,
            dotPosition && 'pl-2',
        ]"
    >
        <span
            v-if="dot"
            :class="['w-1.5 h-1.5 rounded-full', dotClasses]"
        />
        <slot>{{ label }}</slot>
    </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    label: {
        type: String,
        default: '',
    },
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'success', 'error', 'warning', 'info', 'premium'].includes(value),
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
    dot: {
        type: Boolean,
        default: false,
    },
    dotPosition: {
        type: String,
        default: 'left',
        validator: (value) => ['left', 'right'].includes(value),
    },
});

const sizeClasses = computed(() => {
    const classes = {
        sm: 'px-2 py-0.5 text-xs',
        md: 'px-2.5 py-1 text-sm',
        lg: 'px-3 py-1.5 text-base',
    };
    return classes[props.size];
});

const variantClasses = computed(() => {
    const classes = {
        default: 'bg-gray-100 text-gray-800',
        success: 'bg-green-100 text-green-800',
        error: 'bg-red-100 text-red-800',
        warning: 'bg-yellow-100 text-yellow-800',
        info: 'bg-blue-100 text-blue-800',
        premium: 'bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-900',
    };
    return classes[props.variant];
});

const dotClasses = computed(() => {
    const classes = {
        default: 'bg-gray-500',
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500',
        premium: 'bg-amber-500',
    };
    return classes[props.variant];
});
</script>
