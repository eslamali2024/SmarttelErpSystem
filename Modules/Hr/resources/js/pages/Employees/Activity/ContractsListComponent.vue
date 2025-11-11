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
import { computed, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import contractsRoute from '@/routes/hr/contracts';
import DeleteModal from '@/components/ui/Modal/DeleteModal.vue';
import { useI18n } from 'vue-i18n';
import TableActions from '@/components/ui/table/TableActions.vue';
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
import TableBadgeStatus from '@/components/ui/table/TableBadgeStatus.vue';
import A from '@/components/ui/a/A.vue';
import SelectGroup from '@/components/ui/select-group/SelectGroup.vue';
import DataRange from '@/components/ui/date-range/DataRange.vue';
import { numberFormat } from '@/utils/numberFormat';
import Input from '@/components/ui/input/Input.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuLabel,
    DropdownMenuRadioGroup,
    DropdownMenuRadioItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import Button from '@/components/ui/button/Button.vue';

// Master Data
const { t } = useI18n();
const currentItem = ref<any>(null)

const { showToast } = useToast();
const props = defineProps<{
    employee: any,
    contracts?: {
        data?: any[],
        links?: any[],
        total?: number,
        per_page?: number,
        current_page?: number
    },
    statuses?: any,
    redirect_url?: string
}>();

function bindContractStatus(contract) {
    return computed({
        get: () => String(contract.status),
        set: (val) => (contract.status = Number(val)),
    })
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('hr'), href: null },
    { title: t('employees'), href: employeesRoute.index().url },
    { title: t('employee'), href: employeesRoute.show(props.employee.id).url },
    { title: t('contracts'), href: null },
];


// reactive search
const { search } = useSearchTable(employeesActivityRoute.contracts(props.employee.id).url, {
    currentPosition: {
        position: {
            name: ''
        }
    },
    timeManagement: {
        name: ''
    },
    workSchedule: {
        name: ''
    },
    status: {
        value: '',
        operator: '='
    },
    salary: {
        net_salary: '',
        gross_salary: ''
    },
    start_date: '',
    end_date: '',
});

// Date Range
const dateRange = ref<{ start: string | null; end: string | null }>({
    start: search.start_date ?? null,
    end: search.end_date ?? null,
})

watch(
    () => dateRange.value,
    (newVal) => {
        search.start_date = newVal.start
        search.end_date = newVal.end
    },
    { deep: true }
)

// Delete Modal
const showDeleteModal = ref(false)
const isDeleting = ref(false)

const toggleShowDeleteModal = (contract: any) => {
    currentItem.value = contract
    showDeleteModal.value = true
}

const deleteContract = () => {
    if (!currentItem.value) return
    isDeleting.value = true

    router.delete(contractsRoute.destroy(currentItem.value.id).url + `?redirect_url=${props.redirect_url}`, {
        onFinish: () => {
            showDeleteModal.value = false
            currentItem.value = null
            isDeleting.value = false
            showToast({
                title: t('employee_contract_deleted_successfully'),
                type: 'success'
            })
        },
        onError: () => {
            isDeleting.value = false
            showToast({
                title: t('employee_contract_deleted_failed'),
                type: 'error'
            })
        }
    })
}

/**
 * Update the status of a contract.
 *
 */
