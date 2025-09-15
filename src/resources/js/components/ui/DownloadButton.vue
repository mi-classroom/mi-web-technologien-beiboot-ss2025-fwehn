<script setup lang="ts">
import { computed, ref } from 'vue';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
});

const format = ref('jpg');

const fullHref = computed(() => {
    const url = new URL(props.href, window.location.origin);
    url.searchParams.set('format', format.value);
    return url.toString();
});
</script>

<template>
    <div class="group inline-flex h-10 overflow-hidden rounded-md shadow-sm">
        <a
            :href="fullHref"
            class="flex h-full items-center bg-lightest px-4 text-sm font-medium text-warm-dark hover:bg-light hover:text-primary focus:ring-2 focus:ring-primary"
        >
            {{ $t('component.download_button.download') }}
        </a>
        <select
            v-model="format"
            class="h-full border-l-s border-warm-medium bg-lightest px-1 text-sm font-medium text-warm-dark group-hover:bg-light group-hover:text-primary group-focus:outline-none"
        >
            <option value="jpg">.jpg</option>
            <option value="png">.png</option>
            <option value="avif">.avif</option>
            <option value="webp">.webp</option>
        </select>
    </div>
</template>
