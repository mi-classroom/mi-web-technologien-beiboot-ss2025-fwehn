<script setup lang="ts">
import { cn } from '@/lib/utils';
import { ChevronDown } from 'lucide-vue-next';
import { computed, type HTMLAttributes, ref } from 'vue';

const props = defineProps<{
    modelValue: Record<string, string>;
    options: Record<string, string>[];
    class?: HTMLAttributes['class'];
}>();

const emit = defineEmits(['update:modelValue']);

const selected = computed({
    get() {
        return Object.keys(props.modelValue);
    },
    set(newSelected: string[]) {
        const newObj: Record<string, string> = {};
        for (const key of newSelected) {
            newObj[key] = '';
        }
        emit('update:modelValue', newObj);
    },
});

const open = ref(false);

const toggleDropdown = () => {
    open.value = !open.value;
};

const toggleSelectAll = () => {
    const allKeys = props.options.map((option) => Object.keys(option)[0]);
    if (selected.value.length === allKeys.length) {
        // Alles abwählen
        selected.value = [];
    } else {
        // Alles auswählen
        selected.value = allKeys;
    }
};
</script>

<template>
    <div :class="cn('relative inline-block text-left', props.class)">
        <button
            type="button"
            @click="toggleDropdown"
            class="button-primary flex h-full flex-row items-center justify-evenly gap-2 rounded px-4 py-2 shadow"
        >
            Felder wählen
            <ChevronDown />
        </button>

        <div v-if="open" class="absolute right-0 z-10 mt-2 max-h-96 w-64 overflow-y-auto rounded-md bg-light shadow-lg">
            <ul class="space-y-1 p-2 text-warm-dark">
                <li class="mb-2">
                    <button
                        type="button"
                        @click="toggleSelectAll"
                        class="button-primary w-full rounded px-2 py-2 text-center text-sm font-medium"
                    >
                        {{ selected.length === props.options.length ? 'Alle abwählen' : 'Alle auswählen' }}
                    </button>
                </li>
                <li v-for="option in options" :key="Object.keys(option)[0]">
                    <label class="flex cursor-pointer items-center space-x-2 rounded px-2 py-1 hover:bg-warm-light/50">
                        <input
                            type="checkbox"
                            class="form-checkbox"
                            :value="Object.keys(option)[0]"
                            v-model="selected"
                        />
                        <span>{{ Object.values(option)[0] }}</span>
                    </label>
                </li>
            </ul>
        </div>
    </div>
</template>
