<script setup lang="ts">
import Approval from '@/components/approval/approval.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import TableBadge from '@/components/ui/table/TableBadge.vue';
import TextareaGroup from '@/components/ui/textarea-group/TextareaGroup.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import deductions from '@/routes/hr/deductions';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps<{
    deduction: any,
    approval?: any,
    cancel_approval: object | boolean,
    last_request_action_approval: any,
    approval_steps: any
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('hr'), href: null },
    { title: t('master_data'), href: null },
    { title: t('deductions'), href: deductions.index().url },
    { title: t('show_deduction'), href: null },
];

</script>

<template>

    <Head :title="$t('show_deduction')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Approval :approval="approval" :redirect_url="deductions.show(props.deduction.id).url"
            :cancel_approval="cancel_approval" :last_request_action_approval="last_request_action_approval"
            :approval_steps="approval_steps" />

        <Card>
            <CardHeader class="flex justify-between items-center">
                <CardTitle>{{ $t('show_deduction') }}</CardTitle>
                <TableBadge size="sm" :status="deduction.status" :label="deduction.status_label" />
            </CardHeader>

            <CardContent>
                <div class="grid grid-cols-12 gap-3 py-4">
                    <InputGroup :modelValue="deduction.employee?.name ?? ''"
                        class="col-span-12 md:col-span-6 lg:col-span-4" :label="$t('employee')"
                        :placeholder="$t('please_select_a_employee')" type="text" :disabled="true" />

                    <InputGroup :modelValue="deduction.deduction_type?.name ?? ''"
                        class="col-span-12 md:col-span-6 lg:col-span-4" :label="$t('deduction_type')"
                        :placeholder="$t('please_select_a_deduction_type')" type="text" :disabled="true" />

                    <InputGroup :modelValue="deduction.date ?? ''" :label="$t('date')"
                        class="col-span-12 md:col-span-6 lg:col-span-4" :placeholder="$t('please_enter_a_date')"
                        type="text" :disabled="true" />

                    <InputGroup :modelValue="deduction.reason ?? ''" :label="$t('reason')"
                        class="col-span-12 md:col-span-6 " :placeholder="$t('please_enter_a_reason')" type="text"
                        :disabled="true" />

                    <InputGroup :modelValue="deduction.amount ?? ''" :label="$t('amount')"
                        class="col-span-12 md:col-span-6 " :placeholder="$t('please_enter_a_amount')"
                        :disabled="true" />

                    <InputGroup :modelValue="deduction.created_by.name ?? ''" :label="$t('created_by')"
                        class="col-span-12 md:col-span-6" :placeholder="$t('please_enter_a_created_by')"
                        :disabled="true" />

                    <InputGroup :modelValue="deduction.created_at ?? ''" :label="$t('created_at')"
                        class="col-span-12 md:col-span-6" :placeholder="$t('please_enter_a_created_at')" :min-value="0"
                        :disabled="true" />

                    <TextareaGroup :modelValue="deduction.notes ?? ''" :label="$t('notes')" class="col-span-12"
                        :placeholder="$t('please_enter_a_notes')" type="text" :disabled="true" />
                </div>
            </CardContent>
        </Card>
    </AppLayout>
</template>
