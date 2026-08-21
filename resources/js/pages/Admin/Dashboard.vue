<script setup lang="ts">
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import {
    ShieldCheck,
    Building2,
    Users,
    Sliders,
    CheckCircle2,
    Plus,
    Sparkles,
    Eye,
    Check,
    X,
    FileText,
    Award,
    Download,
    UserPlus,
    Filter,
    Menu,
    Trash2,
    Pencil,
} from '@lucide/vue';
import { ref, computed } from 'vue';

const mobileMenuOpen = ref(false);

interface PaginatedData<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
    total: number;
}

interface Organization {
    id: number;
    name: string;
    code: string;
    org_type: string;
    logo_path?: string;
    is_verified: boolean;
    verified_at?: string;
    job_count: number;
    created_at: string;
}

interface Candidate {
    id: number;
    name: string;
    email: string;
    education_level: string;
    years_experience: number;
    reliability_score: number;
    skills: string[];
    is_verified: boolean;
    verified_at?: string;
    resume_filename?: string;
    created_at: string;
}

interface AgencyStaffItem {
    id: number;
    name: string;
    email: string;
    agency_sub_role: string;
    created_at: string;
}

interface Criterion {
    id: number;
    name: string;
    key: string;
    weight: number;
}

interface Rule {
    id: number;
    name: string;
    field: string;
    operator: string;
    value: string;
    action: string;
    explanation_template: string;
    active: boolean;
}

interface AuditApplication {
    id: number;
    job_posting_id?: number;
    candidate_name: string;
    job_title: string;
    organization_name: string;
    status: string;
    applied_at: string;
    kbs_score: number;
    kbs_status: string;
    explanations: string[];
}

const props = defineProps<{
    organizations: PaginatedData<Organization>;
    candidates: PaginatedData<Candidate>;
    agencyStaff: AgencyStaffItem[];
    criteria: Criterion[];
    rules: Rule[];
    applications: AuditApplication[];
    stats: {
        total_organizations: number;
        verified_organizations: number;
        total_candidates: number;
        verified_candidates: number;
        total_applications: number;
        total_staff?: number;
    };
}>();

const page = usePage();
const flash = computed(
    () => page.props.flash as { success?: string; error?: string },
);

const activeTab = ref<
    'overview' | 'organizations' | 'candidates' | 'kbs_rules' | 'staff'
>('overview');
const showProxyModal = ref(false);
const showProvisionStaffModal = ref(false);
const showAddRuleModal = ref(false);
const auditFilter = ref<'all' | 'recommended' | 'flagged' | 'excluded'>('all');

const filteredApplications = computed(() => {
    if (auditFilter.value === 'all') {
        return props.applications;
    }

    return props.applications.filter((a) => a.kbs_status === auditFilter.value);
});

// Proxy Candidate Registration Form
const proxyForm = useForm({
    name: '',
    email: '',
    education_level: "Bachelor's Degree",
    years_experience: 3,
    skills_raw: '',
    summary: '',
});

function submitProxyCandidate() {
    const skillsArr = proxyForm.skills_raw
        .split(',')
        .map((s) => s.trim())
        .filter((s) => s.length > 0);

    proxyForm
        .transform((data) => ({
            ...data,
            skills: skillsArr.length > 0 ? skillsArr : ['General Skills'],
        }))
        .post('/admin/candidates/proxy', {
            onSuccess: () => {
                showProxyModal.value = false;
                proxyForm.reset();
            },
        });
}

// Provision Agency Staff Form
const provisionStaffForm = useForm({
    name: '',
    email: '',
    agency_sub_role: 'verification_officer',
    password: '',
});

function submitProvisionStaff() {
    provisionStaffForm.post('/admin/staff/provision', {
        onSuccess: () => {
            showProvisionStaffModal.value = false;
            provisionStaffForm.reset();
        },
    });
}

// Create Global IF-THEN Rule Form
const newRuleForm = useForm({
    name: '',
    field: 'years_experience',
    operator: '>=',
    value: '3',
    action: 'flag',
    explanation_template: 'Satisfies global requirement for :value',
});

function submitCreateRule() {
    newRuleForm.post('/admin/rules', {
        onSuccess: () => {
            showAddRuleModal.value = false;
            newRuleForm.reset();
        },
    });
}

// Edit Global IF-THEN Rule Form
const showEditRuleModal = ref(false);
const editingRuleId = ref<number | null>(null);
const editRuleForm = useForm({
    name: '',
    field: 'years_experience',
    operator: '>=',
    value: '',
    action: 'flag',
    explanation_template: '',
});

function openEditRuleModal(rule: Rule) {
    editingRuleId.value = rule.id;
    editRuleForm.name = rule.name;
    editRuleForm.field = rule.field;
    editRuleForm.operator = rule.operator;
    editRuleForm.value = rule.value;
    editRuleForm.action = rule.action;
    editRuleForm.explanation_template = rule.explanation_template;
    showEditRuleModal.value = true;
}

function submitUpdateRule() {
    if (!editingRuleId.value) return;
    editRuleForm.put(`/admin/rules/${editingRuleId.value}`, {
        onSuccess: () => {
            showEditRuleModal.value = false;
            editingRuleId.value = null;
            editRuleForm.reset();
        },
    });
}

function deleteRule(rule: Rule) {
    if (confirm(`Are you sure you want to delete the rule "${rule.name}"? This action cannot be undone.`)) {
        router.delete(`/admin/rules/${rule.id}`, {
            preserveScroll: true,
        });
    }
}

// Criteria Weight Form
const criteriaForm = useForm({
    criteria: props.criteria.map((c) => ({ id: c.id, weight: c.weight })),
});

function updateCriteriaWeights() {
    criteriaForm.post('/admin/criteria/weights', {
        preserveScroll: true,
    });
}

function deleteJobByAdmin(id: number, title: string) {
    if (confirm(`Agency Admin Control: Are you sure you want to delete the vacancy posting '${title}'?`)) {
        router.delete(`/admin/jobs/${id}`, {
            preserveScroll: true,
        });
    }
}
</script>

