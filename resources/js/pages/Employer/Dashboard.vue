<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import {
    Briefcase,
    Users,
    CheckCircle2,
    Clock,
    Plus,
    ExternalLink,
    Building2,
    Edit3,
    Eye,
    X,
    Sparkles,
    AlertCircle,
    Trash2,
    Sliders,
    Zap,
    ImageIcon,
    LogOut,
    Menu,
} from '@lucide/vue';
import { ref, computed } from 'vue';

const mobileMenuOpen = ref(false);

interface CustomRuleItem {
    id: string;
    type:
        | 'SKILL'
        | 'LANGUAGE'
        | 'EDUCATION'
        | 'EXPERIENCE'
        | 'CERTIFICATION'
        | 'HARD_GATE';
    title: string;
    value: string;
    mode: 'MANDATORY_KNOCKOUT' | 'RECOMMENDED_BONUS' | 'WEIGHTED_FACTOR';
    weight: number;
}

interface JobSummary {
    id: number;
    title: string;
    grade: string;
    location: string;
    min_experience: number;
    description?: string;
    required_skills?: string[];
    required_languages?: string[];
    custom_rules?: CustomRuleItem[];
    applicant_count: number;
    recommended_count: number;
    created_at: string;
}

interface RuleTemplate {
    id: number;
    name: string;
    criteria_weights: any;
}

const props = defineProps<{
    organization: {
        id: number;
        name: string;
        code: string;
        org_type: string;
        vision?: string;
        about_us?: string;
        logo_path?: string;
    };
    stats: {
        active_jobs: number;
        total_applicants: number;
        qualification_rate: number;
        hours_saved: number;
    };
    jobs: JobSummary[];
    ruleTemplates: RuleTemplate[];
}>();

const page = usePage();
const flash = computed(
    () => page.props.flash as { success?: string; error?: string },
);

// Modals state
const showOrgModal = ref(false);
const showJobModal = ref(false);

// Forms
const orgForm = useForm({
    name: props.organization?.name || '',
    code: props.organization?.code || '',
    org_type: props.organization?.org_type || 'PRIVATE_COMPANY',
    vision: props.organization?.vision || '',
    about_us: props.organization?.about_us || '',
    logo_url: props.organization?.logo_path || '',
    logo: null as File | null,
});

const logoPreview = ref<string | null>(props.organization?.logo_path || null);

function onLogoFileChange(event: Event) {
    const target = event.target as HTMLInputElement;

    if (target.files && target.files[0]) {
        const file = target.files[0];
        orgForm.logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
}

const jobForm = useForm({
    title: '',
    grade: 'P-3',
    location: '',
    min_experience: 0,
    description: '',
    required_skills: [] as string[],
    required_languages: [] as string[],
    custom_rules: [] as CustomRuleItem[],
});

function addCustomRule() {
    jobForm.custom_rules.push({
        id: 'rule_' + Date.now(),
        type: 'SKILL',
        title: 'New Agency KBS Rule',
        value: '',
        mode: 'WEIGHTED_FACTOR',
        weight: 15,
    });
}

function removeCustomRule(index: number) {
    jobForm.custom_rules.splice(index, 1);
}

function submitOrgUpdate() {
    orgForm.post('/employer/organization', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            showOrgModal.value = false;
        },
    });
}

function submitJobPost() {
    jobForm.post('/employer/jobs', {
        preserveScroll: true,
        onSuccess: () => {
            showJobModal.value = false;
            jobForm.reset();
            jobForm.location = '';
            jobForm.min_experience = 0;
            jobForm.required_skills = [];
            jobForm.required_languages = [];
            jobForm.custom_rules = [];
        },
    });
}
</script>

