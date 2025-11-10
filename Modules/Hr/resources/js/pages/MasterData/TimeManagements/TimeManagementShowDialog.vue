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
                <AlertDialogTitle>{{ $t('show_time_management') }}</AlertDialogTitle>
            </AlertDialogHeader>
            <AlertDialogDescription>
                <div class="grid grid-cols-1 gap-3 py-4">
                    <InputGroup :modelValue="item?.name" :label="$t('name')" :placeholder="$t('please_enter_a_name')"
                        type="text" :disabled="true" />

                    <InputGroup :modelValue="item?.payroll == 1 ? $t('yes') : $t('no')" :label="$t('payroll')"
                        :placeholder="$t('please_enter_a_payroll')" type="text" :disabled="true" />

                    <InputGroup :modelValue="item?.fingerprint_in == 1 ? $t('yes') : $t('no')"
                        :label="$t('fingerprint_in')" :placeholder="$t('please_enter_a_fingerprint_in')" type="text"
                        :disabled="true" />

                    <InputGroup :modelValue="item?.fingerprint_out == 1 ? $t('yes') : $t('no')"
                        :label="$t('fingerprint_out')" :placeholder="$t('please_enter_a_fingerprint_out')" type="text"
                        :disabled="true" />

                    <InputGroup :modelValue="item?.factors == 1 ? $t('yes') : $t('no')" :label="$t('factors')"
                        :placeholder="$t('please_enter_a_factors')" type="text" :disabled="true" />
                </div>

            </AlertDialogDescription>
            <AlertDialogFooter>
                <AlertDialogCancel class="cursor-pointer">{{ $t('close') }}</AlertDialogCancel>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
