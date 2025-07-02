<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { useForm } from '@inertiajs/vue3';

const props = defineProps(['image']);
const form = useForm({ ...props.image });
</script>

<template>
    <form
        @submit.prevent="() => form.put(route('images.update', image.id), { preserveState: false })"
        class="flex h-full flex-col justify-start gap-xxl"
    >
        <img
            :src="image.preview_url"
            :alt="image.name"
            class="max-h-[60vh] w-full self-center rounded border-m border-primary bg-lightest object-contain"
        />

        <table class="w-full border-b-s border-t-s border-warm-medium text-sm">
            <tbody>
                <tr
                    v-for="iptcKey in Object.keys(form).filter((key) => key.startsWith('iptc'))"
                    :key="iptcKey"
                    class="border-t-s border-warm-medium"
                >
                    <th class="w-1/4 px-4 py-3 text-right font-semibold text-gray-800">
                        {{ iptcKey }}
                    </th>
                    <td class="px-4 py-3 text-left">
                        <input
                            class="block w-full border border-transparent bg-transparent px-3 py-2 text-gray-800 focus:border-blue-300 focus:outline-none focus:ring"
                            v-model="form[iptcKey]"
                        />
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="flex flex-row justify-center gap-2">
            <Button type="submit" variant="default"> Submit</Button>
        </div>
    </form>
</template>

<style scoped></style>
