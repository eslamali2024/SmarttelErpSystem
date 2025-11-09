<script setup lang="ts">
import { useAppStore } from '@/stores/appStore';
import { storeToRefs } from 'pinia';

const props = defineProps<{
    permissions?: string | string[]
}>()

const appStore = useAppStore()
const { permissions: authPermissions } = storeToRefs(appStore)

const hasPermission = (): boolean => {
    const perms = authPermissions.value ?? []
    if (!props.permissions) return false

    if (Array.isArray(props.permissions)) {
        return props.permissions.some(p => perms.includes(p))
    }

    return perms.includes(props.permissions)
}
</script>

<template>
    <slot v-if="hasPermission()" />
</template>
