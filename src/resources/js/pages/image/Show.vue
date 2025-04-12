<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const props = defineProps(['image']);
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Images', href: '/images' },
    {
        title: props.image.name,
        href: `/images/${props.image.id}`,
    },
];
</script>

<template>
    <Head title="Images" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <pre>
            {{ image }}
        </pre>
    </AppLayout>
</template>
