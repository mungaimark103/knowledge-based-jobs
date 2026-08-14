<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ShieldCheck,
    CheckCircle2,
    AlertTriangle,
    XCircle,
    Scale,
    HelpCircle,
    Building2,
    Lock,
    UserCheck,
    X,
} from '@lucide/vue';
import { ref, computed } from 'vue';
import ApplicationWizardModal from '@/components/ApplicationWizardModal.vue';
import GuestAuthModal from '@/components/GuestAuthModal.vue';

interface BreakdownItem {
    criterion: string;
    key: string;
    raw_score: number;
    weight: number;
    contribution: number;
}

interface Evaluation {
    score: number;
    status: 'recommended' | 'flagged' | 'excluded';
    breakdown: BreakdownItem[];
    explanations: string[];
}

const props = defineProps<{
    opportunity: any;
    organizationInfo?: {
        name: string;
        code: string;
        type: string;
        logo: string;
        duty_station: string;
        contract_type: string;
        mission: string;
    };
    evaluation?: Evaluation;
    isAuthenticated: boolean;
    hasApplied?: boolean;
    candidateProfile?: any;
}>();

const page = usePage();
const currentUser = computed(() => page.props.auth?.user as any);

const showAuthModal = ref(false);
const showWizardModal = ref(false);
const showEmployerModal = ref(false);
const applicationSubmitted = ref(props.hasApplied || false);

const handleApplyClick = () => {
    if (!props.isAuthenticated) {
        showAuthModal.value = true;
    } else if (currentUser.value?.role === 'employer') {
        showEmployerModal.value = true;
    } else {
        showWizardModal.value = true;
    }
};

