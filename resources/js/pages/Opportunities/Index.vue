<script setup lang="ts">
import { ref, computed, watch, onErrorCaptured } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, ShieldCheck, Sparkles, MapPin, Award, CheckCircle2, AlertTriangle, XCircle, ArrowRight, UserCheck, Menu, X, Building2, Briefcase, Users } from '@lucide/vue';

const mobileMenuOpen = ref(false);

interface Opportunity {
    id: number;
    title: string;
    organization: string;
    org_code: string;
    org_type?: string;
    logo_path?: string;
    grade: string;
    location: string;
    is_remote: boolean;
    description: string;
    min_experience: number;
    required_skills: string[];
    required_languages: string[];
    kbs_match?: {
        score: number;
        status: 'recommended' | 'flagged' | 'excluded';
        explanations: string[];
    };
}

interface PaginatedResponse<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
}

const props = defineProps<{
    opportunities: Opportunity[] | PaginatedResponse<Opportunity>;
    filterGrades?: string[];
    filterOrgs?: Array<{ code: string; name: string }>;
    filters?: { search?: string; grade?: string; org?: string };
    candidate?: any;
    hasCredentials?: boolean;
}>();

const capturedError = ref<string | null>(null);
onErrorCaptured((err: any) => {
    console.error('Opportunities Directory Render Error Captured:', err);
    capturedError.value = err?.message || 'An unexpected rendering error occurred.';
    return false;
});

const searchQuery = ref(props.filters?.search || '');
const selectedGrade = ref(props.filters?.grade || 'all');
const selectedOrg = ref(props.filters?.org || 'all');

function applyFilters() {
    router.get('/opportunities', {
        search: searchQuery.value || undefined,
        grade: selectedGrade.value !== 'all' ? selectedGrade.value : undefined,
        org: selectedOrg.value !== 'all' ? selectedOrg.value : undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

watch([selectedGrade, selectedOrg], () => {
    applyFilters();
});

let searchTimer: any = null;
watch(searchQuery, () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        applyFilters();
    }, 300);
});

const opportunitiesData = computed<Opportunity[]>(() => {
    if (Array.isArray(props.opportunities)) {
        return props.opportunities;
    }
    return props.opportunities?.data || [];
});

const paginationLinks = computed(() => {
    if (Array.isArray(props.opportunities)) {
        return [];
    }
    return props.opportunities?.links || [];
});

