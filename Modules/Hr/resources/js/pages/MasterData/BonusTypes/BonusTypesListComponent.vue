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
import bonusTypesRoute from '@/routes/hr/master-data/bonus-types';
import DeleteModal from '@/components/ui/Modal/DeleteModal.vue';
import Button from '@/components/ui/button/Button.vue';
import PublicHolidayFormDialog from './BonusTypeFormDialog.vue';
import PublicHolidayShowDialog from './BonusTypeShowDialog.vue';
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

// Master Data
const { t } = useI18n();
const showFormDialog = ref(false)
const showImportDialog = ref(false)
const currentItem = ref<any>(null)
const method_type = ref("post");
const action = ref(bonusTypesRoute.store().url);
const showLoading = ref(false)
const { showToast } = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('hr'), href: null },
    { title: t('master_data'), href: null },
    { title: t('bonus_types'), href: null },
];

const props = defineProps<{
    bonus_types?: {
        data?: any[],
        links?: any[],
        total?: number,
        per_page?: number,
        current_page?: number
    },
}>()


/**
 * Toggle the form dialog for adding or editing a bonus_type
 * @param {any} item - The item to be edited, or null for adding a new item
 */
const toggleFormDialog = (item?: any) => {
    showLoading.value = true
    showFormDialog.value = true;
    currentItem.value = item;

    if (item) {
        method_type.value = "put";
        action.value = bonusTypesRoute.update(item.id).url;
    } else {
        method_type.value = "post";
        action.value = bonusTypesRoute.store().url;
    }

    showLoading.value = false
}

// reactive search
const { search } = useSearchTable(bonusTypesRoute.index().url);

// Delete Modal
const showDeleteModal = ref(false)
const isDeleting = ref(false)

const toggleShowDeleteModal = (bonus_type: any) => {
    currentItem.value = bonus_type
    showDeleteModal.value = true
}

const deletePublicHoliday = () => {
    if (!currentItem.value) return
    isDeleting.value = true

    router.delete(bonusTypesRoute.destroy(currentItem.value.id).url, {
        onFinish: () => {
            showDeleteModal.value = false
            currentItem.value = null
            isDeleting.value = false
            showToast({
                title: t('bonus_type_deleted_successfully'),
                type: 'success'
            })
        },
        onError: () => {
            isDeleting.value = false
            showToast({
                title: t('bonus_type_deleted_failed'),
                type: 'error'
            })
        }
    })
}

// Show Modal
const showShowDialog = ref(false)
const toggleShowDialog = (bonus_type: any) => {
    currentItem.value = bonus_type
    showShowDialog.value = true
}

/**
 * Toggle the import dialog for importing public holidays
 */
const toggleImportialog = () => {
    showImportDialog.value = true;

    action.value = bonusTypesRoute.import().url
    method_type.value = "post"
}
</script>

<template>

    <Head :title="$t('bonus_types')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <CardHeader class="flex justify-between items-center">
                <CardTitle>{{ $t('bonus_types') }}</CardTitle>
                <Can permissions="bonus_type_create">
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
                                    <i class="ri ri-add-line"></i> {{ $t("add_bonus_type") }}
                                </button>
                            </DropdownMenuItem>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem>
                                <a :href="bonusTypesRoute.downloadTemplate().url" rel="noopener"
                                    class="btn btn-outline-primary">
                                    <i class="ri ri-download-2-line me-2"></i>
                                    {{ $t('download_sample') }}
                                </a>
                            </DropdownMenuItem>
                            <DropdownMenuItem>
                                <button v-on:click="toggleImportialog()" class="cursor-pointer">
                                    <i class="ri ri-file-excel-2-line"></i> {{ $t("import_bonus_types") }}
                                </button>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </Can>
            </CardHeader>

            <CardContent>
                <Table>
                    <TableCaption>{{ $t('bonus_types') }}</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px] text-center">{{ $t('no') }}</TableHead>
                            <TableHead class="text-center">{{ $t('name') }}</TableHead>
                            <TableHead class="text-center">{{ $t('actions') }}</TableHead>
                        </TableRow>
                        <TableRow>
                            <TableHead class="w-[100px]"></TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('name')" v-model="search.name" />
                            </TableHead>
                            <TableHead></TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow v-for="(bonus_type, index) in props.bonus_types?.data || []" :key="bonus_type.id">
                            <TableCell class="font-medium text-center">
                                <TablePaginationNumbers :items="props.bonus_types" :index="index" />
                            </TableCell>
                            <TableCell class="text-center">{{ strLimit(bonus_type.name, 15) }}</TableCell>
                            <TableCell>
                                <TableActionsDialog class="text-center flex justify-center" canShow="bonus_type_show"
                                    :show="() => toggleShowDialog(bonus_type)" canEdit="bonus_type_edit"
                                    :edit="() => toggleFormDialog(bonus_type)" canDelete="bonus_type_delete"
                                    :delete="() => toggleShowDeleteModal(bonus_type)" />
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableFooter>
                        <TableEmpty v-if="!props.bonus_types?.data?.length" :colspan="8">
                            {{ $t('no_data') }}
                        </TableEmpty>
                    </TableFooter>
                </Table>
            </CardContent>
            <CardFooter>
                <PaginationUse :items="props.bonus_types?.links || []" :total="props.bonus_types?.total || 0"
                    :itemsPerPage="props.bonus_types?.per_page || 10"
                    :currentPage="props.bonus_types?.current_page || 1" :defaultPage="1" />
            </CardFooter>
        </Card>
    </AppLayout>

    <Can permissions="bonus_type_delete">
        <DeleteModal v-model:show="showDeleteModal" :item="currentItem" @confirm="deletePublicHoliday"
            :loading="isDeleting" />
    </Can>

    <Can permissions="bonus_type_show">
        <PublicHolidayShowDialog v-model:show="showShowDialog" :item="currentItem" />
    </Can>

    <Can :permissions="['bonus_type_create', 'bonus_type_edit']">
        <PublicHolidayFormDialog v-model:show="showFormDialog" :method_type="method_type" :action="action"
            :item="currentItem" :loading="showLoading" />

        <ImportDialog v-model:show="showImportDialog" :action="action" :item="currentItem" />
    </Can>
</template>
