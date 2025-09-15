<script setup lang="ts">
import { cn } from '@/lib/utils';
import iptcCustomTypes from '@/types/iptc-custom-types';

const props = defineProps<{
    iptc: Iptc;
}>();

const filtered = Object.fromEntries(
    Object.entries(props.iptc)
        .filter(([fieldName, value]) => {
            if (!fieldName.startsWith('iptc_')) return false;

            switch (iptcCustomTypes[fieldName as keyof Iptc]) {
                case 'chips':
                    return (value ?? []).length > 0;
                default:
                    return !!value;
            }
        })
        .map(([fieldName, value]) => {
            switch (iptcCustomTypes[fieldName as keyof Iptc]) {
                case 'date':
                    return [fieldName, new Date(value).toISOString().slice(0, 10)];
                case 'time':
                    return [fieldName, new Date(value).toISOString().slice(11, 19)];
                default:
                    return [fieldName, value];
            }
        }),
);
</script>

<template>
    <p class="w-full overflow-hidden text-ellipsis whitespace-nowrap">
        <template v-for="(value, index) in Object.values(filtered)" :key="index">
            <template v-if="!Array.isArray(value)">{{ value }}</template>
            <template v-else>
                <template v-for="(chip, subIndex) in value" :key="subIndex">
                    <span
                        :class="cn('rounded-lg border-s px-2 py-1', index % 2 === 0 ? 'bg-primary' : 'bg-tertiary')"
                        >{{ chip }}</span
                    >
                    <template v-if="subIndex < value.length - 1">,&nbsp;</template>
                </template>
            </template>
            <template v-if="index < Object.values(filtered).length - 1">,&nbsp;</template>
        </template>
    </p>
</template>

<style scoped></style>
