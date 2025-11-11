<script setup lang="ts">
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
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import leaveTypesRoute from '@/routes/hr/master-data/leave-types';
import DeleteModal from '@/components/ui/Modal/DeleteModal.vue';
import Button from '@/components/ui/button/Button.vue';
import LeaveTypeFormDialog from './LeaveTypeFormDialog.vue';
import LeaveTypeShowDialog from './LeaveTypeShowDialog.vue';
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
import { DropdownMenu } from '@/components/ui/dropdown-menu';
import DropdownMenuTrigger from '@/components/ui/dropdown-menu/DropdownMenuTrigger.vue';
import DropdownMenuContent from '@/components/ui/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import DropdownMenuSeparator from '@/components/ui/dropdown-menu/DropdownMenuSeparator.vue';
import ImportDialog from '@/components/ImportDialog.vue';
import SelectGroup from '@/components/ui/select-group/SelectGroup.vue';
import { numberFormat } from '@/utils/numberFormat';

// Master Data
const { t } = useI18n();
const showFormDialog = ref(false)
const showImportDialog = ref(false)
const currentItem = ref<any>(null)
const method_type = ref("post");
const action = ref(leaveTypesRoute.store().url);
const showLoading = ref(false)
const { showToast } = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('hr'), href: null },
    { title: t('master_data'), href: null },
    { title: t('leave_types'), href: null },
];

const props = defineProps<{
    leave_types?: {
        data?: any[],
        links?: any[],
        total?: number,
        per_page?: number,
        current_page?: number
    },
    units?: {
        id: number
        name: string
    },
    durations?: {
        id: number
        name: string
    }
}>()


/**
 * Toggle the form dialog for adding or editing a leave_type
 * @param {any} item - The item to be edited, or null for adding a new item
 */
const toggleFormDialog = (item?: any) => {
    showLoading.value = true
    showFormDialog.value = true;
    currentItem.value = item;

    if (item) {
        method_type.value = "put";
        action.value = leaveTypesRoute.update(item.id).url;
    } else {
        method_type.value = "post";
        action.value = leaveTypesRoute.store().url;
    }

    showLoading.value = false
}

// reactive search
const { search } = useSearchTable(leaveTypesRoute.index().url, {
    unit: {
        value: '',
        operator: '='
    },
    type_duration: {
        value: '',
        operator: '='
    },
});

// Delete Modal
const showDeleteModal = ref(false)
const isDeleting = ref(false)

const toggleShowDeleteModal = (leave_type: any) => {
    currentItem.value = leave_type
    showDeleteModal.value = true
}

const deleteLeaveType = () => {
    if (!currentItem.value) return
    isDeleting.value = true

    router.delete(leaveTypesRoute.destroy(currentItem.value.id).url, {
        onFinish: () => {
            showDeleteModal.value = false
            currentItem.value = null
            isDeleting.value = false
            showToast({
                title: t('leave_type_deleted_successfully'),
                type: 'success'
            })
        },
        onError: () => {
            isDeleting.value = false
            showToast({
                title: t('leave_type_deleted_failed'),
                type: 'error'
            })
        }
    })
}

// Show Modal
const showShowDialog = ref(false)
const toggleShowDialog = (leave_type: any) => {
    currentItem.value = leave_type
    showShowDialog.value = true
}

/**
 * Toggle the import dialog for importing public holidays
 */
const toggleImportialog = () => {
    showImportDialog.value = true;

    action.value = leaveTypesRoute.import().url
    method_type.value = "post"
}
</script>

