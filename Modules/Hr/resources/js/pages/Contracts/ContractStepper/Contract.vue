<script setup lang="ts">
import { nextTick, computed, watch } from 'vue'
import { useDynamicForm } from '@/composables/useDynamicForm';
import { required, maxLength } from '@vuelidate/validators'
import SelectGroup from '@/components/ui/select-group/SelectGroup.vue';
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import TextareaGroup from '@/components/ui/textarea-group/TextareaGroup.vue';

// Props
const props = defineProps<{
    item?: any,
    employee_id?: number | null | string,
    employees?: { id: number; name: string },
    divisions?: { id: number; name: string },
    departments?: { id: number; name: string; division_id: number }[],
    sections?: { id: number; name: string; department_id: number }[],
    positions?: { id: number; name: string; section_id: number }[],
    time_managements?: { id: number; name: string },
    work_schedules?: { id: number; name: string },
    step: number,
    form: any,
}>();

const emit = defineEmits(['update:form']);

// Vuelidate schema
const formSchema = (props: any) => ({
    formStructure: {
        employee_id: props.item?.employee_id ?? props.employee_id ?? '',
        division_id: props.item?.division_id ?? '',
        department_id: props.item?.department_id ?? '',
        section_id: props.item?.section_id ?? '',
        position_id: props.item?.position_id ?? '',
        start_date: props.item?.start_date ?? new Date().toISOString().split('T')[0],
        end_date: props.item?.end_date ?? '',
        notes: props.item?.notes ?? '',
        time_management_id: props.item?.time_management_id ?? '',
        work_schedule_id: props.item?.work_schedule_id ?? '',
    },
    validationRules: {
        employee_id: { required },
        division_id: { required },
        department_id: { required },
        section_id: { required },
        position_id: { required },
        start_date: { required },
        end_date: { required },
        notes: { maxLength: maxLength(2000) },
        time_management_id: { required },
        work_schedule_id: { required },
    }
})

const { form, $v } = useDynamicForm(props, formSchema)


// Computed options
const departmentOptions = computed(() => {
    return props.departments
        ?.filter(d => String(d.division_id) === String(form.division_id))
        .map(d => ({ value: d.id, label: d.name })) ?? [];
});

const sectionOptions = computed(() => {
    return props.sections
        ?.filter(s => String(s.department_id) === String(form.department_id))
        .map(s => ({ value: s.id, label: s.name })) ?? [];
});

const positionOptions = computed(() => {
    return props.positions
        ?.filter(p => String(p.section_id) === String(form.section_id))
        .map(p => ({ value: p.id, label: p.name })) ?? [];
});

// Watch form to emit changes to parent
watch(
    [form, () => props.item],
    ([newForm, newItem]) => {
        if (newItem && Object.keys(newItem).length > 0) {
            emit('update:form', newForm);
            return;
        }

        if (JSON.stringify(newForm) !== JSON.stringify(props.form)) {
            emit('update:form', newForm);
        }

        // Calculate end date
        if (form.start_date && !isNaN(new Date(form.start_date).getTime())) {
            const start = new Date(form.start_date);
            const end = new Date(start);
            end.setFullYear(start.getFullYear() + 1);
            form.end_date = end.toISOString().split('T')[0];
        }
    },
    { deep: true, immediate: true }
);

watch(() => form.division_id, () => {
    form.department_id = '';
    form.section_id = '';
    form.position_id = '';
})

watch(() => form.department_id, () => {
    form.section_id = '';
    form.position_id = '';
})

watch(() => form.section_id, () => {
    form.position_id = '';
})

// Validation check
const checkValidation = async () => {
    $v.value.$touch();
    await nextTick();

    return $v.value.$errors.length === 0;
}

defineExpose({ checkValidation })
</script>

<template>
    <div class="grid grid-cols-2 gap-3 py-4">
        <SelectGroup v-model="form.employee_id" :modelValueError="props.form?.errors?.employee_id"
            :label="$t('employee')" :placeholder="$t('please_select_a_employee')" :vue-error="$v?.employee_id"
            :options="props.employees" class="col-span-2" :disabled="props.employee_id ? true : false" />

        <InputGroup v-model="form.start_date" :modelValueError="props.form?.errors?.start_date"
            :label="$t('start_date')" :placeholder="$t('please_enter_a_start_date')" type="date"
            :vue-error="$v?.start_date" />

        <InputGroup v-model="form.end_date" :modelValueError="props.form?.errors?.end_date" :label="$t('end_date')"
            :placeholder="$t('please_enter_a_end_date')" type="date" :vue-error="$v?.end_date" />

        <SelectGroup v-model="form.division_id" :modelValueError="props.form?.errors?.division_id"
            :label="$t('division')" :placeholder="$t('please_select_a_division')" :vue-error="$v?.division_id"
            :options="props.divisions" />

        <SelectGroup v-model="form.department_id" :modelValueError="props.form?.errors?.department_id"
            :label="$t('department')" :placeholder="$t('please_select_a_department')" :vue-error="$v?.department_id"
            :options="departmentOptions" />

        <SelectGroup v-model="form.section_id" :modelValueError="props.form?.errors?.section_id" :label="$t('section')"
            :placeholder="$t('please_select_a_section')" :vue-error="$v?.section_id" :options="sectionOptions" />

        <SelectGroup v-model="form.position_id" :modelValueError="props.form?.errors?.position_id"
            :label="$t('position')" :placeholder="$t('please_select_a_position')" :vue-error="$v?.position_id"
            :options="positionOptions" />

        <SelectGroup v-model="form.work_schedule_id" :modelValueError="props.form?.errors?.work_schedule_id"
            :label="$t('work_schedule')" :placeholder="$t('please_select_a_work_schedule')"
            :vue-error="$v?.work_schedule_id" :options="props.work_schedules" />

        <SelectGroup v-model="form.time_management_id" :modelValueError="props.form?.errors?.time_management_id"
            :label="$t('time_management')" :placeholder="$t('please_select_a_time_management')"
            :vue-error="$v?.time_management_id" :options="props.time_managements" />

        <TextareaGroup v-model="form.notes" :modelValueError="props.form?.errors?.notes" :label="$t('notes')"
            :placeholder="$t('please_enter_a_notes')" :vue-error="$v?.notes"
            :placeholder_message="$t('please_enter_a_notes')" class="col-span-2" />
    </div>
</template>
