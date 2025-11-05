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
import insuranceCompaniesRoute from '@/routes/hr/master-data/insurance-companies';
import DeleteModal from '@/components/ui/Modal/DeleteModal.vue';
import Button from '@/components/ui/button/Button.vue';
import InsuranceCompanyFormDialog from './InsuranceCompanyFormDialog.vue';
import InsuranceCompanyShowDialog from './InsuranceCompanyShowDialog.vue';
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
import { useStrLimit } from '@/composables/useStrLimit';

// Master Data
const { t } = useI18n();
const showFormDialog = ref(false)
const currentItem = ref<any>(null)
const method_type = ref("post");
const action = ref(insuranceCompaniesRoute.store().url);
const showLoading = ref(false)

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('hr'), href: null },
    { title: t('master_data'), href: null },
    { title: t('insurance_companies'), href: null },
];

const props = defineProps<{
    insurance_companies?: {
        data?: any[],
        links?: any[],
        total?: number,
        per_page?: number,
        current_page?: number
    }
}>()


/**
 * Toggle the form dialog for adding or editing a insurance_company
 * @param {any} item - The item to be edited, or null for adding a new item
 */
const toggleFormDialog = (item?: any) => {
    showLoading.value = true
    showFormDialog.value = true;
    currentItem.value = item;

    if (item) {
        method_type.value = "put";
        action.value = insuranceCompaniesRoute.update(item.id).url;
    } else {
        method_type.value = "post";
        action.value = insuranceCompaniesRoute.store().url;
    }

    showLoading.value = false
}

const { search } = useSearchTable(insuranceCompaniesRoute.index().url);

// Delete Modal
const showDeleteModal = ref(false)
const isDeleting = ref(false)

const toggleShowDeleteModal = (insurance_company: any) => {
    currentItem.value = insurance_company
    showDeleteModal.value = true
}

const deleteAllowanceType = () => {
    if (!currentItem.value) return
    isDeleting.value = true

    router.delete(insuranceCompaniesRoute.destroy(currentItem.value.id).url, {
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
const toggleShowDialog = (insurance_company: any) => {
    currentItem.value = insurance_company
    showShowDialog.value = true
}
</script>

<template>

    <Head :title="$t('insurance_companies')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <CardHeader class="flex justify-between items-center">
                <CardTitle>{{ $t('insurance_companies') }}</CardTitle>
                <Can permissions="insurance_company_create">
                    <Button v-on:click="toggleFormDialog(null)"
                        class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm">
                        <i class="ri ri-add-line"></i> {{ $t("add_insurance_company") }}
                    </Button>
                </Can>
            </CardHeader>

            <CardContent>
                <Table>
                    <TableCaption>{{ $t('insurance_companies') }}</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px] text-center">{{ $t('no') }}</TableHead>
                            <TableHead class="text-center">{{ $t('name') }}</TableHead>
                            <TableHead class="text-center">{{ $t('email') }}</TableHead>
                            <TableHead class="text-center">{{ $t('phone') }}</TableHead>
                            <TableHead class="text-center">{{ $t('website') }}</TableHead>
                            <TableHead class="text-center">{{ $t('actions') }}</TableHead>
                        </TableRow>
                        <TableRow>
                            <TableHead class="w-[100px]"></TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('name')" v-model="search.name" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('email')" v-model="search.email" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('phone')" v-model="search.phone" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('website')" v-model="search.website" />
                            </TableHead>
                            <TableHead></TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow v-for="(insurance_company, index) in props.insurance_companies?.data || []"
                            :key="insurance_company.id">
                            <TableCell class="font-medium text-center">
                                <TablePaginationNumbers :items="props.insurance_companies" :index="index" />
                            </TableCell>
                            <TableCell class="text-center">{{ useStrLimit(insurance_company.name, 15) }}</TableCell>
                            <TableCell class="text-center">{{ useStrLimit(insurance_company.email, 15) }}</TableCell>
                            <TableCell class="text-center">{{ useStrLimit(insurance_company.phone, 15) }}</TableCell>
                            <TableCell class="text-center">
                                <a v-if="insurance_company.website" :href="insurance_company.website" target="_blank"
                                    rel="noopener noreferrer" class="text-blue-400 hover:text-blue-800 duration-200">
                                    {{ useStrLimit(insurance_company.website, 15) }}
                                </a>
                                <span v-else>-</span>
                            </TableCell>
                            <TableCell>
                                <TableActionsDialog class="text-center flex justify-center"
                                    canShow="insurance_company_show" :show="() => toggleShowDialog(insurance_company)"
                                    canEdit="insurance_company_edit" :edit="() => toggleFormDialog(insurance_company)"
                                    canDelete="insurance_company_delete"
                                    :delete="() => toggleShowDeleteModal(insurance_company)" />
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableFooter>
                        <TableEmpty v-if="!props.insurance_companies?.data?.length" :colspan="6">
                            {{ $t('no_data') }}
                        </TableEmpty>
                    </TableFooter>
                </Table>
            </CardContent>
            <CardFooter>
                <PaginationUse :items="props.insurance_companies?.links || []"
                    :total="props.insurance_companies?.total || 0"
                    :itemsPerPage="props.insurance_companies?.per_page || 10"
                    :currentPage="props.insurance_companies?.current_page || 1" :defaultPage="1" />
            </CardFooter>
        </Card>
    </AppLayout>

    <Can permissions="insurance_company_delete">
        <DeleteModal v-model:show="showDeleteModal" :item="currentItem" @confirm="deleteAllowanceType"
            :loading="isDeleting" />
    </Can>

    <Can permissions="insurance_company_show">
        <InsuranceCompanyShowDialog v-model:show="showShowDialog" :item="currentItem" />
    </Can>

    <Can :permissions="['insurance_company_create', 'insurance_company_edit']">
        <InsuranceCompanyFormDialog v-model:show="showFormDialog" :method_type="method_type" :action="action"
            :item="currentItem" :loading="showLoading" />
    </Can>
</template>
