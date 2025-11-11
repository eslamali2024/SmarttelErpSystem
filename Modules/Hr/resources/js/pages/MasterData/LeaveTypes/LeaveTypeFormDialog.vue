<script setup lang="ts">
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import ButtonSubmit from '@/components/ui/button/ButtonSubmit.vue';
import { required, minLength, maxLength, integer, maxValue } from '@vuelidate/validators'
import { useDynamicForm } from '@/composables/useDynamicForm';
import { useToast } from '@/composables/useToast';
import { useI18n } from 'vue-i18n';
import SelectGroup from '@/components/ui/select-group/SelectGroup.vue';
import Switch from '@/components/ui/switch/Switch.vue';

const props = defineProps<{
    show: boolean,
    method_type: string,
    action: string,
    item?: any,
    loading?: boolean,
    units?: {
        id: number
        name: string
    },
    durations?: {
        id: number
        name: string
    }
}>();

const emit = defineEmits(['update:show']);
const { t } = useI18n()
const { showToast } = useToast();

// Vuelidate
const formSchema = (props: any) => ({
    formStructure: {
        name: props.item?.name ?? '',
        unit: props.item?.unit ?? '',
        type_duration: props.item?.type_duration ?? '',
        for_employee: props.item?.for_employee ?? false,
        company_amount: props.item?.company_amount ?? '',
        duration_deducted_percentage: props.item?.duration_deducted_percentage ?? '',
        salary_deducted_percentage: props.item?.salary_deducted_percentage ?? '',
    },
    validationRules: {
        name: { required, minLength: minLength(5), maxLength: maxLength(255) },
        unit: { required, integer },
        type_duration: { required, integer },
        for_employee: { required },
        company_amount: { required, integer, maxValue: maxValue(100) },
        duration_deducted_percentage: { required, integer, maxValue: maxValue(100) },
        salary_deducted_percentage: { required, integer, maxValue: maxValue(100) },
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
                    {{ props.method_type === 'post' ? $t('add_leave_type') : $t('update_leave_type') }}
                </DialogTitle>
            </DialogHeader>
            <DialogDescription :loading="loading">
                <div class="grid grid-cols-12 gap-3 py-4">
                    <InputGroup v-model="form.name" :modelValueError="form.errors.name" :label="$t('name')"
                        :placeholder="$t('please_enter_a_name')" type="text" :vue-error="$v?.name"
                        class="col-span-12" />

                    <SelectGroup v-model="form.unit" :modelValueError="form.errors.unit" :label="$t('unit')"
                        :options="props.units" :placeholder="$t('please_select_a_unit')" type="text"
                        class="col-span-12 md:col-span-6 lg:col-span-4" :vue-error="$v?.unit" />

                    <SelectGroup v-model="form.type_duration" :modelValueError="form.errors.type_duration"
                        :label="$t('type_duration')" :options="props.durations"
                        :placeholder="$t('please_select_a_duration')" type="text"
                        class="col-span-12 md:col-span-6 lg:col-span-4" :vue-error="$v?.type_duration" />

                    <div
                        class="flex flex-row items-center justify-between border p-2 rounded-lg col-span-12 md:col-span-6 lg:col-span-4">
                        <div class="text-base">
                            {{ $t('for_employee') }}
                        </div>
                        <Switch v-model="form.for_employee" />
                    </div>

                    <InputGroup v-model="form.company_amount" :modelValueError="form.errors.company_amount"
                        :label="$t('company_amount')" :placeholder="$t('please_enter_a_company_amount')" type="number"
                        :vue-error="$v?.company_amount" class="col-span-12 md:col-span-6 lg:col-span-4" />

                    <InputGroup v-model="form.duration_deducted_percentage"
                        :modelValueError="form.errors.duration_deducted_percentage"
                        :label="$t('duration_deducted_percentage')"
                        :placeholder="$t('please_enter_a_duration_deducted_percentage')" type="number"
                        :vue-error="$v?.duration_deducted_percentage" class="col-span-12 md:col-span-6 lg:col-span-4" />

                    <InputGroup v-model="form.salary_deducted_percentage"
                        :modelValueError="form.errors.salary_deducted_percentage"
                        :label="$t('salary_deducted_percentage')"
                        :placeholder="$t('please_enter_a_salary_deducted_percentage')" type="number"
                        :vue-error="$v?.salary_deducted_percentage" class="col-span-12 md:col-span-6 lg:col-span-4" />
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
