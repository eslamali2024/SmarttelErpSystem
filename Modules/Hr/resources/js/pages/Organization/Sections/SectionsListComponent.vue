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
import sectionsRoute from '@/routes/hr/organization/sections';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/button/Button.vue';
import DeleteModal from '@/components/ui/Modal/DeleteModal.vue';
import SectionFormDialog from './SectionFormDialog.vue';
import SectionShowDialog from './SectionShowDialog.vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('hr'), href: null },
    { title: t('organization'), href: null },
    { title: t('sections'), href: null },
];

const props = defineProps<{
    sections?: {
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
    manager: { name: urlParams.get('manager') ?? '' },
    division: { name: urlParams.get('division') ?? '' },
    department: { name: urlParams.get('department') ?? '' }
});

// watch search changes
watch(search, () => {
    router.get(sectionsRoute.index().url, search, {
        preserveState: true,
        replace: true,
    })
}, { deep: true });


// Form Data
const showFormDialog = ref(false)
const currentItem = ref<any>(null)

const method_type = ref("post");
const action = ref(sectionsRoute.store().url);

const toggleFormDialog = (item?: any) => {
    showFormDialog.value = true;
    currentItem.value = item;

    if (item) {
        method_type.value = "put";
        action.value = sectionsRoute.update(item.id).url;
    } else {
        method_type.value = "post";
        action.value = sectionsRoute.store().url;
    }
}


// Delete Modal
const showDeleteModal = ref(false)
const isDeleting = ref(false)

const toggleShowDeleteModal = (section: any) => {
    currentItem.value = section
    showDeleteModal.value = true
}

const deleteSection = () => {
    if (!currentItem.value) return
    isDeleting.value = true

    router.delete(sectionsRoute.destroy(currentItem.value.id).url, {
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

    <Head :title="$t('sections')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <template #header>
                <h4>{{ $t('sections') }}</h4>
                <Button v-on:click="toggleFormDialog(null)"
                    class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm">
                    <i class="ri ri-add-line"></i> {{ $t("add_section") }}
                </Button>
            </template>

            <template #body>
                <Table>
                    <TableCaption>{{ $t('sections') }}</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px] text-center">{{ $t('no') }}</TableHead>
                            <TableHead class="text-center">{{ $t('code') }}</TableHead>
                            <TableHead class="text-center">{{ $t('name') }}</TableHead>
                            <TableHead class="text-center">{{ $t('division') }}</TableHead>
                            <TableHead class="text-center">{{ $t('department') }}</TableHead>
                            <TableHead class="text-center">{{ $t('manager') }}</TableHead>
                            <TableHead class="text-center">{{ $t('actions') }}</TableHead>
                        </TableRow>
                        <TableRow>
                            <TableHead class="w-[100px]"></TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('code')" v-model="search.code" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('name')" v-model="search.name" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('division')" v-model="search.division.name" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('department')" v-model="search.department.name" />
                            </TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('manager')" v-model="search.manager.name" />
                            </TableHead>
                            <TableHead></TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow v-for="(section, index) in props.sections?.data || []" :key="section.id">
                            <TableCell class="font-medium text-center">{{ index + 1 }}</TableCell>
                            <TableCell class="text-center">{{ section.code ?? '-' }}</TableCell>
                            <TableCell class="text-center">{{ section.name ?? '-' }}</TableCell>
                            <TableCell class="text-center">{{ section.division?.name ?? '-' }}</TableCell>
                            <TableCell class="text-center">{{ section.department?.name ?? '-' }}</TableCell>
                            <TableCell class="text-center">{{ section.manager?.name ?? '-' }}</TableCell>
                            <TableCell class="text-center flex">
                                <Button size="sm" v-on:click="toggleShowDialog(section)"
                                    class="mr-2 bg-blue-500 cursor-pointer text-white hover:bg-blue-600">
                                    <i class="ri ri-eye-line"></i>
                                </Button>
                                <Button size="sm" v-on:click="toggleFormDialog(section)"
                                    class="mr-2 bg-yellow-500 cursor-pointer text-white hover:bg-yellow-600">
                                    <i class="ri ri-pencil-line"></i>
                                </Button>
                                <Button size="sm" v-on:click="toggleShowDeleteModal(section)"
                                    class="bg-red-500 cursor-pointer text-white hover:bg-red-600 ">
                                    <i class="ri ri-delete-bin-line"></i>
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableFooter>
                        <TableEmpty v-if="!props.sections?.data?.length" :colspan="7">
                            {{ $t('no_data') }}
                        </TableEmpty>
                    </TableFooter>
                </Table>
            </template>

            <template #footer>
                <PaginationUse :items="props.sections?.links || []" :total="props.sections?.total || 0"
                    :itemsPerPage="props.sections?.per_page || 10" :currentPage="props.sections?.current_page || 1"
                    :defaultPage="1" />
            </template>
        </Card>
    </AppLayout>

    <DeleteModal v-model:show="showDeleteModal" :item="currentItem" @confirm="deleteSection" :loading="isDeleting" />

    <SectionShowDialog v-model:show="showShowDialog" :item="currentItem" />

    <SectionFormDialog v-model:show="showFormDialog" :method_type="method_type" :action="action"
        :divisions="props.divisions" :departments="props.departments" :managers="props.managers" :item="currentItem" />

</template>
