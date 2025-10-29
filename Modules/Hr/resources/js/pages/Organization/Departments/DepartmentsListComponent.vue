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
import { reactive, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import departmentsRoute from '@/routes/hr/organization/departments';
import TableActions from '@/components/ui/table/TableActions.vue';
import A from '@/components/ui/a/A.vue';
import Card from '@/components/ui/Card.vue';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from "@/components/ui/alert-dialog"
import { ref } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import DeleteModal from '@/components/ui/Modal/DeleteModal.vue';
import DepartmentFormDialog from './DepartmentFormDialog.vue';
import DepartmentShowDialog from './DepartmentShowDialog.vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Organization', href: null },
    { title: 'Departments', href: null },
];

const props = defineProps<{
    departments?: {
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
const urlParams = new URLSearchParams(window.location.search);

const search = reactive({
    name: urlParams.get('name') ?? '',
    code: urlParams.get('code') ?? '',
    manager: { name: urlParams.get('manager') ?? '' }
});

// watch search changes
watch(search, () => {
    router.get(departmentsRoute.index().url, search, {
        preserveState: true,
        replace: true,
    })
}, { deep: true });


// Form Data
const showFormDialog = ref(false)
const currentItem = ref<any>(null)

const method_type = ref("post");
const action = ref(departmentsRoute.store().url);

const toggleFormDialog = (item?: any) => {
    showFormDialog.value = true;
    currentItem.value = item;

    if (item) {
        method_type.value = "put";
        action.value = departmentsRoute.update(item.id).url;
    } else {
        method_type.value = "post";
        action.value = departmentsRoute.store().url;
    }
}


// Delete Modal
const showDeleteModal = ref(false)
const isDeleting = ref(false)

const toggleShowDeleteModal = (department: any) => {
    currentItem.value = department
    showDeleteModal.value = true
}

const deleteDepartment = () => {
    if (!currentItem.value) return
    isDeleting.value = true

    router.delete(departmentsRoute.destroy(currentItem.value.id).url, {
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

    <Head :title="$t('departments')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <template #header>
                <h4>{{ $t('departments') }}</h4>
                <Button v-on:click="toggleFormDialog(null)"
                    class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm">
                    <i class="ri ri-add-line"></i> {{ $t("add_department") }}
                </Button>
            </template>

            <template #body>
                <Table>
                    <TableCaption>{{ $t('departments') }}</TableCaption>
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
                        <TableRow v-for="(department, index) in props.departments?.data || []" :key="department.id">
                            <TableCell class="font-medium text-center">{{ index + 1 }}</TableCell>
                            <TableCell class="text-center">{{ department.code ?? '-' }}</TableCell>
                            <TableCell class="text-center">{{ department.name ?? '-' }}</TableCell>
                            <TableCell class="text-center">{{ department.manager?.name ?? '-' }}</TableCell>
                            <TableCell class="text-center">
                                <Button size="sm" v-on:click="toggleShowDialog(department)"
                                    class="mr-2 bg-blue-500 cursor-pointer text-white hover:bg-blue-600">
                                    <i class="ri ri-eye-line"></i>
                                </Button>
                                <Button size="sm" v-on:click="toggleFormDialog(department)"
                                    class="mr-2 bg-yellow-500 cursor-pointer text-white hover:bg-yellow-600">
                                    <i class="ri ri-pencil-line"></i>
                                </Button>
                                <Button size="sm" v-on:click="toggleShowDeleteModal(department)"
                                    class="bg-red-500 cursor-pointer text-white hover:bg-red-600 ">
                                    <i class="ri ri-delete-bin-line"></i>
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableFooter>
                        <TableEmpty v-if="!props.departments?.data?.length" :colspan="5">
                            {{ $t('no_data') }}
                        </TableEmpty>
                    </TableFooter>
                </Table>
            </template>

            <template #footer>
                <PaginationUse :items="props.departments?.links || []" :total="props.departments?.total || 0"
                    :itemsPerPage="props.departments?.per_page || 10"
                    :currentPage="props.departments?.current_page || 1" :defaultPage="1" />
            </template>
        </Card>
    </AppLayout>

    <DeleteModal v-model:show="showDeleteModal" :item="currentItem" @confirm="deleteDepartment" :loading="isDeleting" />

    <DepartmentShowDialog v-model:show="showShowDialog" :item="currentItem" />

    <DepartmentFormDialog v-model:show="showFormDialog" :method_type="method_type" :action="action"
        :managers="props.managers" :item="currentItem" />

</template>
