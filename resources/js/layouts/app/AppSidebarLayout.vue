<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';
import { AuthPermissions } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    auth_permissions?: AuthPermissions[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    auth_permissions: () => [],
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar :auth_permissions="auth_permissions" />
        <AppContent variant="sidebar" class="overflow-x-hidden">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
    </AppShell>
</template>
