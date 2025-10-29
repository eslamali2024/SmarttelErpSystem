<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle
} from '@/components/ui/alert-dialog'
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import TextareaGroup from '@/components/ui/textarea-group/TextareaGroup.vue';
import SelectGroup from '@/components/ui/select-group/SelectGroup.vue';

const props = defineProps<{
    show: boolean
    item: Record<string, any> | null
}>()

const emit = defineEmits(['update:show', 'confirm'])

const confirmDelete = () => {
    emit('confirm')
    emit('update:show', false)
}
</script>

<template>
    <AlertDialog :open="show" @update:open="(val) => emit('update:show', val)">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>{{ $t('show_position') }}</AlertDialogTitle>
            </AlertDialogHeader>
            <AlertDialogDescription>
                <div class="grid grid-cols-1 gap-3 py-4">
                    <InputGroup :modelValue="item.code" :label="$t('code')" :placeholder="$t('please_enter_a_code')"
                        type="text" :disabled="true" />

                    <InputGroup :modelValue="item.department?.name" :label="$t('department')"
                        :placeholder="$t('please_enter_a_department')" type="text" :disabled="true" />

                    <InputGroup :modelValue="item.name" :label="$t('name')" :placeholder="$t('please_enter_a_name')"
                        type="text" :disabled="true" />

                    <InputGroup :modelValue="item.manager?.name" :label="$t('name')" type="text" :disabled="true" />

                    <TextareaGroup modelValue="item.description" :label="$t('description')" :disabled="true"
                        :placeholder="$t('please_enter_a_description')"
                        :placeholder_message="$t('please_enter_a_description')" />
                </div>

            </AlertDialogDescription>
            <AlertDialogFooter>
                <AlertDialogCancel class="cursor-pointer">{{ $t('close') }}</AlertDialogCancel>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
