<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import TextareaGroup from '@/components/ui/textarea-group/TextareaGroup.vue';
import FormRequest from '@/components/ui/formRequest/FormRequest.vue';
import { dashboard } from '@/routes';
import positionsRoute from '@/routes/hr/organization/positions';
import SelectGroup from '@/components/ui/select-group/SelectGroup.vue';
import Card from '@/components/ui/Card.vue';
import A from '@/components/ui/a/A.vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Organization', href: null },
    { title: 'Departments', href: null },
];
const props = defineProps<{
    method_type: string,
    action: string,
    position?: any,
    departments?: any
}>()

const form = useForm({
    name: props.position?.name ?? '',
    department_id: props.position?.department_id ?? '',
    description: props.position?.description ?? '',
});

</script>

<template>

    <Head :title="props.method_type == 'post' ? $t('add_position') : $t('update_position')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <template #header>
                <h4>{{ props.method_type == 'post' ? $t('add_position') : $t('update_position') }}</h4>
                <A class="bg-red-500/50 cursor-pointer hover:bg-red-600 text-white" size="sm"
                    :href="positionsRoute.index().url">
                    <i class="ri ri-arrow-left-line"></i> {{ $t("back") }}
                </A>
            </template>
            <template #body>
                <FormRequest :form="form" :action="props.action" :method_type="props.method_type"
                    :cancelRoute="positionsRoute.index().url">
                    <div class="grid grid-cols-1 items-center gap-3">
                        <InputGroup v-model="form.name" :modelValueError="form.errors.name" :label="$t('name')"
                            :placeholder="$t('please_enter_a_name')" type="text" />

                        <SelectGroup v-model="form.department_id" :modelValueError="form.errors.department_id"
                            :label="$t('department')" :placeholder="$t('please_select_a_department')"
                            :options="departments" />

                        <div class="grid w-full col-span-2 gap-1.5">
                            <TextareaGroup v-model="form.description" :modelValueError="form.errors.description"
                                :label="$t('description')" :placeholder="$t('please_enter_a_description')"
                                :placeholder_message="$t('please_enter_a_description')" />
                        </div>
                    </div>
                </FormRequest>
            </template>
        </Card>
    </AppLayout>
</template>
