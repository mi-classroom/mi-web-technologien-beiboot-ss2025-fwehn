<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { ref } from 'vue';

const isDragging = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    images: [] as File[],
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
    form.images =
        ([...(e.dataTransfer?.files || [])].filter((image) => image.type.startsWith('image/')) as File[]) || [];
    submitForm();
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
    <form
        @submit.prevent="submitForm"
        class="relative h-full w-full"
        @dragover.prevent
        @dragenter.prevent="handleDragEnter"
        @dragleave="handleDragLeave"
        @drop.prevent="handleDrop"
    >
        <div
            class="pointer-events-auto h-full w-full  inset-0 z-40 flex items-center justify-center rounded border-warm-medium border-s bg-lightest text-2xl font-semibold text-primary"
        >
            Datei hierher ziehen zum Hochladen

            <div class="">
                <button
                    type="button"
                    class="rounded-full bg-primary p-4 text-accent shadow-lg transition-all"
                    @click="fileInput?.click()"
                    :disabled="form.processing"
                >
                    <Plus />
                </button>

                <div v-if="form.errors.images" class="mt-2 bg-destructive text-sm">
                    {{ form.errors.images }}
                </div>
            </div>
        </div>

        <input
            type="file"
            multiple
            accept="image/*"
            class="hidden"
            ref="fileInput"
            @change="
                (e) => {
                    form.images =
                        ([...((e.target as HTMLInputElement).files || [])].filter((image) =>
                            image.type.startsWith('image/'),
                        ) as File[]) || [];

                    submitForm();
                }
            "
        />
    </form>
</template>

<style scoped></style>
