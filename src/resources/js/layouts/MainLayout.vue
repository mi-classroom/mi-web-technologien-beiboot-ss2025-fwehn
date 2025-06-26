<script setup lang="ts">
import type { BreadcrumbItemType } from '@/types';
import { Link } from '@inertiajs/vue3';

import { ChevronRight } from 'lucide-vue-next';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <div class="absolute bottom-0 left-0 right-0 top-0 flex justify-stretch">
        <div class="h-full w-72 border-r-2 border-primary"></div>
        <div class="absolute left-72 right-0 top-0 flex h-12 items-center gap-4 pl-4 text-center align-middle">
            <template v-for="(breadcrumb, key) in breadcrumbs" :key="key">
                <template v-if="key !== breadcrumbs.length - 1">
                    <Link :href="breadcrumb.href ?? '#'">{{ breadcrumb.title }}</Link>
                    <ChevronRight />
                </template>
                <p v-else>{{ breadcrumb.title }}</p>
            </template>
        </div>
        <div class="absolute bottom-0 left-72 right-0 top-12">
            <slot />
        </div>
    </div>
</template>

<style scoped></style>
