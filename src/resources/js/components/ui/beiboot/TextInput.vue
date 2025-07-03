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
}>();

const emits = defineEmits<{
    (e: 'update:modelValue', payload: string | number): void;
}>();

const modelValue = useVModel(props, 'modelValue', emits, {
    passive: true,
    defaultValue: props.defaultValue
});
</script>


<template>
    <div>
        <div class="relative mt-2">
            <input type="text"
                   :id="id"
                   placeholder=" "
                   v-model="modelValue"
                   :class="cn(
                       'block py-2.5 pl-2 w-full text-lg  bg-transparent   focus:outline-none focus:ring-0 border-s rounded  font-semibold appearance-none',
                        !!error ? 'text-secondary border-secondary': 'text-warm-dark border-warm-medium focus:border-primary peer'
                   )"
            />
            <label :for="id"
                   :class="cn(
                       'absolute select-none text-lg ml-1 bg-warm-light px-1 duration-300 transform -translate-y-7 scale-75 top-3 z-10 origin-[0] peer-focus:start-0  peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-7 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto',
                       !!error ? 'text-secondary' : 'text-warm-dark peer-focus:text-primary'
                   )"
            >
                {{ label }}
            </label>
        </div>
        <p v-if="!!error" class="my-1 text-xs text-secondary">
            {{ error }}
        </p>
    </div>
</template>


<style scoped>
</style>
