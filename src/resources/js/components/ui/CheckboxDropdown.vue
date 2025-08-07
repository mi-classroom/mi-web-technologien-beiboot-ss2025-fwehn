<script setup lang="ts">
import { computed, type HTMLAttributes, ref } from 'vue';
import { ChevronDown } from 'lucide-vue-next';
import { cn } from '@/lib/utils';

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
    }
});

const open = ref(false);

const toggleDropdown = () => {
    open.value = !open.value;
};

const toggleSelectAll = () => {
    const allKeys = props.options.map(option => Object.keys(option)[0]);
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
            class="px-4 py-2 button-primary rounded shadow flex flex-row items-center justify-evenly gap-2 h-full"
        >
            Felder wählen
            <ChevronDown />
        </button>

        <div
            v-if="open"
            class="absolute right-0 z-10 mt-2 w-64 max-h-96 overflow-y-auto rounded-md bg-light shadow-lg"
        >
            <ul class="p-2 space-y-1 text-warm-dark">
                <li class="mb-2">
                    <button
                        type="button"
                        @click="toggleSelectAll"
                        class="w-full text-center px-2 py-2 rounded  text-sm font-medium button-primary"
                    >
                        {{ selected.length === props.options.length ? 'Alle abwählen' : 'Alle auswählen' }}
                    </button>
                </li>
                <li
                    v-for="option in options"
                    :key="Object.keys(option)[0]"
                >
                    <label class="flex items-center space-x-2 cursor-pointer hover:bg-warm-light/50 px-2 py-1 rounded">
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