const getStatusBadge = (status?: string) => {
    if (status === 'recommended') {
        return {
            label: 'RECOMMENDED',
            class: 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/30',
            icon: CheckCircle2,
        };
    }

    if (status === 'flagged') {
        return {
            label: 'NEEDS REVIEW',
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
    <Head :title="`${opportunity.title} - Job Details`" />

    <div
        class="min-h-screen bg-slate-50 font-sans text-slate-900 transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100"
    >
        <!-- Top Nav -->
        <header
            class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 px-4 py-3.5 backdrop-blur-md sm:px-6 dark:border-slate-800 dark:bg-slate-900/90"
        >
            <div class="mx-auto flex max-w-7xl items-center justify-between">
                <Link
                    href="/opportunities"
                    class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100"
                >
                    <ArrowLeft class="h-4 w-4" /> <span>Back to Directory</span>
                </Link>
                <div class="flex items-center gap-4">
                    <div
                        class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300"
                    >
                        <ShieldCheck class="h-4 w-4 shrink-0 text-[#00b2e3]" />
                        <span class="hidden sm:inline"
                            >JobSync Explanation Facility</span
                        >
                        <span class="sm:hidden">Explanation Facility</span>
                    </div>
                </div>
            </div>
        </header>

        <main
            class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-6 py-10 lg:grid-cols-3"
        >
            <!-- Left 2 Columns: Job Details -->
            <div class="space-y-6 lg:col-span-2">
                <div
                    class="space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span
                                class="rounded-lg border border-[#00b2e3]/20 bg-[#00b2e3]/10 px-3 py-1 font-mono text-xs font-bold text-[#00b2e3]"
                            >
                                {{ opportunity.grade }}
                            </span>
                            <span
                                class="text-xs font-semibold tracking-wider text-slate-500 uppercase dark:text-slate-400"
                            >
                                {{ opportunity.organization }}
                            </span>
                        </div>

                        <!-- Apply Button -->
                        <button
                            @click="handleApplyClick"
                            :disabled="applicationSubmitted"
                            class="rounded-xl bg-[#00b2e3] px-5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#0099c4] disabled:bg-emerald-600"
                        >
                            {{
                                applicationSubmitted
                                    ? 'Application Submitted ✓'
                                    : 'Apply for Position'
                            }}
                        </button>
                    </div>

                    <h1
                        class="text-2xl font-extrabold text-slate-900 dark:text-slate-100"
                    >
                        {{ opportunity.title }}
                    </h1>

                    <div
                        class="flex flex-wrap items-center gap-6 border-t border-slate-100 pt-3 text-xs text-slate-500 dark:border-slate-800 dark:text-slate-400"
                    >
                        <div>
                            Location:
                            <strong
                                class="text-slate-800 dark:text-slate-200"
                                >{{ opportunity.location }}</strong
                            >
                        </div>
                        <div>
                            Min Experience:
                            <strong class="text-slate-800 dark:text-slate-200"
                                >{{ opportunity.min_experience }} Years</strong
                            >
                        </div>
                    </div>
                </div>

                <div
                    class="space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <h2
                        class="text-sm font-bold tracking-wider text-slate-900 uppercase dark:text-slate-100"
                    >
                        Job Description
                    </h2>
                    <p
                        class="text-xs leading-relaxed text-slate-600 dark:text-slate-300"
                    >
                        {{ opportunity.description }}
                    </p>

                    <div
                        class="space-y-2 border-t border-slate-100 pt-4 dark:border-slate-800"
                    >
                        <h3
                            class="text-xs font-semibold tracking-wider text-slate-500 uppercase dark:text-slate-400"
                        >
                            Required Skills
                        </h3>
                        <div class="flex flex-wrap gap-1.5">
                            <span
                                v-for="skill in opportunity.required_skills"
                                :key="skill"
                                class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                            >
                                {{ skill }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Conditional Card -->

            <!-- CASE A: Unauthenticated Visitor / Guest -> Organization Information Card -->
            <div v-if="!isAuthenticated || !evaluation" class="space-y-6">
                <div
                    class="sticky top-6 space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div
                        class="flex items-center space-x-3 border-b border-slate-100 pb-4 dark:border-slate-800"
                    >
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 font-bold text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400"
                        >
                            <Building2 class="h-6 w-6" />
                        </div>
                        <div>
                            <span
                                class="text-[10px] font-bold tracking-wider text-slate-400 uppercase"
                                >{{
                                    organizationInfo?.type ||
                                    'Multilateral Entity'
                                }}</span
                            >
                            <h3
                                class="text-base font-bold text-slate-900 dark:text-slate-100"
                            >
                                {{ opportunity.organization }}
                            </h3>
                        </div>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <h4
                                class="mb-1 font-bold text-slate-700 dark:text-slate-300"
                            >
                                Organization Mission & Policies
                            </h4>
                            <p
                                class="leading-relaxed text-slate-600 dark:text-slate-400"
                            >
                                {{
                                    organizationInfo?.mission ||
                                    'Delivering international development, policy coordination, and humanitarian response under multilateral conventions.'
                                }}
                            </p>
                        </div>

                        <div
                            class="grid grid-cols-2 gap-3 border-t border-slate-100 pt-2 dark:border-slate-800"
                        >
                            <div>
                                <span class="block text-[10px] text-slate-400"
                                    >Duty Station</span
                                >
                                <strong
                                    class="text-slate-800 dark:text-slate-200"
                                    >{{ opportunity.location }}</strong
                                >
                            </div>
                            <div>
                                <span class="block text-[10px] text-slate-400"
                                    >Contract Type</span
                                >
                                <strong
                                    class="text-slate-800 dark:text-slate-200"
                                    >Fixed Term (1 Yr)</strong
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Prominent Auth Callout -->
                    <div
                        class="space-y-3 rounded-2xl border border-[#00b2e3]/20 bg-[#00b2e3]/10 p-4"
                    >
                        <div
                            class="flex items-center gap-2 text-xs font-bold text-[#00b2e3]"
                        >
                            <Lock class="h-4 w-4" /> KBS Suitability Evaluation
                        </div>
                        <p
                            class="text-xs leading-relaxed text-slate-600 dark:text-slate-300"
                        >
                            Sign in or register to benchmark your profile
                            credentials against KBS rules and calculate your
                            transparent suitability score.
                        </p>
                        <button
                            @click="handleApplyClick"
                            class="w-full rounded-xl bg-[#00b2e3] py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-[#0099c4]"
                        >
                            Sign In / Register to Apply
                        </button>
                    </div>
                </div>
            </div>

            <!-- CASE B: Authenticated Candidate -> Personalized KBS Match Scorecard -->
            <div v-else class="space-y-6">
                <div
                    class="sticky top-6 space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div
                        class="flex items-center justify-between border-b border-slate-100 pb-4 dark:border-slate-800"
                    >
                        <div>
                            <h3
                                class="text-xs font-semibold text-slate-500 dark:text-slate-400"
                            >
                                Your KBS Match Score
                            </h3>
                            <div
                                class="mt-0.5 text-3xl font-extrabold text-slate-900 dark:text-slate-100"
                            >
                                {{ evaluation.score
                                }}<span class="text-xs text-slate-400"
                                    >/100</span
                                >
                            </div>
                        </div>
                        <div
                            :class="[
                                'flex items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-bold',
                                getStatusBadge(evaluation.status).class,
                            ]"
                        >
                            <component
                                :is="getStatusBadge(evaluation.status).icon"
                                class="h-3.5 w-3.5"
                            />
                            {{ getStatusBadge(evaluation.status).label }}
                        </div>
                    </div>

                    <!-- Breakdown -->
                    <div class="space-y-3">
                        <h4
                            class="flex items-center gap-1.5 text-xs font-bold tracking-wider text-slate-700 uppercase dark:text-slate-300"
                        >
                            <Scale class="h-3.5 w-3.5 text-[#00b2e3]" /> AHP
                            Weighted Score Breakdown
                        </h4>
                        <div class="space-y-2">
                            <div
                                v-for="item in evaluation.breakdown"
                                :key="item.key"
                                class="space-y-1 rounded-xl border border-slate-200 bg-slate-50 p-2.5 text-xs dark:border-slate-800 dark:bg-slate-950"
                            >
                                <div
                                    class="flex justify-between font-medium text-slate-800 dark:text-slate-200"
                                >
                                    <span>{{ item.criterion }}</span>
                                    <span class="font-mono text-[#00b2e3]"
                                        >+{{ item.contribution }}</span
                                    >
                                </div>
                                <div
                                    class="h-1 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800"
                                >
                                    <div
                                        class="h-full rounded-full bg-[#00b2e3]"
                                        :style="{ width: `${item.raw_score}%` }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Explanations -->
                    <div
                        v-if="evaluation.explanations?.length"
                        class="space-y-2 border-t border-slate-100 pt-3 dark:border-slate-800"
                    >
                        <h4
                            class="flex items-center gap-1.5 text-xs font-bold tracking-wider text-slate-700 uppercase dark:text-slate-300"
                        >
                            <HelpCircle class="h-3.5 w-3.5 text-amber-500" />
                            Rule Inference Explanations
                        </h4>
                        <div
                            v-for="(exp, idx) in evaluation.explanations"
                            :key="idx"
                            class="flex items-start gap-2 rounded-xl border border-amber-500/20 bg-amber-500/10 p-2.5 text-xs text-amber-700 dark:text-amber-300"
                        >
                            <AlertTriangle
                                class="mt-0.5 h-3.5 w-3.5 shrink-0 text-amber-500"
                            />
                            <span>{{ exp }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Guest Auth Modal -->
        <GuestAuthModal
            :show="showAuthModal"
            :jobTitle="opportunity.title"
            @close="showAuthModal = false"
        />

        <!-- Inspira-Style 6-Step Application Wizard Modal -->
        <ApplicationWizardModal
            :show="showWizardModal"
            :job="opportunity"
            :candidateProfile="candidateProfile"
            @close="showWizardModal = false"
            @submitted="
                applicationSubmitted = true;
                showWizardModal = false;
            "
        />

        <!-- Employer Account Action Blocked Modal -->
        <div
            v-if="showEmployerModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm"
        >
            <div
                class="relative w-full max-w-md space-y-5 rounded-3xl border border-slate-200 bg-white p-6 text-center shadow-2xl dark:border-slate-800 dark:bg-slate-900"
            >
                <button
                    @click="showEmployerModal = false"
                    class="absolute top-5 right-5 p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                >
                    <X class="h-5 w-5" />
                </button>

                <div
                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl border border-amber-500/20 bg-amber-500/10 text-amber-500"
                >
                    <Building2 class="h-6 w-6" />
                </div>

                <div class="space-y-2">
                    <h3
                        class="text-base font-bold text-slate-900 dark:text-slate-100"
                    >
                        Employer Account Action Blocked
                    </h3>
                    <p
                        class="text-xs leading-relaxed text-slate-600 dark:text-slate-400"
                    >
                        You are currently logged in as an
                        <strong>Employer / Recruiter Account</strong> ({{
                            currentUser?.name
                        }}). Employer accounts cannot submit job applications.
                    </p>
                </div>

                <div
                    class="rounded-2xl border border-amber-500/20 bg-amber-500/10 p-3.5 text-left text-xs text-amber-700 dark:text-amber-300"
                >
                    To apply for this vacancy, please switch to a
                    <strong>Job Seeker Candidate Account</strong>.
                </div>

                <div class="flex flex-col gap-2 pt-2">
                    <a
                        href="/candidate/portal-switch"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#00b2e3] py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-[#0099c4]"
                    >
                        <UserCheck class="h-4 w-4" /> Switch to Job Seeker
                        Account
                    </a>
                    <button
                        @click="showEmployerModal = false"
                        class="w-full py-2 text-xs font-semibold text-slate-500 hover:underline"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
