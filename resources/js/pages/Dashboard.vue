<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ShieldCheck, FileCheck, CheckCircle2, Award, Sparkles, ArrowRight, UserCheck, Briefcase, ExternalLink, Upload, X, FileText, AlertCircle, Lock, Eye, Download, GraduationCap, Users, Edit3, Plus, List, ListOrdered } from '@lucide/vue';
import ThemeToggle from '@/components/ThemeToggle.vue';

interface ApplicationItem {
    id: number;
    job_id: number;
    job_title: string;
    organization: string;
    org_code: string;
    grade: string;
    location: string;
    status: string;
    applied_at: string;
    kbs_score: number;
    kbs_status: string;
}

interface RecommendedJob {
    id: number;
    title: string;
    organization: string;
    grade: string;
    location: string;
    min_experience: number;
    kbs_match: {
        score: number;
        status: string;
    } | null;
}

const props = defineProps<{
    user: any;
    profile: any;
    hasCredentials?: boolean;
    stats: {
        total_applications: number;
        qualified_count: number;
        reliability_score: number | null;
        years_experience: number;
    };
    applications: ApplicationItem[];
    recommendedJobs: RecommendedJob[];
}>();

const page = usePage();
const flash = computed(() => page.props.flash as { success?: string; error?: string });

const showUploadModal = ref(false);
const showPdfModal = ref(false);
const showEditProfileModal = ref(false);

const resumeForm = useForm({
    resume: null as File | null,
});

const monthsList = [
    { value: '01', name: 'Jan' },
    { value: '02', name: 'Feb' },
    { value: '03', name: 'Mar' },
    { value: '04', name: 'Apr' },
    { value: '05', name: 'May' },
    { value: '06', name: 'Jun' },
    { value: '07', name: 'Jul' },
    { value: '08', name: 'Aug' },
    { value: '09', name: 'Sep' },
    { value: '10', name: 'Oct' },
    { value: '11', name: 'Nov' },
    { value: '12', name: 'Dec' },
];

const currentYearNum = new Date().getFullYear();
const yearsList = Array.from({ length: 35 }, (_, i) => String(currentYearNum - i));

function getPositionTimestamp(w: any, isStart = false): number {
    if (!isStart && w.is_current) {
        return 99999999;
    }
    const year = isStart ? (w.start_year || '1970') : (w.end_year || '1970');
    const month = isStart ? (w.start_month || '01') : (w.end_month || '01');
    return parseInt(year) * 100 + parseInt(month);
}

function sortWorkHistoryReverseChronological(items: any[]): any[] {
    return [...items].sort((a, b) => {
        const endA = getPositionTimestamp(a, false);
        const endB = getPositionTimestamp(b, false);
        if (endB !== endA) {
            return endB - endA;
        }
        const startA = getPositionTimestamp(a, true);
        const startB = getPositionTimestamp(b, true);
        return startB - startA;
    });
}

const profileForm = useForm({
    education_level: props.profile?.education_level || "Bachelor's Degree",
    years_experience: props.profile?.years_experience ?? 0,
    summary: props.profile?.summary || '',
    skills_raw: Array.isArray(props.profile?.skills) ? props.profile.skills.join(', ') : '',
    work_history: Array.isArray(props.profile?.work_history) && props.profile.work_history.length
        ? sortWorkHistoryReverseChronological(props.profile.work_history.map((w: any) => ({
            role: w.role || '',
            employer: w.employer || '',
            start_month: w.start_month || '',
            start_year: w.start_year || '',
            is_current: Boolean(w.is_current),
            end_month: w.end_month || '',
            end_year: w.end_year || '',
            description: w.description || '',
        })))
        : [],
    education_history: Array.isArray(props.profile?.education_history) && props.profile.education_history.length
        ? props.profile.education_history.map((e: any) => ({
            degree: e.degree || '',
            institution: e.institution || '',
            year: e.year || String(currentYearNum - 4),
        }))
        : [{ degree: '', institution: '', year: String(currentYearNum - 4) }],
    references_list: Array.isArray(props.profile?.references_list) && props.profile.references_list.length
        ? props.profile.references_list.map((r: any) => ({
            name: r.name || '',
            title: r.title || '',
            organization: r.organization || '',
            email: r.email || '',
            phone: r.phone || '',
        }))
        : [{ name: '', title: '', organization: '', email: '', phone: '' }],
});

function hasInvalidDates(w: any): boolean {
    if (w.is_current || !w.start_year || !w.start_month || !w.end_year || !w.end_month) {
        return false;
    }
    const sDate = new Date(parseInt(w.start_year), parseInt(w.start_month) - 1, 1);
    const eDate = new Date(parseInt(w.end_year), parseInt(w.end_month) - 1, 1);
    return sDate > eDate;
}

const dateValidationError = computed(() => {
    return (profileForm.work_history || []).some(w => hasInvalidDates(w));
});

function isValidPhone(phone?: string): boolean {
    if (!phone || phone.trim() === '') return true;
    return /^\+?[0-9\s\-\(\)]{7,20}$/.test(phone.trim());
}

function isValidEmail(email?: string): boolean {
    if (!email || email.trim() === '') return true;
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim());
}

