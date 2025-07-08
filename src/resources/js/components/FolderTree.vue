<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import FolderTreeNode from './FolderTreeNode.vue';

const foldersTree = computed(() => usePage().props.foldersTree);
const currentFolder = computed(() => usePage().props.currentFolder);
const selectedId = computed(() => currentFolder.value?.id ?? null);
</script>

<template>
    <div class="no-scrollbar h-full w-full overflow-y-auto">
        <ul class="w-full text-left">
            <FolderTreeNode v-for="folder in foldersTree" :key="folder.id" :folder="folder" :selectedId="selectedId" />
        </ul>
    </div>
</template>
