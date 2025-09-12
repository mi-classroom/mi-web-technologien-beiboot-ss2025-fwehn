<script setup lang="ts">
import BaseInput from '@/components/ui/BaseInput.vue';
import { cn } from '@/lib/utils';
import { useVModel } from '@vueuse/core';
import { computed } from 'vue';

const props = defineProps<{
    modelValue: Date | null;
    label: string;
    error?: string;
    id: string;
    disabled?: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: Date | undefined): void;
}>();

const modelValue = useVModel(props, 'modelValue', emit, { passive: true });

const inputValue = computed({
    get: () => (modelValue.value ? modelValue.value.toISOString().slice(0, 10) : ''),
    set: (val: string) => {
        if (!val) {
            modelValue.value = null;
            return;
        }
        const [y, m, d] = val.split('-').map(Number);
        modelValue.value = new Date(Date.UTC(y, m - 1, d));
    },
});
</script>

<template>
    <BaseInput v-bind="props">
        <input
            :id="id"
            type="date"
            placeholder=" "
            :disabled="disabled"
            v-model="inputValue"
            :class="
                cn(
                    'block w-full appearance-none rounded border-s bg-transparent px-2 py-2.5 text-lg font-semibold focus:outline-none focus:ring-0',
                    !!error
                        ? 'border-secondary text-secondary'
                        : 'peer border-warm-medium text-warm-dark focus:border-primary',
                )
            "
        />
    </BaseInput>
</template>
