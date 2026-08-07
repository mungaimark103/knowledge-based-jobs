<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import {
    Briefcase,
    Users,
    CheckCircle2,
    Clock,
    ShieldCheck,
    Plus,
    ExternalLink,
    Building2,
    Edit3,
    Eye,
    X,
    Check,
    Sparkles,
    AlertCircle,
    Trash2,
    Sliders,
    Zap,
    Upload,
    ImageIcon,
    LogOut
} from '@lucide/vue';

interface CustomRuleItem {
    id: string;
    type: 'SKILL' | 'LANGUAGE' | 'EDUCATION' | 'EXPERIENCE' | 'CERTIFICATION' | 'HARD_GATE';
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
const flash = computed(() => page.props.flash as { success?: string; error?: string });

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

const skillInput = ref('');
const languageInput = ref('');

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

function addSkill() {
    if (skillInput.value.trim() && !jobForm.required_skills.includes(skillInput.value.trim())) {
        jobForm.required_skills.push(skillInput.value.trim());
        skillInput.value = '';
    }
}

function removeSkill(index: number) {
    jobForm.required_skills.splice(index, 1);
}

function addLanguage() {
    if (languageInput.value.trim() && !jobForm.required_languages.includes(languageInput.value.trim())) {
        jobForm.required_languages.push(languageInput.value.trim());
        languageInput.value = '';
    }
}

function removeLanguage(index: number) {
    jobForm.required_languages.splice(index, 1);
}

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

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans transition-colors duration-300">
        <!-- Top Nav Header -->
        <header class="sticky top-0 z-40 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-6 py-4 flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-3">
                <div v-if="organization?.logo_path" class="h-10 w-10 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-center bg-white p-1">
                    <img :src="organization.logo_path" :alt="organization.name" class="h-full w-full object-contain" />
                </div>
                <div v-else class="h-10 w-10 rounded-xl bg-[#00b2e3] text-white flex items-center justify-center shadow-md shadow-[#00b2e3]/20 font-bold">
                    <Building2 class="w-6 h-6" />
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        {{ organization?.name || 'Hiring Organization' }}
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Employer Hiring & Dynamic KBS Rules Portal</p>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <button
                    @click="showJobModal = true"
                    class="px-4 py-2 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl shadow-sm transition inline-flex items-center gap-1.5"
                >
                    <Plus class="w-4 h-4" /> Post Vacancy Ad
                </button>
                <Link href="/opportunities" class="text-xs font-semibold text-[#00b2e3] hover:underline flex items-center gap-1">
                    <Eye class="w-3.5 h-3.5" /> View Public Directory
                </Link>
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="px-3.5 py-2 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-semibold rounded-xl transition inline-flex items-center gap-1.5"
                >
                    <LogOut class="w-3.5 h-3.5" /> Log out
                </Link>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 py-10 space-y-8">
            <!-- System Flash Messages -->
            <div v-if="flash?.success" class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 p-4 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <CheckCircle2 class="w-4 h-4 shrink-0 text-emerald-500" />
                <span>{{ flash.success }}</span>
            </div>
            <div v-if="flash?.error" class="bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 p-4 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <AlertCircle class="w-4 h-4 shrink-0 text-rose-500" />
                <span>{{ flash.error }}</span>
            </div>