const getStatusBadge = (status?: string) => {
    if (status === 'recommended') return { label: 'Recommended Match', class: 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/30', icon: CheckCircle2 };
    if (status === 'flagged') return { label: 'Needs Review', class: 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/30', icon: AlertTriangle };
    return { label: 'Excluded', class: 'bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/30', icon: XCircle };
};
</script>

<template>
    <Head title="JobSync — Exploring Job Vacancies & Directory" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans transition-colors duration-300">
        <!-- Public Navigation Header -->
        <header class="sticky top-0 z-50 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-4 sm:px-6 py-3.5 shadow-xs">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <Link href="/" class="flex items-center space-x-3 shrink-0">
                    <div class="h-10 w-10 rounded-xl bg-[#00b2e3] text-white flex items-center justify-center shadow-md shadow-[#00b2e3]/20 font-bold shrink-0">
                        JS
                    </div>
                    <div>
                        <h1 class="text-base sm:text-lg font-bold text-slate-900 dark:text-slate-100 leading-tight">
                            JobSync
                        </h1>
                        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 hidden sm:block">Knowledge-Based Job Vacancies Directory</p>
                    </div>
                </Link>

                <nav class="hidden md:flex items-center space-x-6">
                    <Link href="/opportunities" class="text-xs font-semibold text-[#00b2e3] flex items-center gap-1.5">
                        <Sparkles class="w-4 h-4" /> Exploring Job Vacancies
                    </Link>

                    <template v-if="candidate">
                        <Link
                            href="/dashboard"
                            class="px-4 py-2 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl shadow-xs transition"
                        >
                            Client Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            href="/login"
                            class="text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition"
                        >
                            Log in
                        </Link>
                        <Link
                            href="/register"
                            class="px-4 py-2 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl shadow-xs transition"
                        >
                            Register Client
                        </Link>
                    </template>
                </nav>

                <!-- Mobile Hamburger Button -->
                <div class="flex items-center md:hidden">
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        type="button"
                        class="p-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none transition"
                    >
                        <Menu v-if="!mobileMenuOpen" class="w-6 h-6" />
                        <X v-else class="w-6 h-6" />
                    </button>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative px-6 py-10 max-w-7xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#00b2e3]/10 border border-[#00b2e3]/20 text-[#00b2e3] text-xs font-semibold mb-4">
                <UserCheck class="w-3.5 h-3.5" /> Rule-Driven Client Qualification Directory
            </div>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Exploring Job Vacancies & Employment Agencies
            </h2>
            <p class="text-xs md:text-sm text-slate-600 dark:text-slate-400 max-w-2xl mx-auto mt-2 leading-relaxed">
                Discover job vacancies posted by leading corporations like Safaricom PLC and top Employment Agencies including Corporate Staffing Services, Summit Recruitment & Search, and Flexi Personnel.
            </p>

            <!-- Featured Employment Agencies Highlights -->
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-4xl mx-auto">
                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-500">
                        <Building2 class="w-5 h-5" />
                    </div>
                    <div class="text-left">
                        <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100">Corporate Staffing Services</h4>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-purple-500/10 text-purple-500 font-semibold">Employment Agency</span>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-sky-500/10 text-sky-500">
                        <Briefcase class="w-5 h-5" />
                    </div>
                    <div class="text-left">
                        <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100">Summit Recruitment & Search</h4>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-sky-500/10 text-sky-500 font-semibold">Executive Search Agency</span>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-500">
                        <Users class="w-5 h-5" />
                    </div>
                    <div class="text-left">
                        <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100">Flexi Personnel</h4>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-500 font-semibold">HR & Staffing Agency</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Directory Filter Bar -->
        <main class="max-w-7xl mx-auto px-6 pb-16 space-y-6">
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 flex flex-col md:flex-row items-center gap-4 shadow-xs">
                <div class="relative w-full md:flex-1">
                    <Search class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search by job title, agency, Safaricom PLC, skill tag..."
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs focus:outline-none focus:border-[#00b2e3]"
                    />
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto">
                    <select
                        v-model="selectedGrade"
                        class="w-full md:w-auto px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs focus:outline-none focus:border-[#00b2e3]"
                    >
                        <option value="all">All Job Grades</option>
                        <option v-for="g in filterGrades" :key="g" :value="g">{{ g }}</option>
                    </select>

                    <select
                        v-model="selectedOrg"
                        class="w-full md:w-auto px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs focus:outline-none focus:border-[#00b2e3]"
                    >
                        <option value="all">All Organizations & Employment Agencies</option>
                        <option v-for="o in filterOrgs" :key="o.code" :value="o.code">{{ o.name }} ({{ o.code }})</option>
                    </select>
                </div>
            </div>

            <!-- Job Vacancies Cards List -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div
                    v-for="job in opportunitiesData"
                    :key="job.id"
                    class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs hover:border-[#00b2e3]/40 transition space-y-4 flex flex-col justify-between"
                >
                    <div class="space-y-3">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-[#00b2e3]/10 text-[#00b2e3] border border-[#00b2e3]/20 uppercase">
                                    {{ job.grade }}
                                </span>
                                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 mt-2 leading-snug">
                                    {{ job.title }}
                                </h3>
                                <div class="text-xs font-semibold text-slate-600 dark:text-slate-300 mt-0.5 flex items-center gap-1.5">
                                    <Building2 class="w-3.5 h-3.5 text-slate-400" />
                                    {{ job.organization }}
                                </div>
                            </div>

                            <div v-if="job.kbs_match" :class="['px-3 py-1.5 rounded-2xl border text-center shrink-0', getStatusBadge(job.kbs_match.status).class]">
                                <div class="text-xs font-black">{{ job.kbs_match.score }}%</div>
                                <div class="text-[9px] font-bold uppercase">{{ job.kbs_match.status }}</div>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-3 leading-relaxed">
                            {{ job.description }}
                        </p>

                        <div class="flex flex-wrap gap-1.5 pt-2">
                            <span v-for="skill in job.required_skills" :key="skill" class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-semibold rounded-md">
                                {{ skill }}
                            </span>
                            <span v-for="lang in job.required_languages" :key="lang" class="px-2 py-0.5 bg-amber-500/10 text-amber-600 text-[10px] font-semibold rounded-md border border-amber-500/20">
                                Language: {{ lang }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div class="text-[11px] text-slate-500 flex items-center gap-1">
                            <MapPin class="w-3.5 h-3.5" /> {{ job.location }}
                        </div>
                        <Link
                            :href="`/opportunities/${job.id}`"
                            class="px-4 py-2 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl shadow-xs transition inline-flex items-center gap-1.5"
                        >
                            View & Apply <ArrowRight class="w-3.5 h-3.5" />
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <div v-if="paginationLinks.length > 3" class="flex justify-center gap-1 pt-6">
                <Link
                    v-for="(link, i) in paginationLinks"
                    :key="i"
                    :href="link.url || '#'"
                    v-html="link.label"
                    :class="[
                        'px-3 py-1.5 rounded-xl text-xs font-semibold transition',
                        link.active ? 'bg-[#00b2e3] text-white' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50'
                    ]"
                />
            </div>
        </main>
    </div>
</template>
