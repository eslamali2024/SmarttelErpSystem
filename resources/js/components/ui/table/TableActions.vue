<script setup lang="ts">
import { TableCell } from '@/components/ui/table'
import A from '@/components/ui/a/A.vue';
import Button from '@/components/ui/button/Button.vue';

const props = defineProps<{
    show?: string
    edit?: string
    disabled?: boolean
    delete?: () => void
}>()

</script>

<template>
    <TableCell class="text-center">
        <!-- Icon Show:start -->
        <A v-if="props.show && !props.disabled" :href="props.show" size="sm"
            class="mr-2 bg-blue-500 cursor-pointer text-white">
            <i class="ri ri-eye-line"></i>
        </A>
        <span v-if="props.disabled"
            class="mr-2 bg-blue-500 opacity-50 cursor-pointer text-white p-2 inline-block rounded-md">
            <i class="ri ri-eye-line"></i>
        </span>
        <!-- Icon Show:end -->
        <!-- Icon Edit:start -->
        <A v-if="props.edit && !props.disabled" :href="props.edit" size="sm"
            class="mr-2 bg-yellow-500 cursor-pointer text-white">
            <i class="ri ri-pencil-line"></i>
        </A>
        <span v-if="props.disabled"
            class="mr-2 bg-yellow-500 opacity-50 cursor-pointer text-white p-2 inline-block rounded-md">
            <i class="ri ri-pencil-line"></i>
        </span>
        <!-- Icon Edit:end -->
        <!-- Icon Delete:start -->
        <Button v-if="props.delete && !props.disabled" size="sm" v-on:click="props.delete"
            class="bg-red-500 cursor-pointer text-white">
            <i class="ri ri-delete-bin-line"></i>
        </Button>
        <span v-if="props.disabled"
            class="mr-2 bg-red-500 opacity-50 cursor-pointer text-white p-2 inline-block rounded-md">
            <i class="ri ri-delete-bin-line"></i>
        </span>
        <!-- Icon Delete:end -->
    </TableCell>
</template>
