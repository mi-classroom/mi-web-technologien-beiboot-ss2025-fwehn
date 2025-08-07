<script setup lang="ts">
import type { SnackbarLevel } from '@/composables/useSnackbar';
import { snackbarKey } from '@/composables/useSnackbar';
import { provide, ref } from 'vue';

interface Snackbar {
    id: number;
    level: SnackbarLevel;
    message: string;
}

const snackbars = ref<Snackbar[]>([]);
let id = 0;

function showSnackbar(level: SnackbarLevel, message: string) {
    const snackbarId = id++;
    snackbars.value.push({ id: snackbarId, level, message });

    setTimeout(() => {
        snackbars.value = snackbars.value.filter((snack) => snack.id !== snackbarId);
    }, 7000);
}

provide(snackbarKey, { showSnackbar });
</script>

<template>
    <div class="fixed left-1/2 top-4 z-50 flex -translate-x-1/2 flex-col items-center space-y-3">
        <transition-group name="fade" tag="div" class="flex flex-col space-y-2">
            <div
                v-for="snackbar in snackbars"
                :key="snackbar.id"
                :class="[
                    'flex w-full max-w-md items-center justify-between rounded px-4 py-3 text-white shadow-lg',
                    {
                        'bg-green-600': snackbar.level === 'success',
                        'bg-blue-600': snackbar.level === 'info',
                        'bg-yellow-500': snackbar.level === 'warning',
                        'bg-red-600': snackbar.level === 'error',
                    },
                ]"
                role="alert"
                @click="snackbars = snackbars.filter((s) => s.id !== snackbar.id)"
            >
                <span>{{ snackbar.message }}</span>
                <button class="ml-4 font-bold" @click.stop="snackbars = snackbars.filter((s) => s.id !== snackbar.id)">
                    ×
                </button>
            </div>
        </transition-group>
    </div>
    <slot />
</template>

<style>
.fade-enter-active,
.fade-leave-active {
    transition:
        opacity 0.3s,
        transform 0.3s;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
