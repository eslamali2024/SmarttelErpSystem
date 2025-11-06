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
import permissionsRoute from '@/routes/user-management/permissions';
import Button from '@/components/ui/button/Button.vue';
import DeleteModal from '@/components/ui/Modal/DeleteModal.vue';
import PermissionFormDialog from './PermissionFormDialog.vue';
import PermissionShowDialog from './PermissionShowDialog.vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import Can from '@/components/ui/Auth/Can.vue';
import TableActionsDialog from '@/components/ui/table/TableActionsDialog.vue';
import TablePaginationNumbers from '@/components/ui/table/TablePaginationNumbers.vue';
import {
    Card,
    CardHeader,
    CardTitle,
    CardContent,
    CardFooter
} from '@/components/ui/card';
import { useSearchTable } from '@/composables/useSearchTable';

const { t } = useI18n();
const showLoading = ref(false)
const showFormDialog = ref(false)
const currentItem = ref<any>(null)
const method_type = ref("post");
const action = ref(permissionsRoute.store().url);

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('user_management'), href: null },
    { title: t('permissions'), href: null },
];

const props = defineProps<{
    permissions?: {
        data?: any[],
        links?: any[],
        total?: number,
        per_page?: number,
        current_page?: number
    }
}>()

// reactive search
const { search } = useSearchTable(permissionsRoute.index().url);


const toggleFormDialog = (item?: any) => {
    showLoading.value = true
    showFormDialog.value = true;
    currentItem.value = item;

    if (item) {
        method_type.value = "put";
        action.value = permissionsRoute.update(item.id).url;
    } else {
        method_type.value = "post";
        action.value = permissionsRoute.store().url;
    }

    showLoading.value = false
}

// Delete Modal
const showDeleteModal = ref(false)
const isDeleting = ref(false)

const toggleShowDeleteModal = (permission: any) => {
    currentItem.value = permission
    showDeleteModal.value = true
}

const deletePermission = () => {
    if (!currentItem.value) return
    isDeleting.value = true

    router.delete(permissionsRoute.destroy(currentItem.value.id).url, {
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
const toggleShowDialog = (poisiton: any) => {
    currentItem.value = poisiton
    showShowDialog.value = true
}
</script>

<template>

    <Head :title="$t('permissions')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <CardHeader class="flex justify-between items-center">
                <CardTitle>
                    {{ $t('permissions') }}
                </CardTitle>
                <Can permissions="permission_create">
                    <Button v-on:click="toggleFormDialog(null)"
                        class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm">
                        <i class="ri ri-add-line"></i> {{ $t("add_permission") }}
                    </Button>
                </Can>
            </CardHeader>

            <CardContent>
                <Table>
                    <TableCaption>{{ $t('permissions') }}</TableCaption>
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
                        <TableRow v-for="(permission, index) in props.permissions?.data || []" :key="permission.id">
                            <TableCell class="font-medium text-center">
                                <TablePaginationNumbers :items="props.permissions" :index="index" />
                            </TableCell>
                            <TableCell class="text-center">{{ permission.name ?? '-' }}</TableCell>
                            <TableCell class="text-center flex justify-center">
                                <TableActionsDialog class="text-center flex justify-center" canShow="permission_show"
                                    :show="() => toggleShowDialog(permission)" canEdit="permission_edit"
                                    :edit="() => toggleFormDialog(permission)" canDelete="permission_delete"
                                    :delete="() => toggleShowDeleteModal(permission)" />
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableFooter>
                        <TableEmpty v-if="!props.permissions?.data?.length" :colspan="3">
                            {{ $t('no_data') }}
                        </TableEmpty>
                    </TableFooter>
                </Table>
            </CardContent>

            <CardFooter>
                <PaginationUse :items="props.permissions?.links || []" :total="props.permissions?.total || 0"
                    :itemsPerPage="props.permissions?.per_page || 10"
                    :currentPage="props.permissions?.current_page || 1" :defaultPage="1" />
            </CardFooter>
        </Card>
    </AppLayout>

    <Can permissions="permission_delete">
        <DeleteModal v-model:show="showDeleteModal" :item="currentItem" @confirm="deletePermission"
            :loading="isDeleting" />
    </Can>

    <Can permissions="permission_show">
        <PermissionShowDialog v-model:show="showShowDialog" :item="currentItem" />
    </Can>

    <Can :permissions="['permission_create', 'permission_edit']">
        <PermissionFormDialog v-model:show="showFormDialog" :method_type="method_type" :action="action"
            :item="currentItem" :loading="showLoading" />
    </Can>

</template>
