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
    divisions?: {
        id: number
        name: string
    },
    departments?: { id: number; name: string; division_id: number }[],
    sections?: { id: number; name: string; department_id: number }[],
    positions?: { id: number; name: string; section_id: number }[],
    step: number,
    form: any,
}>();

const emit = defineEmits(['update:form']);

// Vuelidate schema
const formSchema = (props: any) => ({
    formStructure: {
        division_id: props.item?.division_id ?? '',
        department_id: props.item?.department_id ?? '',
        section_id: props.item?.section_id ?? '',
        position_id: props.item?.position_id ?? '',
        start_date: props.item?.start_date ?? '',
        end_date: props.item?.end_date ?? '',
        notes: props.item?.notes ?? '',
    },
    validationRules: {
        division_id: { required },
        department_id: { required },
        section_id: { required },
        position_id: { required },
        start_date: { required },
        end_date: { required },
        notes: { maxLength: maxLength(2000) },
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
    form,
    (newVal) => {
        emit('update:form', newVal);
    },
    { deep: true }
)

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

        <TextareaGroup v-model="form.notes" :modelValueError="props.form?.errors?.notes" :label="$t('notes')"
            :placeholder="$t('please_enter_a_notes')" :vue-error="$v?.notes"
            :placeholder_message="$t('please_enter_a_notes')" class="col-span-2" />
    </div>
</template>
