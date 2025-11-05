<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle
} from '@/components/ui/alert-dialog'
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import TextareaGroup from '@/components/ui/textarea-group/TextareaGroup.vue';

defineProps<{
    show: boolean
    item: Record<string, any> | null
}>()

const emit = defineEmits(['update:show', 'confirm'])

</script>

<template>
    <AlertDialog :open="show" @update:open="(val) => emit('update:show', val)">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>{{ $t('show_insurance_company') }}</AlertDialogTitle>
            </AlertDialogHeader>
            <AlertDialogDescription>
                <div class="grid grid-cols-1 gap-3 py-4">
                    <InputGroup :modelValue="item?.name" :label="$t('name')" :placeholder="$t('please_enter_a_name')"
                        type="text" :disabled="true" />

                    <InputGroup :modelValue="item?.email" :label="$t('email')" :placeholder="$t('please_enter_a_email')"
                        type="text" :disabled="true" />

                    <InputGroup :modelValue="item?.phone" :label="$t('phone')" :placeholder="$t('please_enter_a_phone')"
                        type="text" :disabled="true" />

                    <InputGroup :modelValue="item?.website" :label="$t('website')"
                        :placeholder="$t('please_enter_a_website')" type="text" :disabled="true" />

                    <TextareaGroup :modelValue="item?.address" :label="$t('address')"
                        :placeholder="$t('please_enter_a_address')" type="text" :disabled="true" />
                </div>

            </AlertDialogDescription>
            <AlertDialogFooter>
                <AlertDialogCancel class="cursor-pointer">{{ $t('close') }}</AlertDialogCancel>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
