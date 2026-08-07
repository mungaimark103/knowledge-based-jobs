<script setup lang="ts">
import { ref, computed, watch, onErrorCaptured } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, ShieldCheck, Sparkles, MapPin, Award, CheckCircle2, AlertTriangle, XCircle, ArrowRight, UserCheck } from '@lucide/vue';
import ThemeToggle from '@/components/ThemeToggle.vue';

interface Opportunity {
    id: number;
    title: string;
    organization: string;
    org_code: string;
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

const filteredOpportunities = computed(() => {
    return opportunitiesData.value;
});

const getStatusBadge = (status?: string) => {
    if (status === 'recommended') return { label: 'Recommended Match', class: 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/30', icon: CheckCircle2 };
    if (status === 'flagged') return { label: 'Needs Review', class: 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/30', icon: AlertTriangle };
    return { label: 'Excluded', class: 'bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/30', icon: XCircle };
};
</script>

<template>
    <Head title="KBS Impact Opportunities Directory" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans transition-colors duration-300">
        <!-- Public Navigation Header -->
        <header class="sticky top-0 z-40 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-6 py-4 flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 rounded-xl bg-[#00b2e3] text-white flex items-center justify-center shadow-md shadow-[#00b2e3]/20 font-bold">
                    <ShieldCheck class="w-6 h-6" />
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">
                        Impact Talent KBS
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Knowledge-Based Career & Talent Platform</p>
                </div>
            </div>

            <nav class="flex items-center space-x-5">
                <Link href="/opportunities" class="text-xs font-semibold text-[#00b2e3] flex items-center gap-1.5">
                    <Sparkles class="w-4 h-4" /> Directory
                </Link>
                <a href="/employer/portal-switch" class="text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100">
                    Employer Portal
                </a>

                <template v-if="candidate">
                    <Link
                        href="/dashboard"
                        class="px-4 py-2 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl shadow-sm transition"
                    >
                        My Candidate Portal
                    </Link>
                </template>
                <template v-else>
                    <Link
                        href="/login"
                        class="text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100"
                    >
                        Log in
                    </Link>
                    <Link
                        href="/register"
                        class="px-4 py-2 bg-[#00b2e3] hover:bg-[#0099c4] text-white text-xs font-semibold rounded-xl shadow-sm transition"
                    >
                        Register Account
                    </Link>
                </template>
            </nav>
        </header>

        <!-- Captured Error Alert Banner -->
        <div v-if="capturedError" class="max-w-7xl mx-auto px-6 pt-6">
            <div class="p-4 bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 rounded-2xl text-xs flex items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <AlertTriangle class="w-4 h-4 text-rose-500 shrink-0" />
                    <span><strong>Rendering Error Captured:</strong> {{ capturedError }}</span>
                </div>
                <button @click="capturedError = null; window.location.reload()" class="px-3 py-1 bg-rose-600 text-white font-semibold rounded-xl text-xs shadow-sm hover:bg-rose-700 transition">
                    Reload Page
                </button>
            </div>
        </div>

        <!-- Hero Section -->
        <section class="relative px-6 py-10 max-w-7xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#00b2e3]/10 border border-[#00b2e3]/20 text-[#00b2e3] text-xs font-semibold mb-4">
                <UserCheck class="w-3.5 h-3.5" /> Rule-Driven Candidate Qualification Engine
            </div>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Find Your Next Career in <span class="text-[#00b2e3]">Global Impact & Public Sector</span>
            </h2>
            <p class="mt-2 text-slate-600 dark:text-slate-400 text-sm max-w-2xl mx-auto">
                Explore verified public vacancies with transparent qualification benchmarking.
            </p>

            <!-- Search Bar -->
            <div class="mt-6 max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-2.5 rounded-2xl shadow-lg">
                <div class="md:col-span-2 relative">
                    <Search class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search title, skills (e.g. M&E, Climate, Policy)..."
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 text-xs rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#00b2e3]/20 transition"
                    />
                </div>
                <div>
                    <select
                        v-model="selectedGrade"
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs rounded-xl px-3 py-2.5 focus:outline-none transition"
                    >
                        <option value="all">All Grades</option>
                        <option v-for="g in (filterGrades || ['P-1', 'P-2', 'P-3', 'P-4', 'P-5', 'D-1', 'C-Suite', 'GS-7'])" :key="g" :value="g">
                            {{ g }}
                        </option>
                    </select>
                </div>
                <div>
                    <select
                        v-model="selectedOrg"
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs rounded-xl px-3 py-2.5 focus:outline-none transition"
                    >
                        <option value="all">All Organizations</option>
                        <option v-for="o in (filterOrgs || [])" :key="o.code" :value="o.code">
                            {{ o.name }} ({{ o.code }})
                        </option>
                    </select>
                </div>
            </div>
        </section>

        <!-- Directory Grid -->
        <main class="max-w-7xl mx-auto px-6 pb-20 grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar Profile Card -->
            <aside class="space-y-6">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-3">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <Award class="w-4 h-4 text-[#00b2e3]" /> Candidate KBS Profile
                    </h3>

                    <div v-if="candidate" class="space-y-2 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500">Candidate</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ candidate.name }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500">Experience</span>
                            <span class="font-semibold text-[#00b2e3]">{{ candidate.candidate_profile?.years_experience || 0 }} Years</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500">Reliability Score</span>
                            <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ candidate.candidate_profile?.reliability_score || 85 }}%</span>
                        </div>
                    </div>

