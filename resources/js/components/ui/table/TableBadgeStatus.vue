<script setup lang="ts">
import { StatusEnum } from '@/enums/StatusEnum';
import Badge from '../badge/Badge.vue';
import { BadgeVariants } from '../badge';

defineProps<{
    status: number,
    label?: string,
    size?: BadgeVariants["size"],
}>()

const getVariant = (status: number) => {
    switch (status) {
        case StatusEnum.ACTIVE:
            return 'green'
        case StatusEnum.IN_ACTIVE:
            return 'yellow'
        default:
            return 'yellow'
    }
}
</script>

<template>
    <Badge :variant="getVariant(status)" :size="size">
        {{ label }}
    </Badge />
</template>
