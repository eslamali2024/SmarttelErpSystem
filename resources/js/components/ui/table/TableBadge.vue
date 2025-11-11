<script setup lang="ts">
import { ApprovalStatusEnum } from '@/enums/ApprovalStatusEnum';
import Badge from '../badge/Badge.vue';
import { BadgeVariants } from '../badge';

defineProps<{
    status: number,
    label?: string,
    size?: BadgeVariants["size"],
}>()

const getVariant = (status: number) => {
    switch (status) {
        case ApprovalStatusEnum.PENDING:
            return 'secondary'
        case ApprovalStatusEnum.IN_PROGRESS:
            return 'yellow'
        case ApprovalStatusEnum.APPROVED:
            return 'green'
        case ApprovalStatusEnum.REJECTED:
            return 'red'
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
