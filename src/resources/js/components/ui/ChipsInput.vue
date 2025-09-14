<script setup lang="ts">
import BaseInput from '@/components/ui/BaseInput.vue';
import { cn } from '@/lib/utils';
import { useVModel } from '@vueuse/core';
import { X } from 'lucide-vue-next';
import { ref } from 'vue';

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

const inputText = ref('');

const addChip = (event: KeyboardEvent) => {
    if (event.key === 'Enter') {
        if (inputText.value.trim()) {
            chips.value.push(inputText.value.trim());
            inputText.value = '';
        }
    }
};

const removeChip = (i: number) => {
    chips.value.splice(i, 1);
};
</script>

<template>
    <BaseInput
        v-bind="props"
        :class="
            cn(
                'rounded border-s',
                !!error
                    ? 'border-secondary text-secondary'
                    : 'border-warm-medium text-warm-dark focus-within:border-primary',
            )
        "
    >
        <div class="flex flex-wrap items-center gap-2 pt-2.5" v-if="chips.length > 0">
            <span
                v-for="(chip, i) in chips"
                :key="i"
                :class="
                    cn(
                        'flex cursor-pointer flex-row items-center gap-0.5 rounded bg-primary px-2 py-1 text-white',
                        i === 0 ? 'ml-2' : '',
                    )
                "
                @click="removeChip(i)"
            >
                {{ chip }} <X />
            </span>
        </div>

        <input
            v-model="inputText"
            :id="id"
            type="text"
            :placeholder="chips.length === 0 ? '' : undefined"
            :disabled="disabled"
            :class="
                cn(
                    'block w-full appearance-none bg-transparent py-2.5 pl-2 text-lg font-semibold focus:outline-none focus:ring-0',
                    chips.length > 0 ? 'placeholder:text-warm-medium' : 'focus:placeholder:text-warm-medium',
                    !!error
                        ? 'border-secondary text-secondary'
                        : 'peer border-warm-medium text-warm-dark focus:border-primary',
                )
            "
            @keydown.enter.prevent="addChip"
        />

        <span
            v-if="chips.length > 0 && inputText.length === 0"
            class="pointer-events-none absolute bottom-2 left-2 text-lg text-warm-medium"
        >
            Add...
        </span>
    </BaseInput>
</template>
