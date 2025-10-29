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
import { Loader2 } from "lucide-vue-next";

const props = defineProps<{
    show: boolean
    item: Record<string, any> | null,
    loading: boolean
}>()

const emit = defineEmits(['update:show', 'confirm'])

const confirmDelete = () => {
    emit('confirm')
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
                <AlertDialogCancel :disabled="loading" class="cursor-pointer">Cancel</AlertDialogCancel>

                <AlertDialogAction @click="confirmDelete" :disabled="loading"
                    class="flex items-center justify-center gap-2 transition-all duration-200 cursor-pointer">
                    <Loader2 v-if="loading" class="w-4 h-4 animate-spin" />
                    <span>{{ loading ? 'Deleting...' : 'Delete' }}</span>
                </AlertDialogAction>
            </AlertDialogFooter>

        </AlertDialogContent>
    </AlertDialog>
</template>
