<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Pencil, Plus } from 'lucide-vue-next';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Images', href: '/images' }];
const props = defineProps(['images']);

const isDragging = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    image: null as File | null,
});

const submitForm = () => {
    form.post(route('images.store'), {
        forceFormData: true,
        preserveScroll: true,
        onError: () => {
            alert('Fehler beim Upload.');
        },
    });
};

const handleDrop = (e: DragEvent) => {
    isDragging.value = false;
    const file = e.dataTransfer?.files?.[0];
    if (file && file.type.startsWith('image/')) {
        form.image = file;
        submitForm();
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

    <AppLayout :breadcrumbs="breadcrumbs">
        <form
            @submit.prevent="submitForm"
            class="relative h-full w-full"
            @dragover.prevent
            @dragenter.prevent="handleDragEnter"
            @dragleave="handleDragLeave"
            @drop.prevent="handleDrop"
        >
            <div
                v-if="isDragging || props.images.length === 0"
                class="drop-overlay pointer-events-auto absolute inset-0 z-40 flex items-center justify-center rounded-xl border-4 border-dashed bg-background text-2xl font-semibold text-primary"
            >
                Datei hierher ziehen zum Hochladen
            </div>

            <input
                type="file"
                accept="image/*"
                class="hidden"
                ref="fileInput"
                @change="
                    (e) => {
                        const file = (e.target as HTMLInputElement).files?.[0] as File | undefined;
                        if (file?.type.startsWith('image/')) {
                            form.image = file;
                            submitForm();
                        }
                    }
                "
            />

            <div class="fixed bottom-8 right-8 z-50">
                <button
                    type="button"
                    class="rounded-full bg-primary p-4 text-accent shadow-lg transition-all"
                    @click="fileInput?.click()"
                    :disabled="form.processing"
                >
                    <Plus />
                </button>

                <div v-if="form.errors.image" class="mt-2 bg-destructive text-sm">
                    {{ form.errors.image }}
                </div>
            </div>

            <div v-if="props.images.length > 0" class="mt-4 flex flex-wrap items-center justify-center gap-4">
                <Link
                    v-for="image in props.images"
                    :key="image.id"
                    :href="route('images.show', image.id)"
                    class="group relative h-60 w-60 shadow"
                >
                    <span class="absolute -right-3 -top-3 hidden items-center justify-center rounded-full bg-secondary p-2 text-secondary-foreground shadow-lg transition-all hover:scale-110 group-hover:flex">
                        <Pencil />
                    </span>

                    <img :src="image.preview_url" :alt="image.name" class="h-full w-full rounded-lg object-cover" />
                </Link>
            </div>
        </form>
    </AppLayout>
</template>
