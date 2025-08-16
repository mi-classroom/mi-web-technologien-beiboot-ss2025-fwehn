<script setup lang="ts">
import BaseModal from '@/components/ui/BaseModal.vue';
import CheckboxDropdown from '@/components/ui/CheckboxDropdown.vue';
import DownloadButton from '@/components/ui/DownloadButton.vue';
import TextInput from '@/components/ui/TextInput.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { FolderOperation } from '@/types/enums';
import { Head, useForm } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { Folder } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps(['folder']);
const breadcrumbs: BreadcrumbItem[] = [{ title: props.folder.name, href: route('folders.edit', props.folder.id) }];

const iptcFields = Object.fromEntries(
    Object.entries(props.folder as Record<string, string>).filter(
        ([key, value]) => key.startsWith('iptc') && key !== 'iptc_fill_percent' && !!value,
    ),
);

const form = useForm<{ [key: string]: string }>(props.folder);

const editable = ref<Record<string, string>>({ ...iptcFields });
const options = Object.keys(props.folder)
    .filter((key) => key.startsWith('iptc'))
    .map((key) => ({ [key]: trans('iptc.' + key) }));

const showModal = ref(false);

function open() {
    showModal.value = true;
}

function close() {
    showModal.value = false;
}

function confirmSubmit(operation: FolderOperation) {
    form.transform((data: Record<string, string | null>) => {
        const filtered: Record<string, any> = {};

        for (const key in data) {
            if (Object.prototype.hasOwnProperty.call(editable.value, key)) {
                filtered[key] = data[key];
            }
        }

        console.log({ name: data.name, operation: operation.toString(), ...filtered });

        return { name: data.name, operation: operation.toString(), ...filtered };
    });

    form.put(route('folders.update', props.folder.id), {
        preserveState: true,
        onSuccess: close,
    });
}
</script>

<template>
    <Head title="Folders" />
    <MainLayout :breadcrumbs="breadcrumbs">
        <form class="flex h-full flex-col items-stretch justify-stretch gap-2">
            <div class="flex flex-row gap-2 px-8 pl-4 pt-4">
                <TextInput
                    id="name_prefix"
                    v-model="form.name"
                    label="Name"
                    :error="form.errors.name"
                    class="flex-grow"
                />
                <div class="flex items-center justify-center pt-2">
                    <CheckboxDropdown :options="options" v-model="editable" class="h-full" />
                </div>
            </div>

            <div class="flex max-h-full min-h-0 flex-grow">
                <div class="max-w-1/2 flex h-full max-h-full w-1/2 flex-col justify-between p-4">
                    <div class="relative h-[calc(100%-4rem)] w-full flex-grow self-center">
                        <Folder class="h-full w-full" />
                    </div>

                    <div class="flex h-16 flex-row items-center justify-center gap-2">
                        <button @click.prevent="open" class="button-primary h-10 rounded-md px-4 align-middle">
                            Submit
                        </button>
                        <button
                            @click.prevent="form.delete(route('folders.destroy', folder))"
                            class="button-secondary h-10 rounded-md px-4 align-middle"
                        >
                            Delete
                        </button>
                        <DownloadButton :href="route('folders.export', folder)" />
                    </div>
                </div>

                <div class="max-w-1/2 flex h-full max-h-full w-1/2 flex-col gap-2 overflow-y-auto p-4">
                    <div v-for="iptcKey in Object.keys(editable)" :key="iptcKey" class="grid gap-3">
                        <div class="flex flex-row flex-nowrap items-stretch justify-stretch gap-3">
                            <TextInput
                                v-model="form[iptcKey]"
                                :id="iptcKey"
                                :label="$t(`iptc.` + iptcKey)"
                                :error="form.errors[iptcKey]"
                                class="flex-grow"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <BaseModal :open="showModal" @cancel="close">
            <h2 class="mb-4 text-lg font-bold">Auf welche Elemente sollen die Einstellungen angewandt werden?</h2>
            <div class="flex flex-col gap-3">
                <button
                    @click="confirmSubmit(FolderOperation.SAVE)"
                    class="button-primary h-10 rounded-md px-4 align-middle"
                >
                    Lediglich diesen Ordner aktualisieren
                </button>
                <button
                    @click="confirmSubmit(FolderOperation.PROPAGATE)"
                    class="button-primary h-10 rounded-md px-4 align-middle"
                >
                    Ordner und alle Bilder aktualisieren
                </button>
                <button
                    @click="confirmSubmit(FolderOperation.MERGE)"
                    class="button-primary h-10 rounded-md px-4 align-middle"
                >
                    Ordner und Bilder mit fehlenden Werten aktualisieren
                </button>
                <button @click="close" class="button-secondary h-10 w-full rounded-md px-4 align-middle">
                    Abbrechen
                </button>
            </div>
        </BaseModal>
    </MainLayout>
</template>

<style scoped></style>
