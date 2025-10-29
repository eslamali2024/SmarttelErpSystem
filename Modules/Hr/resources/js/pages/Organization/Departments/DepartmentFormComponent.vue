<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import TextareaGroup from '@/components/ui/textarea-group/TextareaGroup.vue';
import FormRequest from '@/components/ui/formRequest/FormRequest.vue';
import { dashboard } from '@/routes';
import departmentsRoute from '@/routes/hr/organization/departments';
import SelectGroup from '@/components/ui/select-group/SelectGroup.vue';


const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Organization', href: null },
    { title: 'Departments', href: null },
];
const props = defineProps<{
    method_type: string,
    action: string,
    department?: any,
    managers?: any
}>()

const form = useForm({
    name: props.department?.name ?? '',
    manager_id: props.department?.manager_id ?? '',
    description: props.department?.description ?? '',
});

</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="bg-gray-500/50 p-4 rounded-xl">
                <div class="flex justify-between item-center">
                    <h4>departments</h4>
                </div>
                <hr class="my-2 w-5/6 mx-auto">
                <FormRequest :form="form" :action="props.action" :method_type="props.method_type"
                    :cancelRoute="departmentsRoute.index().url">
                    <div class="grid grid-cols-1 items-center gap-3">
                        <InputGroup v-model="form.name" :modelValueError="form.errors.name" label="Name"
                            placeholder="Please provide a valid name" type="text" />

                        <!-- <SelectGroup v-model="form.manager_id" :modelValueError="form.errors.manager_id" label="Manager"
                            placeholder="Please select a valid manager" :options="managers" /> -->

                        <div class="grid w-full col-span-2 gap-1.5">
                            <TextareaGroup v-model="form.description" :modelValueError="form.errors.description"
                                label="Description" placeholder="Please provide a valid description"
                                placeholder_message="Please provide a valid description" />
                        </div>
                    </div>
                </FormRequest>
            </div>
        </div>
    </AppLayout>
</template>
