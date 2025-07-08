<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    href: {
        type: String,
        required: true
    }
});

const format = ref('jpg');

const fullHref = computed(() => {
    const url = new URL(props.href, window.location.origin);
    url.searchParams.set('format', format.value);
    return url.toString();
});
</script>

<template>
    <div class="inline-flex rounded-md shadow-sm overflow-hidden h-10 group">
        <a
            :href="fullHref"
            class="px-4 text-sm font-medium text-warm-dark bg-lightest hover:bg-light hover:text-primary
             focus:ring-2 focus:ring-primary h-full flex items-center"
        >
            Download
        </a>
        <select
            v-model="format"
            class="px-1 text-sm font-medium text-warm-dark bg-lightest border-l-s border-warm-medium h-full
             group-hover:bg-light group-hover:text-primary
             group-focus:outline-none "
        >
            <option value="jpg">.jpg</option>
            <option value="png">.png</option>
            <option value="avif">.avif</option>
            <option value="webp">.webp</option>
        </select>
    </div>
</template>
