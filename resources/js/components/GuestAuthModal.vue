<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ShieldCheck, UserPlus, LogIn, X, Lock } from '@lucide/vue';

defineProps<{
    show: boolean;
    jobTitle: string;
}>();

defineEmits(['close']);
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-6 relative animate-in fade-in zoom-in-95 duration-200">
            <button @click="$emit('close')" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1">
                <X class="w-5 h-5" />
            </button>

            <div class="flex items-center space-x-3">
                <div class="h-12 w-12 rounded-2xl bg-[#00b2e3]/10 text-[#00b2e3] flex items-center justify-center font-bold">
                    <Lock class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Account Required</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Knowledge-Based Suitability Evaluation</p>
                </div>
            </div>

            <div class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl text-xs text-slate-600 dark:text-slate-300 leading-relaxed space-y-2">
                <p class="font-semibold text-slate-900 dark:text-slate-100">
                    You must log in or create a candidate account to apply for <span class="text-[#00b2e3] font-bold">{{ jobTitle }}</span>.
                </p>
                <p>
                    Creating an account enables our Knowledge-Based System (KBS) engine to benchmark your real credentials against explicit UN/multilateral grade criteria and generate your transparent suitability scorecard.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2">
                <Link
                    href="/login"
                    class="w-full py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-900 dark:text-slate-100 font-semibold text-xs rounded-xl flex items-center justify-center gap-2 transition"
                >
                    <LogIn class="w-4 h-4" /> Log In
                </Link>
                <Link
                    href="/register"
                    class="w-full py-3 bg-[#00b2e3] hover:bg-[#0099c4] text-white font-semibold text-xs rounded-xl flex items-center justify-center gap-2 shadow-sm transition"
                >
                    <UserPlus class="w-4 h-4" /> Register
                </Link>
            </div>
        </div>
    </div>
</template>
