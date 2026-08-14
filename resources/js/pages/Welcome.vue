<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Sparkles, ArrowRight, Menu, X } from '@lucide/vue';
import { ref } from 'vue';

defineProps<{
    auth?: {
        user?: any;
    };
}>();

const mobileMenuOpen = ref(false);
</script>

<template>
    <Head title="JobSync - Knowledge-Based Career & Talent Matching Platform" />

    <div
        class="min-h-screen bg-slate-50 font-sans text-slate-900 transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100"
    >
        <!-- Top Nav Header -->
        <header
            class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 px-4 py-3.5 shadow-xs backdrop-blur-md sm:px-6 dark:border-slate-800 dark:bg-slate-900/90"
        >
            <div class="mx-auto flex max-w-7xl items-center justify-between">
                <!-- Brand Logo & Title -->
                <div class="flex shrink-0 items-center space-x-3">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#00b2e3] text-lg font-bold text-white shadow-md shadow-[#00b2e3]/20"
                    >
                        JS
                    </div>
                    <div>
                        <h1
                            class="text-base leading-tight font-bold text-slate-900 sm:text-lg dark:text-slate-100"
                        >
                            JobSync
                        </h1>
                        <p
                            class="hidden text-[11px] text-slate-500 sm:block sm:text-xs dark:text-slate-400"
                        >
                            Knowledge-Based Job Matching & Client Management
                        </p>
                    </div>
                </div>

                <!-- Desktop Navigation Links -->
                <nav class="hidden items-center space-x-6 md:flex">
                    <Link
                        href="/opportunities"
                        class="text-xs font-semibold text-slate-600 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100"
                    >
                        Exploring Job Vacancies
                    </Link>

                    <template v-if="auth?.user">
                        <Link
                            href="/dashboard"
                            class="rounded-xl bg-[#00b2e3] px-4 py-2 text-xs font-semibold text-white shadow-xs transition hover:bg-[#0099c4]"
                        >
                            Client Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            href="/login"
                            class="text-xs font-semibold text-slate-700 transition hover:text-slate-900 dark:text-slate-300 dark:hover:text-slate-100"
                        >
                            Log in
                        </Link>
                        <Link
                            href="/register"
                            class="rounded-xl bg-[#00b2e3] px-4 py-2 text-xs font-semibold text-white shadow-xs transition hover:bg-[#0099c4]"
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
                        class="rounded-xl p-2 text-slate-700 transition hover:bg-slate-100 focus:outline-none dark:text-slate-300 dark:hover:bg-slate-800"
                    >
                        <Menu v-if="!mobileMenuOpen" class="h-6 w-6" />
                        <X v-else class="h-6 w-6" />
                    </button>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="mx-auto max-w-7xl space-y-8 px-6 py-16 text-center">
            <div
                class="inline-flex items-center gap-2 rounded-full border border-[#00b2e3]/20 bg-[#00b2e3]/10 px-4 py-2 text-xs font-semibold text-[#00b2e3]"
            >
                <Sparkles class="h-4 w-4" /> Next-Generation Deterministic Rule
                Matching Platform
            </div>

            <h1
                class="mx-auto max-w-4xl text-4xl leading-tight font-black tracking-tight text-slate-900 sm:text-6xl dark:text-white"
            >
                Connect Clients with Top Employers &
                <span class="text-[#00b2e3]">Employment Agencies</span>
            </h1>

            <p
                class="mx-auto max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base dark:text-slate-400"
            >
                JobSync provides transparent, rule-driven job matching. Explore
                vacancies from Safaricom PLC, Corporate Staffing Services,
                Summit Recruitment & Search, and Flexi Personnel.
            </p>

            <div
                class="flex flex-col items-center justify-center gap-4 pt-4 sm:flex-row"
            >
                <Link
                    href="/opportunities"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-[#00b2e3] px-6 py-3.5 text-xs font-bold text-white shadow-lg shadow-[#00b2e3]/20 transition hover:bg-[#0099c4] sm:w-auto"
                >
                    Exploring Job Vacancies <ArrowRight class="h-4 w-4" />
                </Link>
            </div>
        </main>
    </div>
</template>
