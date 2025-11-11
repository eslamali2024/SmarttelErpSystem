<script setup lang="ts">
import EmployeeLayout from '@modules/Hr/resources/js/layouts/employee/Layout.vue';
import employeesRoute from '@/routes/hr/employees';
import employeesActivityRoute from '@/routes/hr/employees/activity';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
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
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import deductionsRoute from '@/routes/hr/deductions';
import DeleteModal from '@/components/ui/Modal/DeleteModal.vue';
import Button from '@/components/ui/button/Button.vue';

import { useI18n } from 'vue-i18n';
import TableActionsDialog from '@/components/ui/table/TableActionsDialog.vue';
import TablePaginationNumbers from '@/components/ui/table/TablePaginationNumbers.vue';
import Can from '@/components/ui/Auth/Can.vue';
import {
    Card,
    CardHeader,
    CardTitle,
    CardContent,
    CardFooter
} from '@/components/ui/card';
import { strLimit } from '@/utils/strLimit';
import { useSearchTable } from '@/composables/useSearchTable';
import { useToast } from '@/composables/useToast';
import axios from 'axios';
import { numberFormat } from '@/utils/numberFormat';
import TableBadge from '@/components/ui/table/TableBadge.vue';
import A from '@/components/ui/a/A.vue';
import SelectGroup from '@/components/ui/select-group/SelectGroup.vue';
import DataRange from '@/components/ui/date-range/DataRange.vue';
import DeductionFormDialog from '../../Deductions/DeductionFormDialog.vue';

// Master Data
const { t } = useI18n();
const showFormDialog = ref(false)
const currentItem = ref<any>(null)
const method_type = ref("post");
const action = ref(deductionsRoute.store().url);
const showLoading = ref(false)
const dataCached = ref<any[] | null>(null)

const { showToast } = useToast();
const props = defineProps<{
    employee: any,
    deductions?: {
        data?: any[],
        links?: any[],
        total?: number,
        per_page?: number,
        current_page?: number
    },
    statuses?: any,
    redirect_url?: string
}>();


const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('hr'), href: null },
    { title: t('employees'), href: employeesRoute.index().url },
    { title: t('employee'), href: employeesRoute.show(props.employee.id).url },
    { title: t('deductions'), href: null },
];

/**
 * Loads the initial data from the server and stores it in the reactive cache.
 * If the cache already has data, it will use the cached value instead of making a request.
 * @returns {Promise<void>}
 */
async function loadInitialData(): Promise<void> {
    try {
        if (dataCached.value) {
            return
        }

        const response = await axios.get(deductionsRoute.create().url)
        dataCached.value = Array.isArray(response.data) ? response.data[0] : response.data
    } catch (error) {
        console.error('Error loading data:', error)
    }
}


/**
 * Toggle the form dialog for adding or editing a deduction
 * @param {any} item - The item to be edited, or null for adding a new item
 */
const toggleFormDialog = async (item?: any) => {
    showLoading.value = true
    showFormDialog.value = true;
    currentItem.value = item;

    await loadInitialData();

    if (item) {
        method_type.value = "put";
        action.value = deductionsRoute.update(item.id).url;
    } else {
        method_type.value = "post";
        action.value = deductionsRoute.store().url;
    }

    showLoading.value = false
}

// reactive search
const { search } = useSearchTable(employeesActivityRoute.deductions(props.employee.id).url, {
    deductionType: {
        name: ''
    },
    createdBy: {
        name: ''
    },
    status: {
        value: '',
        operator: '='
    },
    start: '',
    end: '',
    column_date_range: 'date'
});

// Date Range
const dateRange = ref<{ start: string | null; end: string | null }>({
    start: search.start ?? null,
    end: search.end ?? null,
})

watch(
    () => dateRange.value,
    (newVal) => {
        search.start = newVal.start
        search.end = newVal.end
        search.column_date_range = 'date'
    },
    { deep: true }
)

// Delete Modal
const showDeleteModal = ref(false)
const isDeleting = ref(false)

const toggleShowDeleteModal = (deduction: any) => {
    currentItem.value = deduction
    showDeleteModal.value = true
}

