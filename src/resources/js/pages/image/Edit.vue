<script setup lang="ts">
import DownloadButton from '@/components/ui/beiboot/DownloadButton.vue';
import TextInput from '@/components/ui/beiboot/TextInput.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps(['image']);
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Images', href: '/images' },
    {
        title: props.image.name,
        href: `/images/${props.image.id}`,
    },
];
const form = useForm({ ...props.image });
</script>

<template>
    <Head title="Images" />
    <MainLayout :breadcrumbs="breadcrumbs">
        <form
            @submit.prevent="() => form.put(route('images.update', image.id), { preserveState: true })"
            class="flex h-full flex-col items-stretch justify-stretch gap-2"
        >
            <div class="grid gap-2 px-8 pl-4 pt-4">
                <TextInput id="file-name" v-model="form.name" label="Name" :error="form.errors.name" />
            </div>

            <div class="flex max-h-full min-h-0 flex-grow">
                <div class="max-w-1/2 flex h-full max-h-full w-1/2 flex-col justify-between p-4">
                    <img
                        :src="image.preview_url"
                        :alt="image.name"
                        class="h-[calc(100%-4rem)] max-h-[calc(100%-4rem)] w-full max-w-full flex-grow self-center object-contain"
                    />

                    <div class="flex h-16 flex-row items-center justify-center gap-2">
                        <button type="submit" class="button-primary h-10 rounded-md px-4 align-middle">Submit</button>
                        <button
                            @click.prevent="form.delete(route('images.destroy', image.id))"
                            class="button-secondary h-10 rounded-md px-4 align-middle"
                        >
                            Delete
                        </button>
                        <DownloadButton :href="route('images.export', image)" />
                    </div>
                </div>

                <div class="max-w-1/2 flex h-full max-h-full w-1/2 flex-col gap-2 overflow-y-auto p-4">
                    <div
                        v-for="iptcKey in Object.keys(form).filter((key) => key.startsWith('iptc'))"
                        :key="iptcKey"
                        class="grid gap-3"
                    >
                        <TextInput
                            v-model="form[iptcKey]"
                            :id="iptcKey"
                            :label="$t(`iptc.` + iptcKey)"
                            :error="form.errors[iptcKey]"
                        />
                    </div>
                </div>
            </div>
        </form>
    </MainLayout>
</template>
