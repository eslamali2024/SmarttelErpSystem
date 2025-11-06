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
import rolesRoute from '@/routes/user-management/roles';
import Button from '@/components/ui/button/Button.vue';
import DeleteModal from '@/components/ui/Modal/DeleteModal.vue';
import RoleFormDialog from './RoleFormDialog.vue';
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

const { t } = useI18n();
const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('user_management'), href: null },
    { title: t('roles'), href: null },
];

const props = defineProps<{
    roles?: {
        data?: any[],
        links?: any[],
        total?: number,
        per_page?: number,
        current_page?: number
    },
}>()

// reactive search
const { search } = useSearchTable(rolesRoute.index().url);


// Form Data
const showLoading = ref(false)
const showFormDialog = ref(false)
const currentItem = ref<any>(null)
const method_type = ref("post");
const action = ref(rolesRoute.store().url);
const permissions = ref<any[]>([])
const permissionsCache = ref<any[] | null>(null)  // reactive cache

/**
 * Loads all the permissions from the server and stores them in the reactive cache.
 * If the cache already has permissions, it will use the cached value instead of making a request.
 * @returns {Promise<void>}
 */
async function loadPermissions(): Promise<void> {
    try {
        if (permissionsCache.value) {
            permissions.value = permissionsCache.value
            return
        }

        const response = await axios.get(rolesRoute.create().url)
        permissions.value = Array.isArray(response.data) ? response.data[0] : response.data

        permissionsCache.value = permissions.value
    } catch (error) {
        console.error('Error loading permissions:', error)
    }
}

/**
 * Retrieves a role from the server.
 * @param {any} item - The item to be retrieved, or null if no item is provided.
 * @throws {Error} If the item is not found.
 * @returns {Promise<void>}
 */
async function fetchRoleDetails(item?: any): Promise<void> {
    try {
        if (item) {
            const response = await axios.get(rolesRoute.show(item).url)
            if (!response.data) throw new Error('Item not found')
            currentItem.value = response.data;
        } else {
            throw new Error('Item not found')
        }
    } catch (error) {
        console.error('Error loading get role:', error)
    }
}

/**
 * Toggle the form dialog for adding or editing a role
 * @param {any} item - The item to be edited, or null for adding a new item
 */
const toggleFormDialog = async (item?: any, actionType?: string) => {
    showLoading.value = true
    showFormDialog.value = true
    currentItem.value = item

    await loadPermissions()

    if (item) {
        await fetchRoleDetails(item?.id ?? null)
        method_type.value = actionType ?? "put"
        action.value = rolesRoute.update(item.id).url
    } else {
        method_type.value = "post"
        action.value = rolesRoute.store().url
    }

    showLoading.value = false
}

// Delete Modal
const showDeleteModal = ref(false)
const isDeleting = ref(false)

const toggleShowDeleteModal = (role: any) => {
    currentItem.value = role
    showDeleteModal.value = true
}

const deleteRole = () => {
    if (!currentItem.value) return
    isDeleting.value = true

    router.delete(rolesRoute.destroy(currentItem.value.id).url, {
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
</script>

<template>

    <Head :title="$t('roles')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <CardHeader class="flex justify-between items-center">
                <CardTitle>
                    {{ $t('roles') }}
                </CardTitle>
                <Can permissions="role_create">
                    <Button v-on:click="toggleFormDialog(null)"
                        class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm">
                        <i class="ri ri-add-line"></i> {{ $t("add_role") }}
                    </Button>
                </Can>
            </CardHeader>

            <CardContent>
                <Table>
                    <TableCaption>{{ $t('roles') }}</TableCaption>
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
                        <TableRow v-for="(role, index) in props.roles?.data || []" :key="role.id">
                            <TableCell class="font-medium text-center">
                                <TablePaginationNumbers :items="props.roles" :index="index" />
                            </TableCell>
                            <TableCell class="text-center">{{ role.name ?? '-' }}</TableCell>
                            <TableCell>
                                <TableActionsDialog class="text-center flex justify-center" canShow="role_show"
                                    :show="() => toggleFormDialog(role, 'show')" canEdit="role_edit"
                                    :edit="() => toggleFormDialog(role)" canDelete="role_delete"
                                    :delete="() => toggleShowDeleteModal(role)" />
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableFooter>
                        <TableEmpty v-if="!props.roles?.data?.length" :colspan="3">
                            {{ $t('no_data') }}
                        </TableEmpty>
                    </TableFooter>
                </Table>
            </CardContent>

            <CardFooter>
                <PaginationUse :items="props.roles?.links || []" :total="props.roles?.total || 0"
                    :itemsPerPage="props.roles?.per_page || 10" :currentPage="props.roles?.current_page || 1"
                    :defaultPage="1" />
            </CardFooter>
        </Card>
    </AppLayout>

    <Can permissions="role_delete">
        <DeleteModal v-model:show="showDeleteModal" :item="currentItem" @confirm="deleteRole" :loading="isDeleting" />
    </Can>

    <Can :permissions="['role_create', 'role_show', 'role_edit']">
        <RoleFormDialog v-model:show="showFormDialog" :method_type="method_type" :action="action"
            :permissions="permissionsCache" :item="currentItem" :loading="showLoading" />
    </Can>
</template>
