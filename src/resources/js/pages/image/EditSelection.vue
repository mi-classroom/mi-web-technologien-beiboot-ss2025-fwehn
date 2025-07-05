<script setup lang="ts">
import CheckboxDropdown from '@/components/ui/beiboot/CheckboxDropdown.vue';
import DownloadButton from '@/components/ui/beiboot/DownloadButton.vue';
import TextInput from '@/components/ui/beiboot/TextInput.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps(['images']);
const imageIds = props.images.map((image: Image) => image.id);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Images', href: route('images.index') },
    {
        title: 'Edit Selection',
        href: route('images.edit-selection', { images: imageIds }),
    },
];

const initialFormValues = {
    name_prefix: '',
};

if (props.images.length > 0) {
    const iptcKeys = Object.keys(props.images[0]).filter((key) => key.startsWith('iptc'));

    for (const key of iptcKeys) {
        initialFormValues[key] = [
            ...new Set(props.images.map((image: Image) => image[key]).filter((value?: string) => !!value)),
        ].join(', ');
    }
}

const form = useForm<{ [key: string]: any }>(initialFormValues);

const editable = ref<Record<string, string>>({});
const options = Object.keys(props.images[0])
    .filter((key) => key.startsWith('iptc'))
    .map((key) => ({ [key]: trans('iptc.' + key) }));

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
                () => {
                    const payload = Object.fromEntries(
                        Object.entries(form.data()).filter(
                            ([key]) => key === 'name_prefix' || Object.prototype.hasOwnProperty.call(editable, key),
                        ),
                    );

                    router.put(route('images.update-selection', { images: imageIds }), payload, {
                        preserveState: true,
                        onSuccess: () => {},
                    });
                }
            "
            class="flex h-full flex-col items-stretch justify-stretch gap-2"
        >
            <div class="flex flex-row gap-2 px-8 pl-4 pt-4">
                <TextInput
                    id="name_prefix"
                    v-model="form.name_prefix"
                    label="Namenspräfix"
                    :error="form.errors.name_prefix"
                    class="flex-grow"
                />
                <div class="flex items-center justify-center pt-2">
                    <CheckboxDropdown :options="options" v-model="editable" class="h-full" />
                </div>
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
    </MainLayout>
</template>
