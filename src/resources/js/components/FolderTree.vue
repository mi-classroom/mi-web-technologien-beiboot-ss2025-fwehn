<script setup lang="ts">
import BaseModal from '@/components/ui/BaseModal.vue';
import TextInput from '@/components/ui/TextInput.vue';
import { FolderOperation } from '@/types/enums';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { CirclePlus } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import FolderTreeNode from './FolderTreeNode.vue';

const foldersTree = computed(() => usePage().props.foldersTree);
const currentFolder = computed(() => usePage().props.currentFolder);
const selectedId = computed(() => currentFolder.value?.id ?? null);

const form = useForm({
    name: '',
});

const showModal = ref(false);
const parentFolderId = ref<number | null>(null);

function openCreateFolderModal(parentId: number | null) {
    parentFolderId.value = parentId;
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    parentFolderId.value = null;
}

function confirmCreateFolder() {
    router.post(route('folders.store'), {
        name: form.name,
        operation: FolderOperation.SAVE.toString(),
        parent_folder_id: parentFolderId.value,
    });

    form.reset();
    closeModal();
}
</script>

<template>
    <div class="no-scrollbar h-full w-full overflow-y-auto">
        <ul class="w-full text-left">
            <FolderTreeNode
                v-for="folder in foldersTree"
                :key="folder.id"
                :folder="folder"
                :selectedId="selectedId"
                @create-folder="openCreateFolderModal"
            />

            <li>
                <button
                    class="group flex w-full items-center gap-x-2 border text-lg text-warm-medium hover:text-warm-dark"
                    @click="openCreateFolderModal(null)"
                >
                    <CirclePlus class="flex-shrink-0" />
                    <span class="flex-grow overflow-hidden text-ellipsis text-nowrap text-start">
                        Ordner erstellen
                    </span>
                </button>
            </li>
        </ul>
    </div>

    <BaseModal :open="showModal" @cancel="closeModal">
        <h2 class="mb-4 text-lg font-bold">Neuen Ordner erstellen?</h2>
        <form @submit.prevent="confirmCreateFolder" @reset="closeModal" class="flex flex-col gap-3">
            <TextInput id="name" v-model="form.name" label="Name" :error="form.errors.name" class="flex-grow" />

            <div class="flex w-full items-end justify-center gap-3">
                <button type="reset" class="button-secondary h-10 rounded-md px-4">Abbrechen</button>
                <button type="submit" class="button-primary h-10 rounded-md px-4">Speichern</button>
            </div>
        </form>
    </BaseModal>
</template>
