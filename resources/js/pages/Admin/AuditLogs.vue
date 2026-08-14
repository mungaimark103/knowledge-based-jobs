<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ShieldCheck,
    Search,
    Filter,
    ArrowLeft,
    Clock,
    UserCheck,
    Database,
} from '@lucide/vue';
import { ref, computed } from 'vue';

interface AuditLogItem {
    id: number;
    user_id: number | null;
    actor_name: string;
    actor_role: string;
    action: string;
    description: string;
    ip_address: string | null;
    user_agent: string | null;
    changes: any | null;
    created_at: string;
}

const props = defineProps<{
    logs: {
        data: AuditLogItem[];
        links: any[];
        total: number;
    };
    filters?: {
        search?: string;
        action?: string;
    };
}>();

const searchQuery = ref(props.filters?.search || '');
const selectedAction = ref(props.filters?.action || 'ALL');
const selectedLog = ref<AuditLogItem | null>(null);

const filteredLogs = computed(() => {
    return props.logs.data.filter((log) => {
        const matchesSearch =
            !searchQuery.value ||
            log.actor_name
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase()) ||
            log.description
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase()) ||
            log.action.toLowerCase().includes(searchQuery.value.toLowerCase());

        const matchesAction =
            selectedAction.value === 'ALL' ||
            log.action === selectedAction.value;

        return matchesSearch && matchesAction;
    });
});

const getBadgeColor = (action: string) => {
    switch (action) {
        case 'LOGIN':
            return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20';
        case 'LOGOUT':
            return 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20';
        case 'DOCUMENT_UPLOAD':
            return 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/20';
        case 'APPLICATION_SUBMIT':
            return 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20';
        case 'PROFILE_UPDATE':
            return 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20';
        case 'RULE_UPDATE':
            return 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20';
        default:
            return 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20';
    }
};
</script>

