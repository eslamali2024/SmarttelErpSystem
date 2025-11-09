<script setup lang="ts">
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import ButtonSubmit from '@/components/ui/button/ButtonSubmit.vue';
import { required, minLength, maxLength, integer } from '@vuelidate/validators'
import { useDynamicForm } from '@/composables/useDynamicForm';
import { useToast } from '@/composables/useToast';
import { useI18n } from 'vue-i18n';
import { watch } from 'vue';

const props = defineProps<{
    show: boolean,
    method_type: string,
    action: string,
    item?: any,
    loading?: boolean
}>();

const emit = defineEmits(['update:show']);
const { t } = useI18n()
const { showToast } = useToast();

// Vuelidate
const formSchema = (props: any) => ({
    formStructure: {
        name: props.item?.name ?? '',
        start_date: props.item?.start_date ?? '',
        end_date: props.item?.end_date ?? '',
        actual_start_date: props.item?.actual_start_date ?? '',
        actual_end_date: props.item?.actual_end_date ?? '',
        days: props.item?.days ?? 0,
    },
    validationRules: {
        name: { required, minLength: minLength(5), maxLength: maxLength(255) },
        start_date: { required },
        end_date: { required },
        days: { required, integer },
    }
})

const { form, $v } = useDynamicForm(props, formSchema)

watch(
    () => [form.actual_start_date, form.actual_end_date],
    () => {
        if (form.actual_start_date && form.actual_end_date) {
            const start: Date = new Date(form.actual_start_date);
            const end: Date = new Date(form.actual_end_date);
            const startMs: number = start.getTime();
            const endMs: number = end.getTime();
            if (endMs >= startMs) {
                form.days = Math.round((endMs - startMs) / (1000 * 60 * 60 * 24));
                return form.days;
            }
        }
        form.days = 0;
        return form.days;
    }
);

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
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>
                    {{ props.method_type === 'post' ? $t('add_public_holiday') : $t('update_public_holiday') }}
                </DialogTitle>
            </DialogHeader>
            <DialogDescription :loading="loading">
                <div class="grid grid-cols-1 gap-3 py-4">
                    <InputGroup v-model="form.name" :modelValueError="form.errors.name" :label="$t('name')"
                        :placeholder="$t('please_enter_a_name')" type="text" :vue-error="$v?.name" />

                    <InputGroup v-model="form.start_date" :modelValueError="form.errors.start_date"
                        :label="$t('start_date')" :placeholder="$t('please_enter_a_start_date')" type="date"
                        :vue-error="$v?.start_date" />

                    <InputGroup v-model="form.end_date" :modelValueError="form.errors.end_date" :label="$t('end_date')"
                        :placeholder="$t('please_enter_a_end_date')" type="date" :vue-error="$v?.end_date" />

                    <InputGroup v-model="form.actual_start_date" :modelValueError="form.errors.actual_start_date"
                        :label="$t('actual_start_date')" :placeholder="$t('please_enter_a_actual_start_date')"
                        type="date" :vue-error="$v?.actual_start_date" />

                    <InputGroup v-model="form.actual_end_date" :modelValueError="form.errors.actual_end_date"
                        :label="$t('actual_end_date')" :placeholder="$t('please_enter_a_actual_end_date')" type="date"
                        :vue-error="$v?.actual_end_date" />

                    <InputGroup v-model="form.days" :modelValueError="form.errors.days" :label="$t('days')"
                        :placeholder="$t('please_enter_a_days')" type="text" :vue-error="$v?.days" :disabled="true" />
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
