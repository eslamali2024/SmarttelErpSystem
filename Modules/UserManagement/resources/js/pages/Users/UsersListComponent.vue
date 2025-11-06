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
import userRoute from '@/routes/user-management/users';
import DeleteModal from '@/components/ui/Modal/DeleteModal.vue';
import UserFormDialog from './UserFormDialog.vue';
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
import { strLimit } from '@/utils/strLimit';

const { t } = useI18n();
const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('user_management'), href: null },
    { title: t('users'), href: null },
];

const props = defineProps<{
    users?: {
        data?: any[],
        links?: any[],
        total?: number,
        per_page?: number,
        current_page?: number
    },
}>()

// reactive search
const { search } = useSearchTable(userRoute.index().url, {
    name: '',
    email: '',
    roles: {
        name: ''
    }
});

// Form Data
const showFormDialog = ref(false)
const currentItem = ref<any>(null)
const method_type = ref("post");
const action = ref('');
const roles = ref<any[]>([])
const rolesCache = ref<any[] | null>(null)
const permissions = ref<any[]>([])
const permissionsCache = ref<any[] | null>(null)
const showLoading = ref(false)

/**
 * Loads the initial roles and permissions data from the server and stores them in the reactive cache.
 * If the cache already has roles and permissions, it will use the cached value instead of making a request.
 * @returns {Promise<void>}
 */
async function loadInitialData(): Promise<void> {
    try {
        if (rolesCache.value && permissionsCache.value) {
            roles.value = rolesCache.value
            permissions.value = permissionsCache.value
            return
        }

        const response = await axios.get(userRoute.create().url)
        roles.value = Array.isArray(response.data) ? response.data[0].roles : response.data.roles
        permissions.value = Array.isArray(response.data) ? response.data[0].permissions : response.data.permissions

        rolesCache.value = roles.value
        permissionsCache.value = permissions.value
    } catch (error) {
        console.error('Error loading roles:', error)
    }
}

/**
 * Retrieves a role from the server.
 * @param {any} item - The item to be retrieved, or null if no item is provided.
 * @throws {Error} If the item is not found.
 * @returns {Promise<void>}
 */
async function fetchUserDetails(item?: any): Promise<void> {
    try {
        if (item) {
            const response = await axios.get(userRoute.show(item).url)
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

    await loadInitialData()

    if (item) {
        await fetchUserDetails(item?.id ?? null)
        method_type.value = actionType ?? "put"
        action.value = userRoute.update(item.id).url
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

const deleteUser = () => {
    if (!currentItem.value) return
    isDeleting.value = true

    router.delete(userRoute.destroy(currentItem.value.id).url, {
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

    <Head :title="$t('users')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <CardHeader class="flex justify-between items-center">
                <CardTitle>
                    {{ $t('users') }}
                </CardTitle>
            </CardHeader>

            <CardContent>
                <Table>
                    <TableCaption>{{ $t('users') }}</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px] text-center">{{ $t('no') }}</TableHead>
                            <TableHead class="text-center">{{ $t('name') }}</TableHead>
                            <TableHead class="text-center">{{ $t('email') }}</TableHead>
                            <TableHead class="text-center">{{ $t('roles') }}</TableHead>
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
                                <Input :placeholder="$t('role')" v-model="search.roles.name" />
                            </TableHead>
                            <TableHead></TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow v-for="(user, index) in props.users?.data || []" :key="user.id">
                            <TableCell class="font-medium text-center">
                                <TablePaginationNumbers :items="props.users" :index="index" />
                            </TableCell>
                            <TableCell class="text-center">{{ user.name ?? '-' }}</TableCell>
                            <TableCell class="text-center">{{ user.email ?? '-' }}</TableCell>
                            <TableCell class="text-center">
                                {{
                                    strLimit(user.roles?.map((role: any) => role?.name).join(', ').toString(), 15)
                                }}
                            </TableCell>
                            <TableCell>
                                <TableActionsDialog class="text-center flex justify-center" canShow="user_show"
                                    :show="() => toggleFormDialog(user, 'show')" canEdit="user_edit"
                                    :edit="() => toggleFormDialog(user)" canDelete="user_delete"
                                    :delete="() => toggleShowDeleteModal(user)" />
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableFooter>
                        <TableEmpty v-if="!props.users?.data?.length" :colspan="5">
                            {{ $t('no_data') }}
                        </TableEmpty>
                    </TableFooter>
                </Table>
            </CardContent>

            <CardFooter>
                <PaginationUse :items="props.users?.links || []" :total="props.users?.total || 0"
                    :itemsPerPage="props.users?.per_page || 10" :currentPage="props.users?.current_page || 1"
                    :defaultPage="1" />
            </CardFooter>
        </Card>
    </AppLayout>

    <Can permissions="user_delete">
        <DeleteModal v-model:show="showDeleteModal" :item="currentItem" @confirm="deleteUser" :loading="isDeleting" />
    </Can>

    <Can :permissions="['user_create', 'user_show', 'user_edit']">
        <UserFormDialog v-model:show="showFormDialog" :method_type="method_type" :action="action" :roles="rolesCache"
            :permissions="permissionsCache" :item="currentItem" :loading="showLoading" />
    </Can>
</template>
