<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import employeesRoute from '@/routes/hr/employees';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import {
    Tabs,
    TabsContent,
    TabsList,
    TabsTrigger,
} from "@/components/ui/tabs"
import TextareaGroup from '@/components/ui/textarea-group/TextareaGroup.vue';
import employeeActivityRoute from '@/routes/hr/employees/activity';


interface Props {
    contract?: any,
    canEditAvataer?: boolean
}

const { t } = useI18n();
const props = defineProps<Props>();
const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('hr'), href: null },
    { title: t('employees'), href: employeesRoute.index().url },
    { title: t('employee'), href: employeesRoute.show(props.contract?.employee?.id ?? 'show').url },
    { title: t('contracts'), href: employeeActivityRoute.contracts(props.contract?.employee?.id ?? 'show').url },
    { title: t('show_contract'), href: null },
];

</script>

<template>

    <Head :title="$t('show_contract')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card class="p">
            <CardHeader>
                <CardTitle>{{ $t('show_contract') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <Tabs default-value="contract" class="w-full">
                    <TabsList class="grid w-full grid-cols-2">
                        <TabsTrigger value="employee" class="cursor-pointer">
                            {{ $t('employee') }}
                        </TabsTrigger>
                        <TabsTrigger value="contract" class="cursor-pointer">
                            {{ $t('contract') }}
                        </TabsTrigger>
                    </TabsList>
                    <TabsContent value="employee">
                        <Card>
                            <CardHeader>
                                <CardTitle>{{ $t('employee') }}</CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-2">
                                <div class="grid grid-cols-2 gap-3 py-4">
                                    <InputGroup :disabled="true" :modelValue="props.contract.employee.code"
                                        :label="$t('employee_code')" :placeholder="$t('please_enter_a_employee_code')"
                                        type="text" class="col-span-2" />

                                    <InputGroup :disabled="true" :modelValue="props.contract.employee.name"
                                        :label="$t('name')" :placeholder="$t('please_enter_a_name')" type="text" />

                                    <InputGroup :disabled="true" :modelValue="props.contract.employee.name_ar"
                                        :label="$t('name_ar')" :placeholder="$t('please_enter_a_name_ar')"
                                        type="text" />

                                    <InputGroup :disabled="true" :modelValue="props.contract.employee.email"
                                        :label="$t('email')" :placeholder="$t('please_enter_a_email')" type="email" />

                                    <InputGroup :disabled="true" :modelValue="props.contract.employee.phone"
                                        :label="$t('phone')" :placeholder="$t('please_enter_a_phone')" type="text" />

                                    <InputGroup :disabled="true" :modelValue="props.contract.employee.gender_label"
                                        :label="$t('gender')" :placeholder="$t('please_select_a_department')" />

                                    <InputGroup :disabled="true"
                                        :modelValue="props.contract.employee.marital_status_label"
                                        :label="$t('marital_status')"
                                        :placeholder="$t('please_select_a_marital_status')" />

                                    <InputGroup :disabled="true" :modelValue="props.contract.employee.national_id"
                                        :label="$t('national_id')" :placeholder="$t('please_enter_a_national_id')"
                                        type="text" />

                                    <InputGroup :disabled="true" :modelValue="props.contract.employee.joining_date"
                                        :label="$t('joining_date')" :placeholder="$t('please_enter_a_joining_date')"
                                        type="date" />

                                    <InputGroup :disabled="true" :modelValue="props.contract.employee.dob"
                                        :label="$t('dob')" :placeholder="$t('please_enter_a_dob')" type="date" />

                                    <TextareaGroup :modelValue="props.contract.employee.address" :label="$t('address')"
                                        :disabled="true" :placeholder="$t('please_enter_a_address')"
                                        :placeholder_message="$t('please_enter_a_address')" class="col-span-2" />

                                    <TextareaGroup :modelValue="props.contract.employee.notes" :label="$t('notes')"
                                        :disabled="true" :placeholder="$t('please_enter_a_notes')"
                                        :placeholder_message="$t('please_enter_a_notes')" class="col-span-2" />
                                </div>
                            </CardContent>
                        </Card>
                    </TabsContent>
                    <TabsContent value="contract">
                        <Card>
                            <CardHeader>
                                <CardTitle>{{ $t('contract') }}</CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-2">
                                <div class="grid grid-cols-2 gap-3 py-4">
                                    <InputGroup :modelValue="props.contract.contract.start_date"
                                        :label="$t('start_date')" :placeholder="$t('please_enter_a_start_date')"
                                        type="date" :disabled="true" />

                                    <InputGroup :modelValue="props.contract.contract.end_date" :disabled="true"
                                        :label="$t('end_date')" :placeholder="$t('please_enter_a_end_date')"
                                        type="date" />

                                    <InputGroup :modelValue="props.contract.contract.division" :disabled="true"
                                        :label="$t('division')" :placeholder="$t('please_select_a_division')" />

                                    <InputGroup :modelValue="props.contract.contract.department" :disabled="true"
                                        :label="$t('department')" :placeholder="$t('please_select_a_department')" />

                                    <InputGroup :modelValue="props.contract.contract.section" :disabled="true"
                                        :label="$t('section')" :placeholder="$t('please_select_a_section')" />

                                    <InputGroup :modelValue="props.contract.contract.position" :disabled="true"
                                        :label="$t('position')" :placeholder="$t('please_select_a_position')" />

                                    <InputGroup :modelValue="props.contract.contract.work_schedule"
                                        :label="$t('work_schedule')" :disabled="true"
                                        :placeholder="$t('please_select_a_work_schedule')" />

                                    <InputGroup :modelValue="props.contract.contract.time_management"
                                        :label="$t('time_management')" :disabled="true"
                                        :placeholder="$t('please_select_a_time_management')" />

                                    <TextareaGroup :modelValue="props.contract.contract.notes" :label="$t('notes')"
                                        :disabled="true" :placeholder="$t('please_enter_a_notes')"
                                        :placeholder_message="$t('please_enter_a_notes')" class="col-span-2" />
                                </div>
                            </CardContent>
                        </Card>
                    </TabsContent>
                </Tabs>
            </CardContent>
        </Card>
    </AppLayout>
</template>
