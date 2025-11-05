<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { cn } from "@/lib/utils"

const props = defineProps<{
    class?: HTMLAttributes["class"]
}>()
</script>

<template>
    <th :class="cn(
        'relative h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0',
        props.class
    )">
        <slot />
        <!-- Left line -->
        <span class="absolute left-0 top-1/4 h-1/2 w-[.5px] bg-gray-200/50 dark:bg-gray-800/50"></span>
        <!-- Right line -->
        <span class="absolute right-0 top-1/4 h-1/2 w-[.5px] bg-gray-200/50 dark:bg-gray-800/50"></span>
    </th>
</template>
