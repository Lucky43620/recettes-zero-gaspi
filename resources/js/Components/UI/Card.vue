<template>
    <div
        :class="[
            'bg-white rounded-lg shadow-sm border border-gray-200',
            paddingClasses,
            hoverEffect && 'hover:shadow-md transition-shadow duration-200',
            clickable && 'cursor-pointer',
        ]"
        @click="handleClick"
    >
        <div v-if="$slots.header || title" class="border-b border-gray-200 pb-4 mb-4">
            <slot name="header">
                <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
            </slot>
        </div>

        <div :class="bodyClasses">
            <slot />
        </div>

        <div v-if="$slots.footer" class="border-t border-gray-200 pt-4 mt-4">
            <slot name="footer" />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: '',
    },
    padding: {
        type: String,
        default: 'default',
        validator: (value) => ['none', 'sm', 'default', 'lg'].includes(value),
    },
    hoverEffect: {
        type: Boolean,
        default: false,
    },
    clickable: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['click']);

const paddingClasses = computed(() => {
    const classes = {
        none: '',
        sm: 'p-4',
        default: 'p-6',
        lg: 'p-8',
    };
    return classes[props.padding];
});

const bodyClasses = computed(() => {
    return 'text-gray-700';
});

const handleClick = (event) => {
    if (props.clickable) {
        emit('click', event);
    }
};
</script>