const deleteDeduction = () => {
    if (!currentItem.value) return
    isDeleting.value = true

    router.delete(deductionsRoute.destroy(currentItem.value.id).url + `?redirect_url=${props.redirect_url}`, {
        onFinish: () => {
            showDeleteModal.value = false
            currentItem.value = null
            isDeleting.value = false
            showToast({
                title: t('deduction_deleted_successfully'),
                type: 'success'
            })
        },
        onError: () => {
            isDeleting.value = false
            showToast({
                title: t('deduction_deleted_failed'),
                type: 'error'
            })
        }
    })
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head :title="$t('deductions')" />

        <EmployeeLayout :employee="props.employee">
            <Card>
                <CardHeader class="flex justify-between items-center">
                    <CardTitle>{{ $t('deductions') }}</CardTitle>

                    <Can permissions="employee_create">
                        <Button v-on:click="toggleFormDialog(null)"
                            class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm">
                            <i class="ri ri-add-line"></i> {{ $t("add_deduction") }}
                        </Button>
                    </Can>
                </CardHeader>

                <CardContent>
                    <DataRange :label="$t('date_range')" class="mb-4" v-model="dateRange" />

                    <Table>
                        <TableCaption>{{ $t('deductions') }}</TableCaption>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-[100px] text-center">{{ $t('no') }}</TableHead>
                                <TableHead class="text-center text-nowrap">{{ $t('deduction_type') }}</TableHead>
                                <TableHead class="text-center">{{ $t('amount') }}</TableHead>
                                <TableHead class="text-center">{{ $t('date') }}</TableHead>
                                <TableHead class="text-center text-nowrap">{{ $t('created_by') }}</TableHead>
                                <TableHead class="text-center">{{ $t('status') }}</TableHead>
                                <TableHead class="text-center">{{ $t('actions') }}</TableHead>
                            </TableRow>
                            <TableRow>
                                <TableHead class="w-[100px]"></TableHead>
                                <TableHead class="p-2">
                                    <Input :placeholder="$t('deduction_type')" v-model="search.deductionType.name" />
                                </TableHead>
                                <TableHead>
                                    <Input :placeholder="$t('amount')" v-model="search.amount" type="number" />
                                </TableHead>
                                <TableHead>
                                    <Input :placeholder="$t('date')" v-model="search.date" type="date" />
                                </TableHead>
                                <TableHead class="p-2">
                                    <Input :placeholder="$t('created_by')" v-model="search.createdBy.name" />
                                </TableHead>
                                <TableHead class="p-2">
                                    <SelectGroup :options="props.statuses" :placeholder="$t('status')"
                                        v-model="search.status.value" />
                                </TableHead>
                                <TableHead></TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            <TableRow v-for="(deduction, index) in props.deductions?.data || []" :key="deduction.id">
                                <TableCell class="font-medium text-center">
                                    <TablePaginationNumbers :items="props.deductions" :index="index" />
                                </TableCell>
                                <TableCell class="text-center text-nowrap">
                                    {{ strLimit(deduction.deduction_type?.name, 15) }}
                                </TableCell>
                                <TableCell class="text-center text-nowrap">
                                    {{ numberFormat(deduction.amount, 2, true) }}
                                </TableCell>
                                <TableCell class="text-center text-nowrap">{{ deduction.date }}</TableCell>
                                <TableCell class="text-center">{{ strLimit(deduction.created_by?.name, 15) }}
                                </TableCell>
                                <TableCell class="text-center">
                                    <TableBadge :status="deduction.status" :label="deduction.status_label" />
                                </TableCell>
                                <TableCell>
                                    <TableActionsDialog class="text-center flex justify-center" canEdit="deduction_edit"
                                        :edit="() => toggleFormDialog(deduction)" canDelete="deduction_delete"
                                        :delete="() => toggleShowDeleteModal(deduction)">
                                        <template #before>
                                            <Can permissions="deduction_show">
                                                <A size="sm" :href="deductionsRoute.show(deduction.id).url"
                                                    class="me-2 bg-blue-500 cursor-pointer text-white hover:bg-blue-600">
                                                    <i class="ri ri-eye-line"></i>
                                                </A>
                                            </Can>
                                        </template>
                                    </TableActionsDialog>
                                </TableCell>
                            </TableRow>
                        </TableBody>

                        <TableFooter>
                            <TableEmpty v-if="!props.deductions?.data?.length" :colspan="8">
                                {{ $t('no_data') }}
                            </TableEmpty>
                        </TableFooter>
                    </Table>
                </CardContent>
                <CardFooter>
                    <PaginationUse :items="props.deductions?.links || []" :total="props.deductions?.total || 0"
                        :itemsPerPage="props.deductions?.per_page || 10"
                        :currentPage="props.deductions?.current_page || 1" :defaultPage="1" />
                </CardFooter>
            </Card>
        </EmployeeLayout>
    </AppLayout>

    <Can permissions="deduction_delete">
        <DeleteModal v-model:show="showDeleteModal" :item="currentItem" @confirm="deleteDeduction"
            :loading="isDeleting" />
    </Can>

    <Can :permissions="['deduction_create', 'deduction_edit']">
        <DeductionFormDialog v-model:show="showFormDialog" :method_type="method_type" :action="action"
            :item="currentItem" :loading="showLoading" :data="dataCached" :employee_id="props.employee?.id ?? null"
            :redirect_url="props.redirect_url" />
    </Can>
</template>
