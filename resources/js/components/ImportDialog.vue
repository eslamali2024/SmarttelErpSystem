<script setup lang="ts">
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import { useDynamicForm } from '@/composables/useDynamicForm';
import { useToast } from '@/composables/useToast';
import { required } from '@vuelidate/validators';
import { useI18n } from 'vue-i18n';
import ButtonSubmit from './ui/button/ButtonSubmit.vue';
import FileUploader from './FileUploader.vue';

const props = defineProps<{
    show: boolean,
    item?: any,
    action: string;
}>();

const { t } = useI18n();
const { showToast } = useToast();
const emit = defineEmits(['update:show']);

// Vuelidate
const formSchema = (props: any) => ({
    formStructure: {
        import_file: props.item?.import_file ?? '',
    },
    validationRules: {
        import_file: { required },
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
                title: t('uploaded_photo_successfully'),
                type: 'success'
            })
        },
        onError: () => {
            showToast({
                title: t('upload_photo_failed'),
                type: 'error'
            })
        }
    };

    form.post(props.action, options);
};
</script>

<template>
    <Dialog :open="show" @update:open="val => emit('update:show', val)">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>
                    {{ $t('import') }}
                </DialogTitle>
            </DialogHeader>
            <DialogDescription>
                <FileUploader v-model="form.import_file" :modelValueError="form?.errors.import_file"
                    :vue-error="$v?.import_file" accept=".csv, .xlsx, .xls" :preview="item?.import_file" />
            </DialogDescription>
            <DialogFooter>
                <ButtonSubmit :loading="form.processing" :submit="submitForm"
                    :cancel="() => emit('update:show', false)">
                    {{ $t('save') }}
                </ButtonSubmit>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
