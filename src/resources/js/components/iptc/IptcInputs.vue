<script setup lang="ts">
import ChipsInput from '@/components/ui/ChipsInput.vue';
import DateInput from '@/components/ui/DateInput.vue';
import IntegerInput from '@/components/ui/IntegerInput.vue';
import TextareaInput from '@/components/ui/TextareaInput.vue';
import TextInput from '@/components/ui/TextInput.vue';
import TimeInput from '@/components/ui/TimeInput.vue';
import iptcCustomTypes from '@/types/iptc-custom-types';
import { reactive, watch } from 'vue';

const props = defineProps<{
    modelValue: IptcForm;
    editableFields?: Record<string, boolean>;
    errors?: Record<string, string>;
    selectable?: boolean;
    class?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: IptcForm): void;
    (e: 'update:editableFields', value: Record<string, boolean>): void;
}>();

const localModel = reactive(props.modelValue);
const localEditable = reactive({ ...(props.editableFields || {}) });

watch(localModel, (val) => emit('update:modelValue', val), { deep: true });
watch(localEditable, (val) => emit('update:editableFields', val), { deep: true });
watch(
    () => props.modelValue,
    (val) => {
        Object.assign(localModel, val);
    },
    { deep: true },
);
</script>

<template>
    <div :class="props.class">
        <div v-for="(type, key) in iptcCustomTypes" :key="key" class="grid gap-3">
            <div class="flex flex-row items-center gap-2">
                <template v-if="props.selectable">
                    <input
                        type="checkbox"
                        v-model="localEditable[key]"
                        class="h-6 w-6 cursor-pointer rounded-md border-warm-medium accent-primary transition-all duration-200 checked:bg-primary checked:text-white"
                    />
                </template>

                <IntegerInput
                    v-if="type === 'integer'"
                    :id="key"
                    :label="$t(`iptc.` + key)"
                    :error="props.errors?.[`iptc.` + key]"
                    v-model="localModel[key] as number | null | undefined"
                    :disabled="props.selectable && !localEditable[key]"
                    class="flex-grow"
                />

                <DateInput
                    v-else-if="type === 'date'"
                    :id="key"
                    :label="$t(`iptc.` + key)"
                    :error="props.errors?.[`iptc.` + key]"
                    v-model="localModel[key] as unknown as Date | null | undefined"
                    :disabled="props.selectable && !localEditable[key]"
                    class="flex-grow"
                />

                <TimeInput
                    v-else-if="type === 'time'"
                    :id="key"
                    :label="$t(`iptc.` + key)"
                    :error="props.errors?.[`iptc.` + key]"
                    v-model="localModel[key] as unknown as Date | null | undefined"
                    :disabled="props.selectable && !localEditable[key]"
                    class="flex-grow"
                />

                <ChipsInput
                    v-else-if="type === 'chips'"
                    :id="key"
                    :label="$t(`iptc.` + key)"
                    :error="props.errors?.[`iptc.` + key]"
                    v-model="localModel[key] as string[] | undefined"
                    :disabled="props.selectable && !localEditable[key]"
                    class="flex-grow"
                />

                <TextareaInput
                    v-else-if="type === 'textarea'"
                    :id="key"
                    :label="$t(`iptc.` + key)"
                    :error="props.errors?.[`iptc.` + key]"
                    v-model="localModel[key] as string | null | undefined"
                    :disabled="props.selectable && !localEditable[key]"
                    class="flex-grow"
                />

                <TextInput
                    v-else
                    v-model="localModel[key] as string | null | undefined"
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
