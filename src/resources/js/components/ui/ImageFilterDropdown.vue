<script lang="ts" setup>
import { cn } from '@/lib/utils';
import { router, usePage } from '@inertiajs/vue3';
import { Check, LucideFunnel, Slash, X } from 'lucide-vue-next';
import { type HTMLAttributes, onMounted, reactive, ref } from 'vue';

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();

// type Options = {
//     iptc_object_attribute_reference: boolean | null;
//     iptc_object_name: boolean | null;
//     iptc_subject_reference: boolean | null;
//     iptc_keywords: boolean | null;
//     iptc_special_instructions: boolean | null;
//     iptc_date_created: boolean | null;
//     iptc_time_created: boolean | null;
//     iptc_byline: boolean | null;
//     iptc_byline_title: boolean | null;
//     iptc_city: boolean | null;
//     iptc_sub_location: boolean | null;
//     iptc_province_state: boolean | null;
//     iptc_country_primary_location_code: boolean | null;
//     iptc_country_primary_location_name: boolean | null;
//     iptc_original_transmission_reference: boolean | null;
//     iptc_headline: boolean | null;
//     iptc_credit: boolean | null;
//     iptc_source: boolean | null;
//     iptc_copyright_notice: boolean | null;
//     iptc_caption_abstract: boolean | null;
//     iptc_writer_editor: boolean | null;
//     iptc_application_record_version: boolean | null;
// };

const options = reactive({
    iptc_object_attribute_reference: null,
    iptc_object_name: null,
    iptc_subject_reference: null,
    iptc_keywords: null,
    iptc_special_instructions: null,
    iptc_date_created: null,
    iptc_time_created: null,
    iptc_byline: null,
    iptc_byline_title: null,
    iptc_city: null,
    iptc_sub_location: null,
    iptc_province_state: null,
    iptc_country_primary_location_code: null,
    iptc_country_primary_location_name: null,
    iptc_original_transmission_reference: null,
    iptc_headline: null,
    iptc_credit: null,
    iptc_source: null,
    iptc_copyright_notice: null,
    iptc_caption_abstract: null,
    iptc_writer_editor: null,
    iptc_application_record_version: null,
});

const open = ref(false);
const toggleDropdown = () => {
    open.value = !open.value;
};

onMounted(() => {
    const searchParams = new URLSearchParams(usePage().url.split('?')[1] || '');

    searchParams.forEach((val, key) => {
        if (key in options) {
            (options as Record<string, boolean | null>)[key] = val === 'true';
        }
    });
});
</script>

<template>
    <div :class="cn('relative inline-block text-left', props.class)">
        <button type="button" @click="toggleDropdown">
            <LucideFunnel />
        </button>

        <div v-if="open" class="absolute right-0 mt-2 min-w-64 overflow-y-auto rounded-md bg-light p-4 shadow-lg z-50">
            <table class="space-y-1 p-2 text-warm-dark">
                <tbody>
                    <tr v-for="(value, option) in options" :key="option">
                        <td class="whitespace-nowrap py-1 pr-4 text-warm-dark">{{ $t(`iptc.` + option) }}</td>
                        <td class="flex gap-1">
                            <label
                                class="flex h-7 w-7 cursor-pointer items-center justify-center rounded-sm border border-green-600 p-1"
                                :class="{ 'bg-green-600 text-white': value === true }"
                            >
                                <Check />
                                <input type="radio" class="hidden" v-model="options[option]" :value="true" />
                            </label>

                            <label
                                class="flex h-7 w-7 cursor-pointer items-center justify-center rounded-sm border border-gray-500 p-1"
                                :class="{ 'bg-gray-500 text-white': value === null }"
                            >
                                <Slash />
                                <input type="radio" class="hidden" v-model="options[option]" :value="null" />
                            </label>

                            <label
                                class="flex h-7 w-7 cursor-pointer items-center justify-center rounded-sm border border-red-600 p-1"
                                :class="{ 'bg-red-600 text-white': value === false }"
                            >
                                <X />
                                <input type="radio" class="hidden" v-model="options[option]" :value="false" />
                            </label>
                        </td>
                    </tr>
                </tbody>
            </table>

            <button
                class="button-primary mt-2 w-full rounded-md p-2"
                @click="
                    () => {
                        const filtered = Object.fromEntries(
                            Object.entries(options).filter(([_, v]) => v === true || v === false),
                        );

                        router.get(route('images.index'), filtered);
                    }
                "
            >
                Anwenden
            </button>
        </div>
    </div>
</template>
