<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Users,
    CheckCircle2,
    AlertTriangle,
    XCircle,
    ChevronDown,
    ChevronUp,
    Scale,
    ShieldCheck,
} from '@lucide/vue';
import { ref } from 'vue';

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

defineProps<{
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
    if (status === 'recommended') {
        return {
            label: 'RECOMMENDED',
            class: 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/30',
            icon: CheckCircle2,
        };
    }

    if (status === 'flagged') {
        return {
            label: 'FLAGGED',
            class: 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/30',
            icon: AlertTriangle,
        };
    }

    return {
        label: 'EXCLUDED',
        class: 'bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/30',
        icon: XCircle,
    };
};
</script>

<template>
    <Head
        :title="`Recruiter Candidate Sequencing Matrix - ${opportunity.title}`"
    />

    <div
        class="min-h-screen bg-slate-50 font-sans text-slate-900 transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100"
    >
        <!-- Top Nav -->
        <header
            class="border-b border-slate-200 bg-white/80 px-6 py-4 backdrop-blur-md dark:border-slate-800 dark:bg-slate-900/80"
        >
            <div class="mx-auto flex max-w-7xl items-center justify-between">
                <Link
                    href="/employer/dashboard"
                    class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 transition hover:text-slate-900 dark:text-slate-400"
                >
                    <ArrowLeft class="h-4 w-4" /> Back to Dashboard
                </Link>
                <div class="flex items-center gap-4">
                    <!-- <ThemeToggle /> -->
                    <div
                        class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300"
                    >
                        <ShieldCheck class="h-4 w-4 text-[#00b2e3]" /> Candidate
                        Sequencing Matrix
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl space-y-8 px-6 py-10">
            <!-- Summary Stats Header -->
            <div
                class="flex flex-col justify-between gap-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:flex-row md:items-center dark:border-slate-800 dark:bg-slate-900"
            >
                <div>
                    <div
                        class="mb-1 flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400"
                    >
                        <span
                            class="font-semibold text-slate-900 dark:text-slate-100"
                            >{{ opportunity.organization }}</span
                        >
                        •
                        <span class="font-mono font-bold text-[#00b2e3]">{{
                            opportunity.grade
                        }}</span>
                        •
                        <span>{{ opportunity.location }}</span>
                    </div>
                    <h1
                        class="text-2xl font-bold text-slate-900 dark:text-slate-100"
                    >
                        {{ opportunity.title }}
                    </h1>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Applicant pool sequenced by KBS Inference Engine rules
                        from highest match score to lowest.
                    </p>
                </div>

                <!-- Metrics -->
                <div
                    class="grid grid-cols-4 gap-2 rounded-xl border border-slate-200 bg-slate-50 p-2.5 text-center dark:border-slate-800 dark:bg-slate-950"
                >
                    <div class="px-2">
                        <div
                            class="text-[10px] font-semibold text-slate-500 uppercase"
                        >
                            Total
                        </div>
                        <div
                            class="text-base font-bold text-slate-900 dark:text-slate-100"
                        >
                            {{ stats.total }}
                        </div>
                    </div>
                    <div
                        class="border-l border-slate-200 px-2 dark:border-slate-800"
                    >
                        <div
                            class="text-[10px] font-semibold text-emerald-600 uppercase dark:text-emerald-400"
                        >
                            Recommended
                        </div>
                        <div
                            class="text-base font-bold text-emerald-600 dark:text-emerald-400"
                        >
                            {{ stats.recommended }}
                        </div>
                    </div>
                    <div
                        class="border-l border-slate-200 px-2 dark:border-slate-800"
                    >
                        <div
                            class="text-[10px] font-semibold text-amber-600 uppercase dark:text-amber-400"
                        >
                            Flagged
                        </div>
                        <div
                            class="text-base font-bold text-amber-600 dark:text-amber-400"
                        >
                            {{ stats.flagged }}
                        </div>
                    </div>
                    <div
                        class="border-l border-slate-200 px-2 dark:border-slate-800"
                    >
                        <div
                            class="text-[10px] font-semibold text-rose-600 uppercase dark:text-rose-400"
                        >
                            Excluded
                        </div>
                        <div
                            class="text-base font-bold text-rose-600 dark:text-rose-400"
                        >
                            {{ stats.excluded }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Applicants Table List -->
            <div class="space-y-3">
                <h2
                    class="flex items-center gap-2 text-xs font-bold tracking-wider text-slate-500 uppercase dark:text-slate-400"
                >
                    <Users class="h-4 w-4 text-[#00b2e3]" /> Sequenced Applicant
                    Pool
                </h2>

                <div
                    v-for="(applicant, index) in applicants"
                    :key="applicant.application_id"
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition dark:border-slate-800 dark:bg-slate-900"
                >
                    <div
                        @click="toggleExpand(applicant.application_id)"
                        class="flex cursor-pointer items-center justify-between p-5 transition hover:bg-slate-50 dark:hover:bg-slate-800/40"
                    >
                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 font-mono text-xs font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                            >
                                #{{ index + 1 }}
                            </div>

                            <div>
                                <div class="flex items-center gap-2">
                                    <h3
                                        class="text-sm font-bold text-slate-900 dark:text-slate-100"
                                    >
                                        {{ applicant.name }}
                                    </h3>
                                    <span
                                        class="font-mono text-xs text-slate-400"
                                        >{{ applicant.email }}</span
                                    >
                                </div>
                                <div
                                    class="mt-0.5 flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400"
                                >
                                    <span
                                        >Education:
                                        <strong
                                            class="text-slate-700 dark:text-slate-300"
                                            >{{
                                                applicant.profile
                                                    .education_level
                                            }}</strong
                                        ></span
                                    >
                                    <span
                                        >Experience:
                                        <strong class="text-[#00b2e3]"
                                            >{{
                                                applicant.profile
                                                    .years_experience
                                            }}
                                            Yrs</strong
                                        ></span
                                    >
                                    <span
                                        >Reliability:
                                        <strong
                                            class="text-emerald-600 dark:text-emerald-400"
                                            >{{
                                                applicant.profile
                                                    .reliability_score
                                            }}%</strong
                                        ></span
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <div
                                    class="text-xl font-extrabold text-slate-900 dark:text-slate-100"
                                >
                                    {{ applicant.kbs.score }}%
                                </div>
                            </div>

                            <div
                                :class="[
                                    'flex items-center gap-1.5 rounded-xl border px-3 py-1 text-xs font-bold',
                                    getStatusBadge(applicant.kbs.status).class,
                                ]"
                            >
                                <component
                                    :is="
                                        getStatusBadge(applicant.kbs.status)
                                            .icon
                                    "
                                    class="h-3.5 w-3.5"
                                />
                                {{ getStatusBadge(applicant.kbs.status).label }}
                            </div>

                            <button
                                class="p-1 text-slate-400 hover:text-slate-600"
                            >
                                <component
                                    :is="
                                        expandedApplicant ===
                                        applicant.application_id
                                            ? ChevronUp
                                            : ChevronDown
                                    "
                                    class="h-4 w-4"
                                />
                            </button>
                        </div>
                    </div>

                    <!-- Expandable Drawer -->
                    <div
                        v-if="expandedApplicant === applicant.application_id"
                        class="space-y-4 border-t border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950"
                    >
                        <div class="space-y-2">
                            <h4
                                class="flex items-center gap-1.5 text-xs font-bold tracking-wider text-slate-700 uppercase dark:text-slate-300"
                            >
                                <Scale class="h-3.5 w-3.5 text-[#00b2e3]" /> AHP
                                Weighted Score Breakdown
                            </h4>
                            <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                                <div
                                    v-for="item in applicant.kbs.breakdown"
                                    :key="item.key"
                                    class="space-y-1.5 rounded-xl border border-slate-200 bg-white p-3 text-xs shadow-sm dark:border-slate-800 dark:bg-slate-900"
                                >
                                    <div
                                        class="flex justify-between font-semibold text-slate-800 dark:text-slate-200"
                                    >
                                        <span>{{ item.criterion }}</span>
                                        <span class="font-mono text-[#00b2e3]"
                                            >+{{ item.contribution }}</span
                                        >
                                    </div>
                                    <div
                                        class="h-1 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800"
                                    >
                                        <div
                                            class="h-full rounded-full bg-[#00b2e3]"
                                            :style="{
                                                width: `${item.raw_score}%`,
                                            }"
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
