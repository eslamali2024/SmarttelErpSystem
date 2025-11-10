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
import positionsRoute from '@/routes/hr/organization/positions';
import DeleteModal from '@/components/ui/Modal/DeleteModal.vue';
import Button from '@/components/ui/button/Button.vue';
import PositionFormDialog from './PositionFormDialog.vue';
import PositionShowDialog from './PositionShowDialog.vue';
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
import { useSearchTable } from '@/composables/useSearchTable';
import { strLimit } from '@/utils/strLimit';
import { useToast } from '@/composables/useToast';

// Master Data
const { t } = useI18n();
const { showToast } = useToast();
const showFormDialog = ref(false)
const currentItem = ref<any>(null)
const method_type = ref("post");
const action = ref(positionsRoute.store().url);
const showLoading = ref(false)

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('hr'), href: null },
    { title: t('organization'), href: null },
    { title: t('positions'), href: null },
];

const props = defineProps<{
    positions?: {
        data?: any[],
        links?: any[],
        total?: number,
        per_page?: number,
        current_page?: number
    },
    divisions?: {
        id: number
        name: string
    },
    departments?: {
        id: number
        name: string
        division_id: number
    },
    sections?: {
        id: number
        name: string
        department_id: number
    }
}>()


/**
 * Toggle the form dialog for adding or editing a position
 * @param {any} item - The item to be edited, or null for adding a new item
 */
const toggleFormDialog = (item?: any) => {
    showLoading.value = true
    showFormDialog.value = true;
    currentItem.value = item;

    if (item) {
        method_type.value = "put";
        action.value = positionsRoute.update(item.id).url;
    } else {
        method_type.value = "post";
        action.value = positionsRoute.store().url;
    }

    showLoading.value = false
}

// reactive search
const { search } = useSearchTable(positionsRoute.index().url, {
    name: '',
    code: '',
    department: {
        name: ''
    },
    division: {
        name: ''
    },
    section: {
        name: ''
    },
});

// Delete Modal
const showDeleteModal = ref(false)
const isDeleting = ref(false)

const toggleShowDeleteModal = (position: any) => {
    currentItem.value = position
    showDeleteModal.value = true
}

const deletePosition = () => {
    if (!currentItem.value) return
    isDeleting.value = true

    router.delete(positionsRoute.destroy(currentItem.value.id).url, {
        onFinish: () => {
            showDeleteModal.value = false
            currentItem.value = null
            isDeleting.value = false
            showToast({
                title: t('position_deleted_successfully'),
                type: 'success'
            })
        },
        onError: () => {
            isDeleting.value = false
            showToast({
                title: t('position_deleted_failed'),
                type: 'error'
            })
        }
    })
}

// Show Modal
const showShowDialog = ref(false)
const toggleShowDialog = (position: any) => {
    currentItem.value = position
    showShowDialog.value = true
}
</script>

<template>

    <Head :title="$t('positions')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <CardHeader class="flex justify-between items-center">
                <CardTitle>{{ $t('positions') }}</CardTitle>
                <Can permissions="position_create">
                    <Button v-on:click="toggleFormDialog(null)"
                        class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm">
                        <i class="ri ri-add-line"></i> {{ $t("add_position") }}
                    </Button>
                </Can>
            </CardHeader>

            <CardContent>
                <Table>
                    <TableCaption>{{ $t('positions') }}</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px] text-center">{{ $t('no') }}</TableHead>
                            <TableHead class="text-center">{{ $t('code') }}</TableHead>
                            <TableHead class="text-center">{{ $t('name') }}</TableHead>
                            <TableHead class="text-center">{{ $t('section') }}</TableHead>
                            <TableHead class="text-center">{{ $t('department') }}</TableHead>
                            <TableHead class="text-center">{{ $t('division') }}</TableHead>
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
                                <Input :placeholder="$t('section')" v-model="search.section.name" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('department')" v-model="search.department.name" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('division')" v-model="search.division.name" />
                            </TableHead>
                            <TableHead></TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow v-for="(position, index) in props.positions?.data || []" :key="position.id">
                            <TableCell class="font-medium text-center">
                                <TablePaginationNumbers :items="props.positions" :index="index" />
                            </TableCell>
                            <TableCell class="text-center">{{ strLimit(position.code, 15) }}</TableCell>
                            <TableCell class="text-center">{{ strLimit(position.name, 15) }}</TableCell>
                            <TableCell class="text-center">{{ strLimit(position.section?.name, 15) }}</TableCell>
                            <TableCell class="text-center">{{ strLimit(position.department?.name, 15) }}</TableCell>
                            <TableCell class="text-center">{{ strLimit(position.division?.name, 15) }}
                            </TableCell>
                            <TableCell>
                                <TableActionsDialog class="text-center flex justify-center" canShow="position_show"
                                    :show="() => toggleShowDialog(position)" canEdit="position_edit"
                                    :edit="() => toggleFormDialog(position)" canDelete="position_delete"
                                    :delete="() => toggleShowDeleteModal(position)" />
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableFooter>
                        <TableEmpty v-if="!props.positions?.data?.length" :colspan="7">
                            {{ $t('no_data') }}
                        </TableEmpty>
                    </TableFooter>
                </Table>
            </CardContent>
            <CardFooter>
                <PaginationUse :items="props.positions?.links || []" :total="props.positions?.total || 0"
                    :itemsPerPage="props.positions?.per_page || 10" :currentPage="props.positions?.current_page || 1"
                    :defaultPage="1" />
            </CardFooter>
        </Card>
    </AppLayout>

    <Can permissions="position_delete">
        <DeleteModal v-model:show="showDeleteModal" :item="currentItem" @confirm="deletePosition"
            :loading="isDeleting" />
    </Can>

    <Can permissions="position_show">
        <PositionShowDialog v-model:show="showShowDialog" :item="currentItem" />
    </Can>

    <Can :permissions="['position_create', 'position_edit']">
        <PositionFormDialog v-model:show="showFormDialog" :method_type="method_type" :action="action"
            :departments="props?.departments" :divisions="props.divisions" :sections="props?.sections"
            :item="currentItem" :loading="showLoading" />
    </Can>
</template>
