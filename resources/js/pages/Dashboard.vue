<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ShieldCheck, FileCheck, CheckCircle2, Award, Sparkles, ArrowRight, UserCheck, Briefcase, ExternalLink, Upload, X, FileText, AlertCircle, Lock, Eye, Download, GraduationCap, Users, Edit3, Plus, List, ListOrdered, Menu, LogOut, Clock, FolderGit2, Contact, Trash2, BookOpen } from '@lucide/vue';
import NotificationDrawer from '@/components/NotificationDrawer.vue';

const mobileMenuOpen = ref(false);

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

const showEditProfileModal = ref(false);

const docForm = useForm({
    doc_type: 'resume',
    document_file: null as File | null,
    resume: null as File | null,
});

function submitDocumentUpload(docType: string, fileInputEvent: Event) {
    const target = fileInputEvent.target as HTMLInputElement;
    if (!target.files || !target.files[0]) return;

    const file = target.files[0];
    if (docType === 'resume') {
        docForm.resume = file;
        docForm.post('/candidate/resume', {
            preserveScroll: true,
            onSuccess: () => docForm.reset(),
        });
    } else {
        docForm.doc_type = docType;
        docForm.document_file = file;
        docForm.post('/candidate/document', {
            preserveScroll: true,
            onSuccess: () => docForm.reset(),
        });
    }
}

const currentYearNum = new Date().getFullYear();

const profileForm = useForm({
    education_level: props.profile?.education_level || "Master's Degree",
    years_experience: props.profile?.years_experience ?? 0,
    summary: props.profile?.summary || '',
    skills_raw: Array.isArray(props.profile?.skills) ? props.profile.skills.join(', ') : '',
    work_history: Array.isArray(props.profile?.work_history) && props.profile.work_history.length
        ? props.profile.work_history.map((w: any) => ({ ...w }))
        : [],
    education_history: Array.isArray(props.profile?.education_history) && props.profile.education_history.length
        ? props.profile.education_history.map((e: any) => ({ ...e }))
        : [{ degree: '', institution: '', year: String(currentYearNum - 4) }],
    references_list: Array.isArray(props.profile?.references_list) && props.profile.references_list.length
        ? props.profile.references_list.map((r: any) => ({ ...r }))
        : [{ name: '', title: '', organization: '', email: '', phone: '' }],
});

function addWorkItem() {
    profileForm.work_history.push({
        role: '',
        employer: '',
        start_month: '01',
        start_year: String(currentYearNum - 1),
        is_current: false,
        end_month: '12',
        end_year: String(currentYearNum),
        description: '',
    });
}

function removeWorkItem(index: number) {
    profileForm.work_history.splice(index, 1);
}

function addEducationItem() {
    profileForm.education_history.push({
        degree: '',
        institution: '',
        year: String(currentYearNum - 2),
    });
}

function removeEducationItem(index: number) {
    profileForm.education_history.splice(index, 1);
}

function addReferenceItem() {
    profileForm.references_list.push({
        name: '',
        title: '',
        organization: '',
        email: '',
        phone: '',
    });
}

function removeReferenceItem(index: number) {
    profileForm.references_list.splice(index, 1);
}

function submitProfileForm() {
    const rawSkills = profileForm.skills_raw
        .split(',')
        .map((s: string) => s.trim())
        .filter((s: string) => s.length > 0);

    profileForm.transform((data) => ({
        ...data,
        skills: rawSkills,
    })).post('/candidate/profile', {
        preserveScroll: true,
        onSuccess: () => {
            showEditProfileModal.value = false;
        },
    });
}

function getPipelineStage(status: string) {
    const s = (status || '').toLowerCase();
    if (s.includes('hired') || s.includes('offered')) return 4;
    if (s.includes('interview')) return 3;
    if (s.includes('shortlist')) return 2;
    if (s.includes('review')) return 1;
    return 0; // Submitted
}
</script>

