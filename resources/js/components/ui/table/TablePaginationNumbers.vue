<script setup lang="ts">

const props = defineProps<{
    items?: {
        per_page?: number
        current_page?: number
    },
    index?: number
}>()
</script>

<template>
{{ (props.items?.per_page ?? 10) * ((props.items?.current_page ?? 1) - 1) + (props?.index ?? 0) + 1 }}
</template>
