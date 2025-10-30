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
import permissionsRoute from '@/routes/user-management/permissions';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/button/Button.vue';
import DeleteModal from '@/components/ui/Modal/DeleteModal.vue';
import PermissionFormDialog from './PermissionFormDialog.vue';
import PermissionShowDialog from './PermissionShowDialog.vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
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
const urlParams = new URLSearchParams(window.location.search);

const search = reactive({
    name: urlParams.get('name') ?? '',
});

// watch search changes
watch(search, () => {
    router.get(permissionsRoute.index().url, search, {
        preserveState: true,
        replace: true,
    })
}, { deep: true });


// Form Data
const showFormDialog = ref(false)
const currentItem = ref<any>(null)

const method_type = ref("post");
const action = ref(permissionsRoute.store().url);

const toggleFormDialog = (item?: any) => {
    showFormDialog.value = true;
    currentItem.value = item;

    if (item) {
        method_type.value = "put";
        action.value = permissionsRoute.update(item.id).url;
    } else {
        method_type.value = "post";
        action.value = permissionsRoute.store().url;
    }
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
            <template #header>
                <h4>{{ $t('permissions') }}</h4>
                <Button v-on:click="toggleFormDialog(null)"
                    class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm">
                    <i class="ri ri-add-line"></i> {{ $t("add_permission") }}
                </Button>
            </template>

            <template #body>
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
                            <TableCell class="font-medium text-center">{{ index + 1 }}</TableCell>
                            <TableCell class="text-center">{{ permission.name ?? '-' }}</TableCell>
                            <TableCell class="text-center flex justify-center">
                                <Button size="sm" v-on:click="toggleShowDialog(permission)"
                                    class="mr-2 bg-blue-500 cursor-pointer text-white hover:bg-blue-600">
                                    <i class="ri ri-eye-line"></i>
                                </Button>
                                <Button size="sm" v-on:click="toggleFormDialog(permission)"
                                    class="mr-2 bg-yellow-500 cursor-pointer text-white hover:bg-yellow-600">
                                    <i class="ri ri-pencil-line"></i>
                                </Button>
                                <Button size="sm" v-on:click="toggleShowDeleteModal(permission)"
                                    class="bg-red-500 cursor-pointer text-white hover:bg-red-600 ">
                                    <i class="ri ri-delete-bin-line"></i>
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableFooter>
                        <TableEmpty v-if="!props.permissions?.data?.length" :colspan="3">
                            {{ $t('no_data') }}
                        </TableEmpty>
                    </TableFooter>
                </Table>
            </template>

            <template #footer>
                <PaginationUse :items="props.permissions?.links || []" :total="props.permissions?.total || 0"
                    :itemsPerPage="props.permissions?.per_page || 10"
                    :currentPage="props.permissions?.current_page || 1" :defaultPage="1" />
            </template>
        </Card>
    </AppLayout>

    <DeleteModal v-model:show="showDeleteModal" :item="currentItem" @confirm="deletePermission" :loading="isDeleting" />

    <PermissionShowDialog v-model:show="showShowDialog" :item="currentItem" />

    <PermissionFormDialog v-model:show="showFormDialog" :method_type="method_type" :action="action"
        :item="currentItem" />

</template>
