<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    isFollowing: Boolean,
});

const form = useForm({});

function toggleFollow() {
    if (props.isFollowing) {
        form.delete(route('users.unfollow', props.user.id), {
            preserveScroll: true,
        });
    } else {
        form.post(route('users.follow', props.user.id), {
            preserveScroll: true,
        });
    }
}
</script>

<template>
    <button
        @click="toggleFollow"
        :disabled="form.processing"
        :class="[
            'px-4 py-2 rounded-md font-medium transition',
            isFollowing
                ? 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                : 'bg-green-600 text-white hover:bg-green-700'
        ]"
    >
        {{ isFollowing ? 'Ne plus suivre' : 'Suivre' }}
    </button>
</template>
