<script setup lang="ts">
import BaseInput from '@/components/ui/BaseInput.vue';
import { cn } from '@/lib/utils';
import { useVModel } from '@vueuse/core';
import { X } from 'lucide-vue-next';

const props = defineProps<{
    modelValue: string[];
    label: string;
    error?: string;
    id: string;
    disabled?: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string[]): void;
}>();

const chips = useVModel(props, 'modelValue', emit, {
    passive: true,
    defaultValue: [],
});

const addChip = (event: KeyboardEvent) => {
    const input = event.target as HTMLInputElement;
    if (event.key === 'Enter') {
        if (input.value.trim()) {
            chips.value.push(input.value.trim());
            input.value = '';
        }
    }
};

const removeChip = (i: number) => {
    chips.value.splice(i, 1);
};
</script>

<template>
    <BaseInput v-bind="props">
        <div
            :class="
                cn(
                    'flex w-full appearance-none flex-wrap items-center gap-2 rounded border-s bg-transparent py-2.5 pl-2 text-lg font-semibold focus:outline-none focus:ring-0',
                    !!error
                        ? 'border-secondary text-secondary'
                        : 'peer border-warm-medium text-warm-dark focus:border-primary',
                )
            "
        >
            <span
                v-for="(chip, i) in chips"
                :key="i"
                class="flex cursor-pointer flex-row items-center gap-0.5 rounded bg-primary px-2 py-1 text-white"
                @click="removeChip(i)"
            >
                {{ chip }} <X />
            </span>
            <input
                :id="id"
                type="text"
                placeholder="Add..."
                :disabled="disabled"
                class="peer flex-1 bg-transparent py-2.5 focus:outline-none"
                @keydown.enter.prevent="addChip"
            />
        </div>
    </BaseInput>
</template>
