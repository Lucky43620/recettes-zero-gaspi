<template>
    <div
        class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200"
    >
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600 mb-1">
                    {{ label }}
                </p>
                <p class="text-3xl font-bold text-gray-900">
                    <slot name="value">{{ formattedValue }}</slot>
                </p>
                <p v-if="change !== null" :class="['text-sm font-medium mt-2', changeClasses]">
                    <span class="inline-flex items-center gap-1">
                        <svg v-if="change > 0" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <svg v-else-if="change < 0" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M14.707 12.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ Math.abs(change) }}{{ changeUnit }}</span>
                    </span>
                    <span v-if="changeLabel" class="ml-1 text-gray-500">
                        {{ changeLabel }}
                    </span>
                </p>
            </div>
            <div
                v-if="icon || $slots.icon"
                :class="[
                    'flex items-center justify-center w-12 h-12 rounded-lg',
                    iconBgClasses,
                ]"
            >
                <slot name="icon">
                    <component v-if="icon" :is="iconComponent" class="w-6 h-6" />
                </slot>
            </div>
        </div>
        <div v-if="$slots.footer" class="mt-4 pt-4 border-t border-gray-100">
            <slot name="footer" />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    label: {
        type: String,
        required: true,
    },
    value: {
        type: [Number, String],
        required: true,
    },
    change: {
        type: Number,
        default: null,
    },
    changeUnit: {
        type: String,
        default: '%',
    },
    changeLabel: {
        type: String,
        default: '',
    },
    icon: {
        type: String,
        default: '',
        validator: (value) => ['users', 'recipes', 'heart', 'star', 'flag', 'calendar'].includes(value) || value === '',
    },
    iconColor: {
        type: String,
        default: 'blue',
        validator: (value) => ['blue', 'green', 'red', 'yellow', 'purple', 'gray'].includes(value),
    },
    format: {
        type: String,
        default: 'number',
        validator: (value) => ['number', 'currency', 'percent'].includes(value),
    },
});

const formattedValue = computed(() => {
    if (typeof props.value === 'string') return props.value;

    switch (props.format) {
        case 'currency':
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'EUR',
            }).format(props.value);
        case 'percent':
            return `${props.value}%`;
        default:
            return new Intl.NumberFormat('fr-FR').format(props.value);
    }
});

const changeClasses = computed(() => {
    if (props.change === null) return '';
    if (props.change > 0) return 'text-green-600';
    if (props.change < 0) return 'text-red-600';
    return 'text-gray-500';
});

const iconBgClasses = computed(() => {
    const classes = {
        blue: 'bg-blue-100 text-blue-600',
        green: 'bg-green-100 text-green-600',
        red: 'bg-red-100 text-red-600',
        yellow: 'bg-yellow-100 text-yellow-600',
        purple: 'bg-purple-100 text-purple-600',
        gray: 'bg-gray-100 text-gray-600',
    };
    return classes[props.iconColor];
});

const iconComponent = computed(() => {
    const icons = {
        users: {
            template: `<svg fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" /></svg>`,
        },
        recipes: {
            template: `<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd" /></svg>`,
        },
        heart: {
            template: `<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg>`,
        },
        star: {
            template: `<svg fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>`,
        },
        flag: {
            template: `<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd" /></svg>`,
        },
        calendar: {
            template: `<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" /></svg>`,
        },
    };
    return icons[props.icon] || null;
});
</script>
