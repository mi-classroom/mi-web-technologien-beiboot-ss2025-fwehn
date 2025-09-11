<script setup lang="ts">
import FolderTree from '@/components/FolderTree.vue';
import SnackbarService from '@/components/SnackbarService.vue';
import ImageFilterDropdown from '@/components/image/FilterDropdown.vue';
import SnackbarProvider from '@/providers/SnackbarProvider.vue';
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
    <SnackbarProvider>
        <SnackbarService />
        <div class="absolute bottom-0 left-0 right-0 top-0 flex justify-stretch">
            <div class="h-full w-64 border-r-s border-warm-medium">
                <div
                    class="flex h-20 items-center justify-center text-center align-middle text-4xl font-bold text-primary"
                >
                    <Link :href="route('folders.select-none')">IPTC Editor</Link>
                </div>
                <div class="h-[calc(100%-5rem)] w-full px-2">
                    <FolderTree />
                </div>
            </div>
            <div
                class="absolute left-64 right-0 top-0 flex h-12 items-center justify-between gap-4 border-b-s border-warm-medium px-4 align-middle text-warm-medium"
            >
                <div class="flex flex-row justify-start">
                    <template v-for="(breadcrumb, key) in breadcrumbs" :key="key">
                        <template v-if="key !== breadcrumbs.length - 1">
                            <Link :href="breadcrumb.href ?? '#'" class="text-warm-dark">{{ breadcrumb.title }}</Link>
                            <ChevronRight class="text-warm-medium" />
                        </template>
                        <p v-else class="text-warm-medium">{{ breadcrumb.title }}</p>
                    </template>
                </div>

                <ImageFilterDropdown v-if="route().current('images.index')" />
            </div>
            <div class="absolute bottom-0 left-64 right-0 top-12">
                <slot />
            </div>
        </div>
    </SnackbarProvider>
</template>

<style scoped></style>
