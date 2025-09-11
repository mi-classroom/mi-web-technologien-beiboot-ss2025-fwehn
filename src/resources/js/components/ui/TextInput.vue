<script setup lang="ts">
import { useVModel } from '@vueuse/core';
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
    defaultValue?: string | number;
    modelValue?: unknown;
    class?: HTMLAttributes['class'];
    label: string;
    error?: string;
    id: string;
    password?: boolean;
}>();

const emits = defineEmits<{
    (e: 'update:modelValue', payload: string | number): void;
}>();

const modelValue = useVModel(props, 'modelValue', emits, {
    passive: true,
    defaultValue: props.defaultValue,
});
</script>

<template>
    <div :class="class">
        <div class="relative mt-2">
            <input
                :type="password ? 'password' : 'text'"
                :id="id"
                placeholder=" "
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
            <label
                :for="id"
                :class="
                    cn(
                        'absolute top-3 z-10 ml-1 origin-[0] -translate-y-7 scale-75 transform select-none bg-warm-light px-1 text-lg duration-300 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-focus:start-0 peer-focus:-translate-y-7 peer-focus:scale-75 rtl:peer-focus:left-auto rtl:peer-focus:translate-x-1/4',
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
    </div>
</template>

<style scoped></style>
