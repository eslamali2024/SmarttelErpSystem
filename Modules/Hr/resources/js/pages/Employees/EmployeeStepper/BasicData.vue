<script setup lang="ts">
import { watch } from 'vue'
import { useDynamicForm } from '@/composables/useDynamicForm';
import { required, minLength, maxLength, email } from '@vuelidate/validators'
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import SelectGroup from '@/components/ui/select-group/SelectGroup.vue';
import TextareaGroup from '@/components/ui/textarea-group/TextareaGroup.vue';

const props = defineProps<{
    item?: any,
    genders?: any,
    marital_statuess?: any,
    step: number,
    checkValidatedStep?: any,
    form: any,
}>();

const emit = defineEmits(['validation']);

// Vuelidate
const formSchema = (props: any) => ({
    formStructure: {
        employee_code: props.form?.employee_code ?? '',
        name: props.form?.name ?? '',
        name_ar: props.form?.name_ar ?? '',
        email: props.form?.email ?? '',
        gender: props.form?.gender ?? '',
        marital_status: props.form?.marital_status ?? '',
        phone: props.form?.phone ?? '',
        joining_date: props.form?.joining_date ?? '',
        national_id: props.form?.national_id ?? '',
        dob: props.form?.dob ?? '',
        address: props.form?.address ?? '',
        notes: props.form?.notes ?? '',
    },
    validationRules: {
        employee_code: { required, minLength: minLength(2), maxLength: maxLength(255) },
        name: { required, minLength: minLength(5), maxLength: maxLength(255) },
        name_ar: { required, minLength: minLength(5), maxLength: maxLength(255) },
        email: { required, minLength: minLength(5), maxLength: maxLength(255), email },
        marital_status: { required },
        joining_date: { required },
        dob: { required },
        address: { required, maxLength: maxLength(2000) },
        phone: { required },
        national_id: { required, minLength: minLength(14), maxLength: maxLength(14) },
        notes: { maxLength: maxLength(2000) },
        gender: { required },
    }
})

const { form, $v } = useDynamicForm(props, formSchema)

watch(
    [form, () => props.checkValidatedStep, () => props.step, () => props.form],
    () => {
        if (props.checkValidatedStep === props.step) {
            $v.value.$touch();
        }
        console.log($v.value.$errors)

        emit('validation', {
            status: $v.value.$errors.length === 0,
            form: form
        });
    },
    { deep: true, immediate: true }
);


</script>
<template>
    <div class="grid grid-cols-2 gap-3 py-4">
        <InputGroup v-model="form.code" :modelValueError="form.errors.code" :label="$t('employee_code')"
            :placeholder="$t('please_enter_a_employee_code')" type="text" :vue-error="$v?.code" class="col-span-2" />

        <InputGroup v-model="form.name" :modelValueError="form.errors.name" :label="$t('name')"
            :placeholder="$t('please_enter_a_name')" type="text" :vue-error="$v?.name" />

        <InputGroup v-model="form.name_ar" :modelValueError="form.errors.name_ar" :label="$t('name_ar')"
            :placeholder="$t('please_enter_a_name_ar')" type="text" :vue-error="$v?.name_ar" />

        <InputGroup v-model="form.email" :modelValueError="form.errors.email" :label="$t('email')"
            :placeholder="$t('please_enter_a_email')" type="email" :vue-error="$v?.email" />

        <InputGroup v-model="form.phone" :modelValueError="form.errors.phone" :label="$t('phone')"
            :placeholder="$t('please_enter_a_phone')" type="text" :vue-error="$v?.phone" />

        <SelectGroup v-model="form.gender" :modelValueError="form.errors.gender" :label="$t('gender')"
            :placeholder="$t('please_select_a_department')" :vue-error="$v?.gender" :options="props.genders || []" />

        <SelectGroup v-model="form.marital_status" :modelValueError="form.errors.marital_status"
            :label="$t('marital_status')" :placeholder="$t('please_select_a_marital_status')"
            :vue-error="$v?.marital_status" :options="props.marital_statuess || []" />

        <InputGroup v-model="form.national_id" :modelValueError="form.errors.national_id" :label="$t('national_id')"
            :placeholder="$t('please_enter_a_national_id')" type="text" :vue-error="$v?.national_id" />

        <InputGroup v-model="form.joining_date" :modelValueError="form.errors.joining_date" :label="$t('joining_date')"
            :placeholder="$t('please_enter_a_joining_date')" type="date" :vue-error="$v?.joining_date" />

        <InputGroup v-model="form.dob" :modelValueError="form.errors.dob" :label="$t('dob')"
            :placeholder="$t('please_enter_a_dob')" type="date" :vue-error="$v?.dob" />

        <TextareaGroup v-model="form.address" :modelValueError="form.errors.address" :label="$t('address')"
            :placeholder="$t('please_enter_a_address')" :vue-error="$v?.address"
            :placeholder_message="$t('please_enter_a_address')" class="col-span-2" />

        <TextareaGroup v-model="form.notes" :modelValueError="form.errors.notes" :label="$t('notes')"
            :placeholder="$t('please_enter_a_notes')" :vue-error="$v?.notes"
            :placeholder_message="$t('please_enter_a_notes')" class="col-span-2" />
    </div>
</template>
