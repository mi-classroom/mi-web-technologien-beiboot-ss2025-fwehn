<script setup lang="ts">
import BaseInput from '@/components/ui/BaseInput.vue';
import { cn } from '@/lib/utils';
import { useVModel } from '@vueuse/core';

const props = defineProps<{
    modelValue?: string | null;
    defaultValue?: string;
    label: string;
    error?: string;
    id: string;
    disabled?: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const modelValue = useVModel(props, 'modelValue', emit, {
    passive: true,
    defaultValue: props.defaultValue ?? '',
});
</script>

<template>
    <BaseInput v-bind="props">
        <textarea
            :id="id"
            rows="3"
            placeholder=" "
            :disabled="disabled"
            v-model="modelValue"
            :class="
                cn(
                    'block w-full appearance-none rounded border-s bg-transparent py-2.5 pl-2 text-lg font-semibold focus:outline-none focus:ring-0',
                    !!error
                        ? 'border-secondary text-secondary'
                        : 'peer border-warm-medium text-warm-dark focus:border-primary',
                )
            "
        />
    </BaseInput>
</template>
