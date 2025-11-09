<script setup lang="ts">
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import ButtonSubmit from '@/components/ui/button/ButtonSubmit.vue';
import { required, minLength, maxLength } from '@vuelidate/validators'
import { useDynamicForm } from '@/composables/useDynamicForm';
import { useI18n } from 'vue-i18n';
import { useToast } from '@/composables/useToast';

const props = defineProps<{
    show: boolean,
    method_type: string,
    action: string,
    item?: any,
    loading?: boolean
}>();

const { t } = useI18n();
const { showToast } = useToast();
const emit = defineEmits(['update:show']);

// Vuelidate
const formSchema = (props: any) => ({
    formStructure: {
        name: props.item?.name ?? '',

    },
    validationRules: {
        name: { required, minLength: minLength(5), maxLength: maxLength(255) },
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
        form.post(props.action, options,);
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
                    {{ props.method_type === 'post' ? $t('add_permission') : $t('update_permission') }}
                </DialogTitle>
            </DialogHeader>
            <DialogDescription :loading="loading">
                <div class="grid grid-cols-1 gap-3 py-4">
                    <InputGroup v-model="form.name" :modelValueError="form.errors.name" :label="$t('name')"
                        :vue-error="$v?.name" :placeholder="$t('please_enter_a_name')" type="text" />
                </div>
            </DialogDescription>
            <DialogFooter>
                <ButtonSubmit :loading="form.processing" :submit="submitForm" :disabled="form.processing"
                    :cancel="() => emit('update:show', false)">
                    {{ $t('save') }}
                </ButtonSubmit>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
