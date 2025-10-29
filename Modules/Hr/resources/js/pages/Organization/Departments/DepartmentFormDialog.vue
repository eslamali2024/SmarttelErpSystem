<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import { watch } from 'vue';
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import TextareaGroup from '@/components/ui/textarea-group/TextareaGroup.vue';
import SelectGroup from '@/components/ui/select-group/SelectGroup.vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import Button from '@/components/ui/button/Button.vue';
import departmentsRoute from '@/routes/hr/organization/departments';

const props = defineProps<{
    show: boolean,
    method_type: string,
    action: string,
    item?: any,
    managers?: Array<{ value: any, label: string }>
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
});

const submitForm = () => {
    const options = {
        onSuccess: () => emit('update:show', false)
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

            <div class="grid grid-cols-1 gap-3 py-4">
                <InputGroup v-model="form.name" :modelValueError="form.errors.name" :label="$t('name')"
                    :placeholder="$t('please_enter_a_name')" type="text" />

                <SelectGroup v-model="form.manager_id" :modelValueError="form.errors.manager_id" :label="$t('manager')"
                    :placeholder="$t('please_select_a_manager')" :options="props.managers || []" />

                <TextareaGroup v-model="form.description" :modelValueError="form.errors.description"
                    :label="$t('description')" :placeholder="$t('please_enter_a_description')"
                    :placeholder_message="$t('please_enter_a_description')" />
            </div>

            <DialogFooter>
                <Button type="button" @click="emit('update:show', false)"
                    class="bg-red-500/50 cursor-pointer text-white hover:bg-red-600">
                    {{ $t('cancel') }}
                </Button>
                <Button type="button" @click="submitForm"
                    class="bg-green-500/50 cursor-pointer text-white hover:bg-green-600">
                    {{ $t('save') }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
