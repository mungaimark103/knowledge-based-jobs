<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

defineProps<{
    passwordRules: string;
}>();

defineOptions({
    layout: {
        title: 'Create a Client account',
        description:
            'Enter your details below to create your JobSync Client account',
    },
});

const form = useForm({
    role: 'candidate',
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <Head title="Register Client Account" />

    <form @submit.prevent="submit" class="flex flex-col gap-5">
        <div class="grid gap-5">
            <!-- Full Name -->
            <div class="grid gap-2">
                <Label for="name">Full Name</Label>
                <Input
                    id="name"
                    type="text"
                    required
                    v-model="form.name"
                    autocomplete="name"
                    placeholder="e.g. Ian Chitechi"
                />
                <InputError :message="form.errors.name" />
            </div>

            <!-- Email Address -->
            <div class="grid gap-2">
                <Label for="email">Personal / Work Email</Label>
                <Input
                    id="email"
                    type="email"
                    required
                    v-model="form.email"
                    autocomplete="email"
                    placeholder="email@example.com"
                />
                <InputError :message="form.errors.email" />
            </div>

            <!-- Password -->
            <div class="grid gap-2">
                <Label for="password">Password</Label>
                <PasswordInput
                    id="password"
                    required
                    v-model="form.password"
                    autocomplete="new-password"
                    placeholder="Password"
                />
                <InputError :message="form.errors.password" />
            </div>

            <!-- Confirm Password -->
            <div class="grid gap-2">
                <Label for="password_confirmation">Confirm Password</Label>
                <PasswordInput
                    id="password_confirmation"
                    required
                    v-model="form.password_confirmation"
                    autocomplete="new-password"
                    placeholder="Confirm password"
                />
                <InputError :message="form.errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                class="mt-2 w-full font-semibold"
                :disabled="form.processing"
            >
                <Spinner v-if="form.processing" class="mr-2" />
                Register Client Account
            </Button>
        </div>

        <div class="relative my-2 flex items-center justify-center">
            <div class="absolute inset-0 flex items-center">
                <div
                    class="w-full border-t border-slate-200 dark:border-slate-800"
                ></div>
            </div>
            <span
                class="relative bg-white px-3 text-[11px] font-semibold tracking-wider text-slate-400 uppercase dark:bg-slate-900"
                >or</span
            >
        </div>

        <a
            href="/auth/google"
            class="inline-flex w-full items-center justify-center gap-2.5 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 shadow-xs transition hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
        >
            <svg class="h-4 w-4" viewBox="0 0 24 24">
                <path
                    fill="#4285F4"
                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                />
                <path
                    fill="#34A853"
                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                />
                <path
                    fill="#FBBC05"
                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"
                />
                <path
                    fill="#EA4335"
                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"
                />
            </svg>
            <span>Continue with Google</span>
        </a>

        <div class="text-center text-sm text-muted-foreground">
            Already have an account?
            <TextLink href="/login" class="underline underline-offset-4"
                >Log in</TextLink
            >
        </div>
    </form>
</template>
