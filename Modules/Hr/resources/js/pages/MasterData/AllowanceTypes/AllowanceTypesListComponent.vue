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
import { reactive, watch, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import allowanceTypesRoute from '@/routes/hr/master-data/allowance-types';
import DeleteModal from '@/components/ui/Modal/DeleteModal.vue';
import Button from '@/components/ui/button/Button.vue';
import AllowanceTypeFormDialog from './AllowanceTypeFormDialog.vue';
import AllowanceTypeShowDialog from './AllowanceTypeShowDialog.vue';
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
import SelectGroup from '@/components/ui/select-group/SelectGroup.vue';
import { useStrLimit } from '@/composables/useStrLimit';

// Master Data
const { t } = useI18n();
const showFormDialog = ref(false)
const currentItem = ref<any>(null)
const method_type = ref("post");
const action = ref(allowanceTypesRoute.store().url);
const showLoading = ref(false)

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('hr'), href: null },
    { title: t('master_data'), href: null },
    { title: t('allowance_types'), href: null },
];

const props = defineProps<{
    allowance_types?: {
        data?: any[],
        links?: any[],
        total?: number,
        per_page?: number,
        current_page?: number
    },
    types?: {
        id: number
        name: string
    },
    taxables?: {
        id: number
        name: string
    },
}>()


/**
 * Toggle the form dialog for adding or editing a allowance_type
 * @param {any} item - The item to be edited, or null for adding a new item
 */
const toggleFormDialog = (item?: any) => {
    showLoading.value = true
    showFormDialog.value = true;
    currentItem.value = item;

    if (item) {
        method_type.value = "put";
        action.value = allowanceTypesRoute.update(item.id).url;
    } else {
        method_type.value = "post";
        action.value = allowanceTypesRoute.store().url;
    }

    showLoading.value = false
}

// reactive search
const urlParams = new URLSearchParams(window.location.search);

const search = reactive({
    name: urlParams.get('name') ?? '',
    type: urlParams.get('type') ?? '',
    taxable: urlParams.get('taxable') ?? '',
});

// watch search changes
watch(search, () => {
    router.get(allowanceTypesRoute.index().url, search, {
        preserveState: true,
        replace: true,
    })
}, { deep: true });

// Delete Modal
const showDeleteModal = ref(false)
const isDeleting = ref(false)

const toggleShowDeleteModal = (allowance_type: any) => {
    currentItem.value = allowance_type
    showDeleteModal.value = true
}

const deleteAllowanceType = () => {
    if (!currentItem.value) return
    isDeleting.value = true

    router.delete(allowanceTypesRoute.destroy(currentItem.value.id).url, {
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
const toggleShowDialog = (allowance_type: any) => {
    currentItem.value = allowance_type
    showShowDialog.value = true
}
</script>

<template>

    <Head :title="$t('allowance_types')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <CardHeader class="flex justify-between items-center">
                <CardTitle>{{ $t('allowance_types') }}</CardTitle>
                <Can permissions="allowance_type_create">
                    <Button v-on:click="toggleFormDialog(null)"
                        class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm">
                        <i class="ri ri-add-line"></i> {{ $t("add_allowance_type") }}
                    </Button>
                </Can>
            </CardHeader>

            <CardContent>
                <Table>
                    <TableCaption>{{ $t('allowance_types') }}</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px] text-center">{{ $t('no') }}</TableHead>
                            <TableHead class="text-center">{{ $t('name') }}</TableHead>
                            <TableHead class="text-center">{{ $t('type') }}</TableHead>
                            <TableHead class="text-center">{{ $t('taxable') }}</TableHead>
                            <TableHead class="text-center">{{ $t('actions') }}</TableHead>
                        </TableRow>
                        <TableRow>
                            <TableHead class="w-[100px]"></TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('name')" v-model="search.name" />
                            </TableHead>
                            <TableHead class="p-2">
                                <SelectGroup v-model="search.type" :placeholder="$t('please_select_a_type')"
                                    :options="props.types || []" />
                            </TableHead>
                            <TableHead class="p-2">
                                <SelectGroup v-model="search.taxable" :placeholder="$t('please_select_a_taxable')"
                                    :options="props.taxables || []" />
                            </TableHead>
                            <TableHead></TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow v-for="(allowance_type, index) in props.allowance_types?.data || []"
                            :key="allowance_type.id">
                            <TableCell class="font-medium text-center">
                                <TablePaginationNumbers :items="props.allowance_types" :index="index" />
                            </TableCell>
                            <TableCell class="text-center">{{ useStrLimit(allowance_type.name, 15) }}</TableCell>
                            <TableCell class="text-center">{{ useStrLimit(allowance_type.allowance_type, 15) }}
                            </TableCell>
                            <TableCell class="text-center">{{ useStrLimit(allowance_type.allowance_taxable, 15) }}
                            </TableCell>
                            <TableCell>
                                <TableActionsDialog class="text-center flex justify-center"
                                    canShow="allowance_type_show" :show="() => toggleShowDialog(allowance_type)"
                                    canEdit="allowance_type_edit" :edit="() => toggleFormDialog(allowance_type)"
                                    canDelete="allowance_type_delete"
                                    :delete="() => toggleShowDeleteModal(allowance_type)" />
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableFooter>
                        <TableEmpty v-if="!props.allowance_types?.data?.length" :colspan="5">
                            {{ $t('no_data') }}
                        </TableEmpty>
                    </TableFooter>
                </Table>
            </CardContent>
            <CardFooter>
                <PaginationUse :items="props.allowance_types?.links || []" :total="props.allowance_types?.total || 0"
                    :itemsPerPage="props.allowance_types?.per_page || 10"
                    :currentPage="props.allowance_types?.current_page || 1" :defaultPage="1" />
            </CardFooter>
        </Card>
    </AppLayout>

    <Can permissions="allowance_type_delete">
        <DeleteModal v-model:show="showDeleteModal" :item="currentItem" @confirm="deleteAllowanceType"
            :loading="isDeleting" />
    </Can>

    <Can permissions="allowance_type_show">
        <AllowanceTypeShowDialog v-model:show="showShowDialog" :item="currentItem" />
    </Can>

    <Can :permissions="['allowance_type_create', 'allowance_type_edit']">
        <AllowanceTypeFormDialog v-model:show="showFormDialog" :method_type="method_type" :action="action"
            :types="props?.types" :taxables="props.taxables" :item="currentItem" :loading="showLoading" />
    </Can>
</template>