<template>

    <Head :title="$t('leave_types')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <CardHeader class="flex justify-between items-center">
                <CardTitle>{{ $t('leave_types') }}</CardTitle>
                <Can permissions="leave_type_create">
                    <DropdownMenu>
                        <DropdownMenuTrigger>
                            <Button
                                class="cursor-pointer bg-gray-600/70 hover:bg-gray-600/80 dark:bg-gray-50/70 dark:hover:bg-gray-50/80 ">
                                <i class="ri ri-settings-3-line"></i>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent>
                            <DropdownMenuItem>
                                <button v-on:click="toggleFormDialog(null)" class="cursor-pointer">
                                    <i class="ri ri-add-line"></i> {{ $t("add_leave_type") }}
                                </button>
                            </DropdownMenuItem>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem>
                                <a :href="leaveTypesRoute.downloadTemplate().url" rel="noopener"
                                    class="btn btn-outline-primary">
                                    <i class="ri ri-download-2-line me-2"></i>
                                    {{ $t('download_sample') }}
                                </a>
                            </DropdownMenuItem>
                            <DropdownMenuItem>
                                <button v-on:click="toggleImportialog()" class="cursor-pointer">
                                    <i class="ri ri-file-excel-2-line"></i> {{ $t("import_leave_types") }}
                                </button>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </Can>
            </CardHeader>

            <CardContent>
                <Table>
                    <TableCaption>{{ $t('leave_types') }}</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px] text-center">{{ $t('no') }}</TableHead>
                            <TableHead class="text-center">{{ $t('name') }}</TableHead>
                            <TableHead class="text-center">{{ $t('unit') }}</TableHead>
                            <TableHead class="text-center">{{ $t('duration') }}</TableHead>
                            <TableHead class="text-center text-nowrap">{{ $t('for_employee') }}</TableHead>
                            <TableHead class="text-center text-nowrap">{{ $t('company_amount') }}</TableHead>
                            <TableHead class="text-center text-nowrap">
                                {{ $t('duration_deducted_percentage') }}
                            </TableHead>
                            <TableHead class="text-center text-nowrap">
                                {{ $t('salary_deducted_percentage') }}
                            </TableHead>
                            <TableHead class="text-center">{{ $t('actions') }}</TableHead>
                        </TableRow>
                        <TableRow>
                            <TableHead class="w-[100px]"></TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('name')" v-model="search.name" />
                            </TableHead>
                            <TableHead class="p-2">
                                <SelectGroup v-model="search.unit.value" :placeholder="$t('please_select_a_unit')"
                                    :options="props.units || []" />
                            </TableHead>
                            <TableHead class="p-2">
                                <SelectGroup v-model="search.type_duration.value"
                                    :placeholder="$t('please_select_a_duration')" :options="props.durations || []" />
                            </TableHead>
                            <TableHead></TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('company_amount')" v-model="search.company_amount"
                                    type="number" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('duration_deducted_percentage')"
                                    v-model="search.duration_deducted_percentage" type="number" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('salary_deducted_percentage')"
                                    v-model="search.salary_deducted_percentage" type="number" />
                            </TableHead>
                            <TableHead></TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow v-for="(leave_type, index) in props.leave_types?.data || []" :key="leave_type.id">
                            <TableCell class="font-medium text-center">
                                <TablePaginationNumbers :items="props.leave_types" :index="index" />
                            </TableCell>
                            <TableCell class="text-center">{{ strLimit(leave_type.name, 15) }}</TableCell>
                            <TableCell class="text-center">{{ strLimit(leave_type.unit_label, 15) }}</TableCell>
                            <TableCell class="text-center">
                                {{ strLimit(leave_type.type_duration_label, 15) }}
                            </TableCell>
                            <TableCell class="text-center">
                                {{ leave_type.for_employee ? $t('yes') : $t('no') }}
                            </TableCell>
                            <TableCell class="text-center">{{ numberFormat(leave_type.company_amount, 1) }}</TableCell>
                            <TableCell class="text-center">
                                {{ numberFormat(leave_type.duration_deducted_percentage, 1) }}
                            </TableCell>
                            <TableCell class="text-center">
                                {{ numberFormat(leave_type.salary_deducted_percentage, 1) }}
                            </TableCell>
                            <TableCell>
                                <TableActionsDialog class="text-center flex justify-center" canShow="leave_type_show"
                                    :show="() => toggleShowDialog(leave_type)" canEdit="leave_type_edit"
                                    :edit="() => toggleFormDialog(leave_type)" canDelete="leave_type_delete"
                                    :delete="() => toggleShowDeleteModal(leave_type)" />
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableFooter>
                        <TableEmpty v-if="!props.leave_types?.data?.length" :colspan="9">
                            {{ $t('no_data') }}
                        </TableEmpty>
                    </TableFooter>
                </Table>
            </CardContent>
            <CardFooter>
                <PaginationUse :items="props.leave_types?.links || []" :total="props.leave_types?.total || 0"
                    :itemsPerPage="props.leave_types?.per_page || 10"
                    :currentPage="props.leave_types?.current_page || 1" :defaultPage="1" />
            </CardFooter>
        </Card>
    </AppLayout>

    <Can permissions="leave_type_delete">
        <DeleteModal v-model:show="showDeleteModal" :item="currentItem" @confirm="deleteLeaveType"
            :loading="isDeleting" />
    </Can>

    <Can permissions="leave_type_show">
        <LeaveTypeShowDialog v-model:show="showShowDialog" :item="currentItem" />
    </Can>

    <Can :permissions="['leave_type_create', 'leave_type_edit']">
        <LeaveTypeFormDialog v-model:show="showFormDialog" :method_type="method_type" :action="action"
            :item="currentItem" :loading="showLoading" :units="props.units" :durations="props.durations" />

        <ImportDialog v-model:show="showImportDialog" :action="action" :item="currentItem" />
    </Can>
</template>
