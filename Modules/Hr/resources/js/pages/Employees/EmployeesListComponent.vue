<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,

} from '@/components/ui/table';
import PaginationUse from '@/components/ui/pagination-use/PaginationUse.vue';
import TableEmpty from '@/components/ui/table/TableEmpty.vue';
import TableFooter from '@/components/ui/table/TableFooter.vue';
import Input from '@/components/ui/input/Input.vue';
import DeleteModal from '@/components/ui/Modal/DeleteModal.vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import Can from '@/components/ui/Auth/Can.vue';
import TablePaginationNumbers from '@/components/ui/table/TablePaginationNumbers.vue';
import {
    Card,
    CardHeader,
    CardTitle,
    CardContent,
    CardFooter
} from '@/components/ui/card';
import A from '@/components/ui/a/A.vue';
import TableActions from '@/components/ui/table/TableActions.vue';
import employeesRoute from '@/routes/hr/employees';
import { strLimit } from '@/utils/strLimit';
import { useSearchTable } from '@/composables/useSearchTable';

// Master Data
const { t } = useI18n();
const currentItem = ref<any>(null)
const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('hr'), href: null },
    { title: t('employees'), href: null },
];

const props = defineProps<{
    employees?: {
        data?: any[],
        links?: any[],
        total?: number,
        per_page?: number,
        current_page?: number
    },
}>()

// reactive search
const { search } = useSearchTable(employeesRoute.index().url, {
    name: '',
    code: '',
    name_ar: '',
    email: '',
    gender: '',
    phone: '',
    address: '',
    status: ''
});

// Delete Modal
const showDeleteModal = ref(false)
const isDeleting = ref(false)

const toggleShowDeleteModal = (work_schedule: any) => {
    currentItem.value = work_schedule
    showDeleteModal.value = true
}

const deleteEmployee = () => {
    if (!currentItem.value) return
    isDeleting.value = true

    router.delete(employeesRoute.destroy(currentItem.value.id).url, {
        onFinish: () => {
            showDeleteModal.value = false
            currentItem.value = null
            isDeleting.value = false
        },
        onError: () => {
            isDeleting.value = false
        }
    })
}
</script>

<template>

    <Head :title="$t('employees')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <CardHeader class="flex justify-between items-center">
                <CardTitle>{{ $t('employees') }}</CardTitle>
                <Can permissions="employee_create">
                    <A :href="employeesRoute.create().url"
                        class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm">
                        <i class="ri ri-add-line"></i> {{ $t("add_employee") }}
                    </A>
                </Can>
            </CardHeader>

            <CardContent>
                <Table>
                    <TableCaption>{{ $t('employees') }}</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px] text-center">{{ $t('no') }}</TableHead>
                            <TableHead class="text-center">{{ $t('code') }}</TableHead>
                            <TableHead class="text-center">{{ $t('name') }}</TableHead>
                            <TableHead class="text-center whitespace-nowrap">{{ $t('name_ar') }}</TableHead>
                            <TableHead class="text-center">{{ $t('email') }}</TableHead>
                            <TableHead class="text-center">{{ $t('gender') }}</TableHead>
                            <TableHead class="text-center">{{ $t('phone') }}</TableHead>
                            <TableHead class="text-center">{{ $t('address') }}</TableHead>
                            <TableHead class="text-center">{{ $t('status') }}</TableHead>
                            <TableHead class="text-center">{{ $t('actions') }}</TableHead>
                        </TableRow>
                        <TableRow>
                            <TableHead class="w-[100px]"></TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('code')" v-model="search.code" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('name')" v-model="search.name" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('name_ar')" v-model="search.name_ar" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('email')" v-model="search.email" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('gender')" v-model="search.gender" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('phone')" v-model="search.phone" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('address')" v-model="search.address" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('status')" v-model="search.status" />
                            </TableHead>
                            <TableHead></TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow v-for="(employee, index) in props.employees?.data || []" :key="employee.id">
                            <TableCell class="font-medium text-center">
                                <TablePaginationNumbers :items="props.employees" :index="index" />
                            </TableCell>
                            <TableCell class="text-center">{{ strLimit(employee.code, 15) }}</TableCell>
                            <TableCell class="text-center">{{ strLimit(employee.name, 15) }}</TableCell>
                            <TableCell class="text-center">{{ strLimit(employee.name_ar, 15) }}</TableCell>
                            <TableCell class="text-center">{{ strLimit(employee.email, 15) }}</TableCell>
                            <TableCell class="text-center">{{ strLimit(employee.gender_label, 15) }}</TableCell>
                            <TableCell class="text-center">{{ strLimit(employee.phone, 15) }}</TableCell>
                            <TableCell class="text-center">{{ strLimit(employee.address, 15) }}</TableCell>
                            <TableCell class="text-center">{{ strLimit(employee.status_label, 15) }}</TableCell>
                            <TableCell class="text-center flex">
                                <TableActions class="text-center flex justify-center" canShow="employee_show"
                                    :show="employeesRoute.show(employee.id).url" canEdit="employee_edit"
                                    :edit="employeesRoute.edit(employee.id).url" canDelete="employee_delete"
                                    :delete="() => toggleShowDeleteModal(employee)" />
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableFooter>
                        <TableEmpty v-if="!props.employees?.data?.length" :colspan="10">
                            {{ $t('no_data') }}
                        </TableEmpty>
                    </TableFooter>
                </Table>
            </CardContent>

            <CardFooter>
                <PaginationUse :items="props.employees?.links || []" :total="props.employees?.total || 0"
                    :itemsPerPage="props.employees?.per_page || 10" :currentPage="props.employees?.current_page || 1"
                    :defaultPage="1" />
            </CardFooter>
        </Card>
    </AppLayout>

    <Can permissions="employee_delete">
        <DeleteModal v-model:show="showDeleteModal" :item="currentItem" @confirm="deleteEmployee"
            :loading="isDeleting" />
    </Can>
</template>
