<script setup lang="ts">
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import SelectGroup from '@/components/ui/select-group/SelectGroup.vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import ButtonSubmit from '@/components/ui/button/ButtonSubmit.vue';
import { required, minLength, maxLength, integer } from '@vuelidate/validators'
import { useDynamicForm } from '@/composables/useDynamicForm';

const props = defineProps<{
    show: boolean,
    method_type: string,
    action: string,
    item?: any,
    types?: {
        id: number
        name: string
    },
    taxables?: {
        id: number
        name: string
    },
    loading?: boolean
}>();

const emit = defineEmits(['update:show']);

// Vuelidate
const formSchema = (props: any) => ({
    formStructure: {
        name: props.item?.name ?? '',
        type: props.item?.type ?? '',
        taxable: props.item?.taxable ?? '',
    },
    validationRules: {
        name: { required, minLength: minLength(5), maxLength: maxLength(255) },
        type: { required, integer },
        taxable: { required, integer },
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
                    {{ props.method_type === 'post' ? $t('add_allowance_type') : $t('update_allowance_type') }}
                </DialogTitle>
            </DialogHeader>
            <DialogDescription :loading="loading">
                <div class="grid grid-cols-1 gap-3 py-4">
                    <InputGroup v-model="form.name" :modelValueError="form.errors.name" :label="$t('name')"
                        :placeholder="$t('please_enter_a_name')" type="text" :vue-error="$v?.name" />

                    <SelectGroup v-model="form.taxable" :modelValueError="form.errors.taxable" :label="$t('taxable')"
                        :placeholder="$t('please_select_a_taxable')" :vue-error="$v?.taxable"
                        :options="props.taxables || []" />

                    <SelectGroup v-model="form.type" :modelValueError="form.errors.type" :label="$t('type')"
                        :placeholder="$t('please_select_a_type')" :vue-error="$v?.type" :options="props.types || []" />
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
