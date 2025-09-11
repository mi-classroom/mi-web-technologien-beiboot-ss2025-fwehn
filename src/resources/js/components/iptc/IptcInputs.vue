<script setup lang="ts">
import TextInput from '@/components/ui/TextInput.vue';
import { reactive, watch } from 'vue';

const props = defineProps<{
    modelValue: Iptc;
    editableFields?: Record<string, boolean>;
    errors?: Record<string, string>;
    selectable?: boolean;
    class?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: Iptc): void;
    (e: 'update:editableFields', value: Record<string, boolean>): void;
}>();

const iptcFields = [
    'iptc_object_attribute_reference',
    'iptc_object_name',
    'iptc_subject_reference',
    'iptc_keywords',
    'iptc_special_instructions',
    'iptc_date_created',
    'iptc_time_created',
    'iptc_byline',
    'iptc_byline_title',
    'iptc_city',
    'iptc_sub_location',
    'iptc_province_state',
    'iptc_country_primary_location_code',
    'iptc_country_primary_location_name',
    'iptc_original_transmission_reference',
    'iptc_headline',
    'iptc_credit',
    'iptc_source',
    'iptc_copyright_notice',
    'iptc_caption_abstract',
    'iptc_writer_editor',
    'iptc_application_record_version',
];

const localModel = reactive({ ...props.modelValue });
const localEditable = reactive({ ...(props.editableFields || {}) });

watch(localModel, (val) => emit('update:modelValue', val), { deep: true });
watch(localEditable, (val) => emit('update:editableFields', val), { deep: true });
</script>

<template>
    <div :class="props.class">
        <div v-for="key in iptcFields" :key="key" class="grid gap-3">
            <div class="flex flex-row items-center gap-2">
                <template v-if="props.selectable">
                    <input type="checkbox" v-model="localEditable[key]" class="accent-primary" />
                </template>

                <TextInput
                    v-model="localModel[key]"
                    :id="key"
                    :label="$t(`iptc.` + key)"
                    :error="props.errors?.[key]"
                    :disabled="props.selectable && !localEditable[key]"
                    class="flex-grow"
                />
            </div>
        </div>
    </div>
</template>
