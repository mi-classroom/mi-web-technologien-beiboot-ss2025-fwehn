<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import DownloadIcon from '../../assets/downloadIcon.vue';
import DeleteIcon from '../../assets/DeleteIcon.vue';
import EditIcon from '../../assets/EditIcon.vue';

const props = defineProps(['image', 'active']);
</script>

<template>
    <div
        class="h-[113px] bg-lightest rounded flex items-stretch justify-stretch border-m"
        :class="active ? 'border-primary' : 'border-transparent hover:border-primary'"
    >
        <img
            :src="image.preview_url"
            :alt="image.name"
            class="object-contain w-[120px] h-[113px] bg-dark rounded-none rounded-l"
        />

        <Link :href="route('images.index', { selected: image.id })"
              class="flex-1 items-center justify-center text-darkest flex flex-col"
              preserve-scroll
        >
            <h2>{{image.name}}</h2>
            <h3>{{new Date(image.updated_at ?? image.created_at).toDateString()}}</h3>
        </Link>

        <div class="w-[120px] h-full text-black flex items-center justify-center gap-3xs">
            <Link :href="route('images.index', { selected: image.id })" class="imageBtn" preserve-scroll>
                <EditIcon />
            </Link>

            <Link :href="route('images.destroy', image.id)" method="delete" class="imageBtn">
                <DeleteIcon />
            </Link>

            <a type="button" download :href="route('images.export', image.id)" class="bg-transparent imageBtn">
                <DownloadIcon />
            </a>
        </div>
    </div>
</template>

<style scoped>
.imageBtn {
    @apply w-l h-l hover:border-darkest border-transparent border-s;
}
</style>
