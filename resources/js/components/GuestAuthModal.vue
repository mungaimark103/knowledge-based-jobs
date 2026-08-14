<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { UserPlus, LogIn, X, Lock } from '@lucide/vue';

defineProps<{
    show: boolean;
    jobTitle: string;
}>();

defineEmits(['close']);
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm"
    >
        <div
            class="relative w-full max-w-md animate-in space-y-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl duration-200 zoom-in-95 fade-in dark:border-slate-800 dark:bg-slate-900"
        >
            <button
                @click="$emit('close')"
                class="absolute top-5 right-5 p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
            >
                <X class="h-5 w-5" />
            </button>

            <div class="flex items-center space-x-3">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#00b2e3]/10 font-bold text-[#00b2e3]"
                >
                    <Lock class="h-6 w-6" />
                </div>
                <div>
                    <h3
                        class="text-lg font-bold text-slate-900 dark:text-slate-100"
                    >
                        Account Required
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        JobSync Suitability Evaluation
                    </p>
                </div>
            </div>

            <div
                class="space-y-2 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-xs leading-relaxed text-slate-600 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-300"
            >
                <p class="font-semibold text-slate-900 dark:text-slate-100">
                    You must log in or create a Client account to apply for
                    <span class="font-bold text-[#00b2e3]">{{ jobTitle }}</span
                    >.
                </p>
                <p>
                    Creating an account enables our JobSync engine to benchmark
                    your credentials against explicit job criteria and generate
                    your transparent suitability scorecard.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2">
                <Link
                    href="/login"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-100 py-3 text-xs font-semibold text-slate-900 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700"
                >
                    <LogIn class="h-4 w-4" /> Log In
                </Link>
                <Link
                    href="/register"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#00b2e3] py-3 text-xs font-semibold text-white shadow-sm transition hover:bg-[#0099c4]"
                >
                    <UserPlus class="h-4 w-4" /> Register
                </Link>
            </div>
        </div>
    </div>
</template>
