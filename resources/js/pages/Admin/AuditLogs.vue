<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { ShieldCheck, Search, Filter, ArrowLeft, Clock, UserCheck, Activity, Key, FileText, Database } from '@lucide/vue';

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
    return props.logs.data.filter(log => {
        const matchesSearch = !searchQuery.value || 
            log.actor_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            log.description.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            log.action.toLowerCase().includes(searchQuery.value.toLowerCase());

        const matchesAction = selectedAction.value === 'ALL' || log.action === selectedAction.value;

        return matchesSearch && matchesAction;
    });
});

const getBadgeColor = (action: string) => {
    switch (action) {
        case 'LOGIN': return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20';
        case 'LOGOUT': return 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20';
        case 'DOCUMENT_UPLOAD': return 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/20';
        case 'APPLICATION_SUBMIT': return 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20';
        case 'PROFILE_UPDATE': return 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20';
        case 'RULE_UPDATE': return 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20';
        default: return 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20';
    }
};
</script>

<template>
    <Head title="JobSync — System Audit Trail" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans pb-16">
        <!-- Header Bar -->
        <header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 sticky top-0 z-40 px-6 py-4">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Link href="/admin/dashboard" class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-300 transition">
                        <ArrowLeft class="w-4 h-4" />
                    </Link>
                    <div>
                        <h1 class="text-lg font-bold flex items-center gap-2">
                            <ShieldCheck class="w-5 h-5 text-[#00b2e3]" /> System Audit Trail & Actor Actions
                        </h1>
                        <p class="text-xs text-slate-500">Monitor Logins, Logouts, Data Updates, Deletions & Timestamped Events</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-600 text-xs font-semibold rounded-full border border-emerald-500/20">
                        {{ logs.total }} Total Audit Logs Recorded
                    </span>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 py-8 space-y-6">
            <!-- Filter & Search Controls -->
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="relative w-full sm:w-80">
                    <Search class="w-4 h-4 absolute left-3 top-3 text-slate-400" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search actor name, description, IP..."
                        class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs focus:outline-none focus:border-[#00b2e3]"
                    />
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <Filter class="w-4 h-4 text-slate-400" />
                    <select
                        v-model="selectedAction"
                        class="px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs focus:outline-none focus:border-[#00b2e3]"
                    >
                        <option value="ALL">All Event Types</option>
                        <option value="LOGIN">LOGIN</option>
                        <option value="LOGOUT">LOGOUT</option>
                        <option value="DOCUMENT_UPLOAD">DOCUMENT_UPLOAD</option>
                        <option value="PROFILE_UPDATE">PROFILE_UPDATE</option>
                        <option value="APPLICATION_SUBMIT">APPLICATION_SUBMIT</option>
                        <option value="RULE_UPDATE">RULE_UPDATE</option>
                    </select>
                </div>
            </div>

            <!-- Audit Logs Table -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3.5 px-4">Timestamp</th>
                                <th class="py-3.5 px-4">Actor</th>
                                <th class="py-3.5 px-4">Action</th>
                                <th class="py-3.5 px-4">Description</th>
                                <th class="py-3.5 px-4">IP Address</th>
                                <th class="py-3.5 px-4 text-right">Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs">
                            <tr
                                v-for="log in filteredLogs"
                                :key="log.id"
                                class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition"
                            >
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-500 flex items-center gap-1.5">
                                    <Clock class="w-3.5 h-3.5 text-slate-400" />
                                    {{ log.created_at }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-semibold text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
                                        <UserCheck class="w-3.5 h-3.5 text-[#00b2e3]" />
                                        {{ log.actor_name }}
                                    </div>
                                    <span class="text-[10px] text-slate-400 capitalize">Role: {{ log.actor_role }}</span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span :class="['px-2.5 py-1 text-[10px] font-bold rounded-lg border', getBadgeColor(log.action)]">
                                        {{ log.action }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300 max-w-md truncate">
                                    {{ log.description }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-500 font-mono text-[11px]">
                                    {{ log.ip_address || '127.0.0.1' }}
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <button
                                        v-if="log.changes"
                                        @click="selectedLog = log"
                                        class="px-2.5 py-1 text-[11px] font-semibold text-[#00b2e3] bg-[#00b2e3]/10 rounded-lg hover:underline"
                                    >
                                        View Data Diff
                                    </button>
                                    <span v-else class="text-slate-400 text-[11px]">No payload</span>
                                </td>
                            </tr>
                            <tr v-if="filteredLogs.length === 0">
                                <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                                    No audit logs matching search filters.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <!-- Data Changes Modal -->
        <div v-if="selectedLog" class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-bold flex items-center gap-2">
                        <Database class="w-4 h-4 text-[#00b2e3]" /> Data Changes Payload Diff
                    </h3>
                    <button @click="selectedLog = null" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>
                <div class="bg-slate-950 text-slate-200 p-4 rounded-xl text-xs font-mono overflow-x-auto max-h-80">
                    <pre>{{ JSON.stringify(selectedLog.changes, null, 2) }}</pre>
                </div>
                <div class="text-right">
                    <button @click="selectedLog = null" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-xs font-semibold rounded-xl">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
