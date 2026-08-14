<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Search,
    Sparkles,
    MapPin,
    CheckCircle2,
    AlertTriangle,
    XCircle,
    ArrowRight,
    UserCheck,
    Menu,
    X,
    Building2,
    Briefcase,
    Users,
} from '@lucide/vue';
import { ref, computed, watch, onErrorCaptured } from 'vue';

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
    capturedError.value =
        err?.message || 'An unexpected rendering error occurred.';

    return false;
});

const searchQuery = ref(props.filters?.search || '');
const selectedGrade = ref(props.filters?.grade || 'all');
const selectedOrg = ref(props.filters?.org || 'all');

function applyFilters() {
    router.get(
        '/opportunities',
        {
            search: searchQuery.value || undefined,
            grade:
                selectedGrade.value !== 'all' ? selectedGrade.value : undefined,
            org: selectedOrg.value !== 'all' ? selectedOrg.value : undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
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
    if (status === 'recommended') {
        return {
            label: 'Recommended Match',
            class: 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/30',
            icon: CheckCircle2,
        };
    }

    if (status === 'flagged') {
        return {
            label: 'Needs Review',
            class: 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/30',
            icon: AlertTriangle,
        };
    }

    return {
        label: 'Excluded',
        class: 'bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/30',
        icon: XCircle,
    };
};
</script>

<template>
    <Head title="JobSync — Exploring Job Vacancies & Directory" />

    <div
        class="min-h-screen bg-slate-50 font-sans text-slate-900 transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100"
    >
        <!-- Public Navigation Header -->
        <header
            class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 px-4 py-3.5 shadow-xs backdrop-blur-md sm:px-6 dark:border-slate-800 dark:bg-slate-900/90"
        >
            <div class="mx-auto flex max-w-7xl items-center justify-between">
                <Link href="/" class="flex shrink-0 items-center space-x-3">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#00b2e3] font-bold text-white shadow-md shadow-[#00b2e3]/20"
                    >
                        JS
                    </div>
                    <div>
                        <h1
                            class="text-base leading-tight font-bold text-slate-900 sm:text-lg dark:text-slate-100"
                        >
                            JobSync
                        </h1>
                        <p
                            class="hidden text-[11px] text-slate-500 sm:block sm:text-xs dark:text-slate-400"
                        >
                            Knowledge-Based Job Vacancies Directory
                        </p>
                    </div>
                </Link>

                <nav class="hidden items-center space-x-6 md:flex">
                    <Link
                        href="/opportunities"
                        class="flex items-center gap-1.5 text-xs font-semibold text-[#00b2e3]"
                    >
                        <Sparkles class="h-4 w-4" /> Exploring Job Vacancies
                    </Link>

                    <template v-if="candidate">
                        <Link
                            href="/dashboard"
                            class="rounded-xl bg-[#00b2e3] px-4 py-2 text-xs font-semibold text-white shadow-xs transition hover:bg-[#0099c4]"
                        >
                            Client Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            href="/login"
                            class="text-xs font-semibold text-slate-600 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100"
                        >
                            Log in
                        </Link>
                        <Link
                            href="/register"
                            class="rounded-xl bg-[#00b2e3] px-4 py-2 text-xs font-semibold text-white shadow-xs transition hover:bg-[#0099c4]"
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
                        class="rounded-xl p-2 text-slate-700 transition hover:bg-slate-100 focus:outline-none dark:text-slate-300 dark:hover:bg-slate-800"
                    >
                        <Menu v-if="!mobileMenuOpen" class="h-6 w-6" />
                        <X v-else class="h-6 w-6" />
                    </button>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative mx-auto max-w-7xl px-6 py-10 text-center">
            <div
                class="mb-4 inline-flex items-center gap-2 rounded-full border border-[#00b2e3]/20 bg-[#00b2e3]/10 px-3.5 py-1.5 text-xs font-semibold text-[#00b2e3]"
            >
                <UserCheck class="h-3.5 w-3.5" /> Rule-Driven Client
                Qualification Directory
            </div>
            <h2
                class="text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl dark:text-white"
            >
                Exploring Job Vacancies & Employment Agencies
            </h2>
            <p
                class="mx-auto mt-2 max-w-2xl text-xs leading-relaxed text-slate-600 md:text-sm dark:text-slate-400"
            >
                Discover job vacancies posted by leading corporations like
                Safaricom PLC and top Employment Agencies including Corporate
                Staffing Services, Summit Recruitment & Search, and Flexi
                Personnel.
            </p>

            <!-- Featured Employment Agencies Highlights -->
            <div
                class="mx-auto mt-8 grid max-w-4xl grid-cols-1 gap-4 sm:grid-cols-3"
            >
                <div
                    class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900"
                >
                    <div
                        class="rounded-xl bg-purple-500/10 p-2.5 text-purple-500"
                    >
                        <Building2 class="h-5 w-5" />
                    </div>
                    <div class="text-left">
                        <h4
                            class="text-xs font-bold text-slate-900 dark:text-slate-100"
                        >
                            Corporate Staffing Services
                        </h4>
                        <span
                            class="rounded-full bg-purple-500/10 px-2 py-0.5 text-[10px] font-semibold text-purple-500"
                            >Employment Agency</span
                        >
                    </div>
                </div>
                <div
                    class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="rounded-xl bg-sky-500/10 p-2.5 text-sky-500">
                        <Briefcase class="h-5 w-5" />
                    </div>
                    <div class="text-left">
                        <h4
                            class="text-xs font-bold text-slate-900 dark:text-slate-100"
                        >
                            Summit Recruitment & Search
                        </h4>
                        <span
                            class="rounded-full bg-sky-500/10 px-2 py-0.5 text-[10px] font-semibold text-sky-500"
                            >Executive Search Agency</span
                        >
                    </div>
                </div>
                <div
                    class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900"
                >
                    <div
                        class="rounded-xl bg-emerald-500/10 p-2.5 text-emerald-500"
                    >
                        <Users class="h-5 w-5" />
                    </div>
                    <div class="text-left">
                        <h4
                            class="text-xs font-bold text-slate-900 dark:text-slate-100"
                        >
                            Flexi Personnel
                        </h4>
                        <span
                            class="rounded-full bg-emerald-500/10 px-2 py-0.5 text-[10px] font-semibold text-emerald-500"
                            >HR & Staffing Agency</span
                        >
                    </div>
                </div>
            </div>
        </section>

        <!-- Directory Filter Bar -->
        <main class="mx-auto max-w-7xl space-y-6 px-6 pb-16">
            <div
                class="flex flex-col items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-xs md:flex-row dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="relative w-full md:flex-1">
                    <Search
                        class="absolute top-3.5 left-3.5 h-4 w-4 text-slate-400"
                    />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search by job title, agency, Safaricom PLC, skill tag..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pr-4 pl-10 text-xs focus:border-[#00b2e3] focus:outline-none dark:border-slate-800 dark:bg-slate-950"
                    />
                </div>

                <div class="flex w-full items-center gap-3 md:w-auto">
                    <select
                        v-model="selectedGrade"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs focus:border-[#00b2e3] focus:outline-none md:w-auto dark:border-slate-800 dark:bg-slate-950"
                    >
                        <option value="all">All Job Grades</option>
                        <option v-for="g in filterGrades" :key="g" :value="g">
                            {{ g }}
                        </option>
                    </select>

                    <select
                        v-model="selectedOrg"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs focus:border-[#00b2e3] focus:outline-none md:w-auto dark:border-slate-800 dark:bg-slate-950"
                    >
                        <option value="all">
                            All Organizations & Employment Agencies
                        </option>
                        <option
                            v-for="o in filterOrgs"
                            :key="o.code"
                            :value="o.code"
                        >
                            {{ o.name }} ({{ o.code }})
                        </option>
                    </select>
                </div>
            </div>

            <!-- Job Vacancies Cards List -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div
                    v-for="job in opportunitiesData"
                    :key="job.id"
                    class="flex flex-col justify-between space-y-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-xs transition hover:border-[#00b2e3]/40 dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="space-y-3">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <span
                                    class="rounded-full border border-[#00b2e3]/20 bg-[#00b2e3]/10 px-2.5 py-0.5 text-[10px] font-bold text-[#00b2e3] uppercase"
                                >
                                    {{ job.grade }}
                                </span>
                                <h3
                                    class="mt-2 text-base leading-snug font-bold text-slate-900 dark:text-slate-100"
                                >
                                    {{ job.title }}
                                </h3>
                                <div
                                    class="mt-0.5 flex items-center gap-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300"
                                >
                                    <Building2
                                        class="h-3.5 w-3.5 text-slate-400"
                                    />
                                    {{ job.organization }}
                                </div>
                            </div>

                            <div
                                v-if="job.kbs_match"
                                :class="[
                                    'shrink-0 rounded-2xl border px-3 py-1.5 text-center',
                                    getStatusBadge(job.kbs_match.status).class,
                                ]"
                            >
                                <div class="text-xs font-black">
                                    {{ job.kbs_match.score }}%
                                </div>
                                <div class="text-[9px] font-bold uppercase">
                                    {{ job.kbs_match.status }}
                                </div>
                            </div>
                        </div>

                        <p
                            class="line-clamp-3 text-xs leading-relaxed text-slate-600 dark:text-slate-400"
                        >
                            {{ job.description }}
                        </p>

                        <div class="flex flex-wrap gap-1.5 pt-2">
                            <span
                                v-for="skill in job.required_skills"
                                :key="skill"
                                class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300"
                            >
                                {{ skill }}
                            </span>
                            <span
                                v-for="lang in job.required_languages"
                                :key="lang"
                                class="rounded-md border border-amber-500/20 bg-amber-500/10 px-2 py-0.5 text-[10px] font-semibold text-amber-600"
                            >
                                Language: {{ lang }}
                            </span>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-between border-t border-slate-100 pt-4 dark:border-slate-800"
                    >
                        <div
                            class="flex items-center gap-1 text-[11px] text-slate-500"
                        >
                            <MapPin class="h-3.5 w-3.5" /> {{ job.location }}
                        </div>
                        <Link
                            :href="`/opportunities/${job.id}`"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-[#00b2e3] px-4 py-2 text-xs font-semibold text-white shadow-xs transition hover:bg-[#0099c4]"
                        >
                            View & Apply <ArrowRight class="h-3.5 w-3.5" />
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <div
                v-if="paginationLinks.length > 3"
                class="flex justify-center gap-1 pt-6"
            >
                <Link
                    v-for="(link, i) in paginationLinks"
                    :key="i"
                    :href="link.url || '#'"
                    :class="[
                        'rounded-xl px-3 py-1.5 text-xs font-semibold transition',
                        link.active
                            ? 'bg-[#00b2e3] text-white'
                            : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400',
                    ]"
                    ><span v-html="link.label"></span
                ></Link>
            </div>
        </main>
    </div>
</template>