const formValidationErrors = computed(() => {
    const errs: string[] = [];

    if (dateValidationError.value) {
        errs.push('One or more work positions have a Start Date later than the End Date.');
    }

    const skillsCount = profileForm.skills_raw.split(',').filter(s => s.trim().length > 0).length;
    if (skillsCount === 0) {
        errs.push('Please enter at least one technical or professional skill tag.');
    }

    (profileForm.references_list || []).forEach((r, idx) => {
        if (r.phone && !isValidPhone(r.phone)) {
            errs.push(`Referee #${idx + 1} (${r.name || 'Unnamed'}) phone number format is invalid (e.g. +254 700 000 000).`);
        }
        if (r.email && !isValidEmail(r.email)) {
            errs.push(`Referee #${idx + 1} (${r.name || 'Unnamed'}) email address format is invalid.`);
        }
    });

    return errs;
});

function insertBullets(index: number) {
    const item = profileForm.work_history[index];
    if (!item) return;

    if (!item.description || item.description.trim() === '') {
        item.description = '• ';
    } else {
        const lines = item.description.split('\n');
        item.description = lines.map(line => line.trim().startsWith('• ') || /^\d+\.\s/.test(line.trim()) ? line : `• ${line}`).join('\n');
    }
}

function insertNumbers(index: number) {
    const item = profileForm.work_history[index];
    if (!item) return;

    if (!item.description || item.description.trim() === '') {
        item.description = '1. ';
    } else {
        const lines = item.description.split('\n');
        item.description = lines.map((line, i) => {
            const clean = line.replace(/^[•\d\.\s]+/, '').trim();
            return clean ? `${i + 1}. ${clean}` : `${i + 1}. `;
        }).join('\n');
    }
}

function formatDeliverableLines(text?: string): string[] {
    if (!text) return [];
    return text
        .split('\n')
        .map(l => l.trim())
        .filter(l => l.length > 0);
}

const calculatedExperience = computed(() => {
    let totalMonths = 0;
    const now = new Date();
    const cYear = now.getFullYear();
    const cMonth = now.getMonth() + 1;

    (profileForm.work_history || []).forEach(w => {
        if (!w.start_year || !w.start_month) return;
        if (hasInvalidDates(w)) return;

        const sYear = parseInt(w.start_year);
        const sMonth = parseInt(w.start_month);

        let eYear = cYear;
        let eMonth = cMonth;

        if (!w.is_current && w.end_year && w.end_month) {
            eYear = parseInt(w.end_year);
            eMonth = parseInt(w.end_month);
        }

        const diff = (eYear - sYear) * 12 + (eMonth - sMonth);
        if (diff > 0) {
            totalMonths += diff;
        }
    });

    const years = Math.floor(totalMonths / 12);
    const months = totalMonths % 12;
    return { totalMonths, years, months };
});

function addWorkExperience() {
    profileForm.work_history.push({ role: '', employer: '', start_month: '01', start_year: String(currentYearNum - 1), is_current: false, end_month: '12', end_year: String(currentYearNum), description: '' });
}

function removeWorkExperience(index: number) {
    profileForm.work_history.splice(index, 1);
}

function addEducationItem() {
    profileForm.education_history.push({ degree: '', institution: '', year: String(currentYearNum - 4) });
}

function removeEducationItem(index: number) {
    if (profileForm.education_history.length > 1) {
        profileForm.education_history.splice(index, 1);
    }
}

function addReferenceItem() {
    profileForm.references_list.push({ name: '', title: '', organization: '', email: '', phone: '' });
}

function removeReferenceItem(index: number) {
    if (profileForm.references_list.length > 1) {
        profileForm.references_list.splice(index, 1);
    }
}

function submitProfileForm() {
    profileForm.clearErrors();

    if (formValidationErrors.value.length > 0) {
        return;
    }

    const skillsArr = profileForm.skills_raw
        .split(',')
        .map(s => s.trim())
        .filter(s => s.length > 0);

    const autoYears = Math.max(calculatedExperience.value.years, 0);

    const rawFiltered = profileForm.work_history.filter(w => w.role.trim() !== '' || w.employer.trim() !== '');
    const sortedPositions = sortWorkHistoryReverseChronological(rawFiltered);

    const formattedWorkHistory = sortedPositions.map(w => {
        const startLabel = `${monthsList.find(m => m.value === w.start_month)?.name || w.start_month} ${w.start_year}`;
        const endLabel = w.is_current ? 'Present' : `${monthsList.find(m => m.value === w.end_month)?.name || w.end_month} ${w.end_year}`;
        return {
            ...w,
            period: `${startLabel} – ${endLabel}`,
        };
    });

    profileForm.transform(data => ({
        education_level: data.education_level,
        years_experience: autoYears,
        summary: data.summary,
        skills: skillsArr.length > 0 ? skillsArr : ['General Skills'],
        work_history: formattedWorkHistory,
        education_history: data.education_history.filter(e => e.degree.trim() !== '' || e.institution.trim() !== ''),
        references_list: data.references_list.filter(r => r.name.trim() !== '' || r.organization.trim() !== ''),
    })).post('/candidate/profile', {
        onSuccess: () => {
            showEditProfileModal.value = false;
        },
    });
}

function handleFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        resumeForm.resume = target.files[0];
        resumeForm.clearErrors();
    }
}

function submitResume() {
    if (!resumeForm.resume) return;
    resumeForm.post('/candidate/resume', {
        preserveScroll: true,
        onSuccess: () => {
            showUploadModal.value = false;
            resumeForm.reset();
        },
        onError: () => {
            // Keep modal open to display validation error
        },
    });
}

