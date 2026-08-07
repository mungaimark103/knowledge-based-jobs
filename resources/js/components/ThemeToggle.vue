<script setup lang="ts">
import { useTheme } from '@/composables/useTheme';
import { Sun, Moon } from '@lucide/vue';

const { isDark, toggleTheme } = useTheme();
</script>

<template>
    <button
        @click="toggleTheme"
        type="button"
        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-semibold shadow-sm hover:bg-slate-100 dark:hover:bg-slate-700 transition"
        title="Toggle Theme"
    >
        <component :is="isDark ? Sun : Moon" class="w-4 h-4 text-amber-500 dark:text-indigo-400" />
        <span>{{ isDark ? 'Light Mode' : 'Dark Mode' }}</span>
    </button>
</template>
