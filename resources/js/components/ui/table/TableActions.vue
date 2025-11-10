<script setup lang="ts">
import { TableCell } from '@/components/ui/table'
import A from '@/components/ui/a/A.vue';
import Button from '@/components/ui/button/Button.vue';
import Can from '@/components/ui/Auth/Can.vue';

const props = defineProps<{
    show?: string
    canShow?: string
    edit?: string
    canEdit?: string
    delete?: () => void
    canDelete?: string
    disabled?: boolean
}>()
</script>

<template>
    <TableCell class="text-center">
        <slot name="before" />

        <Can :permissions="canShow">
            <A size="sm" v-if="props.show && !props.disabled" :href="props.show"
                class="mr-2 bg-blue-500 cursor-pointer text-white hover:bg-blue-600">
                <i class="ri ri-eye-line"></i>
            </A>
        </Can>

        <Can :permissions="canEdit">
            <A size="sm" v-if="props.edit && !props.disabled" :href="props.edit"
                class="mr-2 bg-yellow-500 cursor-pointer text-white hover:bg-yellow-600">
                <i class="ri ri-pencil-line"></i>
            </A>
        </Can>

        <Can :permissions="canDelete">
            <Button size="sm" v-if="props.delete && !props.disabled" v-on:click="props.delete"
                class="bg-red-500 cursor-pointer text-white hover:bg-red-600 ">
                <i class="ri ri-delete-bin-line"></i>
            </Button>
        </Can>

        <slot name="after" />
        <slot />
    </TableCell>
</template>
