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
                <AlertDialogTitle>{{ $t('show_public_holiday') }}</AlertDialogTitle>
            </AlertDialogHeader>
            <AlertDialogDescription>
                <div class="grid grid-cols-1 gap-3 py-4">
                    <InputGroup :modelValue="item?.name" :label="$t('name')" :placeholder="$t('please_enter_a_name')"
                        type="text" :disabled="true" />

                    <InputGroup :modelValue="item?.start_date" :label="$t('start_date')"
                        :placeholder="$t('please_enter_a_start_date')" type="date" :disabled="true" />

                    <InputGroup :modelValue="item?.end_date" :label="$t('end_date')"
                        :placeholder="$t('please_enter_a_end_date')" type="date" :disabled="true" />

                    <InputGroup :modelValue="item?.actual_start_date" :label="$t('actual_start_date')"
                        :placeholder="$t('please_enter_a_actual_start_date')" type="date" :disabled="true" />

                    <InputGroup :modelValue="item?.actual_end_date" :label="$t('actual_end_date')"
                        :placeholder="$t('please_enter_a_actual_end_date')" type="date" :disabled="true" />

                    <InputGroup :modelValue="item?.days" :label="$t('days')" :placeholder="$t('please_enter_a_days')"
                        type="text" :disabled="true" />
                </div>
            </AlertDialogDescription>
            <AlertDialogFooter>
                <AlertDialogCancel class="cursor-pointer">{{ $t('close') }}</AlertDialogCancel>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
