<script setup lang="ts">
import FileUploader from '@/components/FileUploader.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import ButtonSubmit from '@/components/ui/button/ButtonSubmit.vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import { useDynamicForm } from '@/composables/useDynamicForm';
import { useToast } from '@/composables/useToast';
import { strLimit } from '@/utils/strLimit';
import { required } from '@vuelidate/validators';
import { useI18n } from 'vue-i18n';
import employeesRoute from '@/routes/hr/employees';

const props = defineProps<{
    show: boolean,
    item: any,
    canEdit?: boolean,
}>();

const { t } = useI18n();
const { showToast } = useToast();
const emit = defineEmits(['update:show']);

// Vuelidate
const formSchema = (props: any) => ({
    formStructure: {
        avatar: props.item?.avatar ?? '',
    },
    validationRules: {
        avatar: { required },
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

    form.post(employeesRoute.updateAvatar(props.item.id).url, options);
};
</script>

<template>
    <Dialog :open="show" @update:open="val => emit('update:show', val)">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>
                    {{ $t('employee_photo') }}
                </DialogTitle>
            </DialogHeader>
            <DialogDescription>
                <FileUploader v-if="canEdit" v-model="form.avatar" :modelValueError="form?.errors.avatar"
                    :vue-error="$v?.avatar" :preview="item?.avatar" />
                <div v-else class="flex items-center justify-center">
                    <Avatar class="w-24 h-24 border-2 border-white">
                        <AvatarImage :src="item.avatar ?? '#'" alt="@unovue" />
                        <AvatarFallback class="text-black dark:text-white text-4xl">
                            {{ strLimit(item?.name, 2, '-', '') }}
                        </AvatarFallback>
                    </Avatar>
                </div>
            </DialogDescription>
            <DialogFooter v-if="canEdit">
                <ButtonSubmit :loading="form.processing" :submit="submitForm"
                    :cancel="() => emit('update:show', false)">
                    {{ $t('save') }}
                </ButtonSubmit>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
