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
import approvalFlowsRoute from '@/routes/user-management/approval-flows';
import approvalFlowStepsRoute from '@/routes/user-management/approval-flow-steps';
import Button from '@/components/ui/button/Button.vue';
import DeleteModal from '@/components/ui/Modal/DeleteModal.vue';
import ApprovalFlowStepFormDialog from './ApprovalFlowStepFormDialog.vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import axios from 'axios'
import TablePaginationNumbers from '@/components/ui/table/TablePaginationNumbers.vue';
import TableActionsDialog from '@/components/ui/table/TableActionsDialog.vue';
import Can from '@/components/ui/Auth/Can.vue';
import {
    Card,
    CardHeader,
    CardTitle,
    CardContent,
    CardFooter
} from '@/components/ui/card';
import { useSearchTable } from '@/composables/useSearchTable';
import { useToast } from '@/composables/useToast';
import { strLimit } from '@/utils/strLimit';
import A from '@/components/ui/a/A.vue';
import SelectGroup from '@/components/ui/select-group/SelectGroup.vue';

const { t } = useI18n();
const { showToast } = useToast();
const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('user_management'), href: null },
    { title: t('approval_flows'), href: approvalFlowsRoute.index().url },
    { title: t('approval_flow_steps'), href: null },
];

const props = defineProps<{
    approvalFlow: any,
    approver_types: object,
    approval_flow_steps?: {
        data?: any[],
        links?: any[],
        total?: number,
        per_page?: number,
        current_page?: number
    },
}>()

const approvalFlow = props.approvalFlow ?? null
const { search } = useSearchTable(approvalFlowStepsRoute.index(approvalFlow?.id).url, {
    approver_type: {
        value: '',
        operator: '='
    }
});

// Form Data
const showLoading = ref(false)
const showFormDialog = ref(false)
const currentItem = ref<any>(null)
const method_type = ref("post");
const action = ref(approvalFlowStepsRoute.store(approvalFlow?.id).url);
const data = ref<any[]>([])
const dataCache = ref<any[] | null>(null)  // reactive cache

/**
 * Loads all the data from the server and stores them in the reactive cache.
 * If the cache already has data, it will use the cached value instead of making a request.
 * @returns {Promise<void>}
 */
async function loadData(): Promise<void> {
    try {
        if (dataCache.value) {
            data.value = dataCache.value
            return
        }

        const response = await axios.get(approvalFlowStepsRoute.create(approvalFlow?.id).url)
        data.value = Array.isArray(response.data) ? response.data[0].data : response.data.data

        dataCache.value = data.value
    } catch (error) {
        console.error('Error loading data:', error)
    }
}

/**
 * Toggle the form dialog for adding or editing a approval_flow_step
 * @param {any} item - The item to be edited, or null for adding a new item
 */
const toggleFormDialog = async (item?: any, actionType?: string) => {
    showLoading.value = true
    showFormDialog.value = true
    currentItem.value = item

    await loadData()

    if (item) {
        method_type.value = actionType ?? "put"
        action.value = approvalFlowStepsRoute.update({
            approvalFlow: approvalFlow.id,
            step: item.id
        }).url;
    } else {
        method_type.value = "post"
        action.value = approvalFlowStepsRoute.store(approvalFlow?.id).url
    }

    showLoading.value = false
}


// Watch for changes in showFormDialog and reset currentItem when it is closed
watch(
    () => showFormDialog.value,
    (value) => {
        if (value === false) {
            currentItem.value = null
        }
    }
)

// Delete Modal
const showDeleteModal = ref(false)
const isDeleting = ref(false)

const toggleShowDeleteModal = (approval_flow_step: any) => {
    currentItem.value = approval_flow_step
    showDeleteModal.value = true
}

const deleteRole = () => {
    if (!currentItem.value) return
    isDeleting.value = true

    router.delete(approvalFlowStepsRoute.destroy({
        approvalFlow: approvalFlow.id,
        step: currentItem.value?.id
    }).url, {
        onFinish: () => {
            showDeleteModal.value = false
            currentItem.value = null
            isDeleting.value = false
            showToast({
                title: t('approval_flow_step_deleted_successfully'),
                type: 'success'
            })
        },
        onError: () => {
            isDeleting.value = false
            showToast({
                title: t('approval_flow_step_delete_failed'),
                type: 'error'
            })
        }
    })
}
</script>