<template>
    <Head title="JobSync — System Audit Trail" />

    <div
        class="min-h-screen bg-slate-50 pb-16 font-sans text-slate-900 dark:bg-slate-950 dark:text-slate-100"
    >
        <!-- Header Bar -->
        <header
            class="sticky top-0 z-40 border-b border-slate-200 bg-white px-6 py-4 dark:border-slate-800 dark:bg-slate-900"
        >
            <div class="mx-auto flex max-w-7xl items-center justify-between">
                <div class="flex items-center gap-3">
                    <Link
                        href="/admin/dashboard"
                        class="rounded-xl bg-slate-100 p-2 text-slate-600 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300"
                    >
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                    <div>
                        <h1 class="flex items-center gap-2 text-lg font-bold">
                            <ShieldCheck class="h-5 w-5 text-[#00b2e3]" />
                            System Audit Trail & Actor Actions
                        </h1>
                        <p class="text-xs text-slate-500">
                            Monitor Logins, Logouts, Data Updates, Deletions &
                            Timestamped Events
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span
                        class="rounded-full border border-emerald-500/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-600"
                    >
                        {{ logs.total }} Total Audit Logs Recorded
                    </span>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl space-y-6 px-6 py-8">
            <!-- Filter & Search Controls -->
            <div
                class="flex flex-col items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-4 sm:flex-row dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="relative w-full sm:w-80">
                    <Search
                        class="absolute top-3 left-3 h-4 w-4 text-slate-400"
                    />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search actor name, description, IP..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2 pr-4 pl-9 text-xs focus:border-[#00b2e3] focus:outline-none dark:border-slate-800 dark:bg-slate-950"
                    />
                </div>

                <div class="flex w-full items-center gap-2 sm:w-auto">
                    <Filter class="h-4 w-4 text-slate-400" />
                    <select
                        v-model="selectedAction"
                        class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs focus:border-[#00b2e3] focus:outline-none dark:border-slate-800 dark:bg-slate-950"
                    >
                        <option value="ALL">All Event Types</option>
                        <option value="LOGIN">LOGIN</option>
                        <option value="LOGOUT">LOGOUT</option>
                        <option value="DOCUMENT_UPLOAD">DOCUMENT_UPLOAD</option>
                        <option value="PROFILE_UPDATE">PROFILE_UPDATE</option>
                        <option value="APPLICATION_SUBMIT">
                            APPLICATION_SUBMIT
                        </option>
                        <option value="RULE_UPDATE">RULE_UPDATE</option>
                    </select>
                </div>
            </div>

            <!-- Audit Logs Table -->
            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr
                                class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold tracking-wider text-slate-500 uppercase dark:border-slate-800 dark:bg-slate-800/50"
                            >
                                <th class="px-4 py-3.5">Timestamp</th>
                                <th class="px-4 py-3.5">Actor</th>
                                <th class="px-4 py-3.5">Action</th>
                                <th class="px-4 py-3.5">Description</th>
                                <th class="px-4 py-3.5">IP Address</th>
                                <th class="px-4 py-3.5 text-right">Details</th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-slate-200 text-xs dark:divide-slate-800"
                        >
                            <tr
                                v-for="log in filteredLogs"
                                :key="log.id"
                                class="transition hover:bg-slate-50/80 dark:hover:bg-slate-800/40"
                            >
                                <td
                                    class="flex items-center gap-1.5 px-4 py-3.5 whitespace-nowrap text-slate-500"
                                >
                                    <Clock class="h-3.5 w-3.5 text-slate-400" />
                                    {{ log.created_at }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div
                                        class="flex items-center gap-1.5 font-semibold text-slate-900 dark:text-slate-100"
                                    >
                                        <UserCheck
                                            class="h-3.5 w-3.5 text-[#00b2e3]"
                                        />
                                        {{ log.actor_name }}
                                    </div>
                                    <span
                                        class="text-[10px] text-slate-400 capitalize"
                                        >Role: {{ log.actor_role }}</span
                                    >
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span
                                        :class="[
                                            'rounded-lg border px-2.5 py-1 text-[10px] font-bold',
                                            getBadgeColor(log.action),
                                        ]"
                                    >
                                        {{ log.action }}
                                    </span>
                                </td>
                                <td
                                    class="max-w-md truncate px-4 py-3.5 text-slate-700 dark:text-slate-300"
                                >
                                    {{ log.description }}
                                </td>
                                <td
                                    class="px-4 py-3.5 font-mono text-[11px] whitespace-nowrap text-slate-500"
                                >
                                    {{ log.ip_address || '127.0.0.1' }}
                                </td>
                                <td
                                    class="px-4 py-3.5 text-right whitespace-nowrap"
                                >
                                    <button
                                        v-if="log.changes"
                                        @click="selectedLog = log"
                                        class="rounded-lg bg-[#00b2e3]/10 px-2.5 py-1 text-[11px] font-semibold text-[#00b2e3] hover:underline"
                                    >
                                        View Data Diff
                                    </button>
                                    <span
                                        v-else
                                        class="text-[11px] text-slate-400"
                                        >No payload</span
                                    >
                                </td>
                            </tr>
                            <tr v-if="filteredLogs.length === 0">
                                <td
                                    colspan="6"
                                    class="py-8 text-center text-xs text-slate-400"
                                >
                                    No audit logs matching search filters.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <!-- Data Changes Modal -->
        <div
            v-if="selectedLog"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-xs"
        >
            <div
                class="w-full max-w-lg space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900"
            >
                <div
                    class="flex items-center justify-between border-b border-slate-200 pb-3 dark:border-slate-800"
                >
                    <h3 class="flex items-center gap-2 text-sm font-bold">
                        <Database class="h-4 w-4 text-[#00b2e3]" /> Data Changes
                        Payload Diff
                    </h3>
                    <button
                        @click="selectedLog = null"
                        class="text-slate-400 hover:text-slate-600"
                    >
                        ✕
                    </button>
                </div>
                <div
                    class="max-h-80 overflow-x-auto rounded-xl bg-slate-950 p-4 font-mono text-xs text-slate-200"
                >
                    <pre>{{
                        JSON.stringify(selectedLog.changes, null, 2)
                    }}</pre>
                </div>
                <div class="text-right">
                    <button
                        @click="selectedLog = null"
                        class="rounded-xl bg-slate-100 px-4 py-2 text-xs font-semibold hover:bg-slate-200 dark:bg-slate-800"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
