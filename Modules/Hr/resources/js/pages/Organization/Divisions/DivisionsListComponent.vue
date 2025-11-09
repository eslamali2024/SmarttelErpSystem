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
import { router } from '@inertiajs/vue3';
import divisionsRoute from '@/routes/hr/organization/divisions';
import {
    Card,
    CardHeader,
    CardTitle,
    CardContent,
    CardFooter
} from '@/components/ui/card';
import { ref } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import DeleteModal from '@/components/ui/Modal/DeleteModal.vue';
import DivisionFormDialog from './DivisionFormDialog.vue';
import DivisionShowDialog from './DivisionShowDialog.vue';
import { useI18n } from 'vue-i18n';
import TableActionsDialog from '@/components/ui/table/TableActionsDialog.vue';
import TablePaginationNumbers from '@/components/ui/table/TablePaginationNumbers.vue';
import Can from '@/components/ui/Auth/Can.vue';
import { strLimit } from '@/utils/strLimit';
import { useSearchTable } from '@/composables/useSearchTable';
import { useToast } from '@/composables/useToast';

// Master Data
const { t } = useI18n();
const { showToast } = useToast();
const showFormDialog = ref(false)
const currentItem = ref<any>(null)
const method_type = ref("post");
const action = ref(divisionsRoute.store().url);
const showLoading = ref(false)

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('hr'), href: null },
    { title: t('organization'), href: null },
    { title: t('divisions'), href: null },
];

const props = defineProps<{
    divisions?: {
        data?: any[],
        links?: any[],
        total?: number,
        per_page?: number,
        current_page?: number
    },
    managers?: {
        id: number
        name: string
    }
}>()

// reactive search
const { search } = useSearchTable(divisionsRoute.index().url, {
    name: '',
    code: '',
    manager: { name: '' }
})

/**
 * Toggle the form dialog for adding or editing a division
 * @param {any} item - The item to be edited, or null for adding a new item
 */
const toggleFormDialog = (item?: any) => {
    showLoading.value = true
    showFormDialog.value = true;
    currentItem.value = item;

    if (item) {
        method_type.value = "put";
        action.value = divisionsRoute.update(item.id).url;
    } else {
        method_type.value = "post";
        action.value = divisionsRoute.store().url;
    }

    showLoading.value = false
}

// Delete Modal
const showDeleteModal = ref(false)
const isDeleting = ref(false)

const toggleShowDeleteModal = (division: any) => {
    currentItem.value = division
    showDeleteModal.value = true
}

/**
 * Delete a division
 *
 * If the division exists, toggle the isDeleting flag to true, and make a DELETE request to the server.
 * On success, toggle the showDeleteModal flag to false, set the currentItem to null, and toggle the isDeleting flag to false.
 * On error, toggle the isDeleting flag to false.
 */
const deleteDivision = () => {
    if (!currentItem.value) return
    isDeleting.value = true

    router.delete(divisionsRoute.destroy(currentItem.value.id).url, {
        onFinish: () => {
            showDeleteModal.value = false
            currentItem.value = null
            isDeleting.value = false
            showToast({
                title: t('division_deleted_successfully'),
                type: 'success'
            })
        },
        onError: () => {
            isDeleting.value = false
            showToast({
                title: t('division_deleted_failed'),
                type: 'error'
            })
        }
    })
}

// Show Modal
const showShowDialog = ref(false)
const toggleShowDialog = (poisiton: any) => {
    currentItem.value = poisiton
    showShowDialog.value = true
}
</script>

<template>

    <Head :title="$t('divisions')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <CardHeader class="flex justify-between items-center">
                <CardTitle>
                    {{ $t('divisions') }}
                </CardTitle>
                <Can permissions="division_create">
                    <Button v-on:click="toggleFormDialog(null)"
                        class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm">
                        <i class="ri ri-add-line"></i> {{ $t("add_division") }}
                    </Button>
                </Can>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableCaption>{{ $t('divisions') }}</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px] text-center">{{ $t('no') }}</TableHead>
                            <TableHead class="text-center">{{ $t('code') }}</TableHead>
                            <TableHead class="text-center">{{ $t('name') }}</TableHead>
                            <TableHead class="text-center">{{ $t('manager') }}</TableHead>
                            <TableHead class="text-center">{{ $t('actions') }}</TableHead>
                        </TableRow>
                        <TableRow>
                            <TableHead class="w-[100px]"></TableHead>
                            <TableHead class="p-2">
                                <Input placeholder="Code" v-model="search.code" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input placeholder="Name" v-model="search.name" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input placeholder="Manager" v-model="search.manager.name" />
                            </TableHead>
                            <TableHead></TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow v-for="(division, index) in props.divisions?.data || []" :key="division.id">
                            <TableCell class="font-medium text-center">
                                <TablePaginationNumbers :items="props.divisions" :index="index" />
                            </TableCell>
                            <TableCell class="text-center">{{ strLimit(division.code, 15) }}</TableCell>
                            <TableCell class="text-center">{{ strLimit(division.name, 15) }}</TableCell>
                            <TableCell class="text-center">{{ strLimit(division.manager?.name, 15) }}</TableCell>
                            <TableCell class="text-center flex">
                                <TableActionsDialog class="text-center flex justify-center" canShow="division_show"
                                    :show="() => toggleShowDialog(division)" canEdit="division_edit"
                                    :edit="() => toggleFormDialog(division)" canDelete="division_delete"
                                    :delete="() => toggleShowDeleteModal(division)" />
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableFooter>
                        <TableEmpty v-if="!props.divisions?.data?.length" :colspan="5">
                            {{ $t('no_data') }}
                        </TableEmpty>
                    </TableFooter>
                </Table>
            </CardContent>
            <CardFooter>
                <PaginationUse :items="props.divisions?.links || []" :total="props.divisions?.total || 0"
                    :itemsPerPage="props.divisions?.per_page || 10" :currentPage="props.divisions?.current_page || 1"
                    :defaultPage="1" />
            </CardFooter>
        </Card>
    </AppLayout>

    <Can permissions="division_delete">
        <DeleteModal v-model:show="showDeleteModal" :item="currentItem" @confirm="deleteDivision"
            :loading="isDeleting" />
    </Can>

    <Can permissions="division_show">
        <DivisionShowDialog v-model:show="showShowDialog" :item="currentItem" />
    </Can>

    <Can :permissions="['division_create', 'division_edit']">
        <DivisionFormDialog v-model:show="showFormDialog" :method_type="method_type" :action="action"
            :loading="showLoading" :managers="props.managers" :item="currentItem" />
    </Can>
</template>
