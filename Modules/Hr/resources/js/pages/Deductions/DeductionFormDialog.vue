<script setup lang="ts">
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import ButtonSubmit from '@/components/ui/button/ButtonSubmit.vue';
import { required, minLength, maxLength } from '@vuelidate/validators'
import { useDynamicForm } from '@/composables/useDynamicForm';
import { useToast } from '@/composables/useToast';
import { useI18n } from 'vue-i18n';
import SelectGroup from '@/components/ui/select-group/SelectGroup.vue';
import TextareaGroup from '@/components/ui/textarea-group/TextareaGroup.vue';

const props = defineProps<{
    show: boolean,
    method_type: string,
    action: string,
    item?: any,
    data?: {
        employees: any[],
        deduction_types: any[],
    } | null,
    loading?: boolean,
    employee_id?: number,
    redirect_url?: any
}>();

const emit = defineEmits(['update:show']);
const { t } = useI18n()
const { showToast } = useToast();

// Vuelidate
const formSchema = (props: any) => ({
    formStructure: {
        employee_id: props.item?.employee_id ?? props.employee_id ?? '',
        deduction_type_id: props.item?.deduction_type_id ?? '',
        date: props.item?.date ?? '',
        amount: props.item?.amount ?? '',
        reason: props.item?.reason ?? '',
        notes: props.item?.notes ?? '',
        redirect_url: props.redirect_url ?? ''
    },
    validationRules: {
        reason: { required, minLength: minLength(5), maxLength: maxLength(255) },
        amount: { required },
        date: { required },
        employee_id: { required },
        deduction_type_id: { required },
        notes: { maxLength: maxLength(2000) },
    }
})

const { form, $v } = useDynamicForm(props, formSchema)

const submitForm = () => {
    $v.value.$touch()
    if ($v.value.$invalid) return;

    const options = {
        onSuccess: () => {
            emit('update:show', false)
            form.reset();
            $v.value.$reset();

            showToast({
                title: props.method_type === 'post' ? t('added_successfully') : t('updated_successfully'),
                type: 'success'
            })
        },
        onError: () => {
            showToast({
                title: props.method_type === 'post' ? t('add_failed') : t('update_failed'),
                type: 'error'
            })
        }
    };

    if (props.method_type === 'post') {
        form.post(props.action, options);
    } else if (props.method_type === 'put') {
        form.put(props.action, options);
    }
};
</script>

<template>
    <Dialog :open="show" @update:open="val => emit('update:show', val)">
        <DialogContent class="sm:max-w-[425px] md:max-w-[600px] lg:max-w-5xl">
            <DialogHeader>
                <DialogTitle>
                    {{ props.method_type === 'post' ? $t('add_deduction') : $t('update_deduction') }}
                </DialogTitle>
            </DialogHeader>
            <DialogDescription :loading="loading">
                <div class="grid grid-cols-12 gap-3 py-4">
                    <SelectGroup v-model="form.employee_id" :modelValueError="form.errors.employee_id"
                        class="col-span-12 md:col-span-6 lg:col-span-4" :label="$t('employee')"
                        :options="props.data?.employees" :placeholder="$t('please_select_a_employee')" type="text"
                        :vue-error="$v?.employee_id" :disabled="props.employee_id ? true : false" />

                    <SelectGroup v-model="form.deduction_type_id" :modelValueError="form.errors.deduction_type_id"
                        class="col-span-12 md:col-span-6 lg:col-span-4" :label="$t('deduction_type')"
                        :options="props.data?.deduction_types" :placeholder="$t('please_select_a_deduction_type')"
                        type="text" :vue-error="$v?.deduction_type_id" />

                    <InputGroup v-model="form.date" :modelValueError="form.errors.date" :label="$t('date')"
                        class="col-span-12 md:col-span-6 lg:col-span-4" :placeholder="$t('please_enter_a_date')"
                        type="date" :vue-error="$v?.date" />

                    <InputGroup v-model="form.reason" :modelValueError="form.errors.reason" :label="$t('reason')"
                        class="col-span-12 md:col-span-6" :placeholder="$t('please_enter_a_reason')" type="text"
                        :vue-error="$v?.reason" />

                    <InputGroup v-model="form.amount" :modelValueError="form.errors.amount" :label="$t('amount')"
                        class="col-span-12 md:col-span-6" :placeholder="$t('please_enter_a_amount')" type="number"
                        :min-value="0" :vue-error="$v?.amount" />

                    <TextareaGroup v-model="form.notes" :modelValueError="form.errors.notes" :label="$t('notes')"
                        class="col-span-12" :placeholder="$t('please_enter_a_notes')" type="text"
                        :vue-error="$v?.notes" />
                </div>
            </DialogDescription>
            <DialogFooter>
                <ButtonSubmit :loading="form.processing" :submit="submitForm" :disabled="loading"
                    :cancel="() => emit('update:show', false)">
                    {{ $t('save') }}
                </ButtonSubmit>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
