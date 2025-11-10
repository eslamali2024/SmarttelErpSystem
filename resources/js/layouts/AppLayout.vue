<script setup lang="ts">
import { onMounted } from 'vue'
import AppLayout from '@/layouts/app/AppSidebarLayout.vue'
import { useAppStore } from '@/stores/appStore'
import type { BreadcrumbItemType } from '@/types'
import Spinner from '@/components/ui/spinner/Spinner.vue';
import { Toaster } from 'vue-sonner'
import 'vue-sonner/style.css'

interface Props { breadcrumbs?: BreadcrumbItemType[] }
withDefaults(defineProps<Props>(), { breadcrumbs: () => [] })

const appStore = useAppStore()

onMounted(() => {
    appStore.load()
})

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs" :loading="appStore.loaded">
        <div v-if="appStore.loaded" class="p-3">
            <slot />
        </div>
        <div v-else class="h-25 p-4">
            <div class="bg-muted rounded-lg flex items-center justify-center h-full">
                <Spinner class="size-8" />
            </div>
        </div>

        <Toaster rich-colors expand />
    </AppLayout>
</template>