<template>
    <Head title="Employer Dashboard - KBS Hiring Portal" />

    <div
        class="min-h-screen bg-slate-50 font-sans text-slate-900 transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100"
    >
        <!-- Top Nav Header -->
        <header
            class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 px-4 py-3.5 shadow-sm backdrop-blur-md sm:px-6 dark:border-slate-800 dark:bg-slate-900/90"
        >
            <div class="mx-auto flex max-w-7xl items-center justify-between">
                <Link href="/" class="flex shrink-0 items-center space-x-3">
                    <div
                        v-if="organization?.logo_path"
                        class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-white p-1 shadow-sm dark:border-slate-800"
                    >
                        <img
                            :src="organization.logo_path"
                            :alt="organization.name"
                            class="h-full w-full object-contain"
                        />
                    </div>
                    <div
                        v-else
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#00b2e3] font-bold text-white shadow-md shadow-[#00b2e3]/20"
                    >
                        <Building2 class="h-6 w-6" />
                    </div>
                    <div>
                        <h1
                            class="flex items-center gap-2 text-base leading-tight font-bold text-slate-900 sm:text-lg dark:text-slate-100"
                        >
                            {{ organization?.name || 'Hiring Organization' }}
                        </h1>
                        <p
                            class="hidden text-[11px] text-slate-500 sm:block sm:text-xs dark:text-slate-400"
                        >
                            Employer Hiring & Dynamic KBS Rules Portal
                        </p>
                    </div>
                </Link>

                <div class="hidden items-center space-x-3 md:flex">
                    <button
                        @click="showJobModal = true"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-[#00b2e3] px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#0099c4]"
                    >
                        <Plus class="h-4 w-4" /> Post Vacancy Ad
                    </button>
                    <Link
                        href="/opportunities"
                        class="flex items-center gap-1 text-xs font-semibold text-[#00b2e3] hover:underline"
                    >
                        <Eye class="h-3.5 w-3.5" /> View Public Directory
                    </Link>
                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-rose-500/10 px-3.5 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-500/20 dark:text-rose-400"
                    >
                        <LogOut class="h-3.5 w-3.5" /> Log out
                    </Link>
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex items-center md:hidden">
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        type="button"
                        aria-label="Toggle navigation menu"
                        class="rounded-xl p-2 text-slate-700 transition hover:bg-slate-100 focus:outline-none dark:text-slate-300 dark:hover:bg-slate-800"
                    >
                        <Menu v-if="!mobileMenuOpen" class="h-6 w-6" />
                        <X v-else class="h-6 w-6" />
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
                <div
                    v-if="mobileMenuOpen"
                    class="mt-3 space-y-2 border-t border-slate-200 pt-3 md:hidden dark:border-slate-800"
                >
                    <button
                        @click="
                            showJobModal = true;
                            mobileMenuOpen = false;
                        "
                        class="flex w-full items-center gap-2 rounded-xl bg-[#00b2e3] px-3 py-2.5 text-left text-sm font-semibold text-white transition hover:bg-[#0099c4]"
                    >
                        <Plus class="h-4 w-4" /> Post Vacancy Ad
                    </button>
                    <Link
                        href="/opportunities"
                        @click="mobileMenuOpen = false"
                        class="block rounded-xl bg-[#00b2e3]/10 px-3 py-2.5 text-sm font-semibold text-[#00b2e3] transition"
                    >
                        View Public Directory
                    </Link>
                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        @click="mobileMenuOpen = false"
                        class="flex w-full items-center gap-2 rounded-xl bg-rose-500/10 px-3 py-2.5 text-left text-sm font-semibold text-rose-600 transition hover:bg-rose-500/20 dark:text-rose-400"
                    >
                        <LogOut class="h-4 w-4" /> Log out
                    </Link>
                </div>
            </transition>
        </header>

        <main class="mx-auto max-w-7xl space-y-8 px-6 py-10">
            <!-- System Flash Messages -->
            <div
                v-if="flash?.success"
                class="flex items-center gap-2 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-xs font-semibold text-emerald-600 dark:text-emerald-400"
            >
                <CheckCircle2 class="h-4 w-4 shrink-0 text-emerald-500" />
                <span>{{ flash.success }}</span>
            </div>
            <div
                v-if="flash?.error"
                class="flex items-center gap-2 rounded-2xl border border-rose-500/30 bg-rose-500/10 p-4 text-xs font-semibold text-rose-600 dark:text-rose-400"
            >
                <AlertCircle class="h-4 w-4 shrink-0 text-rose-500" />
                <span>{{ flash.error }}</span>
            </div>

            <!-- Organization Information Card -->
            <div
                class="relative space-y-6 overflow-hidden rounded-3xl border border-slate-200 bg-white p-7 shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <div
                    class="flex flex-col justify-between gap-4 border-b border-slate-100 pb-5 md:flex-row md:items-center dark:border-slate-800"
                >
                    <div class="flex items-center space-x-4">
                        <div
                            v-if="organization?.logo_path"
                            class="flex h-16 w-16 items-center justify-center rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm dark:border-slate-800"
                        >
                            <img
                                :src="organization.logo_path"
                                :alt="organization.name"
                                class="h-full w-full rounded-xl object-contain"
                            />
                        </div>
                        <div
                            v-else
                            class="flex h-16 w-16 items-center justify-center rounded-2xl border border-[#00b2e3]/20 bg-[#00b2e3]/10 font-mono text-xl font-bold text-[#00b2e3] shadow-sm"
                        >
                            {{ organization?.code || 'ORG' }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2
                                    class="text-xl font-extrabold text-slate-900 dark:text-slate-100"
                                >
                                    {{ organization?.name }}
                                </h2>
                                <span
                                    class="rounded-full bg-[#00b2e3]/10 px-2.5 py-0.5 font-mono text-[10px] font-bold text-[#00b2e3]"
                                >
                                    {{ organization?.org_type || 'UN_AGENCY' }}
                                </span>
                            </div>
                            <p
                                class="mt-0.5 text-xs text-slate-500 dark:text-slate-400"
                            >
                                Verified Hiring Entity • Account Code:
                                <strong
                                    class="font-mono text-slate-700 dark:text-slate-300"
                                    >{{ organization?.code }}</strong
                                >
                            </p>
                        </div>
                    </div>

                    <button
                        @click="showOrgModal = true"
                        class="inline-flex items-center gap-1.5 self-start rounded-xl bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-200 md:self-auto dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                    >
                        <Edit3 class="h-3.5 w-3.5 text-[#00b2e3]" /> Edit
                        Organization Details
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-6 text-xs md:grid-cols-2">
                    <!-- Vision Statement -->
                    <div
                        class="space-y-2 rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950"
                    >
                        <h3
                            class="flex items-center gap-1.5 text-[11px] font-bold tracking-wider text-[#00b2e3] text-slate-900 uppercase dark:text-slate-100"
                        >
                            <Sparkles class="h-3.5 w-3.5" /> Organization Vision
                        </h3>
                        <p
                            class="leading-relaxed text-slate-600 italic dark:text-slate-400"
                        >
                            "{{
                                organization?.vision ||
                                'Accelerating sustainable global development through automated knowledge-driven talent acquisition.'
                            }}"
                        </p>
                    </div>

                    <!-- About Us / Overview -->
                    <div
                        class="space-y-2 rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950"
                    >
                        <h3
                            class="flex items-center gap-1.5 text-[11px] font-bold tracking-wider text-[#00b2e3] text-slate-900 uppercase dark:text-slate-100"
                        >
                            <Building2 class="h-3.5 w-3.5" /> About Us &
                            Operational Focus
                        </h3>
                        <p
                            class="leading-relaxed text-slate-600 dark:text-slate-400"
                        >
                            {{
                                organization?.about_us ||
                                'International multilateral organization dedicated to global humanitarian and developmental outcomes.'
                            }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- KBS Metric Highlights -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    class="space-y-1 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div
                        class="flex items-center justify-between text-slate-500 dark:text-slate-400"
                    >
                        <span
                            class="text-xs font-semibold tracking-wider uppercase"
                            >Posted Vacancies</span
                        >
                        <Briefcase class="h-4 w-4 text-[#00b2e3]" />
                    </div>
                    <div
                        class="text-2xl font-extrabold text-slate-900 dark:text-slate-100"
                    >
                        {{ stats.active_jobs }}
                    </div>
                </div>

                <div
                    class="space-y-1 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div
                        class="flex items-center justify-between text-slate-500 dark:text-slate-400"
                    >
                        <span
                            class="text-xs font-semibold tracking-wider uppercase"
                            >Received Applications</span
                        >
                        <Users class="h-4 w-4 text-blue-500" />
                    </div>
                    <div
                        class="text-2xl font-extrabold text-slate-900 dark:text-slate-100"
                    >
                        {{ stats.total_applicants }}
                    </div>
                </div>

                <div
                    class="space-y-1 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div
                        class="flex items-center justify-between text-slate-500 dark:text-slate-400"
                    >
                        <span
                            class="text-xs font-semibold tracking-wider uppercase"
                            >KBS Pass Rate</span
                        >
                        <CheckCircle2 class="h-4 w-4 text-emerald-500" />
                    </div>
                    <div
                        class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400"
                    >
                        {{ stats.qualification_rate }}%
                    </div>
                </div>

                <div
                    class="space-y-1 rounded-2xl border border-[#00b2e3]/30 bg-gradient-to-br from-[#00b2e3]/10 to-blue-50 p-5 shadow-sm dark:from-[#00b2e3]/20 dark:to-slate-900"
                >
                    <div
                        class="flex items-center justify-between text-[#00b2e3]"
                    >
                        <span class="text-xs font-bold tracking-wider uppercase"
                            >Screening Hours Saved</span
                        >
                        <Clock class="h-4 w-4 text-[#00b2e3]" />
                    </div>
                    <div class="text-2xl font-extrabold text-[#00b2e3]">
                        {{ stats.hours_saved }} Hours
                    </div>
                    <p class="text-[10px] text-[#00b2e3]/80">
                        Automated KBS Inference Benefit
                    </p>
                </div>
            </div>

            <!-- Posted Vacancies List -->
            <div
                class="space-y-5 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <div
                    class="flex flex-col justify-between gap-3 border-b border-slate-100 pb-4 sm:flex-row sm:items-center dark:border-slate-800"
                >
                    <div>
                        <h2
                            class="text-lg font-bold text-slate-900 dark:text-slate-100"
                        >
                            Organization Vacancies
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Positions uploaded by your organization and active
                            KBS candidate evaluation pipelines.
                        </p>
                    </div>
                    <button
                        @click="showJobModal = true"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-[#00b2e3] px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#0099c4]"
                    >
                        <Plus class="h-4 w-4" /> Create New Job Ad
                    </button>
                </div>

                <div v-if="jobs && jobs.length" class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr
                                class="border-b border-slate-200 text-xs font-semibold text-slate-500 dark:border-slate-800 dark:text-slate-400"
                            >
                                <th class="px-4 py-3">Position Title</th>
                                <th class="px-4 py-3">Grade</th>
                                <th class="px-4 py-3">Duty Station</th>
                                <th class="px-4 py-3">KBS Rules Configured</th>
                                <th class="px-4 py-3">Total Applicants</th>
                                <th class="px-4 py-3">KBS Qualified</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-slate-100 text-sm dark:divide-slate-800"
                        >
                            <tr
                                v-for="job in jobs"
                                :key="job.id"
                                class="transition hover:bg-slate-50 dark:hover:bg-slate-800/40"
                            >
                                <td
                                    class="px-4 py-4 font-semibold text-slate-900 dark:text-slate-100"
                                >
                                    {{ job.title }}
                                </td>
                                <td
                                    class="px-4 py-4 font-mono text-xs font-bold text-[#00b2e3]"
                                >
                                    {{ job.grade }}
                                </td>
                                <td
                                    class="px-4 py-4 text-xs text-slate-600 dark:text-slate-400"
                                >
                                    {{ job.location }}
                                </td>
                                <td class="px-4 py-4">
                                    <span
                                        class="inline-flex items-center gap-1 rounded-lg bg-blue-500/10 px-2.5 py-1 text-xs font-bold text-blue-600 dark:text-blue-400"
                                    >
                                        <Zap class="h-3 w-3" />
                                        {{
                                            (job.custom_rules?.length || 0) +
                                            (job.required_skills?.length || 0)
                                        }}
                                        Rules
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <span
                                        class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                                    >
                                        {{ job.applicant_count }} Applicants
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <span
                                        class="rounded-lg bg-emerald-500/10 px-2.5 py-1 text-xs font-bold text-emerald-600 dark:text-emerald-400"
                                    >
                                        {{ job.recommended_count }} Recommended
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <Link
                                        :href="`/opportunities/${job.id}/applicants`"
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-[#00b2e3]/10 px-3.5 py-1.5 text-xs font-semibold text-[#00b2e3] transition hover:bg-[#00b2e3]/20"
                                    >
                                        Inspect Applicants Matrix
                                        <ExternalLink class="h-3.5 w-3.5" />
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-else
                    class="space-y-3 rounded-2xl border border-slate-200 bg-slate-50 py-12 text-center dark:border-slate-800 dark:bg-slate-950"
                >
                    <Briefcase class="mx-auto h-10 w-10 text-slate-400" />
                    <h3
                        class="text-sm font-bold text-slate-800 dark:text-slate-200"
                    >
                        No Job Positions Uploaded Yet
                    </h3>
                    <p class="text-xs text-slate-500">
                        Create your organization's first vacancy ad to activate
                        automated KBS screening rules.
                    </p>
                    <button
                        @click="showJobModal = true"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-[#00b2e3] px-4 py-2 text-xs font-semibold text-white"
                    >
                        <Plus class="h-4 w-4" /> Post First Job Ad
                    </button>
                </div>
            </div>
        </main>

        <!-- Modal 1: Edit Organization Profile & Logo Provisioning -->
        <div
            v-if="showOrgModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm"
        >
            <div
                class="relative max-h-[90vh] w-full max-w-lg space-y-5 overflow-y-auto rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900"
            >
                <button
                    @click="showOrgModal = false"
                    class="absolute top-5 right-5 p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                >
                    <X class="h-5 w-5" />
                </button>

                <div class="flex items-center space-x-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#00b2e3]/10 font-bold text-[#00b2e3]"
                    >
                        <Building2 class="h-5 w-5" />
                    </div>
                    <div>
                        <h3
                            class="text-base font-bold text-slate-900 dark:text-slate-100"
                        >
                            Edit Organization Information
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Update Profile Credentials, Logo & Overview
                        </p>
                    </div>
                </div>

                <form
                    @submit.prevent="submitOrgUpdate"
                    class="space-y-4 text-xs"
                >
                    <!-- LOGO PROVISION SECTION -->
                    <div
                        class="space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950"
                    >
                        <label
                            class="block flex items-center gap-1.5 text-[10px] font-bold tracking-wider text-[#00b2e3] uppercase"
                        >
                            <ImageIcon class="h-3.5 w-3.5" /> Organization Logo
                            Provisioning
                        </label>

                        <div class="flex items-center space-x-4">
                            <!-- Logo Preview Box -->
                            <div
                                class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm dark:border-slate-800"
                            >
                                <img
                                    v-if="logoPreview"
                                    :src="logoPreview"
                                    alt="Logo Preview"
                                    class="h-full w-full rounded-xl object-contain"
                                />
                                <Building2
                                    v-else
                                    class="h-6 w-6 text-slate-400"
                                />
                            </div>

                            <div class="flex-1 space-y-2">
                                <div>
                                    <label
                                        class="mb-1 block text-[10px] font-semibold text-slate-500"
                                        >Upload File (PNG, JPG, SVG,
                                        WebP)</label
                                    >
                                    <input
                                        type="file"
                                        accept="image/*"
                                        @change="onLogoFileChange"
                                        class="w-full text-xs text-slate-500 transition file:mr-3 file:rounded-xl file:border-0 file:bg-[#00b2e3]/10 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-[#00b2e3] hover:file:bg-[#00b2e3]/20"
                                    />
                                    <p
                                        v-if="orgForm.errors.logo"
                                        class="mt-1 text-[11px] font-semibold text-rose-500"
                                    >
                                        {{ orgForm.errors.logo }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label
                                class="mb-1 block text-[10px] font-semibold text-slate-500"
                                >Or Logo Image URL / Preset Badge Link</label
                            >
                            <input
                                v-model="orgForm.logo_url"
                                type="text"
                                placeholder="e.g. https://upload.wikimedia.org/wikipedia/commons/e/ed/UNICEF_Logo.svg"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs focus:outline-none dark:border-slate-800 dark:bg-slate-900"
                            />
                            <p
                                v-if="orgForm.errors.logo_url"
                                class="mt-1 text-[11px] font-semibold text-rose-500"
                            >
                                {{ orgForm.errors.logo_url }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <label
                            class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                            >Organization Name</label
                        >
                        <input
                            v-model="orgForm.name"
                            type="text"
                            required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-900 focus:ring-2 focus:ring-[#00b2e3] focus:outline-none dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                                >Acronym / Code</label
                            >
                            <input
                                v-model="orgForm.code"
                                type="text"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 font-mono text-xs text-slate-900 focus:ring-2 focus:ring-[#00b2e3] focus:outline-none dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                                >Organization Type</label
                            >
                            <select
                                v-model="orgForm.org_type"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-[#00b2e3] focus:outline-none dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100"
                            >
                                <option value="PRIVATE_COMPANY">
                                    Private Sector / Corporate
                                </option>
                                <option value="NGO">
                                    NGO / Non-Governmental Organization
                                </option>
                                <option value="PARASTATAL">
                                    Parastatal / State Enterprise
                                </option>
                                <option value="GOV_BODY">
                                    Government Body / Ministry
                                </option>
                                <option value="UN_AGENCY">
                                    UN Agency / Multilateral
                                </option>
                                <option value="INTERNATIONAL_ORG">
                                    International Organization
                                </option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label
                            class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                            >Organization Vision Statement</label
                        >
                        <textarea
                            v-model="orgForm.vision"
                            rows="2"
                            placeholder="State your organization's core vision..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-900 focus:ring-2 focus:ring-[#00b2e3] focus:outline-none dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100"
                        ></textarea>
                    </div>

                    <div>
                        <label
                            class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                            >About Us & Overview</label
                        >
                        <textarea
                            v-model="orgForm.about_us"
                            rows="3"
                            placeholder="Provide a brief overview of your organization's mission and mandate..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-900 focus:ring-2 focus:ring-[#00b2e3] focus:outline-none dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100"
                        ></textarea>
                    </div>

                    <div
                        class="flex justify-end gap-2 border-t border-slate-100 pt-3 dark:border-slate-800"
                    >
                        <button
                            type="button"
                            @click="showOrgModal = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:underline dark:text-slate-400"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="orgForm.processing"
                            class="rounded-xl bg-[#00b2e3] px-5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#0099c4] disabled:opacity-40"
                        >
                            Save Organization Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal 2: Create Job Ad with DYNAMIC Agency KBS Custom Rules Engine Builder -->
        <div
            v-if="showJobModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm"
        >
            <div
                class="relative max-h-[90vh] w-full max-w-3xl space-y-5 overflow-y-auto rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900"
            >
                <button
                    @click="showJobModal = false"
                    class="absolute top-5 right-5 p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                >
                    <X class="h-5 w-5" />
                </button>

                <div class="flex items-center space-x-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#00b2e3]/10 font-bold text-[#00b2e3]"
                    >
                        <Zap class="h-5 w-5" />
                    </div>
                    <div>
                        <h3
                            class="text-base font-bold text-slate-900 dark:text-slate-100"
                        >
                            Create Vacancy Ad & Custom KBS Rules
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Configure dynamic screening rules, knockout
                            parameters, and custom scoring weights.
                        </p>
                    </div>
                </div>

                <form @submit.prevent="submitJobPost" class="space-y-5 text-xs">
                    <!-- Position Title & Duty Station -->
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label
                                class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                                >Job Position Title</label
                            >
                            <input
                                v-model="jobForm.title"
                                type="text"
                                required
                                placeholder="e.g. Senior Climate Policy Advisor"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-900 focus:ring-2 focus:ring-[#00b2e3] focus:outline-none dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                                >Duty Station / Location</label
                            >
                            <input
                                v-model="jobForm.location"
                                type="text"
                                required
                                placeholder="e.g. Nairobi, Kenya"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-900 focus:ring-2 focus:ring-[#00b2e3] focus:outline-none dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100"
                            />
                        </div>
                    </div>

                    <!-- Position Grade & Min Experience -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                                >Position Grade</label
                            >
                            <select
                                v-model="jobForm.grade"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-[#00b2e3] focus:outline-none dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100"
                            >
                                <option value="P-1">
                                    P-1 (Entry Professional)
                                </option>
                                <option value="P-2">
                                    P-2 (Associate Officer)
                                </option>
                                <option value="P-3">
                                    P-3 (Professional Specialist)
                                </option>
                                <option value="P-4">
                                    P-4 (Senior Specialist)
                                </option>
                                <option value="P-5">
                                    P-5 (Chief / Senior Manager)
                                </option>
                                <option value="D-1">D-1 (Director)</option>
                                <option value="GS-7">
                                    GS-7 (General Service Lead)
                                </option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                                >Min Required Experience (Years)</label
                            >
                            <input
                                v-model.number="jobForm.min_experience"
                                type="number"
                                min="0"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-900 focus:ring-2 focus:ring-[#00b2e3] focus:outline-none dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100"
                            />
                        </div>
                    </div>

                    <!-- DYNAMIC AGENCY KBS CUSTOM RULES ENGINE BUILDER -->
                    <div
                        class="space-y-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <h4
                                    class="flex items-center gap-1.5 text-[11px] font-bold tracking-wider text-[#00b2e3] uppercase"
                                >
                                    <Sliders class="h-4 w-4" /> Agency Custom
                                    KBS Rules Builder
                                </h4>
                                <p class="text-[11px] text-slate-500">
                                    Add dynamic screening rules, knockout
                                    parameters, and custom scoring weights
                                    specific to this vacancy.
                                </p>
                            </div>
                            <button
                                type="button"
                                @click="addCustomRule"
                                class="inline-flex items-center gap-1 rounded-xl bg-[#00b2e3] px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-[#0099c4]"
                            >
                                <Plus class="h-3.5 w-3.5" /> Add Custom KBS Rule
                            </button>
                        </div>

                        <!-- Custom Rules List -->
                        <div
                            v-if="
                                jobForm.custom_rules &&
                                jobForm.custom_rules.length
                            "
                            class="space-y-3"
                        >
                            <div
                                v-for="(rule, rIdx) in jobForm.custom_rules"
                                :key="rule.id"
                                class="group relative space-y-3 rounded-xl border border-slate-200 bg-white p-3.5 dark:border-slate-800 dark:bg-slate-900"
                            >
                                <div class="flex items-center justify-between">
                                    <span
                                        class="flex items-center gap-1 text-[11px] font-bold text-slate-700 dark:text-slate-300"
                                    >
                                        ⚡ Rule #{{ rIdx + 1 }}
                                    </span>
                                    <button
                                        type="button"
                                        @click="removeCustomRule(rIdx)"
                                        class="inline-flex items-center gap-1 p-1 text-xs font-semibold text-rose-500 hover:text-rose-700"
                                    >
                                        <Trash2 class="h-3.5 w-3.5" /> Remove
                                    </button>
                                </div>

                                <div
                                    class="grid grid-cols-1 gap-2.5 sm:grid-cols-2 lg:grid-cols-4"
                                >
                                    <!-- Rule Type -->
                                    <div>
                                        <label
                                            class="mb-1 block text-[10px] font-semibold text-slate-500"
                                            >Rule Parameter</label
                                        >
                                        <select
                                            v-model="rule.type"
                                            class="w-full rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs font-semibold text-slate-800 focus:outline-none dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200"
                                        >
                                            <option value="SKILL">
                                                Technical Skill
                                            </option>
                                            <option value="LANGUAGE">
                                                Official Language
                                            </option>
                                            <option value="EDUCATION">
                                                Education Degree
                                            </option>
                                            <option value="EXPERIENCE">
                                                Min Years Experience
                                            </option>
                                            <option value="CERTIFICATION">
                                                License / Certification
                                            </option>
                                            <option value="HARD_GATE">
                                                Hard-Gate Requirement
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Rule Title / Label -->
                                    <div>
                                        <label
                                            class="mb-1 block text-[10px] font-semibold text-slate-500"
                                            >Rule Title / Description</label
                                        >
                                        <input
                                            v-model="rule.title"
                                            type="text"
                                            placeholder="e.g. PMP Certification"
                                            class="w-full rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs text-slate-800 focus:outline-none dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200"
                                        />
                                    </div>

                                    <!-- Requirement Value -->
                                    <div>
                                        <label
                                            class="mb-1 block text-[10px] font-semibold text-slate-500"
                                            >Required Value</label
                                        >
                                        <input
                                            v-model="rule.value"
                                            type="text"
                                            placeholder="e.g. French / PMP / Master"
                                            class="w-full rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs text-slate-800 focus:outline-none dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200"
                                        />
                                    </div>

                                    <!-- Enforcement Mode -->
                                    <div>
                                        <label
                                            class="mb-1 block text-[10px] font-semibold text-slate-500"
                                            >Enforcement Mode</label
                                        >
                                        <select
                                            v-model="rule.mode"
                                            class="w-full rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs font-semibold text-slate-800 focus:outline-none dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200"
                                        >
                                            <option value="MANDATORY_KNOCKOUT">
                                                ⛔ Mandatory Knockout
                                            </option>
                                            <option value="RECOMMENDED_BONUS">
                                                ⭐ Recommended Bonus
                                            </option>
                                            <option value="WEIGHTED_FACTOR">
                                                ⚖️ Weighted Scoring Factor
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            v-else
                            class="py-4 text-center text-xs text-slate-500 italic"
                        >
                            No custom rules added yet. Click "+ Add Custom KBS
                            Rule" to build agency-specific screening parameters.
                        </div>
                    </div>

                    <!-- Job Specification / Description -->
                    <div>
                        <label
                            class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                            >Detailed Job Description & Responsibilities</label
                        >
                        <textarea
                            v-model="jobForm.description"
                            rows="4"
                            required
                            placeholder="Describe the duties, responsibilities, and expected outcomes for this vacancy..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-900 focus:ring-2 focus:ring-[#00b2e3] focus:outline-none dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100"
                        ></textarea>
                    </div>

                    <div
                        class="flex justify-end gap-2 border-t border-slate-100 pt-3 dark:border-slate-800"
                    >
                        <button
                            type="button"
                            @click="showJobModal = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:underline dark:text-slate-400"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="jobForm.processing"
                            class="rounded-xl bg-[#00b2e3] px-5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#0099c4] disabled:opacity-40"
                        >
                            Publish Vacancy & Active KBS Rules
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
