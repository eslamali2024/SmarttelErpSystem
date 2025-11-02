<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'

const props = defineProps<{
    permissions?: string | string[]
}>()

const page = usePage()
const authPermissions = (page.props.auth?.auth_permissions ?? []) as string[]

const hasPermission = (): boolean => {
    if (!props.permissions) return false

    if (Array.isArray(props.permissions)) {
        return props.permissions.some(p => authPermissions.includes(p))
    }

    return authPermissions.includes(props.permissions)
}
</script>

<template>
    <slot v-if="hasPermission()" />
</template>
