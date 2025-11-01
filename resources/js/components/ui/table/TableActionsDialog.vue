<script setup lang="ts">
import { TableCell } from '@/components/ui/table'
import Button from '@/components/ui/button/Button.vue';
import Can from '@/components/ui/Auth/Can.vue';

const props = defineProps<{
    show?: () => void
    canShow?: string
    edit?: () => void
    canEdit?: string
    delete?: () => void
    canDelete?: string
    disabled?: boolean
}>()

</script>

<template>
    <TableCell class="text-center">
        <Can :permissions="canShow">
            <Button size="sm" v-if="props.show && !props.disabled" v-on:click="props.show"
                class="mr-2 bg-blue-500 cursor-pointer text-white hover:bg-blue-600">
                <i class="ri ri-eye-line"></i>
            </Button>
        </Can>

        <Can :permissions="canEdit">
            <Button size="sm" v-if="props.edit && !props.disabled" v-on:click="props.edit"
                class="mr-2 bg-yellow-500 cursor-pointer text-white hover:bg-yellow-600">
                <i class="ri ri-pencil-line"></i>
            </Button>
        </Can>

        <Can :permissions="canDelete">
            <Button size="sm" v-if="props.delete && !props.disabled" v-on:click="props.delete"
                class="bg-red-500 cursor-pointer text-white hover:bg-red-600 ">
                <i class="ri ri-delete-bin-line"></i>
            </Button>
        </Can>

    </TableCell>
</template>
