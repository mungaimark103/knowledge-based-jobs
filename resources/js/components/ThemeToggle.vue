<script setup lang="ts">
import { Sun, Moon } from '@lucide/vue';
import { useTheme } from '@/composables/useTheme';

const { isDark, toggleTheme } = useTheme();
</script>

<template>
    <button
        @click="toggleTheme"
        type="button"
        class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
        title="Toggle Theme"
    >
        <component
            :is="isDark ? Sun : Moon"
            class="h-4 w-4 text-amber-500 dark:text-indigo-400"
        />
        <span>{{ isDark ? 'Light Mode' : 'Dark Mode' }}</span>
    </button>
</template>
