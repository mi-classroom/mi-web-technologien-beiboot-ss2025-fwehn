<script setup lang="ts">
import { cn } from '@/lib/utils';
import { Link } from '@inertiajs/vue3';
import { CirclePlus, EllipsisVertical, Folder } from 'lucide-vue-next';
import FolderTreeNode from './FolderTreeNode.vue';

interface FolderWithChildren {
    id: number;
    name: string;
    parent_folder_id: number | null;
    children: FolderWithChildren[];
}

const emit = defineEmits<{
    (e: 'create-folder', parentId: number): void;
}>();

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

            <Link
                :href="route('folders.select-and-edit', folder.id)"
                :class="
                    cn(
                        'hidden text-warm-medium group-hover:block',
                        selectedId === folder.id ? 'hover:text-primary' : 'hover:text-warm-dark',
                    )
                "
            >
                <EllipsisVertical class="flex-shrink-0" />
            </Link>
        </Link>

        <ul
            v-if="(folder.children && folder.children.length) || selectedId === folder.id"
            class="w-full border-l border-gray-300 pl-6 pt-1"
        >
            <FolderTreeNode
                v-for="child in folder.children"
                :key="child.id"
                :folder="child"
                :selectedId="selectedId"
                @create-folder="emit('create-folder', $event)"
            />

            <li>
                <button
                    class="group flex w-full items-center gap-x-2 border text-lg text-warm-medium hover:text-warm-dark"
                    @click="emit('create-folder', folder.id)"
                >
                    <CirclePlus class="flex-shrink-0" />
                    <span class="flex-grow overflow-hidden text-ellipsis text-nowrap text-start">
                        {{ $t('folder.store._') }}
                    </span>
                </button>
            </li>
        </ul>
    </li>
</template>
