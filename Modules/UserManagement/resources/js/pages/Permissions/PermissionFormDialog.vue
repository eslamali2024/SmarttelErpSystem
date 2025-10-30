<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import ButtonSubmit from '@/components/ui/button/ButtonSubmit.vue';

const props = defineProps<{
    show: boolean,
    method_type: string,
    action: string,
    item?: any
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
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>
                    {{ props.method_type === 'post' ? $t('add_permission') : $t('update_permission') }}
                </DialogTitle>
            </DialogHeader>
            <DialogDescription>
                <div class="grid grid-cols-1 gap-3 py-4">
                    <InputGroup v-model="form.name" :modelValueError="form.errors.name" :label="$t('name')"
                        :placeholder="$t('please_enter_a_name')" type="text" />
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
