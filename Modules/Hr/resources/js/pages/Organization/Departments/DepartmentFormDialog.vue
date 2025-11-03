<script setup lang="ts">
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import TextareaGroup from '@/components/ui/textarea-group/TextareaGroup.vue';
import SelectGroup from '@/components/ui/select-group/SelectGroup.vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import ButtonSubmit from '@/components/ui/button/ButtonSubmit.vue';
import { required, minLength, maxLength } from '@vuelidate/validators'
import { useDynamicForm } from '@/composables/useDynamicForm';

const props = defineProps<{
    show: boolean,
    method_type: string,
    action: string,
    item?: any,
    divisions?: {
        id: number
        name: string
    },
    managers?: {
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
        division_id: props.item?.division_id ?? '',
        manager_id: props.item?.manager_id ?? '',
        description: props.item?.description ?? '',
    },
    validationRules: {
        name: { required, minLength: minLength(1), maxLength: maxLength(255) },
        description: { maxLength: maxLength(2000) },
        division_id: { required },
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
                    {{ props.method_type === 'post' ? $t('add_department') : $t('update_department') }}
                </DialogTitle>
            </DialogHeader>
            <DialogDescription :loading="loading">
                <div class="grid grid-cols-1 gap-3 py-4">
                    <InputGroup v-model="form.name" :modelValueError="form.errors.name" :label="$t('name')"
                        :vue-error="$v?.name" :placeholder="$t('please_enter_a_name')" type="text" />

                    <SelectGroup v-model="form.division_id" :modelValueError="form.errors.division_id"
                        :vue-error="$v?.division_id" :label="$t('division')"
                        :placeholder="$t('please_select_a_division')" :options="props.divisions || []" />

                    <SelectGroup v-model="form.manager_id" :modelValueError="form.errors.manager_id"
                        :vue-error="$v?.manager_id" :label="$t('manager')" :placeholder="$t('please_select_a_manager')"
                        :options="props.managers || []" />

                    <TextareaGroup v-model="form.description" :modelValueError="form.errors.description"
                        :vue-error="$v?.description" :label="$t('description')"
                        :placeholder="$t('please_enter_a_description')"
                        :placeholder_message="$t('please_enter_a_description')" />
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
