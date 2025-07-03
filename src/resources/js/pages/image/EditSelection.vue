<script setup lang="ts">
import DownloadButton from '@/components/ui/beiboot/DownloadButton.vue';
import TextInput from '@/components/ui/beiboot/TextInput.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps(['images']);
const imageIds = props.images.map((image) => image.id);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Images', href: route('images.index') },
    {
        title: 'Edit Selection',
        href: route('images.edit-selection', { images: imageIds }),
    },
];

const form = useForm<{ [key: string]: any }>({
    name_prefix: '',
    iptc_object_name: '',
});

const currentImageIndex = ref(0);

const nextImage = () => {
    currentImageIndex.value = (currentImageIndex.value + 1) % props.images.length;
};

const prevImage = () => {
    currentImageIndex.value = (currentImageIndex.value - 1 + props.images.length) % props.images.length;
};

const currentImage = computed(() => props.images[currentImageIndex.value]);
</script>

<template>
    <Head title="Images" />
    <MainLayout :breadcrumbs="breadcrumbs">
        <form
            @submit.prevent="
                () => form.put(route('images.update-selection', { images: imageIds }), { preserveState: true })
            "
            class="flex h-full flex-col items-stretch justify-stretch gap-2"
        >
            <div class="grid gap-2 px-8 pl-4 pt-4">
                <TextInput id="file-name" v-model="form.name_prefix" label="Name" :error="form.errors.name_prefix" />
            </div>

            <div class="flex max-h-full min-h-0 flex-grow">
                <div class="max-w-1/2 flex h-full max-h-full w-1/2 flex-col justify-between p-4">
                    <div class="relative h-[calc(100%-4rem)] w-full flex-grow self-center">
                        <img
                            :src="currentImage.preview_url"
                            :alt="currentImage.name"
                            class="h-full w-full object-contain"
                        />

                        <button
                            type="button"
                            @click="prevImage"
                            class="absolute left-2 top-1/2 z-10 -translate-y-1/2 rounded-full bg-primary p-2 text-lightest shadow-lg hover:bg-primary-hover"
                        >
                            <ChevronLeft />
                        </button>

                        <button
                            type="button"
                            @click="nextImage"
                            class="absolute right-2 top-1/2 z-10 -translate-y-1/2 rounded-full bg-primary p-2 text-lightest shadow-lg hover:bg-primary-hover"
                        >
                            <ChevronRight />
                        </button>

                        <div
                            class="absolute bottom-2 left-1/2 -translate-x-1/2 rounded bg-warm-dark px-3 py-1 text-sm text-lightest"
                        >
                            {{ currentImageIndex + 1 }} / {{ images.length }}
                        </div>
                    </div>

                    <div class="flex h-16 flex-row items-center justify-center gap-2">
                        <button type="submit" class="button-primary h-10 rounded-md px-4 align-middle">Submit</button>
                        <button
                            @click.prevent="form.delete(route('images.destroy-selection', { images: imageIds }))"
                            class="y button-secondary h-10 rounded-md px-4 align-middle"
                        >
                            Delete
                        </button>
                        <DownloadButton :href="route('images.export-selection', { images: imageIds })" />
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
