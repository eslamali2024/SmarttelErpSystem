<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { cn } from "@/lib/utils"
import ScrollArea from "../scroll-area/ScrollArea.vue";
import ScrollBar from "../scroll-area/ScrollBar.vue";

const props = defineProps<{
    class?: HTMLAttributes["class"]
}>()
</script>

<template>
    <ScrollArea class="pb-4">
        <div class="relative">
            <table
                :class="cn('w-full caption-bottom text-sm rounded-lg p-1 bg-transparent dark:bg-gray-500/50 border', props.class)">
                <slot />
            </table>
        </div>

        <ScrollBar orientation="horizontal" />
    </ScrollArea>
</template>
