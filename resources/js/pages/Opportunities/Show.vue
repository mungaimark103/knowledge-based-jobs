<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, ShieldCheck, CheckCircle2, AlertTriangle, XCircle, Scale, HelpCircle, Building2, MapPin, Calendar, FileText, Lock, UserCheck, X } from '@lucide/vue';
import ThemeToggle from '@/components/ThemeToggle.vue';
import GuestAuthModal from '@/components/GuestAuthModal.vue';
import ApplicationWizardModal from '@/components/ApplicationWizardModal.vue';

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
    if (status === 'recommended') return { label: 'RECOMMENDED', class: 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/30', icon: CheckCircle2 };
    if (status === 'flagged') return { label: 'NEEDS REVIEW', class: 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/30', icon: AlertTriangle };
    return { label: 'EXCLUDED', class: 'bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/30', icon: XCircle };
};
</script>

<template>
    <Head :title="`${opportunity.title} - Job Details`" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans transition-colors duration-300">
        <!-- Top Nav -->
        <header class="border-b border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 px-6 py-4 backdrop-blur-md">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <Link href="/opportunities" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 transition">
                    <ArrowLeft class="w-4 h-4" /> Back to Directory
                </Link>
                <div class="flex items-center gap-4">
                    <!-- <ThemeToggle /> -->
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300">
                        <ShieldCheck class="w-4 h-4 text-[#00b2e3]" /> KBS Explanation Facility
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 py-10 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Columns: Job Details -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 bg-[#00b2e3]/10 border border-[#00b2e3]/20 text-[#00b2e3] font-mono font-bold rounded-lg text-xs">
                                {{ opportunity.grade }}
                            </span>
                            <span class="text-xs uppercase tracking-wider font-semibold text-slate-500 dark:text-slate-400">
                                {{ opportunity.organization }}
                            </span>
                        </div>

                        <!-- Apply Button -->
                        <button
                            @click="handleApplyClick"
                            :disabled="applicationSubmitted"
                            class="px-5 py-2 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl shadow-sm transition disabled:bg-emerald-600"
                        >
                            {{ applicationSubmitted ? 'Application Submitted ✓' : 'Apply for Position' }}
                        </button>
                    </div>

                    <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100">
                        {{ opportunity.title }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-6 text-xs text-slate-500 dark:text-slate-400 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <div>Location: <strong class="text-slate-800 dark:text-slate-200">{{ opportunity.location }}</strong></div>
                        <div>Min Experience: <strong class="text-slate-800 dark:text-slate-200">{{ opportunity.min_experience }} Years</strong></div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">Job Description</h2>
                    <p class="text-slate-600 dark:text-slate-300 text-xs leading-relaxed">
                        {{ opportunity.description }}
                    </p>

                    <div class="space-y-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <h3 class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Required Skills</h3>
                        <div class="flex flex-wrap gap-1.5">
                            <span
                                v-for="skill in opportunity.required_skills"
                                :key="skill"
                                class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-medium"
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
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 space-y-5 shadow-sm sticky top-6">
                    <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="h-12 w-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                            <Building2 class="w-6 h-6" />
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ organizationInfo?.type || 'Multilateral Entity' }}</span>
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">{{ opportunity.organization }}</h3>
                        </div>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-1">Organization Mission & Policies</h4>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ organizationInfo?.mission || 'Delivering international development, policy coordination, and humanitarian response under multilateral conventions.' }}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <div>
                                <span class="text-slate-400 block text-[10px]">Duty Station</span>
                                <strong class="text-slate-800 dark:text-slate-200">{{ opportunity.location }}</strong>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">Contract Type</span>
                                <strong class="text-slate-800 dark:text-slate-200">Fixed Term (1 Yr)</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Prominent Auth Callout -->
                    <div class="bg-[#00b2e3]/10 border border-[#00b2e3]/20 rounded-2xl p-4 space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-[#00b2e3]">
                            <Lock class="w-4 h-4" /> KBS Suitability Evaluation
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            Sign in or register to benchmark your profile credentials against KBS rules and calculate your transparent suitability score.
                        </p>
                        <button
                            @click="handleApplyClick"
                            class="w-full py-2.5 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl transition shadow-sm"
                        >
                            Sign In / Register to Apply
                        </button>
                    </div>
                </div>
            </div>

            <!-- CASE B: Authenticated Candidate -> Personalized KBS Match Scorecard -->
            <div v-else class="space-y-6">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 space-y-6 shadow-sm sticky top-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <h3 class="text-xs font-semibold text-slate-500 dark:text-slate-400">Your KBS Match Score</h3>
                            <div class="text-3xl font-extrabold text-slate-900 dark:text-slate-100 mt-0.5">
                                {{ evaluation.score }}<span class="text-xs text-slate-400">/100</span>
                            </div>
                        </div>
                        <div :class="['px-3 py-1.5 rounded-xl border flex items-center gap-1.5 text-xs font-bold', getStatusBadge(evaluation.status).class]">
                            <component :is="getStatusBadge(evaluation.status).icon" class="w-3.5 h-3.5" />
                            {{ getStatusBadge(evaluation.status).label }}
                        </div>
                    </div>

                    <!-- Breakdown -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5 uppercase tracking-wider">
                            <Scale class="w-3.5 h-3.5 text-[#00b2e3]" /> AHP Weighted Score Breakdown
                        </h4>
                        <div class="space-y-2">
                            <div
                                v-for="item in evaluation.breakdown"
                                :key="item.key"
                                class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 p-2.5 rounded-xl text-xs space-y-1"
                            >
                                <div class="flex justify-between font-medium text-slate-800 dark:text-slate-200">
                                    <span>{{ item.criterion }}</span>
                                    <span class="text-[#00b2e3] font-mono">+{{ item.contribution }}</span>
                                </div>
                                <div class="w-full bg-slate-200 dark:bg-slate-800 h-1 rounded-full overflow-hidden">
                                    <div
                                        class="bg-[#00b2e3] h-full rounded-full"
                                        :style="{ width: `${item.raw_score}%` }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Explanations -->
                    <div v-if="evaluation.explanations?.length" class="space-y-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5 uppercase tracking-wider">
                            <HelpCircle class="w-3.5 h-3.5 text-amber-500" /> Rule Inference Explanations
                        </h4>
                        <div
                            v-for="(exp, idx) in evaluation.explanations"
                            :key="idx"
                            class="bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-300 rounded-xl p-2.5 text-xs flex items-start gap-2"
                        >
                            <AlertTriangle class="w-3.5 h-3.5 text-amber-500 shrink-0 mt-0.5" />
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
            @submitted="applicationSubmitted = true; showWizardModal = false;"
        />

        <!-- Employer Account Action Blocked Modal -->
        <div v-if="showEmployerModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 text-center relative">
                <button @click="showEmployerModal = false" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1">
                    <X class="w-5 h-5" />
                </button>

                <div class="h-12 w-12 rounded-2xl bg-amber-500/10 text-amber-500 border border-amber-500/20 flex items-center justify-center mx-auto">
                    <Building2 class="w-6 h-6" />
                </div>

                <div class="space-y-2">
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">
                        Employer Account Action Blocked
                    </h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                        You are currently logged in as an <strong>Employer / Recruiter Account</strong> ({{ currentUser?.name }}). Employer accounts cannot submit job applications.
                    </p>
                </div>

                <div class="bg-amber-500/10 border border-amber-500/20 p-3.5 rounded-2xl text-xs text-amber-700 dark:text-amber-300 text-left">
                    To apply for this vacancy, please switch to a <strong>Job Seeker Candidate Account</strong>.
                </div>

                <div class="flex flex-col gap-2 pt-2">
                    <a
                        href="/candidate/portal-switch"
                        class="w-full py-2.5 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl shadow-sm transition inline-flex items-center justify-center gap-2"
                    >
                        <UserCheck class="w-4 h-4" /> Switch to Job Seeker Account
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
