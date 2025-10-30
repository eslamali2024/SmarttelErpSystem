<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import ButtonSubmit from '@/components/ui/button/ButtonSubmit.vue';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';

const props = defineProps<{
    show: boolean,
    method_type: string,
    action: string,
    item?: any,
    permissions?: any
}>();

const emit = defineEmits(['update:show']);

const form = useForm({
    name: props.item?.name ?? '',
});

watch(() => props.item, (newItem) => {
    form.name = newItem?.name ?? '';
});


const submitForm = () => {
    const options = {
        onSuccess: () => {
            emit('update:show', false)
            form.reset();
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
        <DialogContent class="sm:max-w-[1000px]">
            <DialogHeader>
                <DialogTitle>
                    {{ props.method_type === 'post' ? $t('add_role') : $t('update_role') }}
                </DialogTitle>
            </DialogHeader>
            <DialogDescription>
                <div class="grid grid-cols-1 gap-3 py-4">
                    <InputGroup v-model="form.name" :modelValueError="form.errors.name" :label="$t('name')"
                        :placeholder="$t('please_enter_a_name')" type="text" />


                    <div class="grid grid-cols-4 gap-3">
                        <label v-for="permission in props.permissions" :key="permission" :for="permission"
                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 cursor-pointer flex gap-2 items-center p-1 border rounded hover:bg-gray-100 duration-200">
                            <Checkbox :id="permission" name="permissions" :value="permission" />

                            {{ permission }}
                        </label>

                    </div>

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
