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
import divisionsRoute from '@/routes/hr/organization/divisions';
import Card from '@/components/ui/Card.vue';
import { ref } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import DeleteModal from '@/components/ui/Modal/DeleteModal.vue';
import DivisionFormDialog from './DivisionFormDialog.vue';
import DivisionShowDialog from './DivisionShowDialog.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('hr'), href: null },
    { title: t('organization'), href: null },
    { title: t('divisions'), href: null },
];

const props = defineProps<{
    divisions?: {
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
    router.get(divisionsRoute.index().url, search, {
        preserveState: true,
        replace: true,
    })
}, { deep: true });


// Form Data
const showFormDialog = ref(false)
const currentItem = ref<any>(null)

const method_type = ref("post");
const action = ref(divisionsRoute.store().url);

const toggleFormDialog = (item?: any) => {
    showFormDialog.value = true;
    currentItem.value = item;

    if (item) {
        method_type.value = "put";
        action.value = divisionsRoute.update(item.id).url;
    } else {
        method_type.value = "post";
        action.value = divisionsRoute.store().url;
    }
}


// Delete Modal
const showDeleteModal = ref(false)
const isDeleting = ref(false)

const toggleShowDeleteModal = (division: any) => {
    currentItem.value = division
    showDeleteModal.value = true
}

const deleteDivision = () => {
    if (!currentItem.value) return
    isDeleting.value = true

    router.delete(divisionsRoute.destroy(currentItem.value.id).url, {
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

    <Head :title="$t('divisions')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <template #header>
                <h4>{{ $t('divisions') }}</h4>
                <Button v-on:click="toggleFormDialog(null)"
                    class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm">
                    <i class="ri ri-add-line"></i> {{ $t("add_division") }}
                </Button>
            </template>

            <template #body>
                <Table>
                    <TableCaption>{{ $t('divisions') }}</TableCaption>
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
                        <TableRow v-for="(division, index) in props.divisions?.data || []" :key="division.id">
                            <TableCell class="font-medium text-center">{{ index + 1 }}</TableCell>
                            <TableCell class="text-center">{{ division.code ?? '-' }}</TableCell>
                            <TableCell class="text-center">{{ division.name ?? '-' }}</TableCell>
                            <TableCell class="text-center">{{ division.manager?.name ?? '-' }}</TableCell>
                            <TableCell class="text-center flex">
                                <Button size="sm" v-on:click="toggleShowDialog(division)"
                                    class="mr-2 bg-blue-500 cursor-pointer text-white hover:bg-blue-600">
                                    <i class="ri ri-eye-line"></i>
                                </Button>
                                <Button size="sm" v-on:click="toggleFormDialog(division)"
                                    class="mr-2 bg-yellow-500 cursor-pointer text-white hover:bg-yellow-600">
                                    <i class="ri ri-pencil-line"></i>
                                </Button>
                                <Button size="sm" v-on:click="toggleShowDeleteModal(division)"
                                    class="bg-red-500 cursor-pointer text-white hover:bg-red-600 ">
                                    <i class="ri ri-delete-bin-line"></i>
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableFooter>
                        <TableEmpty v-if="!props.divisions?.data?.length" :colspan="5">
                            {{ $t('no_data') }}
                        </TableEmpty>
                    </TableFooter>
                </Table>
            </template>

            <template #footer>
                <PaginationUse :items="props.divisions?.links || []" :total="props.divisions?.total || 0"
                    :itemsPerPage="props.divisions?.per_page || 10" :currentPage="props.divisions?.current_page || 1"
                    :defaultPage="1" />
            </template>
        </Card>
    </AppLayout>

    <DeleteModal v-model:show="showDeleteModal" :item="currentItem" @confirm="deleteDivision" :loading="isDeleting" />

    <DivisionShowDialog v-model:show="showShowDialog" :item="currentItem" />

    <DivisionFormDialog v-model:show="showFormDialog" :method_type="method_type" :action="action"
        :managers="props.managers" :item="currentItem" />

</template>
