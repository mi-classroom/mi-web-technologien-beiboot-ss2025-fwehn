<script setup lang="ts">
import IptcInputs from '@/components/iptc/IptcInputs.vue';
import DownloadButton from '@/components/ui/DownloadButton.vue';
import TextInput from '@/components/ui/TextInput.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Image } from '@/types/image';
import { Head, useForm } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps<{ images: Image[] }>();
const imageIds = props.images.map((image) => image.id);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Images', href: route('images.index') },
    { title: 'Edit Selection', href: route('images.edit-selection', { images: imageIds }) },
];

const initialFormValues = {
    name_prefix: '',
    iptc: {} as Iptc,
};

const editableFields: Record<string, boolean> = {};

const allIptcKeys = new Set<string>();
props.images.forEach((image) => {
    if (image.iptc) {
        Object.keys(image.iptc).forEach((key) => allIptcKeys.add(key));
    }
});

allIptcKeys.forEach((key) => {
    const values = props.images.map((img) => img.iptc?.[key]).filter((v) => v !== undefined && v !== null && v !== '');
    initialFormValues.iptc[key] = [...new Set(values)].join(', ');

    editableFields[key] = values.length > 0;
});

const form = useForm<typeof initialFormValues>({ ...initialFormValues });
const editable = ref<Record<string, boolean>>({ ...editableFields });

const currentImageIndex = ref(0);
const currentImage = computed(() => props.images[currentImageIndex.value]);

const nextImage = () => {
    currentImageIndex.value = (currentImageIndex.value + 1) % props.images.length;
};
const prevImage = () => {
    currentImageIndex.value = (currentImageIndex.value - 1 + props.images.length) % props.images.length;
};

function handleSubmit() {
    form.transform((data) => {
        const filtered: Record<string, string> = {};
        for (const key in data.iptc) {
            if (editable.value[key]) {
                filtered[key] = data.iptc[key];
            }
        }
        return { name_prefix: data.name_prefix, iptc: filtered };
    });

    form.put(route('images.update-selection', { images: imageIds }), {
        preserveState: true,
    });
}
</script>

<template>
    <Head title="Images" />
    <MainLayout :breadcrumbs="breadcrumbs">
        <form @submit.prevent="handleSubmit" class="flex h-full flex-col gap-2">
            <div class="flex flex-row gap-2 px-8 pl-4 pt-4">
                <TextInput
                    id="name_prefix"
                    v-model="form.name_prefix"
                    label="Namenspräfix"
                    :error="form.errors.name_prefix"
                    class="flex-grow"
                />
            </div>

            <div class="flex max-h-full min-h-0 flex-grow">
                <div class="max-w-1/2 flex h-full w-1/2 flex-col justify-between p-4">
                    <div class="relative h-[calc(100%-4rem)] w-full flex-grow self-center">
                        <img
                            :src="route('images.preview', { image: currentImage.id, w: 200 })"
                            :srcset="`
                                ${route('images.preview', { image: currentImage.id, w: 200 })} 200w,
                                ${route('images.preview', { image: currentImage.id, w: 400 })} 400w,
                                ${route('images.preview', { image: currentImage.id, w: 800 })} 800w,
                                ${route('images.preview', { image: currentImage.id, w: 1200 })} 1200w
                            `"
                            sizes="50vw"
                            fetchpriority="high"
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
                            {{ currentImageIndex + 1 }} / {{ props.images.length }}
                        </div>
                    </div>

                    <div class="flex h-16 flex-row items-center justify-center gap-2">
                        <button type="submit" class="button-primary h-10 rounded-md px-4">Submit</button>
                        <button
                            @click.prevent="form.delete(route('images.destroy-selection', { images: imageIds }))"
                            class="button-secondary h-10 rounded-md px-4"
                        >
                            Delete
                        </button>
                        <DownloadButton :href="route('images.export-selection', { images: imageIds })" />
                    </div>
                </div>

                <IptcInputs
                    v-model="form.iptc"
                    v-model:editableFields="editable"
                    :errors="form.errors"
                    selectable
                    class="max-w-1/2 flex h-full w-1/2 flex-col gap-2 overflow-y-auto p-4"
                />
            </div>
        </form>
    </MainLayout>
</template>
