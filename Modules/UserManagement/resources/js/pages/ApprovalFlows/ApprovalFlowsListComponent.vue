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
import approvalFlowsRoute from '@/routes/user-management/approval-flows';
import approvalFlowStepsRoute from '@/routes/user-management/approval-flow-steps';
import Button from '@/components/ui/button/Button.vue';
import DeleteModal from '@/components/ui/Modal/DeleteModal.vue';
import ApprovalFlowDialog from './ApprovalFlowDialog.vue';
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

const { t } = useI18n();
const { showToast } = useToast();
const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('user_management'), href: null },
    { title: t('approval_flows'), href: null },
];

const props = defineProps<{
    approval_flows?: {
        data?: any[],
        links?: any[],
        total?: number,
        per_page?: number,
        current_page?: number
    },
}>()

// reactive search
const { search } = useSearchTable(approvalFlowsRoute.index().url);


// Form Data
const showLoading = ref(false)
const showFormDialog = ref(false)
const currentItem = ref<any>(null)
const method_type = ref("post");
const action = ref(approvalFlowsRoute.store().url);
const models = ref<any[]>([])
const modelsCache = ref<any[] | null>(null)  // reactive cache

/**
 * Loads all the models from the server and stores them in the reactive cache.
 * If the cache already has models, it will use the cached value instead of making a request.
 * @returns {Promise<void>}
 */
async function loadModels(): Promise<void> {
    try {
        if (modelsCache.value) {
            models.value = modelsCache.value
            return
        }

        const response = await axios.get(approvalFlowsRoute.create().url)
        models.value = Array.isArray(response.data) ? response.data[0].models : response.data.models

        modelsCache.value = models.value
    } catch (error) {
        console.error('Error loading models:', error)
    }
}

/**
 * Toggle the form dialog for adding or editing a approval_flow
 * @param {any} item - The item to be edited, or null for adding a new item
 */
const toggleFormDialog = async (item?: any, actionType?: string) => {
    showLoading.value = true
    showFormDialog.value = true
    currentItem.value = item

    await loadModels()

    if (item) {
        method_type.value = actionType ?? "put"
        action.value = approvalFlowsRoute.update(item.id).url
    } else {
        method_type.value = "post"
        action.value = approvalFlowsRoute.store().url
    }

    showLoading.value = false
}

// Delete Modal
const showDeleteModal = ref(false)
const isDeleting = ref(false)

const toggleShowDeleteModal = (approval_flow: any) => {
    currentItem.value = approval_flow
    showDeleteModal.value = true
}

const deleteRole = () => {
    if (!currentItem.value) return
    isDeleting.value = true

    router.delete(approvalFlowsRoute.destroy(currentItem.value.id).url, {
        onFinish: () => {
            showDeleteModal.value = false
            currentItem.value = null
            isDeleting.value = false
            showToast({
                title: t('approval_flow_deleted_successfully'),
                type: 'success'
            })
        },
        onError: () => {
            isDeleting.value = false
            showToast({
                title: t('approval_flow_delete_failed'),
                type: 'error'
            })
        }
    })
}
</script>

<template>

    <Head :title="$t('approval_flows')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <CardHeader class="flex justify-between items-center">
                <CardTitle>
                    {{ $t('approval_flows') }}
                </CardTitle>
                <Can permissions="approval_flow_create">
                    <Button v-on:click="toggleFormDialog(null)"
                        class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm">
                        <i class="ri ri-add-line"></i> {{ $t("add_approval_flow") }}
                    </Button>
                </Can>
            </CardHeader>

            <CardContent>
                <Table>
                    <TableCaption>{{ $t('approval_flows') }}</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px] text-center">{{ $t('no') }}</TableHead>
                            <TableHead class="text-center">{{ $t('name') }}</TableHead>
                            <TableHead class="text-center">{{ $t('redirect_route') }}</TableHead>
                            <TableHead class="text-center">{{ $t('model') }}</TableHead>
                            <TableHead class="text-center">{{ $t('actions') }}</TableHead>
                        </TableRow>
                        <TableRow>
                            <TableHead class="w-[100px]"></TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('name')" v-model="search.name" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('redirect_route')" v-model="search.redirect_route" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('model')" v-model="search.approvable_type" />
                            </TableHead>
                            <TableHead></TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow v-for="(approval_flow, index) in props.approval_flows?.data || []"
                            :key="approval_flow.id">
                            <TableCell class="font-medium text-center">
                                <TablePaginationNumbers :items="props.approval_flows" :index="index" />
                            </TableCell>
                            <TableCell class="text-center">{{ strLimit(approval_flow.name, 15) }}</TableCell>
                            <TableCell class="text-center">{{ strLimit(approval_flow.redirect_route, 15) }}</TableCell>
                            <TableCell class="text-center">{{ strLimit(approval_flow.approvable_type, 15) }}</TableCell>
                            <TableCell>
                                <TableActionsDialog class="text-center flex justify-center" canShow="approval_flow_show"
                                    :show="() => toggleFormDialog(approval_flow, 'show')" canEdit="approval_flow_edit"
                                    :edit="() => toggleFormDialog(approval_flow)" canDelete="approval_flow_delete"
                                    :delete="() => toggleShowDeleteModal(approval_flow)">
                                    <Can permissions="approval_flow_step_access">
                                        <A size="sm" :href="approvalFlowStepsRoute.index(approval_flow.id).url"
                                            class="ms-2 bg-purple-500 cursor-pointer text-white hover:bg-purple-600">
                                            <i class="ri ri-flow-chart"></i>
                                        </A>
                                    </Can>
                                </TableActionsDialog>
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableFooter>
                        <TableEmpty v-if="!props.approval_flows?.data?.length" :colspan="5">
                            {{ $t('no_data') }}
                        </TableEmpty>
                    </TableFooter>
                </Table>
            </CardContent>

            <CardFooter>
                <PaginationUse :items="props.approval_flows?.links || []" :total="props.approval_flows?.total || 0"
                    :itemsPerPage="props.approval_flows?.per_page || 10"
                    :currentPage="props.approval_flows?.current_page || 1" :defaultPage="1" />
            </CardFooter>
        </Card>
    </AppLayout>

    <Can permissions="approval_flow_delete">
        <DeleteModal v-model:show="showDeleteModal" :item="currentItem" @confirm="deleteRole" :loading="isDeleting" />
    </Can>

    <Can :permissions="['approval_flow_create', 'approval_flow_show', 'approval_flow_edit']">
        <ApprovalFlowDialog v-model:show="showFormDialog" :method_type="method_type" :action="action"
            :models="modelsCache" :item="currentItem" :loading="showLoading" />
    </Can>
</template>
