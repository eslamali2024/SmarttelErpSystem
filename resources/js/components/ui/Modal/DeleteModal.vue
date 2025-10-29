<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle
} from '@/components/ui/alert-dialog'

const props = defineProps<{
    show: boolean
    item: Record<string, any> | null
}>()

const emit = defineEmits(['update:show', 'confirm'])

const confirmDelete = () => {
    emit('confirm')
    emit('update:show', false)
}
</script>

<template>
    <AlertDialog :open="show" @update:open="(val) => emit('update:show', val)">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Delete Confirmation</AlertDialogTitle>
                <AlertDialogDescription>
                    Are you sure you want to delete
                    <strong>{{ item?.name }}</strong>?
                    This action cannot be undone.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Cancel</AlertDialogCancel>
                <AlertDialogAction @click="confirmDelete">Delete</AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
