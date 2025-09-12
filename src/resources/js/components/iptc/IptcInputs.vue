<script setup lang="ts">
import ChipsInput from '@/components/ui/ChipsInput.vue';
import DateInput from '@/components/ui/DateInput.vue';
import IntegerInput from '@/components/ui/IntegerInput.vue';
import TextareaInput from '@/components/ui/TextareaInput.vue';
import TextInput from '@/components/ui/TextInput.vue';
import TimeInput from '@/components/ui/TimeInput.vue';
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

const iptcFields = {
    iptc_object_attribute_reference: 'text',
    iptc_object_name: 'text',
    iptc_subject_reference: 'chips',
    iptc_keywords: 'chips',
    iptc_special_instructions: 'textarea',
    iptc_date_created: 'date',
    iptc_time_created: 'time',
    iptc_byline: 'text',
    iptc_byline_title: 'text',
    iptc_city: 'text',
    iptc_sub_location: 'text',
    iptc_province_state: 'text',
    iptc_country_primary_location_code: 'text',
    iptc_country_primary_location_name: 'text',
    iptc_original_transmission_reference: 'text',
    iptc_headline: 'text',
    iptc_credit: 'text',
    iptc_source: 'text',
    iptc_copyright_notice: 'text',
    iptc_caption_abstract: 'textarea',
    iptc_writer_editor: 'text',
    iptc_application_record_version: 'integer',
};

const localModel = reactive({ ...props.modelValue });
const localEditable = reactive({ ...(props.editableFields || {}) });

watch(localModel, (val) => emit('update:modelValue', val), { deep: true });
watch(localEditable, (val) => emit('update:editableFields', val), { deep: true });
</script>

<template>
    <div :class="props.class">
        <div v-for="(type, key) in iptcFields" :key="key" class="grid gap-3">
            <div class="flex flex-row items-center gap-2">
                <template v-if="props.selectable">
                    <input type="checkbox" v-model="localEditable[key]" class="accent-primary" />
                </template>

                <IntegerInput
                    v-if="type === 'integer'"
                    :id="key"
                    :label="$t(`iptc.` + key)"
                    :error="props.errors?.[`iptc.` + key]"
                    v-model="localModel[key] as number | null"
                    :disabled="props.selectable && !localEditable[key]"
                    class="flex-grow"
                />

                <DateInput
                    v-else-if="type === 'date'"
                    :id="key"
                    :label="$t(`iptc.` + key)"
                    :error="props.errors?.[`iptc.` + key]"
                    v-model="localModel[key] as unknown as Date | null"
                    :disabled="props.selectable && !localEditable[key]"
                    class="flex-grow"
                />

                <TimeInput
                    v-else-if="type === 'time'"
                    :id="key"
                    :label="$t(`iptc.` + key)"
                    :error="props.errors?.[`iptc.` + key]"
                    v-model="localModel[key] as unknown as Date | null"
                    :disabled="props.selectable && !localEditable[key]"
                    class="flex-grow"
                />

                <ChipsInput
                    v-else-if="type === 'chips'"
                    :id="key"
                    :label="$t(`iptc.` + key)"
                    :error="props.errors?.[`iptc.` + key]"
                    v-model="localModel[key] as string[]"
                    :disabled="props.selectable && !localEditable[key]"
                    class="flex-grow"
                />

                <TextareaInput
                    v-else-if="type === 'textarea'"
                    :id="key"
                    :label="$t(`iptc.` + key)"
                    :error="props.errors?.[`iptc.` + key]"
                    v-model="localModel[key] as string | null"
                    :disabled="props.selectable && !localEditable[key]"
                    class="flex-grow"
                />

                <TextInput
                    v-else
                    v-model="localModel[key] as string | null"
                    :id="key"
                    :label="$t(`iptc.` + key)"
                    :error="props.errors?.[`iptc.` + key]"
                    :disabled="props.selectable && !localEditable[key]"
                    class="flex-grow"
                />
            </div>
        </div>
    </div>
</template>
