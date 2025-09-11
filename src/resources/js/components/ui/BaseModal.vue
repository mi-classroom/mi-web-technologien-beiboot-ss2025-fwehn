<script setup lang="ts">
interface Props {
    open: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'cancel'): void;
}>();

function close() {
    emit('cancel');
}
</script>

<template>
    <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="close">
        <div class="w-full max-w-lg rounded-lg bg-warm-light p-6 shadow-lg">
            <slot />
        </div>
    </div>
</template>
