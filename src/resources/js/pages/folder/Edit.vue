<script setup lang="ts">
import DownloadButton from '@/components/ui/DownloadButton.vue';
import TextInput from '@/components/ui/TextInput.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Folder } from 'lucide-vue-next';

const props = defineProps(['folder']);
const breadcrumbs: BreadcrumbItem[] = [{ title: props.folder.name, href: route('folders.edit', props.folder.id) }];
const form = useForm({ ...props.folder });
</script>

<template>
    <Head title="Folders" />
    <MainLayout :breadcrumbs="breadcrumbs">
        <form
            @submit.prevent="() => form.put(route('folders.update', folder.id), { preserveState: true })"
            class="flex h-full flex-col items-stretch justify-stretch gap-2"
        >
            <div class="flex flex-row gap-2 px-8 pl-4 pt-4">
                <TextInput id="name" v-model="form.name" label="Name" :error="form.errors.name" class="flex-grow" />
            </div>

            <div class="flex max-h-full min-h-0 flex-grow">
                <div class="max-w-1/2 flex h-full max-h-full w-1/2 flex-col justify-between p-4">
                    <div class="relative h-[calc(100%-4rem)] w-full flex-grow self-center">
                        <Folder class="h-full w-full" />
                    </div>

                    <div class="flex h-16 flex-row items-center justify-center gap-2">
                        <button type="submit" class="button-primary h-10 rounded-md px-4 align-middle">Submit</button>
                        <button
                            @click.prevent="form.delete(route('folders.destroy', folder))"
                            class="y button-secondary h-10 rounded-md px-4 align-middle"
                        >
                            Delete
                        </button>
                        <DownloadButton :href="route('folders.export', folder)" />
                    </div>
                </div>

                <div class="max-w-1/2 flex h-full max-h-full w-1/2 flex-col gap-2 overflow-y-auto p-4">
                    <div
                        v-for="iptcKey in Object.keys(form).filter((key) => key.startsWith('iptc_'))"
                        :key="iptcKey"
                        class="grid gap-3"
                    >
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
    </MainLayout>
</template>

<style scoped></style>
