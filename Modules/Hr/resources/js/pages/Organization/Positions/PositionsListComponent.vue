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
import positionsRoute from '@/routes/hr/organization/positions';
import Card from '@/components/ui/Card.vue';
import DeleteModal from '@/components/ui/Modal/DeleteModal.vue';
import Button from '@/components/ui/button/Button.vue';
import PositionFormDialog from './PositionFormDialog.vue';
import PositionShowDialog from './PositionShowDialog.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('hr'), href: null },
    { title: t('organization'), href: null },
    { title: t('positions'), href: null },
];


const props = defineProps<{
    positions?: {
        data?: any[],
        links?: any[],
        total?: number,
        per_page?: number,
        current_page?: number
    },
    divisions?: {
        id: number
        name: string
    },
    departments?: {
        id: number
        name: string
        division_id: number
    },
    sections?: {
        id: number
        name: string
        department_id: number
    }

}>()

// Form Data
const showFormDialog = ref(false)
const currentItem = ref<any>(null)

const method_type = ref("post");
const action = ref(positionsRoute.store().url);

const toggleFormDialog = (item?: any) => {
    showFormDialog.value = true;
    currentItem.value = item;

    if (item) {
        method_type.value = "put";
        action.value = positionsRoute.update(item.id).url;
    } else {
        method_type.value = "post";
        action.value = positionsRoute.store().url;
    }
}

// reactive search
const urlParams = new URLSearchParams(window.location.search);

const search = reactive({
    name: urlParams.get('name') ?? '',
    code: urlParams.get('code') ?? '',
    department: { name: urlParams.get('department') ?? '' },
    section: { name: urlParams.get('section') ?? '' },
    division: { name: urlParams.get('division') ?? '' }
});

// watch search changes
watch(search, () => {
    router.get(positionsRoute.index().url, search, {
        preserveState: true,
        replace: true,
    })
}, { deep: true });

// Delete Modal
const showDeleteModal = ref(false)
const isDeleting = ref(false)

const toggleShowDeleteModal = (position: any) => {
    currentItem.value = position
    showDeleteModal.value = true
}

const deletePosition = () => {
    if (!currentItem.value) return
    isDeleting.value = true

    router.delete(positionsRoute.destroy(currentItem.value.id).url, {
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
const toggleShowDialog = (position: any) => {
    currentItem.value = position
    showShowDialog.value = true
}
</script>

<template>

    <Head :title="$t('positions')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <template #header>
                <h4>{{ $t('positions') }}</h4>
                <Button v-on:click="toggleFormDialog(null)"
                    class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm">
                    <i class="ri ri-add-line"></i> {{ $t("add_position") }}
                </Button>
            </template>

            <template #body>
                <Table>
                    <TableCaption>{{ $t('positions') }}</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px] text-center">{{ $t('no') }}</TableHead>
                            <TableHead class="text-center">{{ $t('code') }}</TableHead>
                            <TableHead class="text-center">{{ $t('name') }}</TableHead>
                            <TableHead class="text-center">{{ $t('section') }}</TableHead>
                            <TableHead class="text-center">{{ $t('department') }}</TableHead>
                            <TableHead class="text-center">{{ $t('division') }}</TableHead>
                            <TableHead class="text-center">{{ $t('actions') }}</TableHead>
                        </TableRow>
                        <TableRow>
                            <TableHead class="w-[100px]"></TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('code')" v-model="search.code" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('section')" v-model="search.section.name" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('department')" v-model="search.department.name" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('division')" v-model="search.division.name" />
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
                            <TableCell class="text-center">{{ position.section?.name ?? '-' }}</TableCell>
                            <TableCell class="text-center">{{ position.department?.name ?? '-' }}</TableCell>
                            <TableCell class="text-center">{{ position.division?.name ?? '-' }}</TableCell>
                            <TableCell class="text-center">{{ position.name }}</TableCell>
                            <TableCell class="text-center flex">
                                <!-- Icon Edit:start -->
                                <Button size="sm" v-on:click="toggleShowDialog(position)"
                                    class="mr-2 bg-blue-500 cursor-pointer text-white hover:bg-blue-600">
                                    <i class="ri ri-eye-line"></i>
                                </Button>

                                <Button size="sm" v-on:click="toggleFormDialog(position)"
                                    class="mr-2 bg-yellow-500 cursor-pointer text-white hover:bg-yellow-600">
                                    <i class="ri ri-pencil-line"></i>
                                </Button>

                                <!-- Icon Edit:end -->
                                <!-- Icon Delete:start -->
                                <Button size="sm" v-on:click="toggleShowDeleteModal(position)"
                                    class="bg-red-500 cursor-pointer text-white hover:bg-red-600 ">
                                    <i class="ri ri-delete-bin-line"></i>
                                </Button>
                                <!-- Icon Delete:end -->
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableFooter>
                        <TableEmpty v-if="!props.positions?.data?.length" :colspan="7">
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

        <DeleteModal v-model:show="showDeleteModal" :item="currentItem" @confirm="deletePosition"
            :loading="isDeleting" />

        <PositionShowDialog v-model:show="showShowDialog" :item="currentItem" />

        <PositionFormDialog v-model:show="showFormDialog" :method_type="method_type" :action="action"
            :departments="props.departments" :divisions="props.divisions" :sections="props.sections"
            :item="currentItem" />
    </AppLayout>

</template>
