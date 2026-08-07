<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Users, CheckCircle2, AlertTriangle, XCircle, ChevronDown, ChevronUp, Scale, ShieldCheck } from '@lucide/vue';
import ThemeToggle from '@/components/ThemeToggle.vue';

interface BreakdownItem {
    criterion: string;
    key: string;
    raw_score: number;
    weight: number;
    contribution: number;
}

interface Applicant {
    application_id: number;
    candidate_id: number;
    name: string;
    email: string;
    applied_at: string;
    status: string;
    profile: {
        education_level: string;
        years_experience: number;
        skills: string[];
        reliability_score: number;
    };
    kbs: {
        score: number;
        status: 'recommended' | 'flagged' | 'excluded';
        breakdown: BreakdownItem[];
        explanations: string[];
    };
}

const props = defineProps<{
    opportunity: any;
    applicants: Applicant[];
    stats: {
        total: number;
        recommended: number;
        flagged: number;
        excluded: number;
    };
}>();

const expandedApplicant = ref<number | null>(null);

const toggleExpand = (id: number) => {
    expandedApplicant.value = expandedApplicant.value === id ? null : id;
};

const getStatusBadge = (status: string) => {
    if (status === 'recommended') return { label: 'RECOMMENDED', class: 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/30', icon: CheckCircle2 };
    if (status === 'flagged') return { label: 'FLAGGED', class: 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/30', icon: AlertTriangle };
    return { label: 'EXCLUDED', class: 'bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/30', icon: XCircle };
};
</script>

<template>
    <Head :title="`Recruiter Candidate Sequencing Matrix - ${opportunity.title}`" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans transition-colors duration-300">
        <!-- Top Nav -->
        <header class="border-b border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 px-6 py-4 backdrop-blur-md">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <Link href="/employer/dashboard" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 transition">
                    <ArrowLeft class="w-4 h-4" /> Back to Dashboard
                </Link>
                <div class="flex items-center gap-4">
                    <!-- <ThemeToggle /> -->
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300">
                        <ShieldCheck class="w-4 h-4 text-[#00b2e3]" /> Candidate Sequencing Matrix
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 py-10 space-y-8">
            <!-- Summary Stats Header -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-sm">
                <div>
                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-1">
                        <span class="font-semibold text-slate-900 dark:text-slate-100">{{ opportunity.organization }}</span> • 
                        <span class="font-mono text-[#00b2e3] font-bold">{{ opportunity.grade }}</span> • 
                        <span>{{ opportunity.location }}</span>
                    </div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">
                        {{ opportunity.title }}
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Applicant pool sequenced by KBS Inference Engine rules from highest match score to lowest.
                    </p>
                </div>

                <!-- Metrics -->
                <div class="grid grid-cols-4 gap-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 p-2.5 rounded-xl text-center">
                    <div class="px-2">
                        <div class="text-[10px] text-slate-500 uppercase font-semibold">Total</div>
                        <div class="text-base font-bold text-slate-900 dark:text-slate-100">{{ stats.total }}</div>
                    </div>
                    <div class="px-2 border-l border-slate-200 dark:border-slate-800">
                        <div class="text-[10px] text-emerald-600 dark:text-emerald-400 uppercase font-semibold">Recommended</div>
                        <div class="text-base font-bold text-emerald-600 dark:text-emerald-400">{{ stats.recommended }}</div>
                    </div>
                    <div class="px-2 border-l border-slate-200 dark:border-slate-800">
                        <div class="text-[10px] text-amber-600 dark:text-amber-400 uppercase font-semibold">Flagged</div>
                        <div class="text-base font-bold text-amber-600 dark:text-amber-400">{{ stats.flagged }}</div>
                    </div>
                    <div class="px-2 border-l border-slate-200 dark:border-slate-800">
                        <div class="text-[10px] text-rose-600 dark:text-rose-400 uppercase font-semibold">Excluded</div>
                        <div class="text-base font-bold text-rose-600 dark:text-rose-400">{{ stats.excluded }}</div>
                    </div>
                </div>
            </div>

            <!-- Applicants Table List -->
            <div class="space-y-3">
                <h2 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-2">
                    <Users class="w-4 h-4 text-[#00b2e3]" /> Sequenced Applicant Pool
                </h2>

                <div
                    v-for="(applicant, index) in applicants"
                    :key="applicant.application_id"
                    class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm transition"
                >
                    <div
                        @click="toggleExpand(applicant.application_id)"
                        class="p-5 flex items-center justify-between cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/40 transition"
                    >
                        <div class="flex items-center gap-4">
                            <div class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center font-mono font-bold text-xs text-slate-700 dark:text-slate-300">
                                #{{ index + 1 }}
                            </div>

                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ applicant.name }}</h3>
                                    <span class="text-xs text-slate-400 font-mono">{{ applicant.email }}</span>
                                </div>
                                <div class="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    <span>Education: <strong class="text-slate-700 dark:text-slate-300">{{ applicant.profile.education_level }}</strong></span>
                                    <span>Experience: <strong class="text-[#00b2e3]">{{ applicant.profile.years_experience }} Yrs</strong></span>
                                    <span>Reliability: <strong class="text-emerald-600 dark:text-emerald-400">{{ applicant.profile.reliability_score }}%</strong></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <div class="text-xl font-extrabold text-slate-900 dark:text-slate-100">
                                    {{ applicant.kbs.score }}%
                                </div>
                            </div>

                            <div :class="['px-3 py-1 rounded-xl border flex items-center gap-1.5 text-xs font-bold', getStatusBadge(applicant.kbs.status).class]">
                                <component :is="getStatusBadge(applicant.kbs.status).icon" class="w-3.5 h-3.5" />
                                {{ getStatusBadge(applicant.kbs.status).label }}
                            </div>

                            <button class="text-slate-400 hover:text-slate-600 p-1">
                                <component :is="expandedApplicant === applicant.application_id ? ChevronUp : ChevronDown" class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Expandable Drawer -->
                    <div
                        v-if="expandedApplicant === applicant.application_id"
                        class="p-5 bg-slate-50 dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800 space-y-4"
                    >
                        <div class="space-y-2">
                            <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5 uppercase tracking-wider">
                                <Scale class="w-3.5 h-3.5 text-[#00b2e3]" /> AHP Weighted Score Breakdown
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div
                                    v-for="item in applicant.kbs.breakdown"
                                    :key="item.key"
                                    class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-3 rounded-xl text-xs space-y-1.5 shadow-sm"
                                >
                                    <div class="flex justify-between font-semibold text-slate-800 dark:text-slate-200">
                                        <span>{{ item.criterion }}</span>
                                        <span class="text-[#00b2e3] font-mono">+{{ item.contribution }}</span>
                                    </div>
                                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-1 rounded-full overflow-hidden">
                                        <div
                                            class="bg-[#00b2e3] h-full rounded-full"
                                            :style="{ width: `${item.raw_score}%` }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
