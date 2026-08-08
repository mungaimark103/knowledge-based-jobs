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
        title: 'Create an account',
        description: 'Enter your details below to create your account',
    },
});

const form = useForm({
    role: 'candidate',
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    organization_name: '',
    organization_code: '',
    organization_type: 'UN_AGENCY',
});

function submit() {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <Head title="Register" />

    <form @submit.prevent="submit" class="flex flex-col gap-5">
        <div class="grid gap-5">
            <!-- Account Type Selection -->
            <div class="grid gap-2">
                <Label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Account Type</Label>
                <div class="grid grid-cols-2 gap-3">
                    <label
                        :class="[
                            'flex items-center justify-center p-3 rounded-xl border cursor-pointer transition text-xs font-semibold',
                            form.role === 'candidate'
                                ? 'border-[#00b2e3] bg-[#00b2e3]/10 text-[#00b2e3]'
                                : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:border-slate-300'
                        ]"
                    >
                        <input type="radio" v-model="form.role" value="candidate" class="sr-only" />
                        <span>👤 Job Seeker (Candidate)</span>
                    </label>
                    <label
                        :class="[
                            'flex items-center justify-center p-3 rounded-xl border cursor-pointer transition text-xs font-semibold',
                            form.role === 'employer'
                                ? 'border-[#00b2e3] bg-[#00b2e3]/10 text-[#00b2e3]'
                                : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:border-slate-300'
                        ]"
                    >
                        <input type="radio" v-model="form.role" value="employer" class="sr-only" />
                        <span>🏢 Employer / Recruiter</span>
                    </label>
                </div>
            </div>

            <!-- Full Name -->
            <div class="grid gap-2">
                <Label for="name">{{ form.role === 'employer' ? 'Recruiter / Admin Full Name' : 'Full Name' }}</Label>
                <Input
                    id="name"
                    type="text"
                    required
                    v-model="form.name"
                    autocomplete="name"
                    :placeholder="form.role === 'employer' ? 'e.g. Jane Doe (HR Lead)' : 'e.g. Mark Mungai'"
                />
                <InputError :message="form.errors.name" />
            </div>

            <!-- Email Address -->
            <div class="grid gap-2">
                <Label for="email">Work / Personal Email</Label>
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

            <!-- Additional Organization Fields for Employers -->
            <div v-if="form.role === 'employer'" class="p-4 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl space-y-4">
                <h4 class="text-xs font-bold text-[#00b2e3] uppercase tracking-wider">Organization Profile</h4>

                <div class="grid gap-2">
                    <Label for="org_name">Organization Name</Label>
                    <Input
                        id="org_name"
                        type="text"
                        required
                        v-model="form.organization_name"
                        placeholder="e.g. UNICEF Kenya / Global Tech Corp"
                    />
                    <InputError :message="form.errors.organization_name" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="grid gap-2">
                        <Label for="org_code">Code / Acronym</Label>
                        <Input
                            id="org_code"
                            type="text"
                            required
                            v-model="form.organization_code"
                            placeholder="e.g. UNICEF"
                        />
                        <InputError :message="form.errors.organization_code" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="org_type">Organization Type</Label>
                        <select
                            id="org_type"
                            v-model="form.organization_type"
                            class="w-full h-9 px-3 py-1 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-md text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-[#00b2e3]"
                        >
                            <option value="PRIVATE_COMPANY">Private Sector / Corporate</option>
                            <option value="NGO">NGO / Non-Governmental Organization</option>
                            <option value="PARASTATAL">Parastatal / State Enterprise</option>
                            <option value="GOV_BODY">Government Body / Ministry</option>
                            <option value="UN_AGENCY">UN Agency / Multilateral</option>
                            <option value="INTERNATIONAL_ORG">International Organization</option>
                        </select>
                    </div>
                </div>
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
                    :passwordrules="passwordRules"
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
                    :passwordrules="passwordRules"
                />
                <InputError :message="form.errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                class="mt-2 w-full bg-[#00b2e3] hover:bg-[#0099c4] text-white"
                :disabled="form.processing"
            >
                <Spinner v-if="form.processing" />
                <span>{{ form.role === 'employer' ? 'Register Organization & Account' : 'Create Candidate Account' }}</span>
            </Button>
        </div>

        <div class="relative my-2 flex items-center justify-center">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200 dark:border-slate-800"></div></div>
            <span class="relative bg-white dark:bg-slate-900 px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">or</span>
        </div>

        <a
            href="/auth/google"
            class="w-full py-2.5 px-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-xl shadow-sm transition inline-flex items-center justify-center gap-2.5"
        >
            <svg class="w-4 h-4" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <span>Continue with Google</span>
        </a>

        <div class="text-center text-sm text-muted-foreground">
            Already have an account?
            <TextLink
                href="/login"
                class="underline underline-offset-4"
            >Log in</TextLink>
        </div>
    </form>
</template>
