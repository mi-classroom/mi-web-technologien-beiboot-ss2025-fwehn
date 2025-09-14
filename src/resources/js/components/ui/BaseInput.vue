<script setup lang="ts">
import { cn } from '@/lib/utils';
import { useVModel } from '@vueuse/core';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
    modelValue: unknown;
    defaultValue?: unknown;
    class?: HTMLAttributes['class'];
    label: string;
    error?: string;
    id: string;
    disabled?: boolean;
}>();

const emits = defineEmits<{
    (e: 'update:modelValue', value: unknown): void;
}>();

const modelValue = useVModel(props, 'modelValue', emits, {
    passive: true,
    defaultValue: props.defaultValue,
});
</script>

<template>
    <div :class="cn(props.class, 'relative mt-2')">
        <slot :model-value="modelValue" :id="id" :disabled="disabled" />
        <label
            :for="id"
            :class="
                cn(
                    'absolute top-3 z-10 ml-1 origin-[0] -translate-y-7 scale-75 transform cursor-text select-none bg-warm-light px-1 text-lg duration-300 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-focus:-translate-y-7 peer-focus:scale-75',
                    !!error ? 'text-secondary' : 'text-warm-dark peer-focus:text-primary',
                )
            "
        >
            {{ label }}
        </label>
    </div>
    <p v-if="!!error" class="my-1 text-xs text-secondary">
        {{ error }}
    </p>
</template>
