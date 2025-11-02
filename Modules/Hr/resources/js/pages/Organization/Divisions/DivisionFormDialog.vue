<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import TextareaGroup from '@/components/ui/textarea-group/TextareaGroup.vue';
import SelectGroup from '@/components/ui/select-group/SelectGroup.vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import ButtonSubmit from '@/components/ui/button/ButtonSubmit.vue';
import { required, minLength, maxLength } from '@vuelidate/validators'
import useVuelidate from '@vuelidate/core'

const props = defineProps<{
    show: boolean,
    method_type: string,
    action: string,
    item?: any,
    managers?: {
        id: number
        name: string
    }[]
}>();

const emit = defineEmits(['update:show']);

const form = useForm({
    name: props.item?.name ?? '',
    manager_id: props.item?.manager_id ?? '',
    description: props.item?.description ?? '',
});

watch(() => props.item, (newItem) => {
    form.name = newItem?.name ?? '';
    form.manager_id = newItem?.manager_id ?? '';
    form.description = newItem?.description ?? '';
    $v.value.$reset();
});

// Vuelidate
const $v = useVuelidate({
    name: { required, minLength: minLength(1), maxLength: maxLength(255) },
    description: { maxLength: maxLength(2000) },
}, form)

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
                    {{ props.method_type === 'post' ? $t('add_division') : $t('update_division') }}
                </DialogTitle>
            </DialogHeader>
            <DialogDescription>
                <div class="grid grid-cols-1 gap-3 py-4">
                    <InputGroup v-model="form.name" :modelValueError="form.errors.name" :label="$t('name')"
                        :vue-error="$v?.name" :placeholder="$t('please_enter_a_name')" type="text" />

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
                <ButtonSubmit :loading="form.processing" :submit="submitForm"
                    :cancel="() => emit('update:show', false)">
                    {{ $t('save') }}
                </ButtonSubmit>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
