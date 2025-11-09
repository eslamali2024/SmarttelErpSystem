<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import employeesRoute from '@/routes/hr/employees';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Avatar } from '@/components/ui/avatar';
import AvatarImage from '@/components/ui/avatar/AvatarImage.vue';
import AvatarFallback from '@/components/ui/avatar/AvatarFallback.vue';
import { strLimit } from '@/utils/strLimit';
import EmployeePhotoDialog from './EmployeePhotoDialog.vue';
import { ref } from 'vue';
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import {
    Tabs,
    TabsContent,
    TabsList,
    TabsTrigger,
} from "@/components/ui/tabs"
import TextareaGroup from '@/components/ui/textarea-group/TextareaGroup.vue';
import A from '@/components/ui/a/A.vue';
import Can from '@/components/ui/Auth/Can.vue';

interface Props {
    employee?: any,
    canEditAvataer?: boolean
}

const { t } = useI18n();
const props = defineProps<Props>();
const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('hr'), href: null },
    { title: t('employees'), href: employeesRoute.index().url },
    { title: t('show_employee'), href: null },
];

// Show Photo Dialog
const showPhoto = ref(false)
const togglePhotoDialog = () => {
    showPhoto.value = !showPhoto.value
}
</script>

<template>

    <Head :title="$t('show_employee')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card class="p">
            <CardHeader>
                <div class="w-full bg-gray-600/50 rounded-lg h-[200px] flex justify-between items-center">
                    <span></span>
                    <Avatar class="w-24 h-24 border-2 border-white cursor-pointer hover:opacity-75 duration-200"
                        @click="togglePhotoDialog">
                        <AvatarImage :src="props.employee.avatar ?? '#'" alt="@unovue" />
                        <AvatarFallback class="text-black dark:text-white text-4xl">
                            {{ strLimit(props.employee?.name, 2, '-', '') }}
                        </AvatarFallback>
                    </Avatar>

                    <Can permissions="employee_edit">
                        <span class="self-baseline p-4">
                            <A :href="employeesRoute.edit(props.employee.id).url"
                                class="bg-yellow-600 hover:bg-yellow-500 duration-200 text-white text-md">
                                <i class="ri ri-edit-line"></i>
                            </A>
                        </span>
                    </Can>
                </div>
            </CardHeader>
            <CardContent>
                <Tabs default-value="basic-data" class="w-full">
                    <TabsList class="grid w-full grid-cols-2">
                        <TabsTrigger value="basic-data" class="cursor-pointer">
                            {{ $t('basic_data') }}
                        </TabsTrigger>
                        <TabsTrigger value="contract" class="cursor-pointer">
                            {{ $t('contract') }}
                        </TabsTrigger>
                    </TabsList>
                    <TabsContent value="basic-data">
                        <Card>
                            <CardHeader>
                                <CardTitle>{{ $t('basic_data') }}</CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-2">
                                <div class="grid grid-cols-2 gap-3 py-4">
                                    <InputGroup :disabled="true" v-model="props.employee.code"
                                        :label="$t('employee_code')" :placeholder="$t('please_enter_a_employee_code')"
                                        type="text" class="col-span-2" />

                                    <InputGroup :disabled="true" v-model="props.employee.name" :label="$t('name')"
                                        :placeholder="$t('please_enter_a_name')" type="text" />

                                    <InputGroup :disabled="true" v-model="props.employee.name_ar" :label="$t('name_ar')"
                                        :placeholder="$t('please_enter_a_name_ar')" type="text" />

                                    <InputGroup :disabled="true" v-model="props.employee.email" :label="$t('email')"
                                        :placeholder="$t('please_enter_a_email')" type="email" />

                                    <InputGroup :disabled="true" v-model="props.employee.phone" :label="$t('phone')"
                                        :placeholder="$t('please_enter_a_phone')" type="text" />

                                    <InputGroup :disabled="true" v-model="props.employee.gender_label"
                                        :label="$t('gender')" :placeholder="$t('please_select_a_department')" />

                                    <InputGroup :disabled="true" v-model="props.employee.marital_status_label"
                                        :label="$t('marital_status')"
                                        :placeholder="$t('please_select_a_marital_status')" />

                                    <InputGroup :disabled="true" v-model="props.employee.national_id"
                                        :label="$t('national_id')" :placeholder="$t('please_enter_a_national_id')"
                                        type="text" />

                                    <InputGroup :disabled="true" v-model="props.employee.joining_date"
                                        :label="$t('joining_date')" :placeholder="$t('please_enter_a_joining_date')"
                                        type="date" />

                                    <InputGroup :disabled="true" v-model="props.employee.dob" :label="$t('dob')"
                                        :placeholder="$t('please_enter_a_dob')" type="date" />

                                    <TextareaGroup v-model="props.employee.address" :label="$t('address')"
                                        :disabled="true" :placeholder="$t('please_enter_a_address')"
                                        :placeholder_message="$t('please_enter_a_address')" class="col-span-2" />

                                    <TextareaGroup v-model="props.employee.notes" :label="$t('notes')" :disabled="true"
                                        :placeholder="$t('please_enter_a_notes')"
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
                                    <InputGroup v-model="props.employee.contract.start_date" :label="$t('start_date')"
                                        :placeholder="$t('please_enter_a_start_date')" type="date" />

                                    <InputGroup v-model="props.employee.contract.end_date" :disabled="true"
                                        :label="$t('end_date')" :placeholder="$t('please_enter_a_end_date')"
                                        type="date" />

                                    <InputGroup v-model="props.employee.contract.division" :disabled="true"
                                        :label="$t('division')" :placeholder="$t('please_select_a_division')" />

                                    <InputGroup v-model="props.employee.contract.department" :disabled="true"
                                        :label="$t('department')" :placeholder="$t('please_select_a_department')" />

                                    <InputGroup v-model="props.employee.contract.section" :disabled="true"
                                        :label="$t('section')" :placeholder="$t('please_select_a_section')" />

                                    <InputGroup v-model="props.employee.contract.position" :disabled="true"
                                        :label="$t('position')" :placeholder="$t('please_select_a_position')" />

                                    <InputGroup v-model="props.employee.contract.work_schedule"
                                        :label="$t('work_schedule')" :disabled="true"
                                        :placeholder="$t('please_select_a_work_schedule')" />

                                    <InputGroup v-model="props.employee.contract.time_management"
                                        :label="$t('time_management')" :disabled="true"
                                        :placeholder="$t('please_select_a_time_management')" />

                                    <TextareaGroup v-model="props.employee.contract.notes" :label="$t('notes')"
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

    <EmployeePhotoDialog :item="props.employee" v-model:show="showPhoto" :canEdit="props.canEditAvataer" />
</template>
