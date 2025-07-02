<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import DeleteIcon from '../../assets/DeleteIcon.vue';
import DownloadIcon from '../../assets/downloadIcon.vue';
import EditIcon from '../../assets/EditIcon.vue';

const props = defineProps(['image', 'active']);
</script>

<template>
    <div
        class="flex h-[113px] items-stretch justify-stretch rounded border-m bg-lightest"
        :class="active ? 'border-primary' : 'border-transparent hover:border-primary'"
    >
        <img
            :src="image.preview_url"
            :alt="image.name"
            class="h-[113px] w-[120px] rounded-none rounded-l bg-dark object-contain"
        />

        <Link
            :href="route('images.index', { selected: image.id })"
            class="flex flex-1 flex-col items-center justify-center text-darkest"
            preserve-scroll
        >
            <h2>{{ image.name }}</h2>
            <h3>{{ new Date(image.updated_at ?? image.created_at).toDateString() }}</h3>
        </Link>

        <div class="flex h-full w-[120px] items-center justify-center gap-3xs text-black">
            <Link :href="route('images.index', { selected: image.id })" class="imageBtn" preserve-scroll>
                <EditIcon />
            </Link>

            <Link :href="route('images.destroy', image.id)" method="delete" class="imageBtn">
                <DeleteIcon />
            </Link>

            <a type="button" download :href="route('images.export', image.id)" class="imageBtn bg-transparent">
                <DownloadIcon />
            </a>
        </div>
    </div>
</template>

<style scoped>
.imageBtn {
    @apply h-l w-l border-s border-transparent hover:border-darkest;
}
</style>
