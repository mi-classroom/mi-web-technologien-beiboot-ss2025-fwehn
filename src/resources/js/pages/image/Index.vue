<script setup lang="ts">
import BaseModal from '@/components/ui/BaseModal.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import { cn } from '@/lib/utils';
import type { BreadcrumbItem } from '@/types';
import { FolderOperation } from '@/types/enums';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Images', href: '/images' }];
const { images } = defineProps(['images']);

const isDragging = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);
const pendingFiles = ref<File[]>([]);
const showModal = ref(false);

const form = useForm<{
    image: File | null;
    operation: 'save' | 'propagate' | 'merge';
}>({
    image: null,
    operation: 'save',
});

const open = () => {
    showModal.value = true;
};

const close = () => {
    showModal.value = false;
};

const handleDrop = (e: DragEvent) => {
    isDragging.value = false;
    if (e.dataTransfer?.files) {
        const validImages = Array.from(e.dataTransfer.files).filter((file) => file.type.startsWith('image/'));
        if (validImages.length > 0) {
            pendingFiles.value = validImages;
            open();
        }
    }
};

const handleFileInputChange = (e: Event) => {
    const files = (e.target as HTMLInputElement)?.files;
    if (!files) return;

    const validImages = Array.from(files).filter((file) => file.type.startsWith('image/'));
    if (validImages.length > 0) {
        pendingFiles.value = validImages;
        open();
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

const confirmUpload = async (operation: FolderOperation) => {
    close();

    for (const file of pendingFiles.value) {
        form.image = file;
        form.operation = operation;

        await new Promise<void>((resolve) => {
            form.post(route('images.store'), {
                forceFormData: true,
                preserveScroll: true,
                headers: { Accept: 'application/json' },
                onError: () => resolve(),
                onSuccess: () => resolve(),
            });
        });

        form.reset();
    }

    pendingFiles.value = [];
};

const selectedImages = ref<number[]>([]);
const lastSelectedIndex = ref<number | null>(null);

const toggleSelection = (imageId: number, event?: MouseEvent) => {
    const index = images.findIndex((img: any) => img.id === imageId);

    if (event?.shiftKey && lastSelectedIndex.value !== null) {
        const start = Math.min(lastSelectedIndex.value, index);
        const end = Math.max(lastSelectedIndex.value, index);
        const rangeIds = images.slice(start, end + 1).map((img: any) => img.id);
        const merged = new Set([...selectedImages.value, ...rangeIds]);
        selectedImages.value = Array.from(merged);
    } else if (event?.ctrlKey || event?.metaKey) {
        const pos = selectedImages.value.indexOf(imageId);
        if (pos === -1) {
            selectedImages.value.push(imageId);
        } else {
            selectedImages.value.splice(pos, 1);
        }
        lastSelectedIndex.value = index;
    } else {
        selectedImages.value = [imageId];
        lastSelectedIndex.value = index;
    }
};
</script>

<template>
    <Head title="Images" />

    <MainLayout :breadcrumbs="breadcrumbs">
        <form
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
                @change="handleFileInputChange"
            />

            <div class="fixed bottom-8 right-8 z-50 flex flex-col items-center justify-end gap-4">
                <Link
                    as="button"
                    v-if="selectedImages.length > 0"
                    :href="
                        selectedImages.length === 1
                            ? route('images.edit', selectedImages[0])
                            : route('images.edit-selection', { images: selectedImages })
                    "
                    class="button-primary rounded-full p-4 shadow-lg transition-all"
                >
                    <Pencil />
                </Link>

                <Link
                    as="button"
                    v-if="selectedImages.length > 0"
                    :href="
                        selectedImages.length === 1
                            ? route('images.destroy', selectedImages[0])
                            : route('images.destroy-selection', { images: selectedImages })
                    "
                    method="delete"
                    class="button-primary rounded-full p-4 shadow-lg transition-all"
                    @success="selectedImages = []"
                >
                    <Trash2 />
                </Link>

                <button
                    type="button"
                    class="button-primary rounded-full p-4 shadow-lg transition-all"
                    @click="fileInput?.click()"
                    :disabled="form.processing"
                >
                    <Plus />
                </button>

                <div v-if="form.errors.image" class="bg-destructive mt-2 text-sm">
                    {{ form.errors.image }}
                </div>
            </div>

            <div v-if="images.length > 0" class="flex h-full w-full flex-col gap-2 p-4">
                <div
                    v-for="image in images"
                    :key="image.id"
                    class="flex w-full cursor-pointer select-none rounded-md border-m"
                    :class="selectedImages.includes(image.id) ? 'border-primary' : 'border-warm-medium'"
                    @click="(e) => toggleSelection(image.id, e)"
                >
                    <input
                        type="checkbox"
                        class="hidden"
                        :checked="selectedImages.includes(image.id)"
                        @change.prevent
                    />

                    <img
                        class="h-40 min-h-40 w-40 min-w-40 bg-warm-medium object-contain p-0"
                        :src="route('images.preview', { image: image.id, w: 200 })"
                        :srcset="`
                            ${route('images.preview', { image: image.id, w: 200 })} 200w,
                            ${route('images.preview', { image: image.id, w: 400 })} 400w,
                            ${route('images.preview', { image: image.id, w: 800 })} 800w,
                            ${route('images.preview', { image: image.id, w: 1200 })} 1200w
                        `"
                        sizes="200px"
                        :alt="image.name"
                        fetchpriority="high"
                        loading="lazy"
                    />
                    <div class="flex h-full w-full flex-col justify-center pl-4">
                        <h1 class="text-xl font-semibold">{{ image.name }}</h1>
                        <!--TODO-->
                        <p>{{ image.iptc?.iptc_date_created ?? '-' }}</p>
                    </div>
                    <div class="flex items-center justify-center px-8">
                        <span
                            :class="
                                cn(
                                    'rounded-full border-2 px-2 py-1 text-center text-lightest',
                                    (image.fill_percent ?? 0) <= 33
                                        ? 'bg-secondary'
                                        : (image.fill_percent ?? 0) < 100
                                          ? 'bg-primary'
                                          : 'bg-tertiary',
                                )
                            "
                        >
                            {{ image.fill_percent ?? 0 }}%
                        </span>
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

        <BaseModal :open="showModal" @cancel="close">
            <h2 class="mb-4 text-lg font-bold">Sollen Ordner-Standards auf die neuen Bilder angewandt werden?</h2>
            <div class="flex flex-col gap-3">
                <button
                    @click="confirmUpload(FolderOperation.SAVE)"
                    class="button-primary h-10 rounded-md px-4 align-middle"
                >
                    Bilder unverändert hochladen
                </button>
                <button
                    @click="confirmUpload(FolderOperation.PROPAGATE)"
                    class="button-primary h-10 rounded-md px-4 align-middle"
                >
                    Bilderdaten mit Ordner-Standards überschreiben
                </button>
                <button
                    @click="confirmUpload(FolderOperation.MERGE)"
                    class="button-primary h-10 rounded-md px-4 align-middle"
                >
                    Fehlende Bilderdaten mit Ordner-Standards besetzen
                </button>
                <button
                    @click="
                        () => {
                            pendingFiles = [];
                            close();
                        }
                    "
                    class="button-secondary h-10 w-full rounded-md px-4 align-middle"
                >
                    Abbrechen
                </button>
            </div>
        </BaseModal>
    </MainLayout>
</template>
