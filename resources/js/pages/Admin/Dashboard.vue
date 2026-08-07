<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import {
    ShieldCheck,
    Building2,
    Users,
    Sliders,
    CheckCircle2,
    XCircle,
    Plus,
    Sparkles,
    AlertCircle,
    Eye,
    Check,
    X,
    FileText,
    Award,
    Download,
    UserPlus,
    Filter,
} from '@lucide/vue';

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
const flash = computed(() => page.props.flash as { success?: string; error?: string });

const activeTab = ref<'overview' | 'organizations' | 'candidates' | 'kbs_rules' | 'staff'>('overview');
const showProxyModal = ref(false);
const showProvisionStaffModal = ref(false);
const showAddRuleModal = ref(false);
const auditFilter = ref<'all' | 'recommended' | 'flagged' | 'excluded'>('all');

const filteredApplications = computed(() => {
    if (auditFilter.value === 'all') return props.applications;
    return props.applications.filter(a => a.kbs_status === auditFilter.value);
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
        .map(s => s.trim())
        .filter(s => s.length > 0);

    proxyForm.transform(data => ({
        ...data,
        skills: skillsArr.length > 0 ? skillsArr : ['General Skills'],
    })).post('/admin/candidates/proxy', {
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

// Criteria Weight Form
const criteriaForm = useForm({
    criteria: props.criteria.map(c => ({ id: c.id, weight: c.weight })),
});

function updateCriteriaWeights() {
    criteriaForm.post('/admin/criteria/weights', {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Agency Super Admin Portal - KBS Control Center" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans transition-colors duration-300">
        <!-- Top Agency Header -->
        <header class="sticky top-0 z-40 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-6 py-4 flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-[#00b2e3] to-indigo-600 text-white flex items-center justify-center shadow-md shadow-[#00b2e3]/20 font-bold">
                    <ShieldCheck class="w-6 h-6" />
                </div>
                <div>
                    <h1 class="text-lg font-extrabold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        Agency Super Admin Portal
                        <span class="px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-mono text-[10px] font-bold border border-indigo-500/20">
                            KBS Governance
                        </span>
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Employer Verification, Candidate Credentials Audit & KBS Rule Engine Control</p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <button
                    @click="showProvisionStaffModal = true"
                    class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition inline-flex items-center gap-1.5"
                >
                    <UserPlus class="w-4 h-4" /> Provision Staff
                </button>
                <button
                    @click="showProxyModal = true"
                    class="px-4 py-2 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl shadow-sm transition inline-flex items-center gap-1.5"
                >
                    <Plus class="w-4 h-4" /> Proxy Candidate Entry
                </button>
                <Link href="/opportunities" class="text-xs font-semibold text-[#00b2e3] hover:underline flex items-center gap-1">
                    <Eye class="w-3.5 h-3.5" /> Public Directory
                </Link>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 py-10 space-y-8">
            <!-- Flash Message Banner -->
            <div v-if="flash?.success" class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 p-4 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <CheckCircle2 class="w-4 h-4 shrink-0 text-emerald-500" />
                <span>{{ flash.success }}</span>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-sm space-y-1">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Organizations</span>
                    <p class="text-2xl font-black text-slate-900 dark:text-slate-100">{{ stats.total_organizations }}</p>
                    <span class="text-[10px] text-emerald-500 font-semibold">{{ stats.verified_organizations }} Verified</span>
                </div>
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-sm space-y-1">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Candidates</span>
                    <p class="text-2xl font-black text-slate-900 dark:text-slate-100">{{ stats.total_candidates }}</p>
                    <span class="text-[10px] text-emerald-500 font-semibold">{{ stats.verified_candidates }} Verified</span>
                </div>
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-sm space-y-1">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Applications</span>
                    <p class="text-2xl font-black text-[#00b2e3]">{{ stats.total_applications }}</p>
                    <span class="text-[10px] text-slate-400 font-medium">Submissions Feed</span>
                </div>
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-sm space-y-1">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Agency Staff</span>
                    <p class="text-2xl font-black text-indigo-500">{{ stats.total_staff || agencyStaff.length }}</p>
                    <span class="text-[10px] text-slate-400 font-medium">Provisioned Auditors</span>
                </div>
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-sm space-y-1">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">KBS Rules & Criteria</span>
                    <p class="text-2xl font-black text-amber-500">{{ rules.length + criteria.length }}</p>
                    <span class="text-[10px] text-slate-400 font-medium">Active Constraints</span>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-2 flex-wrap">
                <button
                    @click="activeTab = 'overview'"
                    :class="[
                        'px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2',
                        activeTab === 'overview'
                            ? 'bg-[#00b2e3] text-white shadow-sm'
                            : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800'
                    ]"
                >
                    <Sparkles class="w-4 h-4" /> Overview & Audit
                </button>
                <button
                    @click="activeTab = 'organizations'"
                    :class="[
                        'px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2',
                        activeTab === 'organizations'
                            ? 'bg-[#00b2e3] text-white shadow-sm'
                            : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800'
                    ]"
                >
                    <Building2 class="w-4 h-4" /> Employer Verifications
                </button>
                <button
                    @click="activeTab = 'candidates'"
                    :class="[
                        'px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2',
                        activeTab === 'candidates'
                            ? 'bg-[#00b2e3] text-white shadow-sm'
                            : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800'
                    ]"
                >
                    <Users class="w-4 h-4" /> Candidate Facts & Verification
                </button>
                <button
                    @click="activeTab = 'staff'"
                    :class="[
                        'px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2',
                        activeTab === 'staff'
                            ? 'bg-[#00b2e3] text-white shadow-sm'
                            : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800'
                    ]"
                >
                    <UserPlus class="w-4 h-4" /> Agency Staff Governance
                </button>
                <button
                    @click="activeTab = 'kbs_rules'"
                    :class="[
                        'px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2',
                        activeTab === 'kbs_rules'
                            ? 'bg-[#00b2e3] text-white shadow-sm'
                            : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800'
                    ]"
                >
                    <Sliders class="w-4 h-4" /> KBS Rules Engine
                </button>
            </div>

            <!-- Tab 1: Overview & Audit Feed -->
            <div v-if="activeTab === 'overview'" class="space-y-6">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div class="flex items-center space-x-2">
                            <Sparkles class="w-4 h-4 text-[#00b2e3]" />
                            <h2 class="text-sm font-bold text-slate-900 dark:text-slate-100">
                                Recent KBS Match Evaluations & Audit Stream
                            </h2>
                        </div>
                        
                        <div class="flex items-center gap-3 flex-wrap">
                            <!-- Status Filter -->
                            <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs">
                                <Filter class="w-3.5 h-3.5 text-slate-500" />
                                <select v-model="auditFilter" class="bg-transparent text-xs font-semibold text-slate-700 dark:text-slate-200 focus:outline-none">
                                    <option value="all">All Evaluations</option>
                                    <option value="recommended">Recommended Only</option>
                                    <option value="flagged">Flagged / Review Only</option>
                                    <option value="excluded">Excluded Only</option>
                                </select>
                            </div>

                            <!-- Export Audit CSV Button -->
                            <a
                                href="/admin/audit/export"
                                class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-sm transition inline-flex items-center gap-1.5"
                            >
                                <Download class="w-3.5 h-3.5" /> Export Audit Trail (CSV)
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-800 text-[10px] uppercase font-bold text-slate-400">
                                    <th class="py-2.5 px-3">Candidate</th>
                                    <th class="py-2.5 px-3">Job Posting</th>
                                    <th class="py-2.5 px-3">Organization</th>
                                    <th class="py-2.5 px-3 text-center">KBS Score</th>
                                    <th class="py-2.5 px-3 text-center">Evaluation</th>
                                    <th class="py-2.5 px-3">Explanations</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-mono">
                                <tr v-for="app in filteredApplications" :key="app.id" class="hover:bg-slate-50 dark:hover:bg-slate-950/50 transition">
                                    <td class="py-3 px-3 font-semibold text-slate-900 dark:text-slate-100 font-sans">{{ app.candidate_name }}</td>
                                    <td class="py-3 px-3 font-medium text-slate-700 dark:text-slate-300 font-sans">{{ app.job_title }}</td>
                                    <td class="py-3 px-3 text-slate-500 font-sans">{{ app.organization_name }}</td>
                                    <td class="py-3 px-3 text-center font-bold text-slate-900 dark:text-slate-100">{{ app.kbs_score }}%</td>
                                    <td class="py-3 px-3 text-center">
                                        <span
                                            :class="[
                                                'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase',
                                                app.kbs_status === 'recommended' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' :
                                                app.kbs_status === 'flagged' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400' :
                                                'bg-rose-500/10 text-rose-600 dark:text-rose-400'
                                            ]"
                                        >
                                            {{ app.kbs_status }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-[11px] text-slate-500 font-sans">
                                        <div v-if="app.explanations && app.explanations.length" class="space-y-0.5">
                                            <div v-for="(exp, idx) in app.explanations" :key="idx" class="flex items-center gap-1 text-slate-600 dark:text-slate-400">
                                                <CheckCircle2 class="w-3 h-3 text-emerald-500 shrink-0" /> {{ exp }}
                                            </div>
                                        </div>
                                        <span v-else class="text-slate-400 italic">Criteria scoring applied</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Organizations Verification -->
            <div v-if="activeTab === 'organizations'" class="space-y-6">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                            <Building2 class="w-4 h-4 text-[#00b2e3]" /> Registered Employer Organizations & Official Badging
                        </h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-800 text-[10px] uppercase font-bold text-slate-400">
                                    <th class="py-2.5 px-3">Organization Name</th>
                                    <th class="py-2.5 px-3">Code</th>
                                    <th class="py-2.5 px-3">Sector / Type</th>
                                    <th class="py-2.5 px-3 text-center">Postings</th>
                                    <th class="py-2.5 px-3 text-center">Status</th>
                                    <th class="py-2.5 px-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr v-for="org in organizations.data" :key="org.id" class="hover:bg-slate-50 dark:hover:bg-slate-950/50 transition">
                                    <td class="py-3 px-3 font-semibold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                        {{ org.name }}
                                        <span v-if="org.is_verified" class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold inline-flex items-center gap-1">
                                            <CheckCircle2 class="w-3 h-3 text-emerald-500" /> Verified
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 font-mono font-bold text-slate-600 dark:text-slate-400">{{ org.code }}</td>
                                    <td class="py-3 px-3 font-medium text-slate-500">{{ org.org_type }}</td>
                                    <td class="py-3 px-3 text-center font-bold text-slate-900 dark:text-slate-100">{{ org.job_count }}</td>
                                    <td class="py-3 px-3 text-center">
                                        <span
                                            :class="[
                                                'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase',
                                                org.is_verified ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-500'
                                            ]"
                                        >
                                            {{ org.is_verified ? 'Verified Entity' : 'Unverified' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        <Link
                                            :href="`/admin/organizations/${org.id}/verify`"
                                            method="patch"
                                            as="button"
                                            preserve-scroll
                                            :class="[
                                                'px-3 py-1.5 rounded-xl text-xs font-semibold transition inline-flex items-center gap-1',
                                                org.is_verified ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-500/20' : 'bg-emerald-500 text-white hover:bg-emerald-600'
                                            ]"
                                        >
                                            <Check v-if="!org.is_verified" class="w-3.5 h-3.5" />
                                            <X v-else class="w-3.5 h-3.5" />
                                            {{ org.is_verified ? 'Revoke Verification' : 'Verify Organization' }}
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Organizations Pagination Bar -->
                    <div v-if="organizations.links && organizations.links.length > 3" class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs">
                        <span class="text-slate-500 font-medium">Showing page {{ organizations.current_page }} of {{ organizations.last_page }} ({{ organizations.total }} total organizations)</span>
                        <div class="flex items-center gap-1 flex-wrap">
                            <Link
                                v-for="(link, idx) in organizations.links"
                                :key="'org-'+idx"
                                :href="link.url || '#'"
                                v-html="link.label"
                                :class="[
                                    'px-3 py-1.5 rounded-xl border text-xs font-semibold transition',
                                    link.active ? 'bg-[#00b2e3] text-white border-[#00b2e3]' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800',
                                    !link.url ? 'opacity-40 cursor-not-allowed' : ''
                                ]"
                                preserve-scroll
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Candidates Verification & Proxy Entry -->
            <div v-if="activeTab === 'candidates'" class="space-y-6">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                            <Users class="w-4 h-4 text-[#00b2e3]" /> Candidate Fact Auditing & Verification Badges
                        </h2>
                        <button
                            @click="showProxyModal = true"
                            class="px-3.5 py-1.5 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl shadow-sm transition inline-flex items-center gap-1.5"
                        >
                            <Plus class="w-3.5 h-3.5" /> Register Proxy Candidate
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-800 text-[10px] uppercase font-bold text-slate-400">
                                    <th class="py-2.5 px-3">Candidate Name</th>
                                    <th class="py-2.5 px-3">Email</th>
                                    <th class="py-2.5 px-3">Education</th>
                                    <th class="py-2.5 px-3 text-center">Exp (Yrs)</th>
                                    <th class="py-2.5 px-3 text-center">Reliability</th>
                                    <th class="py-2.5 px-3 text-center">Status</th>
                                    <th class="py-2.5 px-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr v-for="c in candidates.data" :key="c.id" class="hover:bg-slate-50 dark:hover:bg-slate-950/50 transition">
                                    <td class="py-3 px-3 font-semibold text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
                                        {{ c.name }}
                                        <Award v-if="c.is_verified" class="w-4 h-4 text-emerald-500" />
                                    </td>
                                    <td class="py-3 px-3 font-mono text-slate-500">{{ c.email }}</td>
                                    <td class="py-3 px-3 font-medium text-slate-700 dark:text-slate-300">{{ c.education_level }}</td>
                                    <td class="py-3 px-3 text-center font-bold text-slate-900 dark:text-slate-100">{{ c.years_experience }}</td>
                                    <td class="py-3 px-3 text-center font-bold text-emerald-500">{{ c.reliability_score }}%</td>
                                    <td class="py-3 px-3 text-center">
                                        <span
                                            :class="[
                                                'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase',
                                                c.is_verified ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-500'
                                            ]"
                                        >
                                            {{ c.is_verified ? 'Verified Facts' : 'Unverified' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        <Link
                                            :href="`/admin/candidates/${c.id}/verify`"
                                            method="patch"
                                            as="button"
                                            preserve-scroll
                                            :class="[
                                                'px-3 py-1.5 rounded-xl text-xs font-semibold transition inline-flex items-center gap-1',
                                                c.is_verified ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-500/20' : 'bg-emerald-500 text-white hover:bg-emerald-600'
                                            ]"
                                        >
                                            <Check v-if="!c.is_verified" class="w-3.5 h-3.5" />
                                            <X v-else class="w-3.5 h-3.5" />
                                            {{ c.is_verified ? 'Revoke Verification' : 'Verify Credentials' }}
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Candidates Pagination Bar -->
                    <div v-if="candidates.links && candidates.links.length > 3" class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs">
                        <span class="text-slate-500 font-medium">Showing page {{ candidates.current_page }} of {{ candidates.last_page }} ({{ candidates.total }} total candidates)</span>
                        <div class="flex items-center gap-1 flex-wrap">
                            <Link
                                v-for="(link, idx) in candidates.links"
                                :key="'cand-'+idx"
                                :href="link.url || '#'"
                                v-html="link.label"
                                :class="[
                                    'px-3 py-1.5 rounded-xl border text-xs font-semibold transition',
                                    link.active ? 'bg-[#00b2e3] text-white border-[#00b2e3]' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800',
                                    !link.url ? 'opacity-40 cursor-not-allowed' : ''
                                ]"
                                preserve-scroll
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 4: Agency Staff Governance -->
            <div v-if="activeTab === 'staff'" class="space-y-6">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                <UserPlus class="w-4 h-4 text-indigo-500" /> Provisioned Agency Staff Team & Sub-Role Governance
                            </h2>
                            <p class="text-xs text-slate-500">Manage internal hiring agency auditors, verification officers, and system administrators.</p>
                        </div>
                        <button
                            @click="showProvisionStaffModal = true"
                            class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition inline-flex items-center gap-1.5"
                        >
                            <UserPlus class="w-3.5 h-3.5" /> Provision New Staff Account
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-800 text-[10px] uppercase font-bold text-slate-400">
                                    <th class="py-2.5 px-3">Staff Name</th>
                                    <th class="py-2.5 px-3">Email Address</th>
                                    <th class="py-2.5 px-3">Assigned Sub-Role</th>
                                    <th class="py-2.5 px-3">Provisioned Date</th>
                                    <th class="py-2.5 px-3 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr v-for="staff in agencyStaff" :key="staff.id" class="hover:bg-slate-50 dark:hover:bg-slate-950/50 transition">
                                    <td class="py-3 px-3 font-semibold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                        <ShieldCheck class="w-4 h-4 text-indigo-500" /> {{ staff.name }}
                                    </td>
                                    <td class="py-3 px-3 font-mono text-slate-500">{{ staff.email }}</td>
                                    <td class="py-3 px-3">
                                        <span
                                            :class="[
                                                'px-2.5 py-1 rounded-full text-[10px] font-bold uppercase font-mono',
                                                staff.agency_sub_role === 'super_admin' ? 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20' :
                                                staff.agency_sub_role === 'verification_officer' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' :
                                                'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20'
                                            ]"
                                        >
                                            {{ staff.agency_sub_role.replace('_', ' ') }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 font-mono text-slate-500">{{ staff.created_at }}</td>
                                    <td class="py-3 px-3 text-right">
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold">
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
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                            <Sliders class="w-4 h-4 text-[#00b2e3]" /> Active KBS Criteria Weights Calibration
                        </h2>
                        <button
                            @click="updateCriteriaWeights"
                            :disabled="criteriaForm.processing"
                            class="px-4 py-2 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl shadow-sm transition"
                        >
                            Save Criteria Weights
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div v-for="(crit, index) in criteria" :key="crit.id" class="p-4 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl space-y-2">
                            <div class="flex justify-between items-center text-xs font-bold text-slate-900 dark:text-slate-100">
                                <span>{{ crit.name }}</span>
                                <span class="font-mono text-[#00b2e3]">{{ (criteriaForm.criteria[index].weight * 100).toFixed(0) }}%</span>
                            </div>
                            <input
                                type="range"
                                min="0"
                                max="1"
                                step="0.05"
                                v-model.number="criteriaForm.criteria[index].weight"
                                class="w-full accent-[#00b2e3]"
                            />
                            <p class="text-[10px] text-slate-400">Key: <code class="font-mono text-slate-600 dark:text-slate-400">{{ crit.key }}</code></p>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Global IF-THEN Rules List -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                            <FileText class="w-4 h-4 text-indigo-500" /> Active Global IF-THEN Rules ({{ rules.length }})
                        </h2>
                        <button
                            @click="showAddRuleModal = true"
                            class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition inline-flex items-center gap-1.5"
                        >
                            <Plus class="w-3.5 h-3.5" /> Add Global IF-THEN Rule
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div v-for="r in rules" :key="r.id" class="p-4 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl flex items-center justify-between text-xs">
                            <div class="space-y-1">
                                <span class="font-bold text-slate-900 dark:text-slate-100">{{ r.name }}</span>
                                <p class="text-slate-500 font-mono text-[11px]">
                                    IF candidate.<span class="text-indigo-500">{{ r.field }}</span> {{ r.operator }} '{{ r.value }}' THEN <span class="font-bold uppercase text-amber-500">{{ r.action }}</span>
                                </p>
                            </div>
                            <Link
                                :href="`/admin/rules/${r.id}/toggle`"
                                method="patch"
                                as="button"
                                preserve-scroll
                                :class="[
                                    'px-3 py-1.5 rounded-xl text-xs font-semibold transition inline-flex items-center gap-1',
                                    r.active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/20' : 'bg-slate-200 dark:bg-slate-800 text-slate-500 hover:bg-slate-300'
                                ]"
                            >
                                <Check v-if="r.active" class="w-3.5 h-3.5 text-emerald-500" />
                                <X v-else class="w-3.5 h-3.5" />
                                {{ r.active ? 'Active Rule' : 'Inactive (Enable)' }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Create Global IF-THEN Rule Modal -->
        <div v-if="showAddRuleModal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5">
                <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <Plus class="w-5 h-5 text-indigo-500" /> Add New Global IF-THEN Rule
                    </h3>
                    <button @click="showAddRuleModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitCreateRule" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Rule Name</label>
                        <input v-model="newRuleForm.name" required type="text" placeholder="e.g. Mandatory Security Clearance" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Candidate Attribute Field</label>
                            <select v-model="newRuleForm.field" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold">
                                <option value="years_experience">years_experience</option>
                                <option value="reliability_score">reliability_score</option>
                                <option value="education_level">education_level</option>
                                <option value="is_verified">is_verified</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Operator</label>
                            <select v-model="newRuleForm.operator" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold font-mono">
                                <option value=">=">&gt;= (Greater than or Equal)</option>
                                <option value="<=">&lt;= (Less than or Equal)</option>
                                <option value="==">== (Exact Equals)</option>
                                <option value="contains">contains (In List)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Target Value</label>
                            <input v-model="newRuleForm.value" required type="text" placeholder="e.g. 5 or 85 or Master's Degree" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Rule Action</label>
                            <select v-model="newRuleForm.action" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold uppercase">
                                <option value="flag">FLAG (Audit Badge)</option>
                                <option value="bonus">BONUS (+ Points)</option>
                                <option value="exclude">EXCLUDE (Knockout)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Explanation Template (Use :value for dynamic replacement)</label>
                        <input v-model="newRuleForm.explanation_template" required type="text" placeholder="e.g. Verified candidate possesses at least :value years experience" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-mono" />
                    </div>

                    <div class="flex justify-end space-x-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="showAddRuleModal = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 font-semibold rounded-xl text-slate-700 dark:text-slate-300">
                            Cancel
                        </button>
                        <button type="submit" :disabled="newRuleForm.processing" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm">
                            Create Global Rule
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Proxy Candidate Creation Modal -->
        <div v-if="showProxyModal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5">
                <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <Plus class="w-5 h-5 text-[#00b2e3]" /> Assisted Proxy Candidate Entry
                    </h3>
                    <button @click="showProxyModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitProxyCandidate" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Candidate Full Name</label>
                        <input v-model="proxyForm.name" required type="text" placeholder="e.g. John Mwangi" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
                        <input v-model="proxyForm.email" required type="email" placeholder="e.g. john@example.com" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Education Level</label>
                            <select v-model="proxyForm.education_level" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold">
                                <option value="High School / Secondary">High School / Secondary</option>
                                <option value="Diploma / Associate">Diploma / Associate</option>
                                <option value="Bachelor's Degree">Bachelor's Degree</option>
                                <option value="Master's Degree">Master's Degree</option>
                                <option value="Doctorate (Ph.D. / M.D.)">Doctorate (Ph.D. / M.D.)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Years of Experience</label>
                            <input v-model.number="proxyForm.years_experience" min="0" max="40" type="number" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Skills (Comma Separated)</label>
                        <input v-model="proxyForm.skills_raw" type="text" placeholder="e.g. M&E, Project Management, Data Analysis" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Summary / Qualification Notes</label>
                        <textarea v-model="proxyForm.summary" rows="2" placeholder="Notes from paper CV or assisted interview..." class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="showProxyModal = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 font-semibold rounded-xl text-slate-700 dark:text-slate-300">
                            Cancel
                        </button>
                        <button type="submit" :disabled="proxyForm.processing" class="px-4 py-2 bg-[#00b2e3] hover:bg-[#0099c4] text-white font-semibold rounded-xl shadow-sm">
                            Create & Verify Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Provision Agency Staff Modal -->
        <div v-if="showProvisionStaffModal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5">
                <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <UserPlus class="w-5 h-5 text-indigo-500" /> Provision New Agency Staff Account
                    </h3>
                    <button @click="showProvisionStaffModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitProvisionStaff" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Staff Member Full Name</label>
                        <input v-model="provisionStaffForm.name" required type="text" placeholder="e.g. Inspector David Kimani" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Official Work Email Address</label>
                        <input v-model="provisionStaffForm.email" required type="email" placeholder="e.g. d.kimani@kbsagency.com" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Assigned Governance Sub-Role</label>
                        <select v-model="provisionStaffForm.agency_sub_role" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold">
                            <option value="super_admin">Super Admin (Full Rule Engine & Staff Management)</option>
                            <option value="verification_officer">Verification Officer (Employer & Candidate Badging)</option>
                            <option value="compliance_auditor">Compliance Auditor (Read-Only Audit Stream Reviewer)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Initial Temporary Password (Min 8 characters)</label>
                        <input v-model="provisionStaffForm.password" required type="password" placeholder="••••••••" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                    </div>

                    <div class="flex justify-end space-x-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="showProvisionStaffModal = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 font-semibold rounded-xl text-slate-700 dark:text-slate-300">
                            Cancel
                        </button>
                        <button type="submit" :disabled="provisionStaffForm.processing" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm">
                            Provision Staff Credentials
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
