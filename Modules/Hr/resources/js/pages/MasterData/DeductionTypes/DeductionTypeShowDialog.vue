<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle
} from '@/components/ui/alert-dialog'
import InputGroup from '@/components/ui/input-group/InputGroup.vue';

defineProps<{
    show: boolean
    item: Record<string, any> | null
}>()

const emit = defineEmits(['update:show', 'confirm'])

</script>

<template>
    <AlertDialog :open="show" @update:open="(val) => emit('update:show', val)">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>{{ $t('show_deduction_type') }}</AlertDialogTitle>
            </AlertDialogHeader>
            <AlertDialogDescription>
                <div class="grid grid-cols-1 gap-3 py-4">
                    <InputGroup :modelValue="item?.name" :label="$t('name')" :placeholder="$t('please_enter_a_name')"
                        type="text" :disabled="true" />
                </div>
            </AlertDialogDescription>
            <AlertDialogFooter>
                <AlertDialogCancel class="cursor-pointer">{{ $t('close') }}</AlertDialogCancel>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
