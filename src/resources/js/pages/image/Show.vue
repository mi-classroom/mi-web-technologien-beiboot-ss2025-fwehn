<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import MainLayout from '@/layouts/MainLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps(['image']);
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Images', href: '/images' },
    {
        title: props.image.name,
        href: `/images/${props.image.id}`,
    },
];
const form = useForm({ ...props.image });
</script>

<template>
    <Head title="Images" />
    <MainLayout :breadcrumbs="breadcrumbs">
        <form
            @submit.prevent="() => form.put(route('images.update', image.id), { preserveState: false })"
            class="flex h-full flex-col items-stretch justify-stretch gap-2"
        >
            <div class="grid gap-2  p-4">
                <Label for="index">Name</Label>
                <Input id="index" class="mt-1 block w-full" v-model="form.name" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="flex min-h-0 flex-grow">
                <div class="flex h-full w-1/2 flex-col justify-between border-2">
                    <img
                        :src="image.preview_url"
                        :alt="image.name"
                        class="w-full flex-grow self-center rounded-lg object-contain"
                    />
                    <div class="flex flex-row justify-center gap-2 p-4">
                        <Button
                            type="button"
                            @click.prevent="form.delete(route('images.destroy', image.id))"
                            variant="destructive"
                        >
                            Delete
                        </Button>
                        <Button
                            type="button"
                            as="a"
                            download
                            :href="route('images.export', image.id)"
                            variant="secondary"
                        >
                            Download
                        </Button>
                        <Button type="submit" variant="default"> Submit</Button>
                    </div>
                </div>

                <div class="h-full overflow-y-auto w-1/2 p-4 flex flex-col gap-2">
                    <div
                        v-for="iptcKey in Object.keys(form).filter((key) => key.startsWith('iptc'))"
                        :key="iptcKey"
                        class="grid gap-2"
                    >
                        <Label for="index">{{ iptcKey }}</Label>
                        <Input id="index" class="mt-1 block w-full" v-model="form[iptcKey]" />
                        <InputError class="mt-2" :message="form.errors[iptcKey]" />
                    </div>
                </div>
            </div>
        </form>
    </MainLayout>
</template>
