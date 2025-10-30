<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { AuthPermissions } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage()
const auth_permissions = (page.props.auth.auth_permissions ?? []) as AuthPermissions[]
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs" :auth_permissions="auth_permissions">
        <div class="p-3">
            <slot />
        </div>
    </AppLayout>
</template>
