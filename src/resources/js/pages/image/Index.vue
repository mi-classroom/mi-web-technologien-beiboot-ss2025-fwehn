<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Images', href: '/images' }];
defineProps(['images']);

const isDragging = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    images: [] as File[],
});

const submitForm = () => {
    form.post(route('images.store'), {
        forceFormData: true,
        preserveScroll: true,
        onError: (err) => {
            console.error(err);
            alert('Fehler beim Upload.');
        },
    });
};

const handleDrop = (e: DragEvent) => {
    isDragging.value = false;

    if (e.dataTransfer?.files) {
        for (const file of e.dataTransfer?.files) {
            if (file.type.startsWith('image/')) {
                form.images.push(file);
            }
        }

        if (form.images.length > 0) submitForm();
    }
};

const handleDragEnter = () => {
    isDragging.value = true;
};
const handleDragLeave = (e: DragEvent) => {
    if ((e.target as HTMLElement).classList.contains('drop-overlay')) {
        isDragging.value = false;
    }
};
</script>

<template>
    <Head title="Images" />

    <MainLayout :breadcrumbs="breadcrumbs">
        <form
            @submit.prevent="submitForm"
            class="relative h-full w-full overflow-y-auto"
            @dragover.prevent
            @dragenter.prevent="handleDragEnter"
            @dragleave="handleDragLeave"
            @drop.prevent="handleDrop"
        >
            <div
                v-if="isDragging || images.length === 0"
                class="drop-overlay bg-background pointer-events-auto fixed bottom-0 left-64 right-0 top-12 z-40 bg-warm-light p-4"
            >
                <div
                    class="flex h-full w-full items-center justify-center rounded-xl border-4 border-dashed border-primary text-2xl font-semibold text-primary"
                >
                    Datei hierher ziehen zum Hochladen
                </div>
            </div>

            <input
                type="file"
                accept="image/*"
                multiple
                class="hidden"
                ref="fileInput"
                @change="
                    (e) => {
                        const files = (e.target as HTMLInputElement)?.files;
                        if (!files) return;

                        [...files].forEach((file) => {
                            if (file && file.type.startsWith('image/')) {
                                form.images.push(file);
                            }
                        });

                        if (form.images.length > 0) submitForm();
                    }
                "
            />

            <div class="fixed bottom-8 right-8 z-50">
                <button
                    type="button"
                    class="text-accent rounded-full bg-primary p-4 shadow-lg transition-all"
                    @click="fileInput?.click()"
                    :disabled="form.processing"
                >
                    <Plus />
                </button>

                <div v-if="form.errors.images" class="bg-destructive mt-2 text-sm">
                    {{ form.errors.images }}
                </div>
            </div>

            <div v-if="images.length > 0" class="flex h-full w-full flex-col gap-2 p-4">
                <div v-for="image in images" :key="image.id" class="flex w-full rounded-md border-s border-warm-medium">
                    <img
                        class="h-40 min-h-40 w-40 min-w-40 bg-warm-medium object-contain p-0"
                        :src="image.preview_url"
                        :alt="image.name"
                        loading="lazy"
                    />
                    <div class="flex h-full w-full flex-col justify-center pl-4">
                        <h1 class="text-xl font-semibold">{{ image.name }}</h1>
                        <p>{{ image.iptc_date_created }}</p>
                    </div>
                    <div class="flex h-40 items-center justify-center gap-4 pr-10">
                        <Link as="a" :href="route('images.edit', image)">
                            <Pencil />
                        </Link>

                        <Link method="delete" as="a" :href="route('images.destroy', image)">
                            <Trash2 />
                        </Link>
                    </div>
                </div>
            </div>
        </form>
    </MainLayout>
</template>
