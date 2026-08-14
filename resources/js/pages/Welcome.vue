<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { ShieldCheck, Sparkles, ArrowRight, UserCheck, Briefcase, CheckCircle2, Menu, X, Building2, Users } from '@lucide/vue';

defineProps<{
    auth?: {
        user?: any;
    };
}>();

const mobileMenuOpen = ref(false);
</script>

<template>
    <Head title="JobSync - Knowledge-Based Career & Talent Matching Platform" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans transition-colors duration-300">
        <!-- Top Nav Header -->
        <header class="sticky top-0 z-50 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-4 sm:px-6 py-3.5 shadow-xs">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <!-- Brand Logo & Title -->
                <div class="flex items-center space-x-3 shrink-0">
                    <div class="h-10 w-10 rounded-xl bg-[#00b2e3] text-white flex items-center justify-center shadow-md shadow-[#00b2e3]/20 font-bold shrink-0 text-lg">
                        JS
                    </div>
                    <div>
                        <h1 class="text-base sm:text-lg font-bold text-slate-900 dark:text-slate-100 leading-tight">
                            JobSync
                        </h1>
                        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 hidden sm:block">Knowledge-Based Job Matching & Client Management</p>
                    </div>
                </div>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center space-x-6">
                    <Link href="/opportunities" class="text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition">
                        Exploring Job Vacancies
                    </Link>

                    <template v-if="auth?.user">
                        <Link
                            href="/dashboard"
                            class="px-4 py-2 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl shadow-xs transition"
                        >
                            Client Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            href="/login"
                            class="text-xs font-semibold text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-slate-100 transition"
                        >
                            Log in
                        </Link>
                        <Link
                            href="/register"
                            class="px-4 py-2 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl shadow-xs transition"
                        >
                            Register Client
                        </Link>
                    </template>
                </nav>

                <!-- Mobile Hamburger Button -->
                <div class="flex items-center md:hidden">
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        type="button"
                        class="p-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none transition"
                    >
                        <Menu v-if="!mobileMenuOpen" class="w-6 h-6" />
                        <X v-else class="w-6 h-6" />
                    </button>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="max-w-7xl mx-auto px-6 py-16 text-center space-y-8">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#00b2e3]/10 border border-[#00b2e3]/20 text-[#00b2e3] text-xs font-semibold">
                <Sparkles class="w-4 h-4" /> Next-Generation Deterministic Rule Matching Platform
            </div>

            <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-slate-900 dark:text-white max-w-4xl mx-auto leading-tight">
                Connect Clients with Top Employers & <span class="text-[#00b2e3]">Employment Agencies</span>
            </h1>

            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 max-w-2xl mx-auto leading-relaxed">
                JobSync provides transparent, rule-driven job matching. Explore vacancies from Safaricom PLC, Corporate Staffing Services, Summit Recruitment & Search, and Flexi Personnel.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                <Link
                    href="/opportunities"
                    class="w-full sm:w-auto px-6 py-3.5 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-bold rounded-2xl shadow-lg shadow-[#00b2e3]/20 transition inline-flex items-center justify-center gap-2"
                >
                    Exploring Job Vacancies <ArrowRight class="w-4 h-4" />
                </Link>
            </div>
        </main>
    </div>
</template>
