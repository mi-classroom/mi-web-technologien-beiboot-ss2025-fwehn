<script setup lang="ts">
import { SnackbarLevel, useSnackbar } from '@/composables/useSnackbar';
import { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';

const { showSnackbar } = useSnackbar();

const page = usePage<SharedData>();
watch(
    () => page.props.statuses,
    (statuses: Record<SnackbarLevel, string>) => {
        if (!statuses) return;

        for (const level in statuses) {
            showSnackbar(level as SnackbarLevel, statuses[level as SnackbarLevel]);
        }
    },
    { immediate: true, deep: true },
);
</script>

<template>
    <span></span>
</template>

<style scoped></style>
