<script setup lang="ts">
import BaseModal from '@/components/ui/BaseModal.vue';
import TextInput from '@/components/ui/TextInput.vue';
import { router } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';

const props = defineProps<{
    modelValue: IptcForm;
}>();

const localModel = ref(props.modelValue);
const presetName = ref('');
const fileInput = ref<HTMLInputElement | null>(null);

type Actions = 'select' | 'store' | 'import';

const action = ref<Actions>('select');

const showModal = ref(false);

function open() {
    switch (action.value) {
        case 'import':
            fileInput.value?.click();
            break;
        default:
            showModal.value = true;
    }
}

function close() {
    showModal.value = false;
}

const presets = ref<Preset[]>([]);
const loading = ref(false);

async function loadPresets() {
    loading.value = true;
    try {
        presets.value = await (await fetch(route('presets.list'))).json();
    } finally {
        loading.value = false;
    }
}

async function importPreset(event: Event) {
    const target = event.target as HTMLInputElement;
    if (!target.files?.length) return;

    const file = target.files[0];
    if (file.type !== 'application/json') {
        // TODO mit snackbar austauschen
        alert('Bitte nur JSON-Dateien hochladen!');
        return;
    }

    try {
        const text = await file.text();
        const data = JSON.parse(text);
        Object.keys(localModel.value).forEach((key) => delete localModel.value[key as keyof Iptc]);
        Object.assign(localModel.value, {
            ...(data ?? {}),
            iptc_date_created: data?.iptc_date_created ? new Date(data.iptc_date_created) : null,
            iptc_time_created: data?.iptc_time_created ? new Date(data.iptc_time_created) : null,
        });
    } catch (err) {
        console.error(err);
        // TODO mit snackbar austauschen
        alert('Fehler beim Parsen der Datei!');
    } finally {
        if (target) target.value = '';
    }
}

function exportPreset(preset: Preset) {
    const blob = new Blob([JSON.stringify(preset.iptc ?? {}, null, 2)], {
        type: 'application/json',
    });
    const url = URL.createObjectURL(blob);

    const a = document.createElement('a');
    a.href = url;
    a.download = `${preset.name || 'preset'}.json`;
    document.body.appendChild(a);
    a.click();

    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

const emit = defineEmits<{
    (e: 'update:modelValue', value: IptcForm): void;
}>();
watch(localModel, (val) => emit('update:modelValue', val), { deep: true });
watch(
    () => props.modelValue,
    (val) => {
        localModel.value = val;
    },
    { deep: true },
);
onMounted(() => {
    loadPresets();
});
</script>

<template>
    <div class="group inline-flex h-10 overflow-hidden rounded-md shadow-sm">
        <button
            @click.prevent="open"
            class="flex h-full items-center bg-lightest pl-4 pr-1 text-sm font-medium text-warm-dark hover:bg-light hover:text-primary focus:ring-2 focus:ring-primary"
        >
            {{ $t('preset._') }}
        </button>

        <select
            v-model="action"
            @change.prevent="open"
            class="h-full border-warm-medium bg-lightest text-sm font-medium text-warm-dark group-hover:bg-light group-hover:text-primary group-focus:outline-none"
        >
            <option value="select">
                {{ $t('preset.select._') }}
            </option>
            <option value="store">
                {{ $t('preset.store._') }}
            </option>
            <option value="import">
                {{ $t('preset.import._') }}
            </option>
        </select>

        <BaseModal v-if="action === 'select'" :open="showModal" @close="close">
            <h1 class="mb-4 text-lg font-bold">{{ $t('preset._') }} {{ $t('preset.select._') }}</h1>
            <div class="flex flex-col items-stretch justify-center gap-4">
                <div class="max-h-96 overflow-y-auto">
                    <div v-if="loading" class="p-4 text-center">{{ $t('preset.modal.loading') }}</div>

                    <table v-else class="w-full table-auto border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-2 py-1 text-left">{{ $t('preset.modal.name') }}</th>
                                <th class="border px-2 py-1">{{ $t('preset.modal.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="preset in presets" :key="preset.id">
                                <td class="border px-2 py-1">{{ preset.name }}</td>
                                <td class="border px-2 py-1 text-center">
                                    <div class="flex justify-center gap-2">
                                        <button
                                            class="button-primary px-2 py-1 text-sm"
                                            @click.prevent="
                                                Object.keys(localModel).forEach(
                                                    (key) => delete localModel[key as keyof Iptc],
                                                );
                                                Object.assign(localModel, {
                                                    ...(preset.iptc ?? {}),
                                                    iptc_date_created: preset.iptc?.iptc_date_created
                                                        ? new Date(preset.iptc?.iptc_date_created)
                                                        : null,
                                                    iptc_time_created: preset.iptc?.iptc_time_created
                                                        ? new Date(preset.iptc?.iptc_time_created)
                                                        : null,
                                                });
                                                close();
                                            "
                                        >
                                            {{
                                                $t('preset.select._').charAt(0).toUpperCase() +
                                                $t('preset.select._').slice(1)
                                            }}
                                        </button>

                                        <button
                                            class="bg-lightest px-2 py-1 text-sm font-medium text-warm-dark hover:bg-light hover:text-primary focus:ring-primary"
                                            @click.prevent="exportPreset(preset)"
                                        >
                                            {{
                                                $t('preset.export._').charAt(0).toUpperCase() +
                                                $t('preset.export._').slice(1)
                                            }}
                                        </button>

                                        <button
                                            class="button-secondary px-2 py-1 text-sm"
                                            @click.prevent="
                                                router.delete(route('presets.destroy', preset), {
                                                    onSuccess: () => loadPresets(),
                                                })
                                            "
                                        >
                                            {{
                                                $t('preset.destroy._').charAt(0).toUpperCase() +
                                                $t('preset.destroy._').slice(1)
                                            }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button @click="close" class="button-secondary h-10 w-full rounded-md px-4 align-middle">
                    {{ $t('preset.modal.actions.close') }}
                </button>
            </div>
        </BaseModal>

        <BaseModal v-if="action === 'store'" :open="showModal" @close="close">
            <h1 class="mb-4 text-lg font-bold">{{ $t('preset._') }} {{ $t('preset.store._') }}</h1>

            <div class="flex flex-col items-stretch justify-center gap-4">
                <TextInput id="preset-name" v-model="presetName" :label="$t('preset.name')" />

                <button
                    class="button-primary h-10 w-full rounded-md px-4 align-middle"
                    :disabled="presetName.trim().length === 0"
                    @click.prevent="
                        () => {
                            router.post(
                                route('presets.store'),
                                {
                                    name: presetName.trim(),
                                    iptc: localModel,
                                },
                                { onSuccess: () => loadPresets() },
                            );

                            close();
                        }
                    "
                >
                    {{ $t('preset._') }} {{ $t('preset.store._') }}
                </button>
                <button @click="close" class="button-secondary h-10 w-full rounded-md px-4 align-middle">
                    {{ $t('preset.modal.actions.close') }}
                </button>
            </div>
        </BaseModal>

        <BaseModal v-if="action === 'import'" :open="showModal" @close="close">
            <h1 class="mb-4 text-lg font-bold">{{ $t('preset._') }} {{ $t('preset.import._') }}</h1>
            <button @click="close" class="button-secondary h-10 w-full rounded-md px-4 align-middle">
                {{ $t('preset.modal.actions.close') }}
            </button>
        </BaseModal>

        <input type="file" ref="fileInput" accept="application/json" class="hidden" @change="importPreset" />
    </div>
</template>

<style scoped></style>
