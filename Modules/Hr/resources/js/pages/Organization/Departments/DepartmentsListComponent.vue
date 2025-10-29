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
    }
}>()

// reactive search
const urlParams = new URLSearchParams(window.location.search);

const search = reactive({
    name: urlParams.get('name') ?? '',
    manager: { name: urlParams.get('manager') ?? '' }
});

// watch search changes
watch(search, () => {
    router.get(departmentsRoute.index().url, search, {
        preserveState: true,
        replace: true,
    })
}, { deep: true });

// delete department
const deleteDepartment = (id: number) => {
    router.delete(departmentsRoute.destroy(id).url);
}
</script>

<template>

    <Head title="Departments" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="bg-gray-500/50 p-4 rounded-xl">
                <div class="flex justify-between items-center">
                    <h4>{{ $t('departments') }}</h4>
                    <A class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm"
                        :href="departmentsRoute.create().url">
                        <i class="ri ri-add-line"></i> {{ $t("add_department") }}
                    </A>
                </div>
                <hr class="my-2 w-5/6 mx-auto">

                <Table class="bg-gray-500/50 rounded-sm p-1">
                    <TableCaption>{{ $t('departments') }}</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px] text-center">{{ $t('no') }}</TableHead>
                            <TableHead class="text-center">{{ $t('name') }}</TableHead>
                            <TableHead class="text-center">{{ $t('manager') }}</TableHead>
                            <TableHead class="text-center">{{ $t('actions') }}</TableHead>
                        </TableRow>
                        <TableRow>
                            <TableHead class="w-[100px]"></TableHead>
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
                            <TableCell class="text-center">{{ department.name }}</TableCell>
                            <TableCell class="text-center">{{ department.manager?.name ?? '-' }}</TableCell>
                            <TableActions :edit="departmentsRoute.edit(department.id).url"
                                :delete="() => deleteDepartment(department.id)" />
                        </TableRow>
                    </TableBody>

                    <TableFooter>
                        <TableEmpty v-if="!props.departments?.data?.length" :colspan="4">
                            {{ $t('no_data') }}
                        </TableEmpty>
                    </TableFooter>
                </Table>

                <hr class="my-2 w-5/6 mx-auto">

                <PaginationUse :items="props.departments?.links || []" :total="props.departments?.total || 0"
                    :itemsPerPage="props.departments?.per_page || 10"
                    :currentPage="props.departments?.current_page || 1" :defaultPage="1" />
            </div>
        </div>
    </AppLayout>
</template>