<template>

    <Head :title="$t('approval_flow_steps')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <CardHeader class="flex justify-between items-center">
                <CardTitle>
                    {{ $t('approval_flow_steps') }}
                </CardTitle>
                <Can permissions="approval_flow_step_create">
                    <Button v-on:click="toggleFormDialog(null)"
                        class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm">
                        <i class="ri ri-add-line"></i> {{ $t("add_approval_flow_step") }}
                    </Button>
                </Can>
            </CardHeader>

            <CardContent>
                <Table>
                    <TableCaption>{{ $t('approval_flow_steps') }}</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px] text-center">{{ $t('no') }}</TableHead>
                            <TableHead class="text-center">{{ $t('name') }}</TableHead>
                            <TableHead class="text-center">{{ $t('approval_flow') }}</TableHead>
                            <TableHead class="text-center">{{ $t('approver_type') }}</TableHead>
                            <TableHead class="text-center">{{ $t('order') }}</TableHead>
                            <TableHead class="text-center">{{ $t('actions') }}</TableHead>
                        </TableRow>
                        <TableRow>
                            <TableHead class="w-[100px]"></TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('name')" v-model="search.name" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('approval_flow')" v-model="search.approval_flow" />
                            </TableHead>
                            <TableHead class="p-2">
                                <SelectGroup v-model="search.approver_type.value"
                                    :placeholder="$t('please_select_a_approver_type')"
                                    :options="props.approver_types || []" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('order')" v-model="search.order" />
                            </TableHead>
                            <TableHead></TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow v-for="(approval_flow_step, index) in props.approval_flow_steps?.data || []"
                            :key="approval_flow_step.id">
                            <TableCell class="font-medium text-center">
                                <TablePaginationNumbers :items="props.approval_flow_steps" :index="index" />
                            </TableCell>
                            <TableCell class="text-center">{{ strLimit(approval_flow_step.name, 15) }}</TableCell>
                            <TableCell class="text-center">
                                {{ strLimit(approval_flow_step.approval_flow?.name, 15) }}
                            </TableCell>
                            <TableCell class="text-center">
                                {{ strLimit(approval_flow_step.approver_type_label, 15) }}
                            </TableCell>
                            <TableCell class="text-center">
                                {{ approval_flow_step.order }}
                            </TableCell>
                            <TableCell>
                                <TableActionsDialog class="text-center flex justify-center"
                                    canShow="approval_flow_step_show"
                                    :show="() => toggleFormDialog(approval_flow_step, 'show')"
                                    canEdit="approval_flow_step_edit" :edit="() => toggleFormDialog(approval_flow_step)"
                                    canDelete="approval_flow_step_delete"
                                    :delete="() => toggleShowDeleteModal(approval_flow_step)">
                                    <Can permissions="approval_flow_step_step_access">
                                        <A size="sm" :href="approvalFlowStepsRoute.index(approval_flow_step.id).url"
                                            class="ms-2 bg-purple-500 cursor-pointer text-white hover:bg-purple-600">
                                            <i class="ri ri-flow-chart"></i>
                                        </A>
                                    </Can>
                                </TableActionsDialog>
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableFooter>
                        <TableEmpty v-if="!props.approval_flow_steps?.data?.length" :colspan="6">
                            {{ $t('no_data') }}
                        </TableEmpty>
                    </TableFooter>
                </Table>
            </CardContent>

            <CardFooter>
                <PaginationUse :items="props.approval_flow_steps?.links || []"
                    :total="props.approval_flow_steps?.total || 0"
                    :itemsPerPage="props.approval_flow_steps?.per_page || 10"
                    :currentPage="props.approval_flow_steps?.current_page || 1" :defaultPage="1" />
            </CardFooter>
        </Card>
    </AppLayout>

    <Can permissions="approval_flow_step_delete">
        <DeleteModal v-model:show="showDeleteModal" :item="currentItem" @confirm="deleteRole" :loading="isDeleting" />
    </Can>

    <Can :permissions="['approval_flow_step_create', 'approval_flow_step_show', 'approval_flow_step_edit']">
        <ApprovalFlowStepFormDialog v-model:show="showFormDialog" :method_type="method_type" :action="action"
            :data="dataCache" :item="currentItem" :loading="showLoading" />
    </Can>
</template>
