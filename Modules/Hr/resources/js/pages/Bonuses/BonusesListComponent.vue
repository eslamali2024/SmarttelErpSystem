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
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import bonusesRoute from '@/routes/hr/bonuses';
import DeleteModal from '@/components/ui/Modal/DeleteModal.vue';
import Button from '@/components/ui/button/Button.vue';
import BonuseFormDialog from './BonusFormDialog.vue';
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

// Master Data
const { t } = useI18n();
const showFormDialog = ref(false)
const currentItem = ref<any>(null)
const method_type = ref("post");
const action = ref(bonusesRoute.store().url);
const showLoading = ref(false)
const dataCached = ref<any[] | null>(null)

const { showToast } = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('hr'), href: null },
    { title: t('master_data'), href: null },
    { title: t('bonuses'), href: null },
];

const props = defineProps<{
    bonuses?: {
        data?: any[],
        links?: any[],
        total?: number,
        per_page?: number,
        current_page?: number
    },
    statuses?: any
}>()


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

        const response = await axios.get(bonusesRoute.create().url)
        dataCached.value = Array.isArray(response.data) ? response.data[0] : response.data
    } catch (error) {
        console.error('Error loading data:', error)
    }
}


/**
 * Toggle the form dialog for adding or editing a bonus
 * @param {any} item - The item to be edited, or null for adding a new item
 */
const toggleFormDialog = async (item?: any) => {
    showLoading.value = true
    showFormDialog.value = true;
    currentItem.value = item;

    await loadInitialData();

    if (item) {
        method_type.value = "put";
        action.value = bonusesRoute.update(item.id).url;
    } else {
        method_type.value = "post";
        action.value = bonusesRoute.store().url;
    }

    showLoading.value = false
}

// reactive search
const { search } = useSearchTable(bonusesRoute.index().url, {
    employee: {
        name: ''
    },
    bonusType: {
        name: ''
    },
    createdBy: {
        name: ''
    },
    status: {
        value: '',
        operator: '='
    },
    start: '25/11/2025',
    end: '28/11/2025',
    column_date_range: 'date'
});

// Date Range
const dateRange = ref<{ start: string | null; end: string | null }>({
    start: null,
    end: null,
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

const toggleShowDeleteModal = (bonus: any) => {
    currentItem.value = bonus
    showDeleteModal.value = true
}

const deleteBonuse = () => {
    if (!currentItem.value) return
    isDeleting.value = true

    router.delete(bonusesRoute.destroy(currentItem.value.id).url, {
        onFinish: () => {
            showDeleteModal.value = false
            currentItem.value = null
            isDeleting.value = false
            showToast({
                title: t('bonus_deleted_successfully'),
                type: 'success'
            })
        },
        onError: () => {
            isDeleting.value = false
            showToast({
                title: t('bonus_deleted_failed'),
                type: 'error'
            })
        }
    })
}

</script>

<template>

    <Head :title="$t('bonuses')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <CardHeader class="flex justify-between items-center">
                <CardTitle>{{ $t('bonuses') }}</CardTitle>

                <Can permissions="employee_create">
                    <Button v-on:click="toggleFormDialog(null)"
                        class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm">
                        <i class="ri ri-add-line"></i> {{ $t("add_bonus") }}
                    </Button>
                </Can>
            </CardHeader>

            <CardContent>
                <DataRange :label="$t('date_range')" class="mb-4" v-model="dateRange" />

                <Table>
                    <TableCaption>{{ $t('bonuses') }}</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px] text-center">{{ $t('no') }}</TableHead>
                            <TableHead class="text-center">{{ $t('employee') }}</TableHead>
                            <TableHead class="text-center text-nowrap">{{ $t('bonus_type') }}</TableHead>
                            <TableHead class="text-center">{{ $t('amount') }}</TableHead>
                            <TableHead class="text-center">{{ $t('date') }}</TableHead>
                            <TableHead class="text-center text-nowrap">{{ $t('created_by') }}</TableHead>
                            <TableHead class="text-center">{{ $t('status') }}</TableHead>
                            <TableHead class="text-center">{{ $t('actions') }}</TableHead>
                        </TableRow>
                        <TableRow>
                            <TableHead class="w-[100px]"></TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('employee')" v-model="search.employee.name" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('bonus_type')" v-model="search.bonusType.name" />
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
                        <TableRow v-for="(bonus, index) in props.bonuses?.data || []" :key="bonus.id">
                            <TableCell class="font-medium text-center">
                                <TablePaginationNumbers :items="props.bonuses" :index="index" />
                            </TableCell>
                            <TableCell class="text-center text-nowrap">
                                {{ strLimit(bonus.employee?.name, 15) }}
                            </TableCell>
                            <TableCell class="text-center text-nowrap">
                                {{ strLimit(bonus.bonus_type?.name, 15) }}
                            </TableCell>
                            <TableCell class="text-center text-nowrap">
                                {{ numberFormat(bonus.amount, 2, true) }}
                            </TableCell>
                            <TableCell class="text-center text-nowrap">{{ bonus.date }}</TableCell>
                            <TableCell class="text-center">{{ strLimit(bonus.created_by?.name, 15) }}</TableCell>
                            <TableCell class="text-center">
                                <TableBadge :status="bonus.status" :label="bonus.status_label" />
                            </TableCell>
                            <TableCell>
                                <TableActionsDialog class="text-center flex justify-center" canEdit="bonus_edit"
                                    :edit="() => toggleFormDialog(bonus)" canDelete="bonus_delete"
                                    :delete="() => toggleShowDeleteModal(bonus)">
                                    <template #before>
                                        <Can permissions="bonus_show">
                                            <A size="sm" :href="bonusesRoute.show(bonus.id).url"
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
                        <TableEmpty v-if="!props.bonuses?.data?.length" :colspan="8">
                            {{ $t('no_data') }}
                        </TableEmpty>
                    </TableFooter>
                </Table>
            </CardContent>
            <CardFooter>
                <PaginationUse :items="props.bonuses?.links || []" :total="props.bonuses?.total || 0"
                    :itemsPerPage="props.bonuses?.per_page || 10" :currentPage="props.bonuses?.current_page || 1"
                    :defaultPage="1" />
            </CardFooter>
        </Card>
    </AppLayout>

    <Can permissions="bonus_delete">
        <DeleteModal v-model:show="showDeleteModal" :item="currentItem" @confirm="deleteBonuse" :loading="isDeleting" />
    </Can>

    <Can :permissions="['bonus_create', 'bonus_edit']">
        <BonuseFormDialog v-model:show="showFormDialog" :method_type="method_type" :action="action" :item="currentItem"
            :loading="showLoading" :data="dataCached" />
    </Can>
</template>
