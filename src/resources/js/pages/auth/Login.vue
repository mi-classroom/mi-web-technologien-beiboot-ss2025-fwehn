<script setup lang="ts">
import TextInput from '@/components/ui/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="flex min-h-screen items-center justify-center px-4">
        <Head title="Log in" />

        <div class="w-full max-w-md space-y-6">
            <div class="space-y-1 text-center">
                <h1 class="text-2xl font-semibold">Log in to your account</h1>
                <p class="text-muted-foreground text-sm">Enter your email and password below to log in</p>
            </div>

            <div v-if="status" class="text-center text-sm font-medium text-green-600">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="flex flex-col gap-6">
                <div class="grid gap-6">
                    <TextInput
                        id="email"
                        label="Email address"
                        type="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        v-model="form.email"
                        :error="form.errors.email"
                        placeholder="email@example.com"
                    />

                    <TextInput
                        id="password"
                        label="Password"
                        type="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        v-model="form.password"
                        :error="form.errors.password"
                        placeholder="Password"
                        :password="true"
                    />

                    <div class="flex items-center justify-between" :tabindex="3">
                        <label
                            for="remember"
                            class="flex items-center space-x-3 text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                        >
                            <input
                                id="remember"
                                type="checkbox"
                                v-model="form.remember"
                                :tabindex="4"
                                class="border-input bg-background ring-offset-background focus-visible:ring-ring h-4 w-4 rounded border text-primary shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            />
                            <span>Remember me</span>
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="text-primary-foreground ring-offset-background focus-visible:ring-ring inline-flex h-10 w-full items-center justify-center gap-2 whitespace-nowrap rounded-md bg-primary px-4 py-2 text-sm font-medium transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                        :tabindex="4"
                        :disabled="form.processing"
                    >
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        Log in
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
