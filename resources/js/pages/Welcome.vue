<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ShieldCheck, Sparkles, ArrowRight, UserCheck, Briefcase, Scale, Clock, Award, Users, CheckCircle2 } from '@lucide/vue';
import ThemeToggle from '@/components/ThemeToggle.vue';

defineProps<{
    auth?: {
        user?: any;
    };
}>();
</script>

<template>
    <Head title="Impact Talent KBS - Knowledge-Based Career Platform" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans transition-colors duration-300">
        <!-- Top Nav Header -->
        <header class="sticky top-0 z-40 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-6 py-4 flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 rounded-xl bg-[#00b2e3] text-white flex items-center justify-center shadow-md shadow-[#00b2e3]/20 font-bold">
                    <ShieldCheck class="w-6 h-6" />
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">
                        Impact Talent KBS
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Knowledge-Based Career & Talent Platform</p>
                </div>
            </div>

            <nav class="flex items-center space-x-5">
                <Link href="/opportunities" class="text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100">
                    Directory
                </Link>
                <a href="/employer/portal-switch" class="text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100">
                    Employer Portal
                </a>
                <!-- <ThemeToggle /> -->

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
                        class="text-xs font-semibold text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-slate-100"
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
        </header>

        <!-- Hero Section -->
        <section class="relative px-6 py-20 max-w-7xl mx-auto text-center space-y-6">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#00b2e3]/10 border border-[#00b2e3]/20 text-[#00b2e3] text-xs font-semibold">
                <Sparkles class="w-4 h-4" /> Next-Generation Knowledge-Based Recruitment
            </div>

            <h1 class="text-4xl md:text-6xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight max-w-4xl mx-auto">
                Empowering Global Careers with <span class="text-[#00b2e3]">Knowledge-Based Precision</span>
            </h1>

            <p class="text-slate-600 dark:text-slate-400 text-base md:text-lg max-w-2xl mx-auto leading-relaxed">
                A deterministic, rule-driven talent platform bridging international development, multilateral organizations, and public sector hiring. Replaces black-box keywords with transparent expert inference.
            </p>

            <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
                <Link
                    href="/opportunities"
                    class="px-6 py-3.5 bg-[#00b2e3] hover:bg-[#0099c4] text-white font-semibold text-sm rounded-xl shadow-md shadow-[#00b2e3]/20 inline-flex items-center gap-2 transition"
                >
                    Explore Job Vacancies <ArrowRight class="w-4 h-4" />
                </Link>
                <Link
                    href="/register"
                    class="px-6 py-3.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold text-sm rounded-xl shadow-sm transition"
                >
                    Register Organization Account
                </Link>
            </div>
        </section>

        <!-- Audience Value Proposition Section -->
        <section class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Job Seekers Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 shadow-sm space-y-4">
                <div class="h-12 w-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                    <UserCheck class="w-6 h-6" />
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100">For Job Seekers & Professionals</h3>
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                    Instantly evaluate your qualifications against formal UN grade criteria (P-1 to P-5, GS). Get transparent match score breakdowns, hard eligibility warnings, and actionable gap mitigation steps before submitting applications.
                </p>
                <ul class="space-y-2 text-xs text-slate-700 dark:text-slate-300 pt-2">
                    <li class="flex items-center gap-2"><CheckCircle2 class="w-4 h-4 text-emerald-500" /> Personalized Recommended Jobs feed</li>
                    <li class="flex items-center gap-2"><CheckCircle2 class="w-4 h-4 text-emerald-500" /> Transparent AHP Scorecard Breakdown</li>
                    <li class="flex items-center gap-2"><CheckCircle2 class="w-4 h-4 text-emerald-500" /> Inbuilt 1-Click Job Application</li>
                </ul>
            </div>

            <!-- Hiring Organizations Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 shadow-sm space-y-4">
                <div class="h-12 w-12 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
                    <Briefcase class="w-6 h-6" />
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100">For Hiring Organizations & Recruiters</h3>
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                    Automate manual screening and save 35+ hours per vacancy. When the application period closes, our KBS Inference Engine automatically sequences candidate pipelines from 100% suitability down to lowest.
                </p>
                <ul class="space-y-2 text-xs text-slate-700 dark:text-slate-300 pt-2">
                    <li class="flex items-center gap-2"><CheckCircle2 class="w-4 h-4 text-emerald-500" /> Candidate Sequencing from highest to lowest score</li>
                    <li class="flex items-center gap-2"><CheckCircle2 class="w-4 h-4 text-emerald-500" /> Reusable KBS Rule Templates & Job-Specific Criteria</li>
                    <li class="flex items-center gap-2"><CheckCircle2 class="w-4 h-4 text-emerald-500" /> Estimated Screening Hours Saved Analytics</li>
                </ul>
            </div>
        </section>

        <!-- How KBS Engine Works -->
        <section class="max-w-7xl mx-auto px-6 py-16 border-t border-slate-200 dark:border-slate-800">
            <div class="text-center space-y-3 mb-12">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-slate-100">How the Knowledge-Based System Works</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Three-tier expert system reasoning architecture</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl space-y-3 text-center">
                    <div class="h-10 w-10 mx-auto rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">1</div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100">Knowledge Base Storage</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Criteria weights (Skill, Experience, Reliability) and IF-THEN rules stored as editable database records.</p>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl space-y-3 text-center">
                    <div class="h-10 w-10 mx-auto rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">2</div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100">Inference Engine Deduction</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Evaluates candidate credentials against job facts deterministically without black-box machine learning.</p>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl space-y-3 text-center">
                    <div class="h-10 w-10 mx-auto rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">3</div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100">Explanation Facility</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Outputs transparent human-readable explanations explaining exactly why each score was awarded.</p>
                </div>
            </div>
        </section>
    </div>
</template>
