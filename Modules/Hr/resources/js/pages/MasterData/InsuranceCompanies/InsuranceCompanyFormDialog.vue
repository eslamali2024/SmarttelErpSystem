<script setup lang="ts">
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import ButtonSubmit from '@/components/ui/button/ButtonSubmit.vue';
import { required, minLength, maxLength, email, url } from '@vuelidate/validators'
import { useDynamicForm } from '@/composables/useDynamicForm';
import TextareaGroup from '@/components/ui/textarea-group/TextareaGroup.vue';

const props = defineProps<{
    show: boolean,
    method_type: string,
    action: string,
    item?: any,
    loading?: boolean
}>();

const emit = defineEmits(['update:show']);

// Vuelidate
const formSchema = (props: any) => ({
    formStructure: {
        name: props.item?.name ?? '',
        email: props.item?.email ?? '',
        phone: props.item?.phone ?? '',
        website: props.item?.website ?? '',
        address: props.item?.address ?? '',
    },
    validationRules: {
        name: { required, minLength: minLength(5), maxLength: maxLength(255) },
        email: { email, maxLength: maxLength(255) },
        phone: { maxLength: maxLength(255) },
        website: { url, maxLength: maxLength(255) },
        address: { maxLength: maxLength(255) },
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
                    {{ props.method_type === 'post' ? $t('add_insurance_company') : $t('update_insurance_company') }}
                </DialogTitle>
            </DialogHeader>
            <DialogDescription :loading="loading">
                <div class="grid grid-cols-1 gap-3 py-4">
                    <InputGroup v-model="form.name" :modelValueError="form.errors.name" :label="$t('name')"
                        :placeholder="$t('please_enter_a_name')" type="text" :vue-error="$v?.name" />

                    <InputGroup v-model="form.email" :modelValueError="form.errors.email" :label="$t('email')"
                        :placeholder="$t('please_enter_a_email')" type="email" :vue-error="$v?.email" />

                    <InputGroup v-model="form.phone" :modelValueError="form.errors.phone" :label="$t('phone')"
                        :placeholder="$t('please_enter_a_phone')" type="text" :vue-error="$v?.phone" />

                    <InputGroup v-model="form.website" :modelValueError="form.errors.website" :label="$t('website')"
                        :placeholder="$t('please_enter_a_website')" type="text" :vue-error="$v?.website" />

                    <TextareaGroup v-model="form.address" :modelValueError="form.errors.address" :label="$t('address')"
                        :placeholder="$t('please_enter_a_address')" :vue-error="$v?.address" />
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
