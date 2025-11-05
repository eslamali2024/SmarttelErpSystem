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
import timeManagementsRoute from '@/routes/hr/master-data/time-managements';
import DeleteModal from '@/components/ui/Modal/DeleteModal.vue';
import Button from '@/components/ui/button/Button.vue';
import TimeMaangementFormDialog from './TimeManagementFormDialog.vue';
import TimeMaangementShowDialog from './TimeManagementShowDialog.vue';
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
import { useStrLimit } from '@/composables/useStrLimit';
import { useSearchTable } from '@/composables/useSearchTable';

// Master Data
const { t } = useI18n();
const showFormDialog = ref(false)
const currentItem = ref<any>(null)
const method_type = ref("post");
const action = ref(timeManagementsRoute.store().url);
const showLoading = ref(false)

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('hr'), href: null },
    { title: t('master_data'), href: null },
    { title: t('time_managements'), href: null },
];

const props = defineProps<{
    time_managements?: {
        data?: any[],
        links?: any[],
        total?: number,
        per_page?: number,
        current_page?: number
    }
}>()


/**
 * Toggle the form dialog for adding or editing a time_management
 * @param {any} item - The item to be edited, or null for adding a new item
 */
const toggleFormDialog = (item?: any) => {
    showLoading.value = true
    showFormDialog.value = true;
    currentItem.value = item;

    if (item) {
        method_type.value = "put";
        action.value = timeManagementsRoute.update(item.id).url;
    } else {
        method_type.value = "post";
        action.value = timeManagementsRoute.store().url;
    }

    showLoading.value = false
}

// reactive search
const { search } = useSearchTable(timeManagementsRoute.index().url);

// Delete Modal
const showDeleteModal = ref(false)
const isDeleting = ref(false)

const toggleShowDeleteModal = (time_management: any) => {
    currentItem.value = time_management
    showDeleteModal.value = true
}

const deleteTimeMaangement = () => {
    if (!currentItem.value) return
    isDeleting.value = true

    router.delete(timeManagementsRoute.destroy(currentItem.value.id).url, {
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

// Show Modal
const showShowDialog = ref(false)
const toggleShowDialog = (time_management: any) => {
    currentItem.value = time_management
    showShowDialog.value = true
}
</script>

<template>

    <Head :title="$t('time_managements')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <CardHeader class="flex justify-between items-center">
                <CardTitle>{{ $t('time_managements') }}</CardTitle>
                <Can permissions="time_management_create">
                    <Button v-on:click="toggleFormDialog(null)"
                        class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm">
                        <i class="ri ri-add-line"></i> {{ $t("add_time_management") }}
                    </Button>
                </Can>
            </CardHeader>

            <CardContent>
                <Table>
                    <TableCaption>{{ $t('time_managements') }}</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px] text-center">{{ $t('no') }}</TableHead>
                            <TableHead class="text-center">{{ $t('name') }}</TableHead>
                            <TableHead class="text-center">{{ $t('payroll') }}</TableHead>
                            <TableHead class="text-center">{{ $t('fingerprint_in') }}</TableHead>
                            <TableHead class="text-center">{{ $t('fingerprint_out') }}</TableHead>
                            <TableHead class="text-center">{{ $t('factors') }}</TableHead>
                            <TableHead class="text-center">{{ $t('actions') }}</TableHead>
                        </TableRow>
                        <TableRow>
                            <TableHead class="w-[100px]"></TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('name')" v-model="search.name" />
                            </TableHead>
                            <TableHead class="p-2"></TableHead>
                            <TableHead class="p-2"></TableHead>
                            <TableHead class="p-2"></TableHead>
                            <TableHead></TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow v-for="(time_management, index) in props.time_managements?.data || []"
                            :key="time_management.id">
                            <TableCell class="font-medium text-center">
                                <TablePaginationNumbers :items="props.time_managements" :index="index" />
                            </TableCell>
                            <TableCell class="text-center">
                                {{ useStrLimit(time_management.name, 15) }}
                            </TableCell>
                            <TableCell class="text-center">
                                {{ time_management.payroll ? $t('yes') : $t('no') }}
                            </TableCell>
                            <TableCell class="text-center">
                                {{ time_management.fingerprint_in ? $t('yes') : $t('no') }}
                            </TableCell>
                            <TableCell class="text-center">
                                {{ time_management.fingerprint_out ? $t('yes') : $t('no') }}
                            </TableCell>
                            <TableCell class="text-center">
                                {{ time_management.factors ? $t('yes') : $t('no') }}
                            </TableCell>
                            <TableCell>
                                <TableActionsDialog class="text-center flex justify-center"
                                    canShow="time_management_show" :show="() => toggleShowDialog(time_management)"
                                    canEdit="time_management_edit" :edit="() => toggleFormDialog(time_management)"
                                    canDelete="time_management_delete"
                                    :delete="() => toggleShowDeleteModal(time_management)" />
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableFooter>
                        <TableEmpty v-if="!props.time_managements?.data?.length" :colspan="7">
                            {{ $t('no_data') }}
                        </TableEmpty>
                    </TableFooter>
                </Table>
            </CardContent>
            <CardFooter>
                <PaginationUse :items="props.time_managements?.links || []" :total="props.time_managements?.total || 0"
                    :itemsPerPage="props.time_managements?.per_page || 10"
                    :currentPage="props.time_managements?.current_page || 1" :defaultPage="1" />
            </CardFooter>
        </Card>
    </AppLayout>

    <Can permissions="time_management_delete">
        <DeleteModal v-model:show="showDeleteModal" :item="currentItem" @confirm="deleteTimeMaangement"
            :loading="isDeleting" />
    </Can>

    <Can permissions="time_management_show">
        <TimeMaangementShowDialog v-model:show="showShowDialog" :item="currentItem" />
    </Can>

    <Can :permissions="['time_management_create', 'time_management_edit']">
        <TimeMaangementFormDialog v-model:show="showFormDialog" :method_type="method_type" :action="action"
            :types="props?.types" :taxables="props.taxables" :item="currentItem" :loading="showLoading" />
    </Can>
</template>