const getStatusBadge = (status: string) => {
    if (status === 'job_unavailable') return { label: 'JOB NO LONGER AVAILABLE', class: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/30 font-bold' };
    if (status === 'submitted') return { label: 'SUBMITTED', class: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/30' };
    if (status === 'shortlisted') return { label: 'SHORTLISTED', class: 'bg-[#00b2e3]/10 text-[#00b2e3] border-[#00b2e3]/30' };
    return { label: 'UNDER REVIEW', class: 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' };
};
</script>

<template>
    <Head title="Candidate Dashboard - KBS System" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans transition-colors duration-300">
        <!-- Top Nav Header -->
        <header class="sticky top-0 z-40 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-6 py-4 flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 rounded-xl bg-[#00b2e3] text-white flex items-center justify-center shadow-md shadow-[#00b2e3]/20 font-bold">
                    <ShieldCheck class="w-6 h-6" />
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        Welcome back, {{ user?.name }}
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Job Seeker Candidate Portal</p>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <button
                    @click="showUploadModal = true"
                    class="px-3.5 py-2 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl shadow-sm transition inline-flex items-center gap-1.5"
                >
                    <Upload class="w-3.5 h-3.5" /> Upload CV Document
                </button>

                <!-- <ThemeToggle /> -->

                <a
                    href="/employer/portal-switch"
                    class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-semibold rounded-xl transition inline-flex items-center gap-1.5"
                >
                    🏢 Employer Portal
                </a>

                <Link
                    href="/opportunities"
                    class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-semibold rounded-xl transition inline-flex items-center gap-1.5"
                >
                    <Sparkles class="w-3.5 h-3.5" /> Explore Directory
                </Link>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 py-10 space-y-8">
            <!-- System Flash Notification Banners -->
            <div v-if="flash?.success" class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 p-4 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <CheckCircle2 class="w-4 h-4 shrink-0 text-emerald-500" />
                <span>{{ flash.success }}</span>
            </div>
            <div v-if="flash?.warning" class="bg-amber-500/10 border border-amber-500/30 text-amber-700 dark:text-amber-400 p-4 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <AlertCircle class="w-4 h-4 shrink-0 text-amber-500" />
                <span>{{ flash.warning }}</span>
            </div>
            <div v-if="flash?.error" class="bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 p-4 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <AlertCircle class="w-4 h-4 shrink-0 text-rose-500" />
                <span>{{ flash.error }}</span>
            </div>
            <!-- Mandatory CV Upload Banner for New Candidates -->
            <div
                v-if="!profile?.education_level"
                class="bg-gradient-to-r from-[#00b2e3]/15 to-indigo-50 dark:from-[#00b2e3]/20 dark:to-slate-900 border border-[#00b2e3]/30 rounded-2xl p-5 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4"
            >
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-[#00b2e3] text-white flex items-center justify-center font-bold shrink-0">
                        <FileText class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Upload CV Document to Deduce Credentials</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                            Candidate credentials (Education Level, Experience, Skills) are authentically deduced from your uploaded CV document by our KBS Inference Engine.
                        </p>
                    </div>
                </div>

                <div class="shrink-0">
                    <button
                        @click="showUploadModal = true"
                        class="px-5 py-2.5 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl shadow-md shadow-[#00b2e3]/20 transition inline-flex items-center gap-1.5"
                    >
                        <Upload class="w-4 h-4" /> Upload CV (PDF / DOCX)
                    </button>
                </div>
            </div>

            <!-- Candidate Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-1">
                    <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                        <span class="text-xs font-semibold uppercase tracking-wider">Submitted Applications</span>
                        <FileCheck class="w-4 h-4 text-[#00b2e3]" />
                    </div>
                    <div class="text-2xl font-extrabold text-slate-900 dark:text-slate-100">
                        {{ stats.total_applications }}
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-1">
                    <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                        <span class="text-xs font-semibold uppercase tracking-wider">KBS Qualified</span>
                        <CheckCircle2 class="w-4 h-4 text-emerald-500" />
                    </div>
                    <div class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">
                        {{ stats.qualified_count }} Positions
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-1">
                    <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                        <span class="text-xs font-semibold uppercase tracking-wider">Reliability Score</span>
                        <Award class="w-4 h-4 text-amber-500" />
                    </div>
                    <div class="text-2xl font-extrabold text-slate-900 dark:text-slate-100">
                        {{ stats.reliability_score !== null ? `${stats.reliability_score}%` : '—' }}
                    </div>
                    <p v-if="stats.reliability_score === null" class="text-[10px] text-slate-400">Not rated yet (Requires CV)</p>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-1">
                    <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                        <span class="text-xs font-semibold uppercase tracking-wider">Experience Level</span>
                        <Briefcase class="w-4 h-4 text-[#00b2e3]" />
                    </div>
                    <div class="text-2xl font-extrabold text-slate-900 dark:text-slate-100">
                        {{ profile?.education_level ? `${stats.years_experience} Years` : '—' }}
                    </div>
                </div>
            </div>

            <!-- Content Grid: Left 2 Cols (Applications & Structured Parsed CV Viewer), Right 1 Col (Profile Facts & Recommended) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left 2 Cols -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- My Applications -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">My Applications</h2>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Track application status and KBS match scores.</p>
                            </div>
                            <Link href="/opportunities" class="text-xs font-semibold text-[#00b2e3] hover:underline">
                                Browse More Vacancies &rarr;
                            </Link>
                        </div>

                        <div v-if="applications.length" class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-200 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400 font-semibold">
                                        <th class="py-3 px-4">Position</th>
                                        <th class="py-3 px-4">Organization</th>
                                        <th class="py-3 px-4">Applied Date</th>
                                        <th class="py-3 px-4">KBS Match</th>
                                        <th class="py-3 px-4 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                                    <tr v-for="app in applications" :key="app.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                        <td class="py-4 px-4 font-semibold text-slate-900 dark:text-slate-100">
                                            <Link :href="`/opportunities/${app.job_id}`" class="hover:text-[#00b2e3] transition">
                                                {{ app.job_title }}
                                            </Link>
                                            <div class="text-xs text-slate-400 font-mono font-normal">{{ app.grade }} • {{ app.location }}</div>
                                        </td>
                                        <td class="py-4 px-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase">
                                            {{ app.organization }}
                                        </td>
                                        <td class="py-4 px-4 text-xs text-slate-500">
                                            {{ app.applied_at }}
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="px-2.5 py-1 bg-[#00b2e3]/10 text-[#00b2e3] rounded-lg text-xs font-bold font-mono">
                                                {{ app.kbs_score }}%
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-right">
                                            <span :class="['px-2.5 py-1 rounded-lg text-xs font-bold border', getStatusBadge(app.status).class]">
                                                {{ getStatusBadge(app.status).label }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-else class="text-center py-10 space-y-3 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-200 dark:border-slate-800">
                            <Briefcase class="w-8 h-8 mx-auto text-slate-400" />
                            <p class="text-xs text-slate-500">You haven't submitted any job applications yet.</p>
                            <Link href="/opportunities" class="px-4 py-2 bg-[#00b2e3] text-white text-xs font-semibold rounded-xl inline-block">
                                Find Impact Opportunities
                            </Link>
                        </div>
                    </div>

                    <!-- Parsed CV Document Viewer Card (Standard CV Structure) -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-6">
                        <!-- Card Header & Document Actions -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">Structured Digital CV & Qualifications Profile</h2>
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold">
                                        ✓ Active Facts Base
                                    </span>
                                </div>
                                <p v-if="profile?.resume_filename" class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    Reference Document: <strong class="font-mono text-slate-700 dark:text-slate-300">{{ profile.resume_filename }}</strong>
                                </p>
                            </div>

                            <div class="flex items-center gap-2 flex-wrap">
                                <button
                                    @click="showEditProfileModal = true"
                                    class="px-3.5 py-2 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl shadow-sm transition inline-flex items-center gap-1.5"
                                >
                                    <Edit3 class="w-3.5 h-3.5" /> Edit CV Form Details
                                </button>

                                <button
                                    v-if="profile?.resume_path"
                                    @click="showPdfModal = true"
                                    class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-800 dark:text-slate-200 text-xs font-semibold rounded-xl transition inline-flex items-center gap-1.5"
                                >
                                    <Eye class="w-3.5 h-3.5" /> View PDF
                                </button>

                                <a
                                    v-if="profile?.resume_path"
                                    href="/candidate/resume/download"
                                    class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-800 dark:text-slate-200 text-xs font-semibold rounded-xl transition inline-flex items-center gap-1.5"
                                >
                                    <Download class="w-3.5 h-3.5" /> Download
                                </a>

                                <button
                                    @click="showUploadModal = true"
                                    class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-800 dark:text-slate-200 text-xs font-semibold rounded-xl transition inline-flex items-center gap-1.5"
                                >
                                    <Upload class="w-3.5 h-3.5" /> Attach PDF
                                </button>
                            </div>
                        </div>

                        <!-- 1. Professional Summary -->
                        <div v-if="profile?.summary" class="space-y-2">
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                                <FileText class="w-3.5 h-3.5 text-[#00b2e3]" /> Professional Summary
                            </h3>
                            <p class="text-xs text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-950 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 leading-relaxed">
                                {{ profile.summary }}
                            </p>
                        </div>

                        <!-- 2. Work History -->
                        <div class="space-y-3">
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                                <Briefcase class="w-3.5 h-3.5 text-[#00b2e3]" /> Work History & Employment Timeline
                            </h3>

                            <div v-if="profile?.work_history && profile.work_history.length" class="space-y-3">
                                <div
                                    v-for="(job, idx) in profile.work_history"
                                    :key="idx"
                                    class="bg-slate-50 dark:bg-slate-950 p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-1.5 text-xs"
                                >
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-bold text-slate-900 dark:text-slate-100 text-sm">{{ job.role }}</h4>
                                            <span class="text-slate-500 font-semibold">{{ job.employer }}</span>
                                        </div>
                                        <span v-if="job.period || job.duration" class="px-2 py-0.5 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded font-mono font-bold text-[10px]">
                                            {{ job.period || job.duration }}
                                        </span>
                                    </div>
                                    <p v-if="job.description" class="text-slate-600 dark:text-slate-400 pt-1 leading-relaxed whitespace-pre-line">
                                        {{ job.description }}
                                    </p>
                                    <div v-else-if="job.works_done && job.works_done.length" class="pt-1.5 space-y-1 text-slate-600 dark:text-slate-400">
                                        <ul class="list-disc list-inside space-y-0.5 text-xs pl-1">
                                            <li v-for="(task, tIdx) in job.works_done" :key="tIdx">{{ task }}</li>
                                        </ul>
                                    </div>
                                    <p v-else-if="job.responsibilities" class="text-slate-600 dark:text-slate-400 pt-1 leading-relaxed">
                                        {{ job.responsibilities }}
                                    </p>
                                </div>
                            </div>
                            <div v-else class="text-xs text-slate-500 italic bg-slate-50 dark:bg-slate-950 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800">
                                No work positions added yet. Click "Edit CV Form Details" above to add your employment positions.
                            </div>
                        </div>

                        <!-- 3. Education Qualifications -->
                        <div class="space-y-3">
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                                <GraduationCap class="w-3.5 h-3.5 text-[#00b2e3]" /> Education Qualifications
                            </h3>

                            <div v-if="profile?.education_history && profile.education_history.length" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div
                                    v-for="(edu, idx) in profile.education_history"
                                    :key="idx"
                                    class="bg-slate-50 dark:bg-slate-950 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-1 text-xs"
                                >
                                    <div class="flex justify-between items-center">
                                        <h4 class="font-bold text-slate-900 dark:text-slate-100">{{ edu.degree }}</h4>
                                        <span v-if="edu.year || edu.graduation_year" class="text-slate-400 font-mono text-[10px]">{{ edu.year || edu.graduation_year }}</span>
                                    </div>
                                    <p class="text-slate-500">{{ edu.institution }}</p>
                                    <p v-if="edu.specialization" class="text-[#00b2e3] font-semibold text-[11px]">{{ edu.specialization }}</p>
                                </div>
                            </div>
                            <div v-else class="text-xs text-slate-500 italic bg-slate-50 dark:bg-slate-950 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800">
                                Highest Education: {{ profile?.education_level || 'Bachelor\'s Degree' }}
                            </div>
                        </div>

                        <!-- 4. Verified Core Competencies & Languages -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                                    <Sparkles class="w-3.5 h-3.5 text-[#00b2e3]" /> Extracted Core Skills
                                </h3>
                                <div class="flex flex-wrap gap-1.5">
                                    <span
                                        v-for="skill in (profile?.skills || [])"
                                        :key="skill"
                                        class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-medium"
                                    >
                                        {{ skill }}
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                                    <UserCheck class="w-3.5 h-3.5 text-[#00b2e3]" /> Languages Spoken
                                </h3>
                                <div class="flex flex-wrap gap-1.5">
                                    <span
                                        v-for="lang in (profile?.languages || [])"
                                        :key="lang"
                                        class="px-2.5 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg text-xs font-bold"
                                    >
                                        {{ lang }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Professional References -->
                        <div class="space-y-3">
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                                <Users class="w-3.5 h-3.5 text-[#00b2e3]" /> Professional References
                            </h3>

                            <div v-if="profile?.references_list && profile.references_list.length" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div
                                    v-for="(refItem, idx) in profile.references_list"
                                    :key="idx"
                                    class="bg-slate-50 dark:bg-slate-950 p-3 rounded-xl border border-slate-200 dark:border-slate-800 text-xs space-y-1"
                                >
                                    <h4 class="font-bold text-slate-900 dark:text-slate-100">{{ refItem.name }}</h4>
                                    <p class="text-slate-500">{{ refItem.title }} • {{ refItem.organization }}</p>
                                    <p v-if="refItem.email" class="font-mono text-[#00b2e3] text-[11px]">{{ refItem.email }}</p>
                                </div>
                            </div>
                            <div v-else class="text-xs text-slate-500 italic bg-slate-50 dark:bg-slate-950 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800">
                                References available upon request.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right 1 Col: Profile Credentials & Recommended Jobs -->
                <div class="space-y-6">
                    <!-- Profile Credentials Card -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                            <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                                <UserCheck class="w-4 h-4 text-[#00b2e3]" /> Profile Credentials
                            </h3>
                            <span v-if="profile?.education_level" class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold rounded">
                                ✓ Verified from CV
                            </span>
                        </div>

                        <div class="space-y-2.5 text-xs">
                            <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                                <span class="text-slate-500">Full Name</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ user.name }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                                <span class="text-slate-500">Email</span>
                                <span class="font-mono text-slate-600 dark:text-slate-400">{{ user.email }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                                <span class="text-slate-500">Education Level</span>
                                <span :class="['font-semibold', (profile?.education_level && profile.education_level !== 'Not Specified') || profile?.resume_path ? 'text-slate-800 dark:text-slate-200' : 'text-slate-400 italic']">
                                    {{ profile?.education_level && profile.education_level !== 'Not Specified' ? profile.education_level : (profile?.resume_path ? "Bachelor's / Tertiary Qualification" : "Not Uploaded (Blank)") }}
                                </span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                                <span class="text-slate-500">Field Experience</span>
                                <span :class="['font-semibold', profile?.resume_path || (profile?.field_experience_months && profile.field_experience_months > 0) ? 'text-[#00b2e3]' : 'text-slate-400 italic']">
                                    {{ profile?.resume_path || (profile?.field_experience_months && profile.field_experience_months > 0) ? `${profile?.field_experience_months || (stats.years_experience * 12) || 48} Months` : 'Not Uploaded (Blank)' }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-2 pt-1">
                            <button
                                @click="showEditProfileModal = true"
                                class="w-full py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold transition flex items-center justify-center gap-1.5"
                            >
                                <Edit3 class="w-3.5 h-3.5" /> Edit Digital CV Builder Facts
                            </button>
                            <button
                                @click="showUploadModal = true"
                                class="w-full py-2.5 bg-[#00b2e3]/10 hover:bg-[#00b2e3]/20 text-[#00b2e3] rounded-xl text-xs font-semibold transition flex items-center justify-center gap-1.5"
                            >
                                <Upload class="w-3.5 h-3.5" /> {{ profile?.education_level ? 'Re-upload / Re-parse CV' : 'Upload CV Document (PDF)' }}
                            </button>
                        </div>
                    </div>

                    <!-- Recommended Vacancies -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-4">
                        <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <Sparkles class="w-4 h-4 text-[#00b2e3]" /> Recommended Positions
                        </h3>

                        <div class="space-y-3">
                            <div
                                v-for="job in recommendedJobs"
                                :key="job.id"
                                class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 p-3.5 rounded-xl space-y-2 text-xs"
                            >
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="text-[10px] font-mono font-bold text-[#00b2e3]">{{ job.grade }}</span> • 
                                        <span class="text-slate-500 font-semibold uppercase">{{ job.organization }}</span>
                                        <h4 class="font-bold text-slate-900 dark:text-slate-100">{{ job.title }}</h4>
                                    </div>
                                    <span v-if="job.kbs_match" class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold rounded text-[10px]">
                                        {{ job.kbs_match.score }}% Match
                                    </span>
                                    <span v-else class="px-2 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 font-medium rounded text-[10px]">
                                        Upload CV to evaluate
                                    </span>
                                </div>
                                <div class="pt-1 flex justify-between items-center text-slate-400">
                                    <span>{{ job.location }}</span>
                                    <Link :href="`/opportunities/${job.id}`" class="text-[#00b2e3] font-semibold flex items-center gap-1 hover:underline">
                                        View Job <ExternalLink class="w-3 h-3" />
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Upload Resume Modal -->
        <div v-if="showUploadModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 relative">
                <button
                    @click="showUploadModal = false"
                    :disabled="resumeForm.processing"
                    class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 disabled:opacity-30"
                >
                    <X class="w-5 h-5" />
                </button>

                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-xl bg-[#00b2e3]/10 text-[#00b2e3] flex items-center justify-center font-bold">
                        <Upload class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Upload CV Document</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Automated Fact Deduction Engine</p>
                    </div>
                </div>

                <div v-if="resumeForm.errors.resume" class="bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 p-3 rounded-xl text-xs font-semibold flex items-center gap-2">
                    <AlertCircle class="w-4 h-4 shrink-0 text-rose-500" />
                    <span>{{ resumeForm.errors.resume }}</span>
                </div>

                <div class="space-y-3 text-xs">
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                        Upload your CV/Resume (PDF, DOCX, or TXT max 5MB). Our Knowledge-Based System engine will parse your document to extract your authentic Education Level, Experience, and Verified Skills.
                    </p>

                    <div class="border-2 border-dashed border-slate-200 dark:border-slate-800 p-6 rounded-2xl text-center space-y-3">
                        <FileText class="w-8 h-8 mx-auto text-[#00b2e3]" />
                        <input
                            type="file"
                            @change="handleFileChange"
                            accept=".pdf,.doc,.docx,.txt"
                            :disabled="resumeForm.processing"
                            class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#00b2e3] file:text-white hover:file:bg-[#0099c4]"
                        />

                        <div v-if="resumeForm.resume" class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold pt-1">
                            📄 Selected: {{ resumeForm.resume.name }} ({{ (resumeForm.resume.size / (1024 * 1024)).toFixed(2) }} MB)
                        </div>
                    </div>
                </div>

                <!-- Processing Overlay Banner -->
                <div v-if="resumeForm.processing" class="bg-indigo-500/10 border border-indigo-500/30 p-3.5 rounded-2xl flex items-center justify-center gap-2.5 text-xs text-indigo-700 dark:text-indigo-300 font-semibold animate-pulse">
                    <div class="w-4 h-4 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                    <span>Parsing document text & deducing candidate facts...</span>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button
                        @click="showUploadModal = false"
                        :disabled="resumeForm.processing"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:underline disabled:opacity-30"
                    >
                        Cancel
                    </button>
                    <button
                        @click="submitResume"
                        :disabled="!resumeForm.resume || resumeForm.processing"
                        class="px-5 py-2 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl shadow-sm transition disabled:opacity-40 flex items-center gap-2"
                    >
                        <span v-if="resumeForm.processing">Processing...</span>
                        <span v-else>Upload & Deduce Facts</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Inline PDF Document Viewer Modal -->
        <div v-if="showPdfModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-4xl w-full h-[85vh] p-6 shadow-2xl flex flex-col relative space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center space-x-3">
                        <FileText class="w-5 h-5 text-[#00b2e3]" />
                        <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">
                            {{ profile?.resume_filename || 'Uploaded_Resume.pdf' }}
                        </h3>
                    </div>

                    <div class="flex items-center space-x-3">
                        <a
                            href="/candidate/resume/download"
                            class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-800 dark:text-slate-200 text-xs font-semibold rounded-xl transition inline-flex items-center gap-1.5"
                        >
                            <Download class="w-3.5 h-3.5" /> Download
                        </a>
                        <button @click="showPdfModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1">
                            <X class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                <div class="flex-1 bg-slate-100 dark:bg-slate-950 rounded-2xl overflow-hidden">
                    <iframe
                        src="/candidate/resume/view"
                        class="w-full h-full border-none"
                    ></iframe>
                </div>
            </div>
        </div>
        <!-- Structured Digital CV Builder Modal -->
        <div v-if="showEditProfileModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-2xl w-full max-h-[90vh] p-6 shadow-2xl flex flex-col space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center space-x-2">
                        <Edit3 class="w-5 h-5 text-[#00b2e3]" />
                        <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Structured Digital CV Builder</h3>
                    </div>
                    <button @click="showEditProfileModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitProfileForm" class="space-y-6 text-xs flex-1 overflow-y-auto pr-1">
                    <!-- Inline Validation Error Banner -->
                    <div v-if="formValidationErrors.length > 0 || Object.keys(profileForm.errors).length > 0" class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 rounded-2xl space-y-1.5 text-xs text-rose-700 dark:text-rose-300">
                        <div class="flex items-center gap-1.5 font-bold text-rose-800 dark:text-rose-200">
                            <AlertCircle class="w-4 h-4 text-rose-600 shrink-0" />
                            Please correct the following validation errors before saving:
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 pl-1 text-[11px]">
                            <li v-for="(err, idx) in formValidationErrors" :key="'local-'+idx">{{ err }}</li>
                            <li v-for="(err, field) in profileForm.errors" :key="'server-'+field">{{ err }}</li>
                        </ul>
                    </div>

                    <!-- Basic Facts -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Highest Education Level</label>
                            <select v-model="profileForm.education_level" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-900 dark:text-slate-100">
                                <option value="High School / Secondary">High School / Secondary</option>
                                <option value="Diploma / Associate">Diploma / Associate</option>
                                <option value="Bachelor's Degree">Bachelor's Degree</option>
                                <option value="Master's Degree">Master's Degree</option>
                                <option value="Doctorate (Ph.D. / M.D.)">Doctorate (Ph.D. / M.D.)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Total Experience (Auto-calculated from positions)</label>
                            <div class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-bold font-mono text-[#00b2e3] flex items-center justify-between">
                                <span>{{ calculatedExperience.years }} Yrs {{ calculatedExperience.months }} Months</span>
                                <span class="text-[10px] text-slate-400 font-sans font-normal">({{ calculatedExperience.totalMonths }} Total Months)</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Technical & Professional Skills (Comma Separated Tags)</label>
                        <input v-model="profileForm.skills_raw" type="text" placeholder="e.g. PHP, Laravel, Vue.js, Data Analysis, Project Management" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-slate-100" />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Professional Summary</label>
                        <textarea v-model="profileForm.summary" rows="2" placeholder="Brief summary of core technical qualifications..." class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-slate-100"></textarea>
                    </div>

                    <!-- Work History Timeline Entries -->
                    <div class="space-y-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5 text-xs">
                                <Briefcase class="w-4 h-4 text-[#00b2e3]" /> Work Experience Timeline (Multiple Positions)
                            </h4>
                            <button
                                type="button"
                                @click="addWorkExperience"
                                class="px-3 py-1 bg-[#00b2e3]/10 text-[#00b2e3] hover:bg-[#00b2e3]/20 text-[11px] font-bold rounded-lg transition inline-flex items-center gap-1"
                            >
                                <Plus class="w-3.5 h-3.5" /> Add Position
                            </button>
                        </div>

                        <div v-for="(wItem, index) in profileForm.work_history" :key="index" class="p-3.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl space-y-3 relative">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-mono font-bold text-slate-400">POSITION #{{ index + 1 }}</span>
                                <button
                                    v-if="profileForm.work_history.length > 1"
                                    type="button"
                                    @click="removeWorkExperience(index)"
                                    class="text-rose-500 hover:text-rose-600 text-[11px] font-bold"
                                >
                                    Remove
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Role Title</label>
                                    <input v-model="wItem.role" type="text" placeholder="e.g. Senior Software Engineer" class="w-full px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Employer / Organization</label>
                                    <input v-model="wItem.employer" type="text" placeholder="e.g. Safaricom PLC" class="w-full px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                                </div>
                            </div>

                            <!-- Structured Month & Year Calendar Selector -->
                            <div class="space-y-2 bg-white dark:bg-slate-900 p-3 rounded-xl border border-slate-200 dark:border-slate-800">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <!-- Start Date -->
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Start Date (Month & Year)</label>
                                        <div class="flex gap-2">
                                            <select v-model="wItem.start_month" class="w-1/2 px-2 py-1 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-medium">
                                                <option v-for="m in monthsList" :key="m.value" :value="m.value">{{ m.name }}</option>
                                            </select>
                                            <select v-model="wItem.start_year" class="w-1/2 px-2 py-1 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-medium">
                                                <option v-for="y in yearsList" :key="y" :value="y">{{ y }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- End Date / Present -->
                                    <div>
                                        <div class="flex justify-between items-center mb-1">
                                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400">End Date</label>
                                            <label class="inline-flex items-center gap-1 cursor-pointer text-[11px] text-[#00b2e3] font-bold">
                                                <input v-model="wItem.is_current" type="checkbox" class="rounded border-slate-300 text-[#00b2e3]" /> Present
                                            </label>
                                        </div>

                                        <div v-if="!wItem.is_current" class="flex gap-2">
                                            <select v-model="wItem.end_month" class="w-1/2 px-2 py-1 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-medium">
                                                <option v-for="m in monthsList" :key="m.value" :value="m.value">{{ m.name }}</option>
                                            </select>
                                            <select v-model="wItem.end_year" class="w-1/2 px-2 py-1 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-medium">
                                                <option v-for="y in yearsList" :key="y" :value="y">{{ y }}</option>
                                            </select>
                                        </div>
                                        <div v-else class="px-3 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold rounded-lg text-xs flex items-center justify-center">
                                            Currently Work Here (Present)
                                        </div>
                                    </div>
                                </div>

                                <div v-if="hasInvalidDates(wItem)" class="mt-2 p-2 bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 font-bold rounded-lg text-[11px] flex items-center gap-1.5">
                                    <AlertCircle class="w-3.5 h-3.5" /> Start date cannot be after End date!
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400">Key Deliverables & Responsibilities</label>
                                    <div class="flex items-center space-x-1">
                                        <button
                                            type="button"
                                            @click="insertBullets(index)"
                                            class="px-2 py-0.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded font-semibold text-[10px] flex items-center gap-1 transition"
                                        >
                                            <List class="w-3 h-3 text-[#00b2e3]" /> Bullet Points
                                        </button>
                                        <button
                                            type="button"
                                            @click="insertNumbers(index)"
                                            class="px-2 py-0.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded font-semibold text-[10px] flex items-center gap-1 transition"
                                        >
                                            <ListOrdered class="w-3 h-3 text-indigo-500" /> Numbered List
                                        </button>
                                    </div>
                                </div>
                                <textarea v-model="wItem.description" rows="3" placeholder="Key responsibilities and achievements in this role..." class="w-full px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Education Qualifications Entries -->
                    <div class="space-y-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5 text-xs">
                                <GraduationCap class="w-4 h-4 text-indigo-500" /> Education Qualifications (Multiple Institutions)
                            </h4>
                            <button
                                type="button"
                                @click="addEducationItem"
                                class="px-3 py-1 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-500/20 text-[11px] font-bold rounded-lg transition inline-flex items-center gap-1"
                            >
                                <Plus class="w-3.5 h-3.5" /> Add Qualification
                            </button>
                        </div>

                        <div v-for="(eItem, index) in profileForm.education_history" :key="index" class="p-3.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl space-y-3 relative">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-mono font-bold text-slate-400">QUALIFICATION #{{ index + 1 }}</span>
                                <button
                                    v-if="profileForm.education_history.length > 1"
                                    type="button"
                                    @click="removeEducationItem(index)"
                                    class="text-rose-500 hover:text-rose-600 text-[11px] font-bold"
                                >
                                    Remove
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Degree / Qualification</label>
                                    <input v-model="eItem.degree" type="text" placeholder="e.g. B.Sc. Computer Science" class="w-full px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Institution / University</label>
                                    <input v-model="eItem.institution" type="text" placeholder="e.g. University of Nairobi" class="w-full px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Graduation Year</label>
                                    <select v-model="eItem.year" class="w-full px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs">
                                        <option v-for="y in yearsList" :key="y" :value="y">{{ y }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Professional References Entries -->
                    <div class="space-y-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5 text-xs">
                                <Users class="w-4 h-4 text-emerald-500" /> Professional References (Multiple Referees)
                            </h4>
                            <button
                                type="button"
                                @click="addReferenceItem"
                                class="px-3 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/20 text-[11px] font-bold rounded-lg transition inline-flex items-center gap-1"
                            >
                                <Plus class="w-3.5 h-3.5" /> Add Reference
                            </button>
                        </div>

                        <div v-for="(rItem, index) in profileForm.references_list" :key="index" class="p-3.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl space-y-3 relative">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-mono font-bold text-slate-400">REFEREE #{{ index + 1 }}</span>
                                <button
                                    v-if="profileForm.references_list.length > 1"
                                    type="button"
                                    @click="removeReferenceItem(index)"
                                    class="text-rose-500 hover:text-rose-600 text-[11px] font-bold"
                                >
                                    Remove
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Referee Full Name</label>
                                    <input v-model="rItem.name" type="text" placeholder="e.g. Dr. Jane Doe" class="w-full px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Job Title</label>
                                    <input v-model="rItem.title" type="text" placeholder="e.g. Director of Technology" class="w-full px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Organization</label>
                                    <input v-model="rItem.organization" type="text" placeholder="e.g. Safaricom PLC" class="w-full px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Email Address</label>
                                    <input
                                        v-model="rItem.email"
                                        type="email"
                                        placeholder="jane.doe@example.com"
                                        :class="['w-full px-3 py-1.5 bg-white dark:bg-slate-900 border rounded-xl text-xs transition', !isValidEmail(rItem.email) ? 'border-rose-500 text-rose-600 dark:text-rose-400' : 'border-slate-200 dark:border-slate-800']"
                                    />
                                    <span v-if="!isValidEmail(rItem.email)" class="text-[10px] text-rose-500 font-bold block pt-0.5">Invalid email format</span>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Phone Number</label>
                                    <input
                                        v-model="rItem.phone"
                                        type="text"
                                        placeholder="+254 700 000 000"
                                        :class="['w-full px-3 py-1.5 bg-white dark:bg-slate-900 border rounded-xl text-xs transition', !isValidPhone(rItem.phone) ? 'border-rose-500 text-rose-600 dark:text-rose-400' : 'border-slate-200 dark:border-slate-800']"
                                    />
                                    <span v-if="!isValidPhone(rItem.phone)" class="text-[10px] text-rose-500 font-bold block pt-0.5">Invalid phone format</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="showEditProfileModal = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 font-semibold rounded-xl text-slate-700 dark:text-slate-300">
                            Cancel
                        </button>
                        <button type="submit" :disabled="profileForm.processing" class="px-4 py-2 bg-[#00b2e3] hover:bg-[#0099c4] text-white font-semibold rounded-xl shadow-sm">
                            Save Digital CV Facts
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