            <!-- Organization Information Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-7 shadow-sm relative overflow-hidden space-y-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
                    <div class="flex items-center space-x-4">
                        <div v-if="organization?.logo_path" class="h-16 w-16 rounded-2xl bg-white border border-slate-200 dark:border-slate-800 p-1.5 flex items-center justify-center shadow-sm">
                            <img :src="organization.logo_path" :alt="organization.name" class="h-full w-full object-contain rounded-xl" />
                        </div>
                        <div v-else class="h-16 w-16 rounded-2xl bg-[#00b2e3]/10 text-[#00b2e3] border border-[#00b2e3]/20 flex items-center justify-center font-bold text-xl font-mono shadow-sm">
                            {{ organization?.code || 'ORG' }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-xl font-extrabold text-slate-900 dark:text-slate-100">
                                    {{ organization?.name }}
                                </h2>
                                <span class="px-2.5 py-0.5 rounded-full bg-[#00b2e3]/10 text-[#00b2e3] font-mono text-[10px] font-bold">
                                    {{ organization?.org_type || 'UN_AGENCY' }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Verified Hiring Entity • Account Code: <strong class="font-mono text-slate-700 dark:text-slate-300">{{ organization?.code }}</strong>
                            </p>
                        </div>
                    </div>

                    <button
                        @click="showOrgModal = true"
                        class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition inline-flex items-center gap-1.5 self-start md:self-auto"
                    >
                        <Edit3 class="w-3.5 h-3.5 text-[#00b2e3]" /> Edit Organization Details
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                    <!-- Vision Statement -->
                    <div class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 p-5 rounded-2xl space-y-2">
                        <h3 class="font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider text-[11px] flex items-center gap-1.5 text-[#00b2e3]">
                            <Sparkles class="w-3.5 h-3.5" /> Organization Vision
                        </h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed italic">
                            "{{ organization?.vision || 'Accelerating sustainable global development through automated knowledge-driven talent acquisition.' }}"
                        </p>
                    </div>

                    <!-- About Us / Overview -->
                    <div class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 p-5 rounded-2xl space-y-2">
                        <h3 class="font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider text-[11px] flex items-center gap-1.5 text-[#00b2e3]">
                            <Building2 class="w-3.5 h-3.5" /> About Us & Operational Focus
                        </h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                            {{ organization?.about_us || 'International multilateral organization dedicated to global humanitarian and developmental outcomes.' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- KBS Metric Highlights -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-1">
                    <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                        <span class="text-xs font-semibold uppercase tracking-wider">Posted Vacancies</span>
                        <Briefcase class="w-4 h-4 text-[#00b2e3]" />
                    </div>
                    <div class="text-2xl font-extrabold text-slate-900 dark:text-slate-100">
                        {{ stats.active_jobs }}
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-1">
                    <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                        <span class="text-xs font-semibold uppercase tracking-wider">Received Applications</span>
                        <Users class="w-4 h-4 text-blue-500" />
                    </div>
                    <div class="text-2xl font-extrabold text-slate-900 dark:text-slate-100">
                        {{ stats.total_applicants }}
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-1">
                    <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                        <span class="text-xs font-semibold uppercase tracking-wider">KBS Pass Rate</span>
                        <CheckCircle2 class="w-4 h-4 text-emerald-500" />
                    </div>
                    <div class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">
                        {{ stats.qualification_rate }}%
                    </div>
                </div>

                <div class="bg-gradient-to-br from-[#00b2e3]/10 to-blue-50 dark:from-[#00b2e3]/20 dark:to-slate-900 border border-[#00b2e3]/30 rounded-2xl p-5 shadow-sm space-y-1">
                    <div class="flex items-center justify-between text-[#00b2e3]">
                        <span class="text-xs font-bold uppercase tracking-wider">Screening Hours Saved</span>
                        <Clock class="w-4 h-4 text-[#00b2e3]" />
                    </div>
                    <div class="text-2xl font-extrabold text-[#00b2e3]">
                        {{ stats.hours_saved }} Hours
                    </div>
                    <p class="text-[10px] text-[#00b2e3]/80">Automated KBS Inference Benefit</p>
                </div>
            </div>

            <!-- Posted Vacancies List -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">Organization Vacancies</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Positions uploaded by your organization and active KBS candidate evaluation pipelines.</p>
                    </div>
                    <button
                        @click="showJobModal = true"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#00b2e3] hover:bg-[#0099c4] text-white rounded-xl text-xs font-semibold shadow-sm transition"
                    >
                        <Plus class="w-4 h-4" /> Create New Job Ad
                    </button>
                </div>

                <div v-if="jobs && jobs.length" class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400 font-semibold">
                                <th class="py-3 px-4">Position Title</th>
                                <th class="py-3 px-4">Grade</th>
                                <th class="py-3 px-4">Duty Station</th>
                                <th class="py-3 px-4">KBS Rules Configured</th>
                                <th class="py-3 px-4">Total Applicants</th>
                                <th class="py-3 px-4">KBS Qualified</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                            <tr v-for="job in jobs" :key="job.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                <td class="py-4 px-4 font-semibold text-slate-900 dark:text-slate-100">
                                    {{ job.title }}
                                </td>
                                <td class="py-4 px-4 font-mono text-xs text-[#00b2e3] font-bold">
                                    {{ job.grade }}
                                </td>
                                <td class="py-4 px-4 text-slate-600 dark:text-slate-400 text-xs">
                                    {{ job.location }}
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-lg text-xs font-bold inline-flex items-center gap-1">
                                        <Zap class="w-3 h-3" /> {{ (job.custom_rules?.length || 0) + (job.required_skills?.length || 0) }} Rules
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-bold">
                                        {{ job.applicant_count }} Applicants
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg text-xs font-bold">
                                        {{ job.recommended_count }} Recommended
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <Link
                                        :href="`/opportunities/${job.id}/applicants`"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#00b2e3]/10 text-[#00b2e3] hover:bg-[#00b2e3]/20 rounded-xl text-xs font-semibold transition"
                                    >
                                        Inspect Applicants Matrix <ExternalLink class="w-3.5 h-3.5" />
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else class="text-center py-12 space-y-3 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-200 dark:border-slate-800">
                    <Briefcase class="w-10 h-10 mx-auto text-slate-400" />
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">No Job Positions Uploaded Yet</h3>
                    <p class="text-xs text-slate-500">Create your organization's first vacancy ad to activate automated KBS screening rules.</p>
                    <button
                        @click="showJobModal = true"
                        class="px-4 py-2 bg-[#00b2e3] text-white text-xs font-semibold rounded-xl inline-flex items-center gap-1.5"
                    >
                        <Plus class="w-4 h-4" /> Post First Job Ad
                    </button>
                </div>
            </div>
        </main>

        <!-- Modal 1: Edit Organization Profile & Logo Provisioning -->
        <div v-if="showOrgModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5 relative max-h-[90vh] overflow-y-auto">
                <button @click="showOrgModal = false" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1">
                    <X class="w-5 h-5" />
                </button>

                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-xl bg-[#00b2e3]/10 text-[#00b2e3] flex items-center justify-center font-bold">
                        <Building2 class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Edit Organization Information</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Update Profile Credentials, Logo & Overview</p>
                    </div>
                </div>

                <form @submit.prevent="submitOrgUpdate" class="space-y-4 text-xs">
                    <!-- LOGO PROVISION SECTION -->
                    <div class="p-4 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-3">
                        <label class="block font-bold text-[#00b2e3] uppercase tracking-wider text-[10px] flex items-center gap-1.5">
                            <ImageIcon class="w-3.5 h-3.5" /> Organization Logo Provisioning
                        </label>

                        <div class="flex items-center space-x-4">
                            <!-- Logo Preview Box -->
                            <div class="h-14 w-14 rounded-2xl bg-white border border-slate-200 dark:border-slate-800 p-1.5 flex items-center justify-center shrink-0 shadow-sm overflow-hidden">
                                <img v-if="logoPreview" :src="logoPreview" alt="Logo Preview" class="h-full w-full object-contain rounded-xl" />
                                <Building2 v-else class="w-6 h-6 text-slate-400" />
                            </div>

                            <div class="flex-1 space-y-2">
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-500 mb-1">Upload File (PNG, JPG, SVG, WebP)</label>
                                    <input
                                        type="file"
                                        accept="image/*"
                                        @change="onLogoFileChange"
                                        class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#00b2e3]/10 file:text-[#00b2e3] hover:file:bg-[#00b2e3]/20 transition"
                                    />
                                    <p v-if="orgForm.errors.logo" class="text-rose-500 text-[11px] font-semibold mt-1">
                                        {{ orgForm.errors.logo }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-semibold text-slate-500 mb-1">Or Logo Image URL / Preset Badge Link</label>
                            <input
                                v-model="orgForm.logo_url"
                                type="text"
                                placeholder="e.g. https://upload.wikimedia.org/wikipedia/commons/e/ed/UNICEF_Logo.svg"
                                class="w-full px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs focus:outline-none"
                            />
                            <p v-if="orgForm.errors.logo_url" class="text-rose-500 text-[11px] font-semibold mt-1">
                                {{ orgForm.errors.logo_url }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Organization Name</label>
                        <input
                            v-model="orgForm.name"
                            type="text"
                            required
                            class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-[#00b2e3]"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Acronym / Code</label>
                            <input
                                v-model="orgForm.code"
                                type="text"
                                required
                                class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-mono text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-[#00b2e3]"
                            />
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Organization Type</label>
                            <select
                                v-model="orgForm.org_type"
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-[#00b2e3]"
                            >
                                <option value="PRIVATE_COMPANY">Private Sector / Corporate</option>
                                <option value="NGO">NGO / Non-Governmental Organization</option>
                                <option value="PARASTATAL">Parastatal / State Enterprise</option>
                                <option value="GOV_BODY">Government Body / Ministry</option>
                                <option value="UN_AGENCY">UN Agency / Multilateral</option>
                                <option value="INTERNATIONAL_ORG">International Organization</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Organization Vision Statement</label>
                        <textarea
                            v-model="orgForm.vision"
                            rows="2"
                            placeholder="State your organization's core vision..."
                            class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-[#00b2e3]"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">About Us & Overview</label>
                        <textarea
                            v-model="orgForm.about_us"
                            rows="3"
                            placeholder="Provide a brief overview of your organization's mission and mandate..."
                            class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-[#00b2e3]"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button
                            type="button"
                            @click="showOrgModal = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:underline"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="orgForm.processing"
                            class="px-5 py-2 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl shadow-sm transition disabled:opacity-40"
                        >
                            Save Organization Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal 2: Create Job Ad with DYNAMIC Agency KBS Custom Rules Engine Builder -->
        <div v-if="showJobModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-3xl w-full p-6 shadow-2xl space-y-5 relative max-h-[90vh] overflow-y-auto">
                <button @click="showJobModal = false" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1">
                    <X class="w-5 h-5" />
                </button>

                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-xl bg-[#00b2e3]/10 text-[#00b2e3] flex items-center justify-center font-bold">
                        <Zap class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Create Vacancy Ad & Custom KBS Rules</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Configure dynamic screening rules, knockout parameters, and custom scoring weights.</p>
                    </div>
                </div>

                <form @submit.prevent="submitJobPost" class="space-y-5 text-xs">
                    <!-- Position Title & Duty Station -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Job Position Title</label>
                            <input
                                v-model="jobForm.title"
                                type="text"
                                required
                                placeholder="e.g. Senior Climate Policy Advisor"
                                class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-[#00b2e3]"
                            />
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Duty Station / Location</label>
                            <input
                                v-model="jobForm.location"
                                type="text"
                                required
                                placeholder="e.g. Nairobi, Kenya"
                                class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-[#00b2e3]"
                            />
                        </div>
                    </div>

                    <!-- Position Grade & Min Experience -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Position Grade</label>
                            <select
                                v-model="jobForm.grade"
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-[#00b2e3]"
                            >
                                <option value="P-1">P-1 (Entry Professional)</option>
                                <option value="P-2">P-2 (Associate Officer)</option>
                                <option value="P-3">P-3 (Professional Specialist)</option>
                                <option value="P-4">P-4 (Senior Specialist)</option>
                                <option value="P-5">P-5 (Chief / Senior Manager)</option>
                                <option value="D-1">D-1 (Director)</option>
                                <option value="GS-7">GS-7 (General Service Lead)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Min Required Experience (Years)</label>
                            <input
                                v-model.number="jobForm.min_experience"
                                type="number"
                                min="0"
                                required
                                class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-[#00b2e3]"
                            />
                        </div>
                    </div>

                    <!-- DYNAMIC AGENCY KBS CUSTOM RULES ENGINE BUILDER -->
                    <div class="p-4 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-[#00b2e3] uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                                    <Sliders class="w-4 h-4" /> Agency Custom KBS Rules Builder
                                </h4>
                                <p class="text-[11px] text-slate-500">Add dynamic screening rules, knockout parameters, and custom scoring weights specific to this vacancy.</p>
                            </div>
                            <button
                                type="button"
                                @click="addCustomRule"
                                class="px-3 py-1.5 bg-[#00b2e3] text-white text-xs font-semibold rounded-xl hover:bg-[#0099c4] transition inline-flex items-center gap-1"
                            >
                                <Plus class="w-3.5 h-3.5" /> Add Custom KBS Rule
                            </button>
                        </div>

                        <!-- Custom Rules List -->
                        <div v-if="jobForm.custom_rules && jobForm.custom_rules.length" class="space-y-3">
                            <div
                                v-for="(rule, rIdx) in jobForm.custom_rules"
                                :key="rule.id"
                                class="p-3.5 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 space-y-3 relative group"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-[11px] text-slate-700 dark:text-slate-300 flex items-center gap-1">
                                        ⚡ Rule #{{ rIdx + 1 }}
                                    </span>
                                    <button
                                        type="button"
                                        @click="removeCustomRule(rIdx)"
                                        class="text-rose-500 hover:text-rose-700 text-xs font-semibold inline-flex items-center gap-1 p-1"
                                    >
                                        <Trash2 class="w-3.5 h-3.5" /> Remove
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                                    <!-- Rule Type -->
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-500 mb-1">Rule Parameter</label>
                                        <select
                                            v-model="rule.type"
                                            class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none"
                                        >
                                            <option value="SKILL">Technical Skill</option>
                                            <option value="LANGUAGE">Official Language</option>
                                            <option value="EDUCATION">Education Degree</option>
                                            <option value="EXPERIENCE">Min Years Experience</option>
                                            <option value="CERTIFICATION">License / Certification</option>
                                            <option value="HARD_GATE">Hard-Gate Requirement</option>
                                        </select>
                                    </div>

                                    <!-- Rule Title / Label -->
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-500 mb-1">Rule Title / Description</label>
                                        <input
                                            v-model="rule.title"
                                            type="text"
                                            placeholder="e.g. PMP Certification"
                                            class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-800 dark:text-slate-200 focus:outline-none"
                                        />
                                    </div>

                                    <!-- Requirement Value -->
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-500 mb-1">Required Value</label>
                                        <input
                                            v-model="rule.value"
                                            type="text"
                                            placeholder="e.g. French / PMP / Master"
                                            class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-800 dark:text-slate-200 focus:outline-none"
                                        />
                                    </div>

                                    <!-- Enforcement Mode -->
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-500 mb-1">Enforcement Mode</label>
                                        <select
                                            v-model="rule.mode"
                                            class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none"
                                        >
                                            <option value="MANDATORY_KNOCKOUT">⛔ Mandatory Knockout</option>
                                            <option value="RECOMMENDED_BONUS">⭐ Recommended Bonus</option>
                                            <option value="WEIGHTED_FACTOR">⚖️ Weighted Scoring Factor</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-4 text-slate-500 text-xs italic">
                            No custom rules added yet. Click "+ Add Custom KBS Rule" to build agency-specific screening parameters.
                        </div>
                    </div>

                    <!-- Job Specification / Description -->
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Detailed Job Description & Responsibilities</label>
                        <textarea
                            v-model="jobForm.description"
                            rows="4"
                            required
                            placeholder="Describe the duties, responsibilities, and expected outcomes for this vacancy..."
                            class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-[#00b2e3]"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button
                            type="button"
                            @click="showJobModal = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:underline"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="jobForm.processing"
                            class="px-5 py-2 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl shadow-sm transition disabled:opacity-40"
                        >
                            Publish Vacancy & Active KBS Rules
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
