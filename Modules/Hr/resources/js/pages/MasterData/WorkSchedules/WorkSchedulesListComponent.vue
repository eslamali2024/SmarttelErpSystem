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
import { router } from '@inertiajs/vue3';
import workSchedulesRoute from '@/routes/hr/master-data/work-schedules';
import DeleteModal from '@/components/ui/Modal/DeleteModal.vue';
import { useI18n } from 'vue-i18n';
import TablePaginationNumbers from '@/components/ui/table/TablePaginationNumbers.vue';
import Can from '@/components/ui/Auth/Can.vue';
import {
    Card,
    CardHeader,
    CardTitle,
    CardContent,
    CardFooter
} from '@/components/ui/card';
import { useSearchTable } from '@/composables/useSearchTable';
import TableActions from '@/components/ui/table/TableActions.vue';
import A from '@/components/ui/a/A.vue';
import { strLimit } from '@/utils/strLimit';

// Master Data
const { t } = useI18n();
const currentItem = ref<any>(null)

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('hr'), href: null },
    { title: t('master_data'), href: null },
    { title: t('work_schedules'), href: null },
];

const props = defineProps<{
    work_schedules?: {
        data?: any[],
        links?: any[],
        total?: number,
        per_page?: number,
        current_page?: number
    }
}>()

// reactive search
const { search } = useSearchTable(workSchedulesRoute.index().url, {
    name: '',
});

// Delete Modal
const showDeleteModal = ref(false)
const isDeleting = ref(false)

const toggleShowDeleteModal = (work_schedule: any) => {
    currentItem.value = work_schedule
    showDeleteModal.value = true
}

const deleteTimeMaangement = () => {
    if (!currentItem.value) return
    isDeleting.value = true

    router.delete(workSchedulesRoute.destroy(currentItem.value.id).url, {
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

    <Head :title="$t('work_schedules')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <CardHeader class="flex justify-between items-center">
                <CardTitle>{{ $t('work_schedules') }}</CardTitle>
                <Can permissions="work_schedule_create">
                    <A :href="workSchedulesRoute.create().url"
                        class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm">
                        <i class="ri ri-add-line"></i> {{ $t("add_work_schedule") }}
                    </A>
                </Can>
            </CardHeader>

            <CardContent>
                <Table>
                    <TableCaption>{{ $t('work_schedules') }}</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px] text-center">{{ $t('no') }}</TableHead>
                            <TableHead class="text-center">{{ $t('name') }}</TableHead>
                            <TableHead class="text-center text-nowrap">{{ $t('start_time') }}</TableHead>
                            <TableHead class="text-center text-nowrap">{{ $t('end_time') }}</TableHead>
                            <TableHead class="text-center text-nowrap">{{ $t('allow_late_sign_in') }}</TableHead>
                            <TableHead class="text-center text-nowrap">{{ $t('allow_early_sign_out') }}</TableHead>
                            <TableHead class="text-center text-nowrap">{{ $t('actions') }}</TableHead>
                        </TableRow>
                        <TableRow>
                            <TableHead class="w-[100px]"></TableHead>
                            <TableHead class="p-2">
                                <Input :placeholder="$t('name')" v-model="search.name" />
                            </TableHead>
                            <TableHead class="p-2"></TableHead>
                            <TableHead class="p-2"></TableHead>
                            <TableHead class="p-2"></TableHead>
                            <TableHead></TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow v-for="(work_schedule, index) in props.work_schedules?.data || []"
                            :key="work_schedule.id">
                            <TableCell class="font-medium text-center">
                                <TablePaginationNumbers :items="props.work_schedules" :index="index" />
                            </TableCell>
                            <TableCell class="text-center">
                                {{ strLimit(work_schedule.name, 15) }}
                            </TableCell>
                            <TableCell class="text-center">
                                {{ work_schedule.start_time }}
                            </TableCell>
                            <TableCell class="text-center">
                                {{ work_schedule.end_time }}
                            </TableCell>
                            <TableCell class="text-center">
                                {{ work_schedule.allow_late_sign_in }}
                            </TableCell>
                            <TableCell class="text-center">
                                {{ work_schedule.allow_early_sign_out }}
                            </TableCell>
                            <TableCell>
                                <TableActions class="text-center flex justify-center" canShow="work_schedule_show"
                                    :show="workSchedulesRoute.show(work_schedule.id).url" canEdit="work_schedule_edit"
                                    :edit="workSchedulesRoute.edit(work_schedule.id).url"
                                    canDelete="work_schedule_delete"
                                    :delete="() => toggleShowDeleteModal(work_schedule)" />
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableFooter>
                        <TableEmpty v-if="!props.work_schedules?.data?.length" :colspan="7">
                            {{ $t('no_data') }}
                        </TableEmpty>
                    </TableFooter>
                </Table>
            </CardContent>
            <CardFooter>
                <PaginationUse :items="props.work_schedules?.links || []" :total="props.work_schedules?.total || 0"
                    :itemsPerPage="props.work_schedules?.per_page || 10"
                    :currentPage="props.work_schedules?.current_page || 1" :defaultPage="1" />
            </CardFooter>
        </Card>
    </AppLayout>

    <Can permissions="work_schedule_delete">
        <DeleteModal v-model:show="showDeleteModal" :item="currentItem" @confirm="deleteTimeMaangement"
            :loading="isDeleting" />
    </Can>
</template>