const updateStatusContract: any = (contract: any, index: number) => {
    if (bindContractStatus(contract).value == String(index)) return
    router.post(contractsRoute.changeStatus({
        contract: contract.id,
        status: Number(index)
    }).url, {
        onFinish: () => {
            showToast({
                title: t('employee_contract_status_updated_successfully'),
                type: 'success'
            })
        },
        onError: () => {
            showToast({
                title: t('employee_contract_status_updated_failed'),
                type: 'error'
            })
        }
    })
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head :title="$t('contracts')" />

        <EmployeeLayout :employee="props.employee">
            <Card>
                <CardHeader class="flex justify-between items-center">
                    <CardTitle>{{ $t('contracts') }}</CardTitle>

                    <Can permissions="employee_create">
                        <A :href="contractsRoute.create().url + `?employee_id=${props.employee.id}&redirect_url=${props.redirect_url}`"
                            class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm">
                            <i class="ri ri-add-line"></i> {{ $t("add_contract") }}
                        </A>
                    </Can>
                </CardHeader>

                <CardContent>
                    <DataRange :label="$t('date_range')" class="mb-4" v-model="dateRange" />

                    <Table>
                        <TableCaption>{{ $t('contracts') }}</TableCaption>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-[100px] text-center">{{ $t('no') }}</TableHead>
                                <TableHead class="text-center text-nowrap">{{ $t('start_date') }}</TableHead>
                                <TableHead class="text-center text-nowrap">{{ $t('end_date') }}</TableHead>
                                <TableHead class="text-center text-nowrap">{{ $t('gross_salary') }}</TableHead>
                                <TableHead class="text-center text-nowrap">{{ $t('net_salary') }}</TableHead>
                                <TableHead class="text-center text-nowrap">{{ $t('work_schedule') }}</TableHead>
                                <TableHead class="text-center text-nowrap">{{ $t('time_management') }}</TableHead>
                                <TableHead class="text-center text-nowrap">{{ $t('position') }}</TableHead>
                                <TableHead class="text-center">{{ $t('status') }}</TableHead>
                                <TableHead class="text-center">{{ $t('actions') }}</TableHead>
                            </TableRow>
                            <TableRow>
                                <TableHead class="w-[100px]"></TableHead>
                                <TableHead></TableHead>
                                <TableHead></TableHead>
                                <TableHead class="p-2">
                                    <Input :placeholder="$t('gross_salary')" v-model="search.salary.gross_salary" />
                                </TableHead>
                                <TableHead class="p-2">
                                    <Input :placeholder="$t('net_salary')" v-model="search.salary.net_salary" />
                                </TableHead>
                                <TableHead class="p-2">
                                    <Input :placeholder="$t('work_schedule')" v-model="search.workSchedule.name" />
                                </TableHead>
                                <TableHead class="p-2">
                                    <Input :placeholder="$t('time_management')" v-model="search.timeManagement.name" />
                                </TableHead>
                                <TableHead class="p-2">
                                    <Input :placeholder="$t('position')"
                                        v-model="search.currentPosition.position.name" />
                                </TableHead>

                                <TableHead class="p-2">
                                    <SelectGroup :options="props.statuses" :placeholder="$t('status')"
                                        v-model="search.status.value" />
                                </TableHead>
                                <TableHead></TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            <TableRow v-for="(contract, index) in props.contracts?.data || []" :key="contract.id">
                                <TableCell class="font-medium text-center">
                                    <TablePaginationNumbers :items="props.contracts" :index="index" />
                                </TableCell>
                                <TableCell class="text-center">
                                    {{ contract.start_date }}
                                </TableCell>
                                <TableCell class="text-center">
                                    {{ contract.end_date }}
                                </TableCell>
                                <TableCell class="text-center">
                                    {{ numberFormat(contract.salary?.gross_salary, 3) }}
                                </TableCell>
                                <TableCell class="text-center">
                                    {{ numberFormat(contract.salary?.net_salary, 3) }}
                                </TableCell>
                                <TableCell class="text-center">
                                    {{ strLimit(contract.work_schedule?.name, 15) }}
                                </TableCell>
                                <TableCell class="text-center">
                                    {{ strLimit(contract.time_management?.name, 15) }}
                                </TableCell>
                                <TableCell class="text-center">
                                    {{ strLimit(contract.current_position?.position?.name, 15) }}
                                </TableCell>
                                <TableCell class="text-center flex justify-center gap-2 items-center">
                                    <TableBadgeStatus :status="contract.status" :label="contract.status_label" />

                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child class="p-2">
                                            <Button variant="outline">
                                                <i class="ri-skip-down-line"></i>
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent class="w-56">
                                            <DropdownMenuLabel>{{ $t('change_status') }}</DropdownMenuLabel>
                                            <DropdownMenuSeparator />
                                            <DropdownMenuRadioGroup :v-model="bindContractStatus(contract)">
                                                <DropdownMenuRadioItem v-for="(status, index) in props.statuses"
                                                    v-on:click="updateStatusContract(contract, index)" :key="index"
                                                    :value="String(index)">
                                                    {{ status }}
                                                </DropdownMenuRadioItem>
                                            </DropdownMenuRadioGroup>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </TableCell>
                                <TableCell>
                                    <TableActions class="text-center flex justify-center"
                                        canShow="employee_contract_show" :show="contractsRoute.show(contract.id).url"
                                        canEdit="employee_contract_edit" :edit="contractsRoute.edit(contract.id).url"
                                        canDelete="employee_contract_delete"
                                        :delete="() => toggleShowDeleteModal(contract)" />
                                </TableCell>
                            </TableRow>
                        </TableBody>

                        <TableFooter>
                            <TableEmpty v-if="!props.contracts?.data?.length" :colspan="10">
                                {{ $t('no_data') }}
                            </TableEmpty>
                        </TableFooter>
                    </Table>
                </CardContent>
                <CardFooter>
                    <PaginationUse :items="props.contracts?.links || []" :total="props.contracts?.total || 0"
                        :itemsPerPage="props.contracts?.per_page || 10"
                        :currentPage="props.contracts?.current_page || 1" :defaultPage="1" />
                </CardFooter>
            </Card>
        </EmployeeLayout>
    </AppLayout>

    <Can permissions="employee_contract_delete">
        <DeleteModal v-model:show="showDeleteModal" :item="currentItem" @confirm="deleteContract"
            :loading="isDeleting" />
    </Can>
</template>
