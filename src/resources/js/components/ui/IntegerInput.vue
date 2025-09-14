<script setup lang="ts">
import BaseInput from '@/components/ui/BaseInput.vue';
import { cn } from '@/lib/utils';
import { useVModel } from '@vueuse/core';

const props = defineProps<{
    modelValue?: number | null;
    defaultValue?: number;
    label: string;
    error?: string;
    id: string;
    disabled?: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: number): void;
}>();

const modelValue = useVModel(props, 'modelValue', emit, {
    passive: true,
    defaultValue: props.defaultValue ?? undefined,
});
</script>

<template>
    <BaseInput v-bind="props">
        <input
            :id="id"
            type="number"
            step="1"
            placeholder=" "
            :disabled="disabled"
            v-model.number="modelValue"
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