<template>
    <Head title="Agency Super Admin Portal - KBS Control Center" />

    <div
        class="min-h-screen bg-slate-50 font-sans text-slate-900 transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100"
    >
        <!-- Top Agency Header -->
        <header
            class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 px-4 py-3.5 shadow-sm backdrop-blur-md sm:px-6 dark:border-slate-800 dark:bg-slate-900/90"
        >
            <div class="mx-auto flex max-w-7xl items-center justify-between">
                <div class="flex shrink-0 items-center space-x-3">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-[#00b2e3] to-indigo-600 font-bold text-white shadow-md shadow-[#00b2e3]/20"
                    >
                        <ShieldCheck class="h-6 w-6" />
                    </div>
                    <div>
                        <h1
                            class="flex items-center gap-2 text-base leading-tight font-extrabold text-slate-900 sm:text-lg dark:text-slate-100"
                        >
                            Agency Admin Portal
                            <span
                                class="hidden rounded-full border border-indigo-500/20 bg-indigo-500/10 px-2 py-0.5 font-mono text-[10px] font-bold text-indigo-600 sm:inline-block dark:text-indigo-400"
                            >
                                KBS Governance
                            </span>
                        </h1>
                        <p
                            class="hidden text-[11px] text-slate-500 sm:text-xs md:block dark:text-slate-400"
                        >
                            Employer Verification & KBS Rule Engine Control
                        </p>
                    </div>
                </div>

                <div class="hidden items-center space-x-3 md:flex">
                    <Link
                        href="/admin/audit"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                    >
                        <ShieldCheck class="h-4 w-4" /> System Audit Trail
                    </Link>
                    <button
                        @click="showProvisionStaffModal = true"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                    >
                        <UserPlus class="h-4 w-4" /> Provision Staff
                    </button>
                    <button
                        @click="showProxyModal = true"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-[#00b2e3] px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#0099c4]"
                    >
                        <Plus class="h-4 w-4" /> Proxy Client Entry
                    </button>
                    <Link
                        href="/opportunities"
                        class="flex items-center gap-1 text-xs font-semibold text-[#00b2e3] hover:underline"
                    >
                        <Eye class="h-3.5 w-3.5" /> Public Directory
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
                            showProvisionStaffModal = true;
                            mobileMenuOpen = false;
                        "
                        class="flex w-full items-center gap-2 rounded-xl bg-indigo-50 px-3 py-2.5 text-left text-sm font-semibold text-indigo-600 transition dark:bg-indigo-950/50"
                    >
                        <UserPlus class="h-4 w-4" /> Provision Staff
                    </button>
                    <button
                        @click="
                            showProxyModal = true;
                            mobileMenuOpen = false;
                        "
                        class="flex w-full items-center gap-2 rounded-xl bg-[#00b2e3]/10 px-3 py-2.5 text-left text-sm font-semibold text-[#00b2e3] transition"
                    >
                        <Plus class="h-4 w-4" /> Proxy Candidate Entry
                    </button>
                    <Link
                        href="/opportunities"
                        @click="mobileMenuOpen = false"
                        class="block rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800"
                    >
                        Public Directory
                    </Link>
                </div>
            </transition>
        </header>

        <main class="mx-auto max-w-7xl space-y-8 px-6 py-10">
            <!-- Flash Message Banner -->
            <div
                v-if="flash?.success"
                class="flex items-center gap-2 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-xs font-semibold text-emerald-600 dark:text-emerald-400"
            >
                <CheckCircle2 class="h-4 w-4 shrink-0 text-emerald-500" />
                <span>{{ flash.success }}</span>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-5">
                <div
                    class="space-y-1 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <span
                        class="text-[11px] font-bold tracking-wider text-slate-400 uppercase"
                        >Organizations</span
                    >
                    <p
                        class="text-2xl font-black text-slate-900 dark:text-slate-100"
                    >
                        {{ stats.total_organizations }}
                    </p>
                    <span class="text-[10px] font-semibold text-emerald-500"
                        >{{ stats.verified_organizations }} Verified</span
                    >
                </div>
                <div
                    class="space-y-1 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <span
                        class="text-[11px] font-bold tracking-wider text-slate-400 uppercase"
                        >Candidates</span
                    >
                    <p
                        class="text-2xl font-black text-slate-900 dark:text-slate-100"
                    >
                        {{ stats.total_candidates }}
                    </p>
                    <span class="text-[10px] font-semibold text-emerald-500"
                        >{{ stats.verified_candidates }} Verified</span
                    >
                </div>
                <div
                    class="space-y-1 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <span
                        class="text-[11px] font-bold tracking-wider text-slate-400 uppercase"
                        >Total Applications</span
                    >
                    <p class="text-2xl font-black text-[#00b2e3]">
                        {{ stats.total_applications }}
                    </p>
                    <span class="text-[10px] font-medium text-slate-400"
                        >Submissions Feed</span
                    >
                </div>
                <div
                    class="space-y-1 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <span
                        class="text-[11px] font-bold tracking-wider text-slate-400 uppercase"
                        >Agency Staff</span
                    >
                    <p class="text-2xl font-black text-indigo-500">
                        {{ stats.total_staff || agencyStaff.length }}
                    </p>
                    <span class="text-[10px] font-medium text-slate-400"
                        >Provisioned Auditors</span
                    >
                </div>
                <div
                    class="space-y-1 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <span
                        class="text-[11px] font-bold tracking-wider text-slate-400 uppercase"
                        >KBS Rules & Criteria</span
                    >
                    <p class="text-2xl font-black text-amber-500">
                        {{ rules.length + criteria.length }}
                    </p>
                    <span class="text-[10px] font-medium text-slate-400"
                        >Active Constraints</span
                    >
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div
                class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-2 dark:border-slate-800"
            >
                <button
                    @click="activeTab = 'overview'"
                    :class="[
                        'flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold transition',
                        activeTab === 'overview'
                            ? 'bg-[#00b2e3] text-white shadow-sm'
                            : 'text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-800',
                    ]"
                >
                    <Sparkles class="h-4 w-4" /> Overview & Audit
                </button>
                <button
                    @click="activeTab = 'organizations'"
                    :class="[
                        'flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold transition',
                        activeTab === 'organizations'
                            ? 'bg-[#00b2e3] text-white shadow-sm'
                            : 'text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-800',
                    ]"
                >
                    <Building2 class="h-4 w-4" /> Employer Verifications
                </button>
                <button
                    @click="activeTab = 'candidates'"
                    :class="[
                        'flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold transition',
                        activeTab === 'candidates'
                            ? 'bg-[#00b2e3] text-white shadow-sm'
                            : 'text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-800',
                    ]"
                >
                    <Users class="h-4 w-4" /> Candidate Facts & Verification
                </button>
                <button
                    @click="activeTab = 'staff'"
                    :class="[
                        'flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold transition',
                        activeTab === 'staff'
                            ? 'bg-[#00b2e3] text-white shadow-sm'
                            : 'text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-800',
                    ]"
                >
                    <UserPlus class="h-4 w-4" /> Agency Staff Governance
                </button>
                <button
                    @click="activeTab = 'kbs_rules'"
                    :class="[
                        'flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold transition',
                        activeTab === 'kbs_rules'
                            ? 'bg-[#00b2e3] text-white shadow-sm'
                            : 'text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-800',
                    ]"
                >
                    <Sliders class="h-4 w-4" /> KBS Rules Engine
                </button>
            </div>

            <!-- Tab 1: Overview & Audit Feed -->
            <div v-if="activeTab === 'overview'" class="space-y-6">
                <div
                    class="space-y-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div
                        class="flex flex-col justify-between gap-4 border-b border-slate-100 pb-3 sm:flex-row sm:items-center dark:border-slate-800"
                    >
                        <div class="flex items-center space-x-2">
                            <Sparkles class="h-4 w-4 text-[#00b2e3]" />
                            <h2
                                class="text-sm font-bold text-slate-900 dark:text-slate-100"
                            >
                                Recent KBS Match Evaluations & Audit Stream
                            </h2>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Status Filter -->
                            <div
                                class="flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-100 px-3 py-1.5 text-xs dark:border-slate-700 dark:bg-slate-800"
                            >
                                <Filter class="h-3.5 w-3.5 text-slate-500" />
                                <select
                                    v-model="auditFilter"
                                    class="bg-transparent text-xs font-semibold text-slate-700 focus:outline-none dark:text-slate-200"
                                >
                                    <option value="all">All Evaluations</option>
                                    <option value="recommended">
                                        Recommended Only
                                    </option>
                                    <option value="flagged">
                                        Flagged / Review Only
                                    </option>
                                    <option value="excluded">
                                        Excluded Only
                                    </option>
                                </select>
                            </div>

                            <!-- Export Audit CSV Button -->
                            <a
                                href="/admin/audit/export"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                            >
                                <Download class="h-3.5 w-3.5" /> Export Audit
                                Trail (CSV)
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table
                            class="w-full text-left text-xs text-slate-700 dark:text-slate-300"
                        >
                            <thead>
                                <tr
                                    class="border-b border-slate-200 text-[10px] font-bold text-slate-400 uppercase dark:border-slate-800"
                                >
                                    <th class="px-3 py-2.5">Candidate</th>
                                    <th class="px-3 py-2.5">Job Posting</th>
                                    <th class="px-3 py-2.5">Organization</th>
                                    <th class="px-3 py-2.5 text-center">
                                        KBS Score
                                    </th>
                                    <th class="px-3 py-2.5 text-center">
                                        Evaluation
                                    </th>
                                    <th class="px-3 py-2.5">Explanations</th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-slate-100 font-mono dark:divide-slate-800"
                            >
                                <tr
                                    v-for="app in filteredApplications"
                                    :key="app.id"
                                    class="transition hover:bg-slate-50 dark:hover:bg-slate-950/50"
                                >
                                    <td
                                        class="px-3 py-3 font-sans font-semibold text-slate-900 dark:text-slate-100"
                                    >
                                        {{ app.candidate_name }}
                                    </td>
                                    <td
                                        class="px-3 py-3 font-sans font-medium text-slate-700 dark:text-slate-300"
                                    >
                                        <div class="flex items-center justify-between gap-2">
                                            <span>{{ app.job_title }}</span>
                                            <button
                                                v-if="app.job_posting_id"
                                                @click="deleteJobByAdmin(app.job_posting_id, app.job_title)"
                                                class="inline-flex items-center gap-0.5 text-[10px] font-semibold text-rose-500 hover:text-rose-600"
                                                title="Delete Vacancy Posting"
                                            >
                                                <Trash2 class="h-3 w-3" /> Delete
                                            </button>
                                        </div>
                                    </td>
                                    <td
                                        class="px-3 py-3 font-sans text-slate-500"
                                    >
                                        {{ app.organization_name }}
                                    </td>
                                    <td
                                        class="px-3 py-3 text-center font-bold text-slate-900 dark:text-slate-100"
                                    >
                                        {{ app.kbs_score }}%
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span
                                            :class="[
                                                'rounded-full px-2 py-0.5 text-[10px] font-bold uppercase',
                                                app.kbs_status === 'recommended'
                                                    ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                                                    : app.kbs_status ===
                                                        'flagged'
                                                      ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400'
                                                      : 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
                                            ]"
                                        >
                                            {{ app.kbs_status }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-3 py-3 font-sans text-[11px] text-slate-500"
                                    >
                                        <div
                                            v-if="
                                                app.explanations &&
                                                app.explanations.length
                                            "
                                            class="space-y-0.5"
                                        >
                                            <div
                                                v-for="(
                                                    exp, idx
                                                ) in app.explanations"
                                                :key="idx"
                                                class="flex items-center gap-1 text-slate-600 dark:text-slate-400"
                                            >
                                                <CheckCircle2
                                                    class="h-3 w-3 shrink-0 text-emerald-500"
                                                />
                                                {{ exp }}
                                            </div>
                                        </div>
                                        <span
                                            v-else
                                            class="text-slate-400 italic"
                                            >Criteria scoring applied</span
                                        >
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Organizations Verification -->
            <div v-if="activeTab === 'organizations'" class="space-y-6">
                <div
                    class="space-y-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div
                        class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800"
                    >
                        <h2
                            class="flex items-center gap-2 text-sm font-bold text-slate-900 dark:text-slate-100"
                        >
                            <Building2 class="h-4 w-4 text-[#00b2e3]" />
                            Registered Employer Organizations & Official Badging
                        </h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table
                            class="w-full text-left text-xs text-slate-700 dark:text-slate-300"
                        >
                            <thead>
                                <tr
                                    class="border-b border-slate-200 text-[10px] font-bold text-slate-400 uppercase dark:border-slate-800"
                                >
                                    <th class="px-3 py-2.5">
                                        Organization Name
                                    </th>
                                    <th class="px-3 py-2.5">Code</th>
                                    <th class="px-3 py-2.5">Sector / Type</th>
                                    <th class="px-3 py-2.5 text-center">
                                        Postings
                                    </th>
                                    <th class="px-3 py-2.5 text-center">
                                        Status
                                    </th>
                                    <th class="px-3 py-2.5 text-right">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-slate-100 dark:divide-slate-800"
                            >
                                <tr
                                    v-for="org in organizations.data"
                                    :key="org.id"
                                    class="transition hover:bg-slate-50 dark:hover:bg-slate-950/50"
                                >
                                    <td
                                        class="flex items-center gap-2 px-3 py-3 font-semibold text-slate-900 dark:text-slate-100"
                                    >
                                        {{ org.name }}
                                        <span
                                            v-if="org.is_verified"
                                            class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2 py-0.5 text-[10px] font-bold text-emerald-600 dark:text-emerald-400"
                                        >
                                            <CheckCircle2
                                                class="h-3 w-3 text-emerald-500"
                                            />
                                            Verified
                                        </span>
                                    </td>
                                    <td
                                        class="px-3 py-3 font-mono font-bold text-slate-600 dark:text-slate-400"
                                    >
                                        {{ org.code }}
                                    </td>
                                    <td
                                        class="px-3 py-3 font-medium text-slate-500"
                                    >
                                        {{ org.org_type }}
                                    </td>
                                    <td
                                        class="px-3 py-3 text-center font-bold text-slate-900 dark:text-slate-100"
                                    >
                                        {{ org.job_count }}
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span
                                            :class="[
                                                'rounded-full px-2 py-0.5 text-[10px] font-bold uppercase',
                                                org.is_verified
                                                    ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                                                    : 'bg-slate-100 text-slate-500 dark:bg-slate-800',
                                            ]"
                                        >
                                            {{
                                                org.is_verified
                                                    ? 'Verified Entity'
                                                    : 'Unverified'
                                            }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        <Link
                                            :href="`/admin/organizations/${org.id}/verify`"
                                            method="patch"
                                            as="button"
                                            preserve-scroll
                                            :class="[
                                                'inline-flex items-center gap-1 rounded-xl px-3 py-1.5 text-xs font-semibold transition',
                                                org.is_verified
                                                    ? 'bg-rose-500/10 text-rose-600 hover:bg-rose-500/20 dark:text-rose-400'
                                                    : 'bg-emerald-500 text-white hover:bg-emerald-600',
                                            ]"
                                        >
                                            <Check
                                                v-if="!org.is_verified"
                                                class="h-3.5 w-3.5"
                                            />
                                            <X v-else class="h-3.5 w-3.5" />
                                            {{
                                                org.is_verified
                                                    ? 'Revoke Verification'
                                                    : 'Verify Organization'
                                            }}
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Organizations Pagination Bar -->
                    <div
                        v-if="
                            organizations.links &&
                            organizations.links.length > 3
                        "
                        class="flex flex-col items-center justify-between gap-3 border-t border-slate-100 pt-4 text-xs sm:flex-row dark:border-slate-800"
                    >
                        <span class="font-medium text-slate-500"
                            >Showing page {{ organizations.current_page }} of
                            {{ organizations.last_page }} ({{
                                organizations.total
                            }}
                            total organizations)</span
                        >
                        <div class="flex flex-wrap items-center gap-1">
                            <Link
                                v-for="(link, idx) in organizations.links"
                                :key="'org-' + idx"
                                :href="link.url || '#'"
                                :class="[
                                    'rounded-xl border px-3 py-1.5 text-xs font-semibold transition',
                                    link.active
                                        ? 'border-[#00b2e3] bg-[#00b2e3] text-white'
                                        : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800',
                                    !link.url
                                        ? 'cursor-not-allowed opacity-40'
                                        : '',
                                ]"
                                preserve-scroll
                                ><span v-html="link.label"></span
                            ></Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Candidates Verification & Proxy Entry -->
            <div v-if="activeTab === 'candidates'" class="space-y-6">
                <div
                    class="space-y-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div
                        class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800"
                    >
                        <h2
                            class="flex items-center gap-2 text-sm font-bold text-slate-900 dark:text-slate-100"
                        >
                            <Users class="h-4 w-4 text-[#00b2e3]" /> Candidate
                            Fact Auditing & Verification Badges
                        </h2>
                        <button
                            @click="showProxyModal = true"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-[#00b2e3] px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-[#0099c4]"
                        >
                            <Plus class="h-3.5 w-3.5" /> Register Proxy
                            Candidate
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table
                            class="w-full text-left text-xs text-slate-700 dark:text-slate-300"
                        >
                            <thead>
                                <tr
                                    class="border-b border-slate-200 text-[10px] font-bold text-slate-400 uppercase dark:border-slate-800"
                                >
                                    <th class="px-3 py-2.5">Candidate Name</th>
                                    <th class="px-3 py-2.5">Email</th>
                                    <th class="px-3 py-2.5">Education</th>
                                    <th class="px-3 py-2.5 text-center">
                                        Exp (Yrs)
                                    </th>
                                    <th class="px-3 py-2.5 text-center">
                                        Reliability
                                    </th>
                                    <th class="px-3 py-2.5 text-center">
                                        Status
                                    </th>
                                    <th class="px-3 py-2.5 text-right">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-slate-100 dark:divide-slate-800"
                            >
                                <tr
                                    v-for="c in candidates.data"
                                    :key="c.id"
                                    class="transition hover:bg-slate-50 dark:hover:bg-slate-950/50"
                                >
                                    <td
                                        class="flex items-center gap-1.5 px-3 py-3 font-semibold text-slate-900 dark:text-slate-100"
                                    >
                                        {{ c.name }}
                                        <Award
                                            v-if="c.is_verified"
                                            class="h-4 w-4 text-emerald-500"
                                        />
                                    </td>
                                    <td
                                        class="px-3 py-3 font-mono text-slate-500"
                                    >
                                        {{ c.email }}
                                    </td>
                                    <td
                                        class="px-3 py-3 font-medium text-slate-700 dark:text-slate-300"
                                    >
                                        {{ c.education_level }}
                                    </td>
                                    <td
                                        class="px-3 py-3 text-center font-bold text-slate-900 dark:text-slate-100"
                                    >
                                        {{ c.years_experience }}
                                    </td>
                                    <td
                                        class="px-3 py-3 text-center font-bold text-emerald-500"
                                    >
                                        {{ c.reliability_score }}%
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span
                                            :class="[
                                                'rounded-full px-2 py-0.5 text-[10px] font-bold uppercase',
                                                c.is_verified
                                                    ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                                                    : 'bg-slate-100 text-slate-500 dark:bg-slate-800',
                                            ]"
                                        >
                                            {{
                                                c.is_verified
                                                    ? 'Verified Facts'
                                                    : 'Unverified'
                                            }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        <Link
                                            :href="`/admin/candidates/${c.id}/verify`"
                                            method="patch"
                                            as="button"
                                            preserve-scroll
                                            :class="[
                                                'inline-flex items-center gap-1 rounded-xl px-3 py-1.5 text-xs font-semibold transition',
                                                c.is_verified
                                                    ? 'bg-rose-500/10 text-rose-600 hover:bg-rose-500/20 dark:text-rose-400'
                                                    : 'bg-emerald-500 text-white hover:bg-emerald-600',
                                            ]"
                                        >
                                            <Check
                                                v-if="!c.is_verified"
                                                class="h-3.5 w-3.5"
                                            />
                                            <X v-else class="h-3.5 w-3.5" />
                                            {{
                                                c.is_verified
                                                    ? 'Revoke Verification'
                                                    : 'Verify Credentials'
                                            }}
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Candidates Pagination Bar -->
                    <div
                        v-if="candidates.links && candidates.links.length > 3"
                        class="flex flex-col items-center justify-between gap-3 border-t border-slate-100 pt-4 text-xs sm:flex-row dark:border-slate-800"
                    >
                        <span class="font-medium text-slate-500"
                            >Showing page {{ candidates.current_page }} of
                            {{ candidates.last_page }} ({{
                                candidates.total
                            }}
                            total candidates)</span
                        >
                        <div class="flex flex-wrap items-center gap-1">
                            <Link
                                v-for="(link, idx) in candidates.links"
                                :key="'cand-' + idx"
                                :href="link.url || '#'"
                                :class="[
                                    'rounded-xl border px-3 py-1.5 text-xs font-semibold transition',
                                    link.active
                                        ? 'border-[#00b2e3] bg-[#00b2e3] text-white'
                                        : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800',
                                    !link.url
                                        ? 'cursor-not-allowed opacity-40'
                                        : '',
                                ]"
                                preserve-scroll
                                ><span v-html="link.label"></span
                            ></Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 4: Agency Staff Governance -->
            <div v-if="activeTab === 'staff'" class="space-y-6">
                <div
                    class="space-y-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div
                        class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800"
                    >
                        <div>
                            <h2
                                class="flex items-center gap-2 text-sm font-bold text-slate-900 dark:text-slate-100"
                            >
                                <UserPlus class="h-4 w-4 text-indigo-500" />
                                Provisioned Agency Staff Team & Sub-Role
                                Governance
                            </h2>
                            <p class="text-xs text-slate-500">
                                Manage internal hiring agency auditors,
                                verification officers, and system
                                administrators.
                            </p>
                        </div>
                        <button
                            @click="showProvisionStaffModal = true"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                        >
                            <UserPlus class="h-3.5 w-3.5" /> Provision New Staff
                            Account
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table
                            class="w-full text-left text-xs text-slate-700 dark:text-slate-300"
                        >
                            <thead>
                                <tr
                                    class="border-b border-slate-200 text-[10px] font-bold text-slate-400 uppercase dark:border-slate-800"
                                >
                                    <th class="px-3 py-2.5">Staff Name</th>
                                    <th class="px-3 py-2.5">Email Address</th>
                                    <th class="px-3 py-2.5">
                                        Assigned Sub-Role
                                    </th>
                                    <th class="px-3 py-2.5">
                                        Provisioned Date
                                    </th>
                                    <th class="px-3 py-2.5 text-right">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-slate-100 dark:divide-slate-800"
                            >
                                <tr
                                    v-for="staff in agencyStaff"
                                    :key="staff.id"
                                    class="transition hover:bg-slate-50 dark:hover:bg-slate-950/50"
                                >
                                    <td
                                        class="flex items-center gap-2 px-3 py-3 font-semibold text-slate-900 dark:text-slate-100"
                                    >
                                        <ShieldCheck
                                            class="h-4 w-4 text-indigo-500"
                                        />
                                        {{ staff.name }}
                                    </td>
                                    <td
                                        class="px-3 py-3 font-mono text-slate-500"
                                    >
                                        {{ staff.email }}
                                    </td>
                                    <td class="px-3 py-3">
                                        <span
                                            :class="[
                                                'rounded-full px-2.5 py-1 font-mono text-[10px] font-bold uppercase',
                                                staff.agency_sub_role ===
                                                'super_admin'
                                                    ? 'border border-indigo-500/20 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'
                                                    : staff.agency_sub_role ===
                                                        'verification_officer'
                                                      ? 'border border-emerald-500/20 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                                                      : 'border border-amber-500/20 bg-amber-500/10 text-amber-600 dark:text-amber-400',
                                            ]"
                                        >
                                            {{
                                                staff.agency_sub_role.replace(
                                                    '_',
                                                    ' ',
                                                )
                                            }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-3 py-3 font-mono text-slate-500"
                                    >
                                        {{ staff.created_at }}
                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        <span
                                            class="rounded-full bg-emerald-500/10 px-2 py-0.5 text-[10px] font-bold text-emerald-600 dark:text-emerald-400"
                                        >
                                            ✓ Active Auditor
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab 5: KBS Rules Engine -->
            <div v-if="activeTab === 'kbs_rules'" class="space-y-6">
                <!-- Criteria Weights Form -->
                <div
                    class="space-y-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div
                        class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800"
                    >
                        <h2
                            class="flex items-center gap-2 text-sm font-bold text-slate-900 dark:text-slate-100"
                        >
                            <Sliders class="h-4 w-4 text-[#00b2e3]" /> Active
                            KBS Criteria Weights Calibration
                        </h2>
                        <button
                            @click="updateCriteriaWeights"
                            :disabled="criteriaForm.processing"
                            class="rounded-xl bg-[#00b2e3] px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#0099c4]"
                        >
                            Save Criteria Weights
                        </button>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div
                            v-for="(crit, index) in criteria"
                            :key="crit.id"
                            class="space-y-2 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950"
                        >
                            <div
                                class="flex items-center justify-between text-xs font-bold text-slate-900 dark:text-slate-100"
                            >
                                <span>{{ crit.name }}</span>
                                <span class="font-mono text-[#00b2e3]"
                                    >{{
                                        (
                                            criteriaForm.criteria[index]
                                                .weight * 100
                                        ).toFixed(0)
                                    }}%</span
                                >
                            </div>
                            <input
                                type="range"
                                min="0"
                                max="1"
                                step="0.05"
                                v-model.number="
                                    criteriaForm.criteria[index].weight
                                "
                                class="w-full accent-[#00b2e3]"
                            />
                            <p class="text-[10px] text-slate-400">
                                Key:
                                <code
                                    class="font-mono text-slate-600 dark:text-slate-400"
                                    >{{ crit.key }}</code
                                >
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Global IF-THEN Rules List -->
                <div
                    class="space-y-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div
                        class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800"
                    >
                        <h2
                            class="flex items-center gap-2 text-sm font-bold text-slate-900 dark:text-slate-100"
                        >
                            <FileText class="h-4 w-4 text-indigo-500" /> Active
                            Global IF-THEN Rules ({{ rules.length }})
                        </h2>
                        <button
                            @click="showAddRuleModal = true"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                        >
                            <Plus class="h-3.5 w-3.5" /> Add Global IF-THEN Rule
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="r in rules"
                            :key="r.id"
                            class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 p-4 text-xs dark:border-slate-800 dark:bg-slate-950"
                        >
                            <div class="space-y-1">
                                <span
                                    class="font-bold text-slate-900 dark:text-slate-100"
                                    >{{ r.name }}</span
                                >
                                <p class="font-mono text-[11px] text-slate-500">
                                    IF candidate.<span
                                        class="text-indigo-500"
                                        >{{ r.field }}</span
                                    >
                                    {{ r.operator }} '{{ r.value }}' THEN
                                    <span
                                        class="font-bold text-amber-500 uppercase"
                                        >{{ r.action }}</span
                                    >
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <Link
                                    :href="`/admin/rules/${r.id}/toggle`"
                                    method="patch"
                                    as="button"
                                    preserve-scroll
                                    :class="[
                                        'inline-flex items-center gap-1 rounded-xl px-3 py-1.5 text-xs font-semibold transition',
                                        r.active
                                            ? 'bg-emerald-500/10 text-emerald-600 hover:bg-emerald-500/20 dark:text-emerald-400'
                                            : 'bg-slate-200 text-slate-500 hover:bg-slate-300 dark:bg-slate-800',
                                    ]"
                                >
                                    <Check
                                        v-if="r.active"
                                        class="h-3.5 w-3.5 text-emerald-500"
                                    />
                                    <X v-else class="h-3.5 w-3.5" />
                                    {{
                                        r.active
                                            ? 'Active'
                                            : 'Inactive'
                                    }}
                                </Link>
                                <button
                                    @click="openEditRuleModal(r)"
                                    title="Edit Rule"
                                    class="inline-flex items-center justify-center rounded-xl bg-indigo-50 p-2 text-indigo-600 transition hover:bg-indigo-100 dark:bg-indigo-950/50 dark:text-indigo-400 dark:hover:bg-indigo-900/50"
                                >
                                    <Pencil class="h-3.5 w-3.5" />
                                </button>
                                <button
                                    @click="deleteRule(r)"
                                    title="Delete Rule"
                                    class="inline-flex items-center justify-center rounded-xl bg-rose-50 p-2 text-rose-600 transition hover:bg-rose-100 dark:bg-rose-950/50 dark:text-rose-400 dark:hover:bg-rose-900/50"
                                >
                                    <Trash2 class="h-3.5 w-3.5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Create Global IF-THEN Rule Modal -->
        <div
            v-if="showAddRuleModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
        >
            <div
                class="w-full max-w-lg space-y-5 rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900"
            >
                <div
                    class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800"
                >
                    <h3
                        class="flex items-center gap-2 text-base font-bold text-slate-900 dark:text-slate-100"
                    >
                        <Plus class="h-5 w-5 text-indigo-500" /> Add New Global
                        IF-THEN Rule
                    </h3>
                    <button
                        @click="showAddRuleModal = false"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                    >
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <form
                    @submit.prevent="submitCreateRule"
                    class="space-y-4 text-xs"
                >
                    <div>
                        <label
                            class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                            >Rule Name</label
                        >
                        <input
                            v-model="newRuleForm.name"
                            required
                            type="text"
                            placeholder="e.g. Mandatory Security Clearance"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs dark:border-slate-800 dark:bg-slate-950"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                                >Candidate Attribute Field</label
                            >
                            <select
                                v-model="newRuleForm.field"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold dark:border-slate-800 dark:bg-slate-950"
                            >
                                <option value="years_experience">
                                    years_experience
                                </option>
                                <option value="reliability_score">
                                    reliability_score
                                </option>
                                <option value="education_level">
                                    education_level
                                </option>
                                <option value="is_verified">is_verified</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                                >Operator</label
                            >
                            <select
                                v-model="newRuleForm.operator"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 font-mono text-xs font-semibold dark:border-slate-800 dark:bg-slate-950"
                            >
                                <option value=">=">
                                    &gt;= (Greater than or Equal)
                                </option>
                                <option value="<=">
                                    &lt;= (Less than or Equal)
                                </option>
                                <option value="==">== (Exact Equals)</option>
                                <option value="contains">
                                    contains (In List)
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                                >Target Value</label
                            >
                            <input
                                v-model="newRuleForm.value"
                                required
                                type="text"
                                placeholder="e.g. 5 or 85 or Master's Degree"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs dark:border-slate-800 dark:bg-slate-950"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                                >Rule Action</label
                            >
                            <select
                                v-model="newRuleForm.action"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold uppercase dark:border-slate-800 dark:bg-slate-950"
                            >
                                <option value="flag">FLAG (Audit Badge)</option>
                                <option value="bonus">BONUS (+ Points)</option>
                                <option value="exclude">
                                    EXCLUDE (Knockout)
                                </option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label
                            class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                            >Explanation Template (Use :value for dynamic
                            replacement)</label
                        >
                        <input
                            v-model="newRuleForm.explanation_template"
                            required
                            type="text"
                            placeholder="e.g. Verified candidate possesses at least :value years experience"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 font-mono text-xs dark:border-slate-800 dark:bg-slate-950"
                        />
                    </div>

                    <div
                        class="flex justify-end space-x-3 border-t border-slate-100 pt-3 dark:border-slate-800"
                    >
                        <button
                            type="button"
                            @click="showAddRuleModal = false"
                            class="rounded-xl bg-slate-100 px-4 py-2 font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="newRuleForm.processing"
                            class="rounded-xl bg-indigo-600 px-4 py-2 font-semibold text-white shadow-sm hover:bg-indigo-700"
                        >
                            Create Global Rule
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Global IF-THEN Rule Modal -->
        <div
            v-if="showEditRuleModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
        >
            <div
                class="w-full max-w-lg space-y-5 rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900"
            >
                <div
                    class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800"
                >
                    <h3
                        class="flex items-center gap-2 text-base font-bold text-slate-900 dark:text-slate-100"
                    >
                        <Pencil class="h-5 w-5 text-indigo-500" /> Edit Global IF-THEN Rule
                    </h3>
                    <button
                        @click="showEditRuleModal = false"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                    >
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <form
                    @submit.prevent="submitUpdateRule"
                    class="space-y-4 text-xs"
                >
                    <div>
                        <label
                            class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                            >Rule Name</label
                        >
                        <input
                            v-model="editRuleForm.name"
                            required
                            type="text"
                            placeholder="e.g. Mandatory Security Clearance"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs dark:border-slate-800 dark:bg-slate-950"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                                >Candidate Attribute Field</label
                            >
                            <select
                                v-model="editRuleForm.field"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold dark:border-slate-800 dark:bg-slate-950"
                            >
                                <option value="years_experience">
                                    years_experience
                                </option>
                                <option value="reliability_score">
                                    reliability_score
                                </option>
                                <option value="education_level">
                                    education_level
                                </option>
                                <option value="is_verified">is_verified</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                                >Operator</label
                            >
                            <select
                                v-model="editRuleForm.operator"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 font-mono text-xs font-semibold dark:border-slate-800 dark:bg-slate-950"
                            >
                                <option value=">=">
                                    &gt;= (Greater than or Equal)
                                </option>
                                <option value="<=">
                                    &lt;= (Less than or Equal)
                                </option>
                                <option value="==">== (Exact Equals)</option>
                                <option value="contains">
                                    contains (In List)
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                                >Target Value</label
                            >
                            <input
                                v-model="editRuleForm.value"
                                required
                                type="text"
                                placeholder="e.g. 5 or 85 or Master's Degree"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs dark:border-slate-800 dark:bg-slate-950"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                                >Rule Action</label
                            >
                            <select
                                v-model="editRuleForm.action"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold uppercase dark:border-slate-800 dark:bg-slate-950"
                            >
                                <option value="flag">FLAG (Audit Badge)</option>
                                <option value="bonus">BONUS (+ Points)</option>
                                <option value="exclude">
                                    EXCLUDE (Knockout)
                                </option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label
                            class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                            >Explanation Template (Use :value for dynamic
                            replacement)</label
                        >
                        <input
                            v-model="editRuleForm.explanation_template"
                            required
                            type="text"
                            placeholder="e.g. Verified candidate possesses at least :value years experience"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 font-mono text-xs dark:border-slate-800 dark:bg-slate-950"
                        />
                    </div>

                    <div
                        class="flex justify-end space-x-3 border-t border-slate-100 pt-3 dark:border-slate-800"
                    >
                        <button
                            type="button"
                            @click="showEditRuleModal = false"
                            class="rounded-xl bg-slate-100 px-4 py-2 font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="editRuleForm.processing"
                            class="rounded-xl bg-indigo-600 px-4 py-2 font-semibold text-white shadow-sm hover:bg-indigo-700"
                        >
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Proxy Candidate Creation Modal -->
        <div
            v-if="showProxyModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
        >
            <div
                class="w-full max-w-lg space-y-5 rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900"
            >
                <div
                    class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800"
                >
                    <h3
                        class="flex items-center gap-2 text-base font-bold text-slate-900 dark:text-slate-100"
                    >
                        <Plus class="h-5 w-5 text-[#00b2e3]" /> Assisted Proxy
                        Candidate Entry
                    </h3>
                    <button
                        @click="showProxyModal = false"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                    >
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <form
                    @submit.prevent="submitProxyCandidate"
                    class="space-y-4 text-xs"
                >
                    <div>
                        <label
                            class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                            >Candidate Full Name</label
                        >
                        <input
                            v-model="proxyForm.name"
                            required
                            type="text"
                            placeholder="e.g. John Mwangi"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs dark:border-slate-800 dark:bg-slate-950"
                        />
                    </div>

                    <div>
                        <label
                            class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                            >Email Address</label
                        >
                        <input
                            v-model="proxyForm.email"
                            required
                            type="email"
                            placeholder="e.g. john@example.com"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs dark:border-slate-800 dark:bg-slate-950"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                                >Education Level</label
                            >
                            <select
                                v-model="proxyForm.education_level"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold dark:border-slate-800 dark:bg-slate-950"
                            >
                                <option value="High School / Secondary">
                                    High School / Secondary
                                </option>
                                <option value="Diploma / Associate">
                                    Diploma / Associate
                                </option>
                                <option value="Bachelor's Degree">
                                    Bachelor's Degree
                                </option>
                                <option value="Master's Degree">
                                    Master's Degree
                                </option>
                                <option value="Doctorate (Ph.D. / M.D.)">
                                    Doctorate (Ph.D. / M.D.)
                                </option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                                >Years of Experience</label
                            >
                            <input
                                v-model.number="proxyForm.years_experience"
                                min="0"
                                max="40"
                                type="number"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs dark:border-slate-800 dark:bg-slate-950"
                            />
                        </div>
                    </div>

                    <div>
                        <label
                            class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                            >Skills (Comma Separated)</label
                        >
                        <input
                            v-model="proxyForm.skills_raw"
                            type="text"
                            placeholder="e.g. M&E, Project Management, Data Analysis"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs dark:border-slate-800 dark:bg-slate-950"
                        />
                    </div>

                    <div>
                        <label
                            class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                            >Summary / Qualification Notes</label
                        >
                        <textarea
                            v-model="proxyForm.summary"
                            rows="2"
                            placeholder="Notes from paper CV or assisted interview..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs dark:border-slate-800 dark:bg-slate-950"
                        ></textarea>
                    </div>

                    <div
                        class="flex justify-end space-x-3 border-t border-slate-100 pt-3 dark:border-slate-800"
                    >
                        <button
                            type="button"
                            @click="showProxyModal = false"
                            class="rounded-xl bg-slate-100 px-4 py-2 font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="proxyForm.processing"
                            class="rounded-xl bg-[#00b2e3] px-4 py-2 font-semibold text-white shadow-sm hover:bg-[#0099c4]"
                        >
                            Create & Verify Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Provision Agency Staff Modal -->
        <div
            v-if="showProvisionStaffModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
        >
            <div
                class="w-full max-w-lg space-y-5 rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900"
            >
                <div
                    class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800"
                >
                    <h3
                        class="flex items-center gap-2 text-base font-bold text-slate-900 dark:text-slate-100"
                    >
                        <UserPlus class="h-5 w-5 text-indigo-500" /> Provision
                        New Agency Staff Account
                    </h3>
                    <button
                        @click="showProvisionStaffModal = false"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                    >
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <form
                    @submit.prevent="submitProvisionStaff"
                    class="space-y-4 text-xs"
                >
                    <div>
                        <label
                            class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                            >Staff Member Full Name</label
                        >
                        <input
                            v-model="provisionStaffForm.name"
                            required
                            type="text"
                            placeholder="e.g. Inspector David Kimani"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs dark:border-slate-800 dark:bg-slate-950"
                        />
                    </div>

                    <div>
                        <label
                            class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                            >Official Work Email Address</label
                        >
                        <input
                            v-model="provisionStaffForm.email"
                            required
                            type="email"
                            placeholder="e.g. d.kimani@kbsagency.com"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs dark:border-slate-800 dark:bg-slate-950"
                        />
                    </div>

                    <div>
                        <label
                            class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                            >Assigned Governance Sub-Role</label
                        >
                        <select
                            v-model="provisionStaffForm.agency_sub_role"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold dark:border-slate-800 dark:bg-slate-950"
                        >
                            <option value="super_admin">
                                Super Admin (Full Rule Engine & Staff
                                Management)
                            </option>
                            <option value="verification_officer">
                                Verification Officer (Employer & Candidate
                                Badging)
                            </option>
                            <option value="compliance_auditor">
                                Compliance Auditor (Read-Only Audit Stream
                                Reviewer)
                            </option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="mb-1 block font-semibold text-slate-700 dark:text-slate-300"
                            >Initial Temporary Password (Min 8
                            characters)</label
                        >
                        <input
                            v-model="provisionStaffForm.password"
                            required
                            type="password"
                            placeholder="••••••••"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs dark:border-slate-800 dark:bg-slate-950"
                        />
                    </div>

                    <div
                        class="flex justify-end space-x-3 border-t border-slate-100 pt-3 dark:border-slate-800"
                    >
                        <button
                            type="button"
                            @click="showProvisionStaffModal = false"
                            class="rounded-xl bg-slate-100 px-4 py-2 font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="provisionStaffForm.processing"
                            class="rounded-xl bg-indigo-600 px-4 py-2 font-semibold text-white shadow-sm hover:bg-indigo-700"
                        >
                            Provision Staff Credentials
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