<template>
    <Head title="JobSync — Client Dashboard & Application Progress Tracking" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans pb-16">
        <!-- Main Sticky Navbar -->
        <header class="sticky top-0 z-50 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-4 sm:px-6 py-3.5 shadow-xs">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <Link href="/" class="flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-xl bg-[#00b2e3] text-white flex items-center justify-center font-bold text-lg shadow-md shadow-[#00b2e3]/20">
                        JS
                    </div>
                    <div>
                        <h1 class="text-base sm:text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2 leading-tight">
                            JobSync <span class="text-[10px] px-2 py-0.5 rounded-full bg-[#00b2e3]/10 text-[#00b2e3] border border-[#00b2e3]/20 font-bold uppercase">Client Portal</span>
                        </h1>
                        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 hidden sm:block">Deterministic Talent Matching & Client Application Tracking</p>
                    </div>
                </Link>

                <div class="hidden md:flex items-center space-x-4">
                    <!-- Header Notification Drawer -->
                    <NotificationDrawer />

                    <Link href="/opportunities" class="text-xs font-semibold text-[#00b2e3] hover:underline flex items-center gap-1.5">
                        <Briefcase class="w-3.5 h-3.5" /> Explore Job Vacancies
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

                <!-- Mobile Menu Button -->
                <div class="flex items-center gap-2 md:hidden">
                    <NotificationDrawer />
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <Menu v-if="!mobileMenuOpen" class="w-6 h-6" />
                        <X v-else class="w-6 h-6" />
                    </button>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-8">
            <!-- Flash Notification -->
            <div v-if="flash?.success" class="p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl text-emerald-600 dark:text-emerald-400 text-xs font-semibold flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <CheckCircle2 class="w-4 h-4" /> {{ flash.success }}
                </div>
            </div>

            <!-- Welcome Header Card -->
            <div class="bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-800 relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                    <div class="space-y-2 max-w-2xl">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#00b2e3]/20 border border-[#00b2e3]/30 text-[#00b2e3] text-xs font-semibold">
                            <UserCheck class="w-3.5 h-3.5" /> Logged in as Verified Client
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Welcome back, {{ user.name }}</h2>
                        <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                            Manage your client credentials, upload required documentation (Recommendation Letter, References, Portfolio, Transcripts), and track application progress in real time.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <button
                            @click="showEditProfileModal = true"
                            class="px-4 py-2.5 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl shadow-md transition inline-flex items-center gap-2"
                        >
                            <Edit3 class="w-4 h-4" /> Edit Client Profile
                        </button>
                    </div>
                </div>
            </div>

            <!-- Client Credentials Summary Card (Displays saved information) -->
            <section class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-[#00b2e3]/10 text-[#00b2e3] rounded-xl font-bold">
                            <BookOpen class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Saved Client Credentials Summary</h3>
                            <p class="text-xs text-slate-500">Overview of your active profile facts used for automated JobSync matching score calculation.</p>
                        </div>
                    </div>
                    <button
                        @click="showEditProfileModal = true"
                        class="text-xs font-semibold text-[#00b2e3] hover:underline flex items-center gap-1"
                    >
                        <Edit3 class="w-3.5 h-3.5" /> Update Facts
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Column 1: Core Credentials & Skills -->
                    <div class="space-y-4 lg:border-r border-slate-100 dark:border-slate-800 lg:pr-6">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <GraduationCap class="w-4 h-4 text-[#00b2e3]" />
                                <span>Education Level:</span>
                                <strong class="text-slate-800 dark:text-slate-200 font-bold">{{ profile?.education_level || 'Not set' }}</strong>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <Briefcase class="w-4 h-4 text-[#00b2e3]" />
                                <span>Years of Experience:</span>
                                <strong class="text-slate-800 dark:text-slate-200 font-bold">{{ profile?.years_experience ?? 0 }} Years</strong>
                            </div>
                        </div>

                        <div v-if="profile?.summary" class="space-y-1">
                            <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300">Professional Overview</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed bg-slate-50 dark:bg-slate-950 p-3 rounded-xl border border-slate-200/60 dark:border-slate-800/60">
                                {{ profile.summary }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300">Technical & Professional Skills</h4>
                            <div v-if="profile?.skills && profile.skills.length > 0" class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="(skill, idx) in profile.skills"
                                    :key="idx"
                                    class="px-2.5 py-1 bg-[#00b2e3]/10 text-[#00b2e3] border border-[#00b2e3]/20 rounded-lg text-xs font-semibold"
                                >
                                    {{ skill }}
                                </span>
                            </div>
                            <p v-else class="text-xs text-slate-400 italic">No skills listed yet.</p>
                        </div>
                    </div>

                    <!-- Column 2: Work History Timeline -->
                    <div class="space-y-3 lg:border-r border-slate-100 dark:border-slate-800 lg:pr-6">
                        <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                            <Briefcase class="w-4 h-4 text-[#00b2e3]" /> Work History Positions
                        </h4>
                        <div v-if="profile?.work_history && profile.work_history.length > 0" class="space-y-3">
                            <div
                                v-for="(w, idx) in profile.work_history"
                                :key="idx"
                                class="p-3 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-200/60 dark:border-slate-800/60 space-y-1 text-xs"
                            >
                                <div class="flex items-center justify-between">
                                    <strong class="text-slate-900 dark:text-slate-100 font-bold">{{ w.role || 'Position' }}</strong>
                                    <span v-if="w.is_current" class="text-[10px] px-2 py-0.5 bg-emerald-500/10 text-emerald-600 rounded-md font-bold">Current</span>
                                </div>
                                <div class="text-slate-500">{{ w.employer }} &bull; {{ w.start_year }} - {{ w.is_current ? 'Present' : (w.end_year || 'N/A') }}</div>
                            </div>
                        </div>
                        <p v-else class="text-xs text-slate-400 italic">No work history positions recorded.</p>
                    </div>

                    <!-- Column 3: Referees & Qualifications -->
                    <div class="space-y-4">
                        <div class="space-y-3">
                            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                <GraduationCap class="w-4 h-4 text-[#00b2e3]" /> Degrees & Academic History
                            </h4>
                            <div v-if="profile?.education_history && profile.education_history.length > 0" class="space-y-2">
                                <div v-for="(e, idx) in profile.education_history" :key="idx" class="p-3 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-200/60 dark:border-slate-800/60 text-xs">
                                    <strong class="text-slate-900 dark:text-slate-100 font-bold block">{{ e.degree }}</strong>
                                    <span class="text-slate-500 text-[11px]">{{ e.institution }} &bull; {{ e.year }}</span>
                                </div>
                            </div>
                            <p v-else class="text-xs text-slate-400 italic">No academic degrees listed.</p>
                        </div>

                        <div class="space-y-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                <Contact class="w-4 h-4 text-[#00b2e3]" /> Professional Referees
                            </h4>
                            <div v-if="profile?.references_list && profile.references_list.length > 0" class="space-y-2">
                                <div v-for="(r, idx) in profile.references_list" :key="idx" class="p-3 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-200/60 dark:border-slate-800/60 text-xs space-y-0.5">
                                    <strong class="text-slate-900 dark:text-slate-100 font-bold block">{{ r.name }}</strong>
                                    <span class="text-slate-500 text-[11px] block">{{ r.title }} @ {{ r.organization }}</span>
                                    <span class="text-[10px] text-[#00b2e3] block">{{ r.email }} &bull; {{ r.phone }}</span>
                                </div>
                            </div>
                            <p v-else class="text-xs text-slate-400 italic">No referees listed.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Client Multi-Document Upload Subsystem Section -->
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                            <FolderGit2 class="w-5 h-5 text-[#00b2e3]" /> Client Documents & Credentials Verification
                        </h3>
                        <p class="text-xs text-slate-500">Upload official client documents required for job matching and recruiter verification.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- 1. CV / Resume Document -->
                    <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <div class="h-10 w-10 rounded-xl bg-sky-500/10 text-sky-500 flex items-center justify-center font-bold">
                                <FileText class="w-5 h-5" />
                            </div>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100">CV / Resume PDF</h4>
                            <p class="text-[11px] text-slate-500 leading-normal">Curriculum Vitae document for recruiter reference.</p>
                        </div>
                        <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <div v-if="profile?.resume_path" class="flex items-center justify-between text-[11px] text-slate-600 dark:text-slate-300">
                                <span class="truncate font-semibold max-w-[120px]">{{ profile.resume_filename || 'resume.pdf' }}</span>
                                <a :href="'/candidate/document/view/resume'" target="_blank" class="text-[#00b2e3] font-semibold hover:underline">View</a>
                            </div>
                            <label class="w-full py-2 px-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200 text-[11px] font-semibold rounded-xl transition text-center cursor-pointer block">
                                <span>{{ profile?.resume_path ? 'Replace CV' : 'Upload CV' }}</span>
                                <input type="file" class="hidden" accept=".pdf,.doc,.docx" @change="submitDocumentUpload('resume', $event)" />
                            </label>
                        </div>
                    </div>

                    <!-- 2. Recommendation Letter -->
                    <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <div class="h-10 w-10 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center font-bold">
                                <Award class="w-5 h-5" />
                            </div>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100">Recommendation Letter</h4>
                            <p class="text-[11px] text-slate-500 leading-normal">Official letter of recommendation from prior employer or mentor.</p>
                        </div>
                        <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <div v-if="profile?.recommendation_letter_path" class="flex items-center justify-between text-[11px] text-slate-600 dark:text-slate-300">
                                <span class="truncate font-semibold max-w-[120px]">{{ profile.recommendation_letter_filename || 'letter.pdf' }}</span>
                                <a :href="'/candidate/document/view/recommendation_letter'" target="_blank" class="text-[#00b2e3] font-semibold hover:underline">View</a>
                            </div>
                            <label class="w-full py-2 px-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200 text-[11px] font-semibold rounded-xl transition text-center cursor-pointer block">
                                <span>{{ profile?.recommendation_letter_path ? 'Replace Letter' : 'Upload Letter' }}</span>
                                <input type="file" class="hidden" accept=".pdf,.doc,.docx" @change="submitDocumentUpload('recommendation_letter', $event)" />
                            </label>
                        </div>
                    </div>

                    <!-- 3. References Document -->
                    <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <div class="h-10 w-10 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center font-bold">
                                <Contact class="w-5 h-5" />
                            </div>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100">References List</h4>
                            <p class="text-[11px] text-slate-500 leading-normal">Document listing professional referees who vouch for client.</p>
                        </div>
                        <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <div v-if="profile?.references_doc_path" class="flex items-center justify-between text-[11px] text-slate-600 dark:text-slate-300">
                                <span class="truncate font-semibold max-w-[120px]">{{ profile.references_doc_filename || 'referees.pdf' }}</span>
                                <a :href="'/candidate/document/view/references_doc'" target="_blank" class="text-[#00b2e3] font-semibold hover:underline">View</a>
                            </div>
                            <label class="w-full py-2 px-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200 text-[11px] font-semibold rounded-xl transition text-center cursor-pointer block">
                                <span>{{ profile?.references_doc_path ? 'Replace List' : 'Upload List' }}</span>
                                <input type="file" class="hidden" accept=".pdf,.doc,.docx" @change="submitDocumentUpload('references_doc', $event)" />
                            </label>
                        </div>
                    </div>

                    <!-- 4. Work Portfolio -->
                    <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <div class="h-10 w-10 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center font-bold">
                                <Briefcase class="w-5 h-5" />
                            </div>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100">Work Portfolio</h4>
                            <p class="text-[11px] text-slate-500 leading-normal">Portfolio displaying past creative, technical, or case work.</p>
                        </div>
                        <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <div v-if="profile?.portfolio_doc_path" class="flex items-center justify-between text-[11px] text-slate-600 dark:text-slate-300">
                                <span class="truncate font-semibold max-w-[120px]">{{ profile.portfolio_doc_filename || 'portfolio.pdf' }}</span>
                                <a :href="'/candidate/document/view/portfolio_doc'" target="_blank" class="text-[#00b2e3] font-semibold hover:underline">View</a>
                            </div>
                            <label class="w-full py-2 px-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200 text-[11px] font-semibold rounded-xl transition text-center cursor-pointer block">
                                <span>{{ profile?.portfolio_doc_path ? 'Replace Portfolio' : 'Upload Portfolio' }}</span>
                                <input type="file" class="hidden" accept=".pdf,.doc,.docx,.zip" @change="submitDocumentUpload('portfolio_doc', $event)" />
                            </label>
                        </div>
                    </div>

                    <!-- 5. Academic Transcripts -->
                    <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <div class="h-10 w-10 rounded-xl bg-rose-500/10 text-rose-500 flex items-center justify-center font-bold">
                                <GraduationCap class="w-5 h-5" />
                            </div>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100">Academic Transcripts</h4>
                            <p class="text-[11px] text-slate-500 leading-normal">Official transcripts proving degree qualification and grades.</p>
                        </div>
                        <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <div v-if="profile?.transcripts_doc_path" class="flex items-center justify-between text-[11px] text-slate-600 dark:text-slate-300">
                                <span class="truncate font-semibold max-w-[120px]">{{ profile.transcripts_doc_filename || 'transcripts.pdf' }}</span>
                                <a :href="'/candidate/document/view/transcripts_doc'" target="_blank" class="text-[#00b2e3] font-semibold hover:underline">View</a>
                            </div>
                            <label class="w-full py-2 px-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-[#00b2e3] dark:text-slate-200 text-[11px] font-semibold rounded-xl transition text-center cursor-pointer block">
                                <span>{{ profile?.transcripts_doc_path ? 'Replace Transcripts' : 'Upload Transcripts' }}</span>
                                <input type="file" class="hidden" accept=".pdf,.doc,.docx" @change="submitDocumentUpload('transcripts_doc', $event)" />
                            </label>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Application Progress Tracking Section -->
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                            <Clock class="w-5 h-5 text-[#00b2e3]" /> Client Application Progress Tracking
                        </h3>
                        <p class="text-xs text-slate-500">Track real-time progress, matching scores, and hiring stages for your submitted job applications.</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs space-y-6">
                    <div v-if="applications && applications.length > 0" class="space-y-6 divide-y divide-slate-100 dark:divide-slate-800">
                        <div v-for="app in applications" :key="app.id" class="pt-6 first:pt-0 space-y-4">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div>
                                    <h4 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                        {{ app.job_title }}
                                        <span class="text-xs px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold rounded-md">
                                            {{ app.organization }}
                                        </span>
                                    </h4>
                                    <p class="text-xs text-slate-500">Applied on {{ app.applied_at }} &bull; Location: {{ app.location }}</p>
                                </div>

                                <div class="flex items-center gap-3">
                                    <div class="text-right">
                                        <div class="text-xs font-bold text-[#00b2e3]">JobSync Match Score: {{ app.kbs_score }}%</div>
                                        <div class="text-[10px] text-slate-400 uppercase font-semibold">Status: {{ app.status }}</div>
                                    </div>
                                    <Link :href="`/opportunities/${app.job_id}`" class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-[#00b2e3] hover:underline text-xs font-semibold">
                                        View Job &rarr;
                                    </Link>
                                </div>
                            </div>

                            <!-- 5-Stage Visual Progress Pipeline -->
                            <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-2xl border border-slate-200/60 dark:border-slate-800/60">
                                <div class="grid grid-cols-5 gap-2 text-center text-xs font-semibold">
                                    <div :class="['py-2 px-1 rounded-xl transition', getPipelineStage(app.status) >= 0 ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-200 dark:bg-slate-800 text-slate-400']">
                                        1. Submitted
                                    </div>
                                    <div :class="['py-2 px-1 rounded-xl transition', getPipelineStage(app.status) >= 1 ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-200 dark:bg-slate-800 text-slate-400']">
                                        2. In Review
                                    </div>
                                    <div :class="['py-2 px-1 rounded-xl transition', getPipelineStage(app.status) >= 2 ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-200 dark:bg-slate-800 text-slate-400']">
                                        3. Shortlisted
                                    </div>
                                    <div :class="['py-2 px-1 rounded-xl transition', getPipelineStage(app.status) >= 3 ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-200 dark:bg-slate-800 text-slate-400']">
                                        4. Interview
                                    </div>
                                    <div :class="['py-2 px-1 rounded-xl transition', getPipelineStage(app.status) >= 4 ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-200 dark:bg-slate-800 text-slate-400']">
                                        5. Hired / Offered
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-8 text-slate-400 text-xs">
                        No job applications submitted yet. Browse open vacancies to apply!
                    </div>
                </div>
            </section>
        </main>

        <!-- Edit Client Profile Modal -->
        <teleport to="body">
            <div v-if="showEditProfileModal" class="fixed inset-0 z-[100] bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-3xl w-full p-6 sm:p-8 shadow-2xl space-y-6 my-8 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-[#00b2e3]/10 text-[#00b2e3] rounded-xl font-bold">
                                <Edit3 class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Edit Client Profile Credentials</h3>
                                <p class="text-xs text-slate-500">Update your structured facts for deterministic JobSync job matching.</p>
                            </div>
                        </div>
                        <button @click="showEditProfileModal = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl">
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <form @submit.prevent="submitProfileForm" class="space-y-6 text-xs">
                        <!-- Education Level & Experience -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="font-bold text-slate-700 dark:text-slate-300">Highest Education Level</label>
                                <select v-model="profileForm.education_level" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl font-semibold">
                                    <option value="Doctorate (Ph.D. / M.D.)">Doctorate (Ph.D. / M.D.)</option>
                                    <option value="Master's Degree">Master's Degree</option>
                                    <option value="Bachelor's Degree">Bachelor's Degree</option>
                                    <option value="Diploma / Associate">Diploma / Associate</option>
                                    <option value="High School / Secondary">High School / Secondary</option>
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label class="font-bold text-slate-700 dark:text-slate-300">Total Years of Experience</label>
                                <input v-model.number="profileForm.years_experience" type="number" min="0" max="50" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl font-semibold" />
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-700 dark:text-slate-300">Professional Summary</label>
                            <textarea v-model="profileForm.summary" rows="3" placeholder="Brief summary of professional background and expertise..." class="w-full p-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl leading-relaxed"></textarea>
                        </div>

                        <!-- Skills -->
                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-700 dark:text-slate-300">Technical & Professional Skills (comma-separated)</label>
                            <input v-model="profileForm.skills_raw" type="text" placeholder="e.g. PHP, Laravel, Customer Support, M-PESA, SQL" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl font-semibold" />
                        </div>

                        <!-- Dynamic Work History Cards -->
                        <div class="space-y-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                    <Briefcase class="w-4 h-4 text-[#00b2e3]" /> Work History Positions
                                </h4>
                                <button type="button" @click="addWorkItem" class="px-3 py-1.5 bg-[#00b2e3]/10 text-[#00b2e3] font-semibold rounded-lg hover:underline flex items-center gap-1">
                                    <Plus class="w-3.5 h-3.5" /> Add Position
                                </button>
                            </div>

                            <div v-for="(w, idx) in profileForm.work_history" :key="idx" class="p-4 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl space-y-3 relative">
                                <button type="button" @click="removeWorkItem(idx)" class="absolute top-3 right-3 text-rose-500 hover:text-rose-700 p-1">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pr-8">
                                    <input v-model="w.role" type="text" placeholder="Job Title / Role" class="px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                                    <input v-model="w.employer" type="text" placeholder="Company / Organization" class="px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <input v-model="w.start_year" type="text" placeholder="Start Year (e.g. 2021)" class="px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                                    <input v-model="w.end_year" type="text" placeholder="End Year (or Present)" class="px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                                    <label class="flex items-center gap-2 text-[11px] font-semibold text-slate-600 dark:text-slate-300 col-span-2">
                                        <input type="checkbox" v-model="w.is_current" /> Current Position
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Education History Cards -->
                        <div class="space-y-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                    <GraduationCap class="w-4 h-4 text-[#00b2e3]" /> Academic Degrees & Certifications
                                </h4>
                                <button type="button" @click="addEducationItem" class="px-3 py-1.5 bg-[#00b2e3]/10 text-[#00b2e3] font-semibold rounded-lg hover:underline flex items-center gap-1">
                                    <Plus class="w-3.5 h-3.5" /> Add Degree
                                </button>
                            </div>

                            <div v-for="(e, idx) in profileForm.education_history" :key="idx" class="p-4 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl space-y-3 relative">
                                <button type="button" @click="removeEducationItem(idx)" class="absolute top-3 right-3 text-rose-500 hover:text-rose-700 p-1">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pr-8">
                                    <input v-model="e.degree" type="text" placeholder="Degree / Certificate Name" class="px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                                    <input v-model="e.institution" type="text" placeholder="University / Institution" class="px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                                    <input v-model="e.year" type="text" placeholder="Graduation Year" class="px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Referees Cards -->
                        <div class="space-y-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                    <Contact class="w-4 h-4 text-[#00b2e3]" /> Professional Referees
                                </h4>
                                <button type="button" @click="addReferenceItem" class="px-3 py-1.5 bg-[#00b2e3]/10 text-[#00b2e3] font-semibold rounded-lg hover:underline flex items-center gap-1">
                                    <Plus class="w-3.5 h-3.5" /> Add Referee
                                </button>
                            </div>

                            <div v-for="(r, idx) in profileForm.references_list" :key="idx" class="p-4 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl space-y-3 relative">
                                <button type="button" @click="removeReferenceItem(idx)" class="absolute top-3 right-3 text-rose-500 hover:text-rose-700 p-1">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pr-8">
                                    <input v-model="r.name" type="text" placeholder="Referee Name" class="px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                                    <input v-model="r.title" type="text" placeholder="Title / Designation" class="px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                                    <input v-model="r.organization" type="text" placeholder="Organization" class="px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <input v-model="r.email" type="email" placeholder="Email Address" class="px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                                    <input v-model="r.phone" type="text" placeholder="Phone Number" class="px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs" />
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                            <button type="button" @click="showEditProfileModal = false" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold rounded-xl hover:bg-slate-200">
                                Cancel
                            </button>
                            <button type="submit" :disabled="profileForm.processing" class="px-6 py-2.5 bg-[#00b2e3] hover:bg-[#0099c4] text-white font-bold rounded-xl shadow-md transition">
                                {{ profileForm.processing ? 'Saving Changes...' : 'Save Profile Changes' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </teleport>
    </div>
</template>
