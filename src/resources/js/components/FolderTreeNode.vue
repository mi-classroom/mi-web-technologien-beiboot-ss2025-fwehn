<script setup lang="ts">
import { cn } from '@/lib/utils';
import { Link } from '@inertiajs/vue3';
import { EllipsisVertical, Folder } from 'lucide-vue-next';
import FolderTreeNode from './FolderTreeNode.vue';

interface FolderWithChildren {
    id: number;
    name: string;
    parent_folder_id: number | null;
    children: FolderWithChildren[];
}

defineProps<{
    folder: FolderWithChildren;
    selectedId: number | null;
}>();
</script>

<template>
    <li>
        <Link
            :href="route('folders.select', folder.id)"
            :class="
                cn(
                    'group flex w-full items-center gap-x-2 border text-lg',
                    selectedId === folder.id ? 'font-bold text-primary' : 'text-warm-medium hover:text-warm-dark',
                )
            "
        >
            <Folder class="flex-shrink-0" />
            <span class="flex-grow overflow-hidden text-ellipsis text-nowrap">
                {{ folder.name }}
            </span>

            <Link :href="route('folders.edit', folder.id)" class="hidden hover:text-warm-dark group-hover:block">
                <EllipsisVertical class="flex-shrink-0" />
            </Link>
        </Link>

        <ul v-if="folder.children && folder.children.length" class="w-full border-l border-gray-300 pl-6 pt-1">
            <FolderTreeNode v-for="child in folder.children" :key="child.id" :folder="child" :selectedId="selectedId" />
        </ul>
    </li>
</template>