                    <div v-else class="space-y-3 text-xs text-slate-500">
                        <p class="leading-relaxed">
                            Sign in or create a free Job Seeker profile to unlock automated KBS suitability scoring against active vacancies.
                        </p>
                        <div class="flex flex-col gap-2 pt-1">
                            <Link
                                href="/register"
                                class="w-full py-2 bg-[#00b2e3] text-white font-semibold text-xs rounded-xl text-center shadow-sm hover:bg-[#0099c4] transition"
                            >
                                Register Candidate Account
                            </Link>
                            <Link
                                href="/login"
                                class="w-full py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-xl text-center hover:bg-slate-200 transition"
                            >
                                Existing Candidate Login
                            </Link>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Opportunities List -->
            <section class="lg:col-span-3 space-y-4">
                <div
                    v-for="opp in filteredOpportunities"
                    :key="opp.id"
                    class="bg-white dark:bg-slate-900 hover:bg-slate-50/80 dark:hover:bg-slate-900/90 border border-slate-200 dark:border-slate-800 hover:border-[#00b2e3]/40 rounded-2xl p-6 transition-all duration-200 shadow-sm"
                >
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-md bg-[#00b2e3]/10 text-[#00b2e3] border border-[#00b2e3]/20 text-xs font-bold font-mono">
                                    {{ opp.grade }}
                                </span>
                                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    {{ opp.organization }}
                                </span>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100">
                                {{ opp.title }}
                            </h3>
                            <div class="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400">
                                <span class="flex items-center gap-1"><MapPin class="w-3.5 h-3.5" /> {{ opp.location }}</span>
                                <span>Min Experience: {{ opp.min_experience }} Yrs</span>
                            </div>
                        </div>

                        <!-- KBS Evaluation Badge -->
                        <div v-if="opp.kbs_match" class="flex flex-col items-end gap-1">
                            <div :class="['px-3 py-1 rounded-xl border flex items-center gap-1.5 text-xs font-bold shadow-sm', getStatusBadge(opp.kbs_match.status).class]">
                                <component :is="getStatusBadge(opp.kbs_match.status).icon" class="w-3.5 h-3.5" />
                                <span>KBS Match: {{ opp.kbs_match.score }}%</span>
                            </div>
                        </div>
                    </div>

                    <p class="mt-3 text-xs text-slate-600 dark:text-slate-400 line-clamp-2">
                        {{ opp.description }}
                    </p>

                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div class="flex flex-wrap gap-1.5">
                            <span
                                v-for="skill in opp.required_skills"
                                :key="skill"
                                class="px-2.5 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-md text-[11px]"
                            >
                                {{ skill }}
                            </span>
                        </div>

                        <Link
                            :href="`/opportunities/${opp.id}`"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#00b2e3] hover:bg-[#0099c4] text-white rounded-xl text-xs font-semibold shadow-sm transition"
                        >
                            View Job Specification <ArrowRight class="w-3.5 h-3.5" />
                        </Link>
                    </div>
                </div>

                <div v-if="!filteredOpportunities.length" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-12 text-center space-y-3">
                    <ShieldCheck class="w-10 h-10 text-slate-400 mx-auto" />
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">No Matching Vacancies Found</h3>
                    <p class="text-xs text-slate-500">Try broadening your search query or grade filters.</p>
                </div>

                <!-- Pagination Controls Bar -->
                <div v-if="paginationLinks.length > 3" class="mt-8 flex items-center justify-center gap-1.5 pt-6 border-t border-slate-200 dark:border-slate-800">
                    <template v-for="(link, key) in paginationLinks" :key="key">
                        <div
                            v-if="link.url === null"
                            class="px-3 py-1.5 text-xs font-semibold text-slate-400 dark:text-slate-600 rounded-xl bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 cursor-not-allowed opacity-50"
                            v-html="link.label"
                        />
                        <Link
                            v-else
                            :href="link.url"
                            :class="[
                                'px-3.5 py-1.5 text-xs font-semibold rounded-xl border transition shadow-sm',
                                link.active
                                    ? 'bg-[#00b2e3] border-[#00b2e3] text-white font-bold'
                                    : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
                            ]"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </section>
        </main>
    </div>
</template>
