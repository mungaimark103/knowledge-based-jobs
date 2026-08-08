<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { Menu, PanelLeftClose, PanelLeftOpen } from "@lucide/vue"
import { cn } from "@/lib/utils"
import { Button } from '@/components/ui/button'
import { useSidebar } from "./utils"

const props = defineProps<{
  class?: HTMLAttributes["class"]
}>()

const { isMobile, state, toggleSidebar } = useSidebar()
</script>

<template>
  <Button
    data-sidebar="trigger"
    data-slot="sidebar-trigger"
    variant="ghost"
    size="icon"
    :class="cn('h-8 w-8', props.class)"
    @click="toggleSidebar"
  >
    <Menu v-if="isMobile" class="h-5 w-5" />
    <PanelLeftOpen v-else-if="state === 'collapsed'" class="h-5 w-5" />
    <PanelLeftClose v-else class="h-5 w-5" />
    <span class="sr-only">Toggle sidebar</span>
  </Button>
</template>
