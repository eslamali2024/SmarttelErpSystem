<script setup lang="ts">
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import { required, minLength, maxLength } from '@vuelidate/validators'
import { useDynamicForm } from '@/composables/useDynamicForm';
import workSchedulesRoute from '@/routes/hr/master-data/work-schedules';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import A from '@/components/ui/a/A.vue';
import {
    Card,
    CardHeader,
    CardTitle,
    CardContent,
    CardAction,
    CardFooter
} from '@/components/ui/card';
import ButtonSubmit from '@/components/ui/button/ButtonSubmit.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Switch } from "@/components/ui/switch"
import WorkScheduleRulesFormComponent from './WorkScheduleRulesFormComponent.vue';
import { computed } from "vue";
import Can from '@/components/ui/Auth/Can.vue';

const props = defineProps<{
    method_type: string,
    action?: string,
    item?: any,
    days?: {
        day: number
        name: string
        status: number
    },
    isDisabled?: boolean
}>();

// Master Data
const { t } = useI18n();
const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('hr'), href: null },
    { title: t('master_data'), href: null },
    { title: t('work_schedules'), href: workSchedulesRoute.index().url },
    { title: props.method_type === 'post' ? t('add_work_schedule') : t('edit_work_schedule'), href: null },
];

// Vuelidate
const formSchema = (props: any) => ({
    formStructure: {
        name: props.item?.name ?? '',
        start_time: props.item?.start_time ?? '',
        end_time: props.item?.end_time ?? '',
        allow_late_sign_in: props.item?.allow_late_sign_in ?? 0,
        allow_early_sign_out: props.item?.allow_early_sign_out ?? 0,
        days: props.item?.days ?? props.days ?? [],
        rules: props.item?.rules ?? [],
    },
    validationRules: {
        name: { required, minLength: minLength(5), maxLength: maxLength(255) },
        start_time: { required },
        end_time: { required },
        allow_late_sign_in: { required },
        allow_early_sign_out: { required },
    }
})

const { form, $v } = useDynamicForm(props, formSchema)

const submitForm = () => {
    if (!props.action || !props.method_type) return;
    $v.value.$touch()
    if ($v.value.$invalid) return;

    const options = {
        onSuccess: () => {
            form.reset();
            $v.value.$reset();
        }
    };

    if (props.method_type === 'post') {
        form.post(props.action, options);
    } else if (props.method_type === 'put') {
        form.put(props.action, options);
    }
};

const cardTitle = computed(() => {
    switch (props.method_type) {
        case 'post':
            return t('add_work_schedule')
        case 'put':
            return t('edit_work_schedule')
        case 'show':
            return t('show_work_schedule')
        default:
            return t('add_work_schedule')
    }
})
</script>

<template>

    <Head :title="props.method_type === 'post' ? $t('add_work_schedule') : $t('edit_work_schedule')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <CardHeader class="flex justify-between items-center">
                <CardTitle>
                    {{ cardTitle }}
                </CardTitle>
                <CardAction class="flex items-center gap-2">
                    <A :href="workSchedulesRoute.index().url"
                        class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm">
                        <i class="ri ri-arrow-left-line"></i> {{ $t("back") }}
                    </A>
                    <Can v-if="isDisabled" permissions="work_schedule_edit">
                        <A :href="workSchedulesRoute.edit(props.item.id).url"
                            class="bg-yellow-500 cursor-pointer hover:bg-yellow-600 text-white" size="sm">
                            <i class="ri ri-edit-line"></i> {{ $t("edit") }}
                        </A>
                    </Can>
                </CardAction>
            </CardHeader>
            <CardContent>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 py-4">
                    <InputGroup v-model="form.name" :modelValueError="form?.errors?.name" :label="$t('name')"
                        :placeholder="$t('please_enter_a_name')" type="text" :vue-error="$v?.name" class="col-span-2"
                        :disabled="isDisabled" />

                    <InputGroup v-model="form.start_time" :modelValueError="form?.errors?.start_time"
                        :label="$t('start_time')" :placeholder="$t('please_enter_a_start_time')" type="time"
                        :vue-error="$v?.start_time" :disabled="isDisabled" />

                    <InputGroup v-model="form.end_time" :modelValueError="form?.errors?.end_time"
                        :label="$t('end_time')" :placeholder="$t('please_enter_a_end_time')" type="time"
                        :vue-error="$v?.end_time" :disabled="isDisabled" />

                    <InputGroup v-model="form.allow_late_sign_in" :modelValueError="form?.errors?.allow_late_sign_in"
                        :label="$t('allow_late_sign_in')" :placeholder="$t('please_enter_a_allow_late_sign_in')"
                        type="time" :vue-error="$v?.allow_late_sign_in" :disabled="isDisabled" />

                    <InputGroup v-model="form.allow_early_sign_out"
                        :modelValueError="form?.errors?.allow_early_sign_out" :label="$t('allow_early_sign_out')"
                        :placeholder="$t('please_enter_a_allow_early_sign_out')" type="time"
                        :vue-error="$v?.allow_early_sign_out" :disabled="isDisabled" />
                </div>
            </CardContent>
        </Card>
        <Card class="mt-4">
            <CardHeader>
                <CardTitle>
                    {{ $t('work_schedule_days') }}
                </CardTitle>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class=" w-[800px] text-center text-nowrap">
                                {{ $t('day') }}
                            </TableHead>
                            <TableHead class="text-center text-nowrap">
                                {{ $t('status') }}
                            </TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow v-for="day in form.days" :key="day.day">
                            <TableCell class="text-center text-nowrap">{{ day.name }}</TableCell>
                            <TableCell class="text-center text-nowrap">
                                <Switch v-model="day.status" :disabled="isDisabled" />
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>

        <WorkScheduleRulesFormComponent v-model:rules="form.rules" :form="form" :isDisabled />

        <Card class="mt-4" v-if="!isDisabled">
            <CardFooter class="flex items-center justify-end gap-2">
                <ButtonSubmit :loading="form.processing" :submit="submitForm">
                    {{ $t('save') }}
                </ButtonSubmit>
            </CardFooter>
        </Card>
    </AppLayout>
</template>
