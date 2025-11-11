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
import Switch from '@/components/ui/switch/Switch.vue';

defineProps<{
    show: boolean
    item: Record<string, any> | null
}>()

const emit = defineEmits(['update:show', 'confirm'])

</script>

<template>
    <AlertDialog :open="show" @update:open="(val) => emit('update:show', val)">
        <AlertDialogContent class="sm:max-w-[425px] md:max-w-[600px] lg:max-w-5xl">
            <AlertDialogHeader>
                <AlertDialogTitle>{{ $t('show_leave_type') }}</AlertDialogTitle>
            </AlertDialogHeader>
            <AlertDialogDescription>
                <div class="grid grid-cols-12 gap-3 py-4">
                    <InputGroup :modelValue="item?.name" :label="$t('name')" :placeholder="$t('please_enter_a_name')"
                        type="text" class="col-span-12" :disabled="true" />

                    <InputGroup :modelValue="item?.unit_label" :label="$t('unit')"
                        :placeholder="$t('please_enter_a_unit')" type="text"
                        class="col-span-12 md:col-span-6 lg:col-span-4" :disabled="true" />

                    <InputGroup :modelValue="item?.type_duration_label" :label="$t('type_duration')"
                        :placeholder="$t('please_enter_a_type_duration')" type="text"
                        class="col-span-12 md:col-span-6 lg:col-span-4" :disabled="true" />

                    <div
                        class="flex flex-row items-center justify-between border p-2 rounded-lg col-span-12 md:col-span-6 lg:col-span-4">
                        <div class="text-base">
                            {{ $t('for_employee') }}
                        </div>
                        <Switch :modelValue="item?.for_employee" :disabled="true" />
                    </div>

                    <InputGroup :modelValue="item?.company_amount" :label="$t('company_amount')"
                        :placeholder="$t('please_enter_a_company_amount')" type="number"
                        class="col-span-12 md:col-span-6 lg:col-span-4" :disabled="true" />

                    <InputGroup :modelValue="item?.duration_deducted_percentage"
                        :label="$t('duration_deducted_percentage')"
                        :placeholder="$t('please_enter_a_duration_deducted_percentage')" type="number"
                        class="col-span-12 md:col-span-6 lg:col-span-4" :disabled="true" />

                    <InputGroup :modelValue="item?.salary_deducted_percentage" :label="$t('salary_deducted_percentage')"
                        :placeholder="$t('please_enter_a_salary_deducted_percentage')" type="number"
                        class="col-span-12 md:col-span-6 lg:col-span-4" :disabled="true" />
                </div>
            </AlertDialogDescription>
            <AlertDialogFooter>
                <AlertDialogCancel class="cursor-pointer">{{ $t('close') }}</AlertDialogCancel>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
