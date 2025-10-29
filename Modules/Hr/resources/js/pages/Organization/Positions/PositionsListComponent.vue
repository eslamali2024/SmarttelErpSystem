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
import positionsRoute from '@/routes/hr/organization/positions';
import TableActions from '@/components/ui/table/TableActions.vue';
import A from '@/components/ui/a/A.vue';
import Card from '@/components/ui/Card.vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Organization', href: null },
    { title: 'Positions', href: null },
];

const props = defineProps<{
    positions?: {
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
    code: urlParams.get('code') ?? '',
    department: { name: urlParams.get('department') ?? '' }
});

// watch search changes
watch(search, () => {
    router.get(positionsRoute.index().url, search, {
        preserveState: true,
        replace: true,
    })
}, { deep: true });

// delete position
const deletePosition = (id: number) => {
    router.delete(positionsRoute.destroy(id).url);
}
</script>

<template>

    <Head :title="$t('positions')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <template #header>
                <h4>{{ $t('positions') }}</h4>
                <A class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm"
                    :href="positionsRoute.create().url">
                    <i class="ri ri-add-line"></i> {{ $t("add_position") }}
                </A>
            </template>

            <template #body>
                <Table>
                    <TableCaption>{{ $t('positions') }}</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px] text-center">{{ $t('no') }}</TableHead>
                            <TableHead class="text-center">{{ $t('code') }}</TableHead>
                            <TableHead class="text-center">{{ $t('department') }}</TableHead>
                            <TableHead class="text-center">{{ $t('name') }}</TableHead>
                            <TableHead class="text-center">{{ $t('actions') }}</TableHead>
                        </TableRow>
                        <TableRow>
                            <TableHead class="w-[100px]"></TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('code')" v-model="search.code" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('department')" v-model="search.department.name" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('name')" v-model="search.name" />
                            </TableHead>
                            <TableHead></TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow v-for="(position, index) in props.positions?.data || []" :key="position.id">
                            <TableCell class="font-medium text-center">{{ index + 1 }}</TableCell>
                            <TableCell class="text-center">{{ position.code ?? '-' }}</TableCell>
                            <TableCell class="text-center">{{ position.department?.name ?? '-' }}</TableCell>
                            <TableCell class="text-center">{{ position.name }}</TableCell>
                            <TableActions :edit="positionsRoute.edit(position.id).url"
                                :delete="() => deletePosition(position.id)" />
                        </TableRow>
                    </TableBody>

                    <TableFooter>
                        <TableEmpty v-if="!props.positions?.data?.length" :colspan="5">
                            {{ $t('no_data') }}
                        </TableEmpty>
                    </TableFooter>
                </Table>
            </template>
            <template #footer>
                <PaginationUse :items="props.positions?.links || []" :total="props.positions?.total || 0"
                    :itemsPerPage="props.positions?.per_page || 10" :currentPage="props.positions?.current_page || 1"
                    :defaultPage="1" />
            </template>
        </Card>
    </AppLayout>
</template>
