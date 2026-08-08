<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { ShieldCheck, Sparkles, ArrowRight, UserCheck, Briefcase, CheckCircle2, Menu, X } from '@lucide/vue';

defineProps<{
    auth?: {
        user?: any;
    };
}>();

const mobileMenuOpen = ref(false);
</script>

<template>
    <Head title="Impact Talent KBS - Knowledge-Based Career Platform" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans transition-colors duration-300">
        <!-- Top Nav Header -->
        <header class="sticky top-0 z-50 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-4 sm:px-6 py-3.5 shadow-sm">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <!-- Brand Logo & Title -->
                <div class="flex items-center space-x-3 shrink-0">
                    <div class="h-10 w-10 rounded-xl bg-[#00b2e3] text-white flex items-center justify-center shadow-md shadow-[#00b2e3]/20 font-bold shrink-0">
                        <ShieldCheck class="w-6 h-6" />
                    </div>
                    <div>
                        <h1 class="text-base sm:text-lg font-bold text-slate-900 dark:text-slate-100 leading-tight">
                            Impact Talent KBS
                        </h1>
                        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 hidden sm:block">Knowledge-Based Career & Talent Platform</p>
                    </div>
                </div>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center space-x-6">
                    <Link href="/opportunities" class="text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition">
                        Directory
                    </Link>
                    <a href="/employer/portal-switch" class="text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition">
                        Employer Portal
                    </a>

                    <template v-if="auth?.user">
                        <Link
                            href="/opportunities"
                            class="px-4 py-2 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl shadow-sm transition"
                        >
                            Go to Directory
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
                            class="px-4 py-2 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl shadow-sm transition"
                        >
                            Get Started
                        </Link>
                    </template>
                </nav>

                <!-- Mobile Hamburger Button -->
                <div class="flex items-center md:hidden">
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        type="button"
                        aria-label="Toggle navigation menu"
                        class="p-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none transition"
                    >
                        <Menu v-if="!mobileMenuOpen" class="w-6 h-6" />
                        <X v-else class="w-6 h-6" />
                    </button>
                </div>
            </div>

            <!-- Mobile Drawer Menu -->
            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-2"
            >
                <div v-if="mobileMenuOpen" class="md:hidden mt-3 pt-3 border-t border-slate-200 dark:border-slate-800 space-y-2">
                    <Link
                        href="/opportunities"
                        @click="mobileMenuOpen = false"
                        class="block px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                    >
                        Directory
                    </Link>
                    <a
                        href="/employer/portal-switch"
                        @click="mobileMenuOpen = false"
                        class="block px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                    >
                        Employer Portal
                    </a>

                    <div class="pt-2 border-t border-slate-200/60 dark:border-slate-800/60 space-y-2">
                        <template v-if="auth?.user">
                            <Link
                                href="/opportunities"
                                @click="mobileMenuOpen = false"
                                class="block w-full text-center px-4 py-2.5 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-sm font-semibold rounded-xl shadow-sm transition"
                            >
                                Go to Directory
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                href="/login"
                                @click="mobileMenuOpen = false"
                                class="block w-full text-center px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                            >
                                Log in
                            </Link>
                            <Link
                                href="/register"
                                @click="mobileMenuOpen = false"
                                class="block w-full text-center px-4 py-2.5 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-sm font-semibold rounded-xl shadow-sm transition"
                            >
                                Get Started
                            </Link>
                        </template>
                    </div>
                </div>
            </transition>
        </header>

        <!-- Hero Section -->
        <section class="relative px-4 sm:px-6 py-12 md:py-20 max-w-7xl mx-auto text-center space-y-6">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#00b2e3]/10 border border-[#00b2e3]/20 text-[#00b2e3] text-xs font-semibold">
                <Sparkles class="w-4 h-4 shrink-0" />
                <span>Next-Generation Knowledge-Based Recruitment</span>
            </div>

            <h1 class="text-3xl sm:text-5xl md:text-6xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight max-w-4xl mx-auto">
                Empowering Global Careers with <span class="text-[#00b2e3]">Knowledge-Based Precision</span>
            </h1>

            <p class="text-slate-600 dark:text-slate-400 text-sm sm:text-base md:text-lg max-w-2xl mx-auto leading-relaxed">
                A deterministic, rule-driven talent platform connecting global professionals with verified opportunities across leading enterprises, NGOs, and public sector institutions. Replaces opaque ATS filters with transparent expert inference.
            </p>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3 sm:gap-4 pt-4 max-w-md mx-auto sm:max-w-none">
                <Link
                    href="/opportunities"
                    class="px-6 py-3.5 bg-[#00b2e3] hover:bg-[#0099c4] text-white font-semibold text-sm rounded-xl shadow-md shadow-[#00b2e3]/20 inline-flex items-center justify-center gap-2 transition"
                >
                    Explore Job Vacancies <ArrowRight class="w-4 h-4" />
                </Link>
                <Link
                    href="/register"
                    class="px-6 py-3.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold text-sm rounded-xl shadow-sm text-center transition"
                >
                    Register Organization Account
                </Link>
            </div>
        </section>

        <!-- Audience Value Proposition Section -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 py-8 sm:py-12 grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
            <!-- Job Seekers Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-4">
                <div class="h-12 w-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                    <UserCheck class="w-6 h-6" />
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-slate-100">For Job Seekers & Professionals</h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Instantly evaluate your qualifications against standardized job requirements and experience benchmarks. Receive transparent match score breakdowns, hard eligibility checks, and actionable gap analysis before submitting applications.
                </p>
                <ul class="space-y-2 text-xs sm:text-sm text-slate-700 dark:text-slate-300 pt-2">
                    <li class="flex items-center gap-2"><CheckCircle2 class="w-4 h-4 text-emerald-500 shrink-0" /> Personalized Recommended Jobs feed</li>
                    <li class="flex items-center gap-2"><CheckCircle2 class="w-4 h-4 text-emerald-500 shrink-0" /> Transparent Suitability Scorecard Breakdown</li>
                    <li class="flex items-center gap-2"><CheckCircle2 class="w-4 h-4 text-emerald-500 shrink-0" /> Streamlined Direct Job Application</li>
                </ul>
            </div>

            <!-- Hiring Organizations Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-4">
                <div class="h-12 w-12 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
                    <Briefcase class="w-6 h-6" />
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-slate-100">For Hiring Organizations & Recruiters</h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Automate manual screening and save 35+ hours per vacancy. When applications close, our KBS Inference Engine automatically ranks and sequences candidate pipelines from highest suitability to lowest.
                </p>
                <ul class="space-y-2 text-xs sm:text-sm text-slate-700 dark:text-slate-300 pt-2">
                    <li class="flex items-center gap-2"><CheckCircle2 class="w-4 h-4 text-emerald-500 shrink-0" /> Candidate Pipeline Sequencing from highest to lowest score</li>
                    <li class="flex items-center gap-2"><CheckCircle2 class="w-4 h-4 text-emerald-500 shrink-0" /> Reusable KBS Rule Templates & Job-Specific Criteria</li>
                    <li class="flex items-center gap-2"><CheckCircle2 class="w-4 h-4 text-emerald-500 shrink-0" /> Real-Time Screening Hours Saved & Recruitment Analytics</li>
                </ul>
            </div>
        </section>

        <!-- How KBS Engine Works -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-16 border-t border-slate-200 dark:border-slate-800">
            <div class="text-center space-y-3 mb-8 sm:mb-12">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-slate-100">How the Knowledge-Based System Works</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Three-tier expert system reasoning architecture</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl space-y-3 text-center">
                    <div class="h-10 w-10 mx-auto rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">1</div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100">Knowledge Base Storage</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Criteria weights (Skill, Experience, Reliability) and IF-THEN evaluation rules stored as flexible, configurable database logic.</p>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl space-y-3 text-center">
                    <div class="h-10 w-10 mx-auto rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">2</div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100">Inference Engine Deduction</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Evaluates candidate credentials against verified job requirements deterministically, avoiding black-box machine learning bias.</p>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl space-y-3 text-center">
                    <div class="h-10 w-10 mx-auto rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">3</div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100">Explanation Facility</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Generates clear, human-readable explanations detailing exactly how every suitability score and recommendation was calculated.</p>
                </div>
            </div>
        </section>
    </div>
</template>
