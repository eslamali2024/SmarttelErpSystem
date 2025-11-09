<script setup lang="ts">
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import ButtonSubmit from '@/components/ui/button/ButtonSubmit.vue';
import { required, minLength, maxLength } from '@vuelidate/validators'
import { useDynamicForm } from '@/composables/useDynamicForm';
import { Switch } from "@/components/ui/switch"
import { useToast } from '@/composables/useToast';
import { useI18n } from 'vue-i18n';

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

const { t } = useI18n()
const { showToast } = useToast();
const emit = defineEmits(['update:show']);

// Vuelidate
const formSchema = (props: any) => ({
    formStructure: {
        name: props.item?.name ?? '',
        payroll: ((props.item?.payroll ?? 1) == 1 ? true : false),
        fingerprint_in: ((props.item?.fingerprint_in ?? 1) == 1 ? true : false),
        fingerprint_out: ((props.item?.fingerprint_out ?? 1) == 1 ? true : false),
        factors: ((props.item?.factors ?? 1) == 1 ? true : false),
    },
    validationRules: {
        name: { required, minLength: minLength(5), maxLength: maxLength(255) },
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
                title: props.method_type === 'post' ? t('added_successfully') : t('updated_successfully'),
                type: 'success'
            })
        },
        onError: () => {
            showToast({
                title: props.method_type === 'post' ? t('add_failed') : t('update_failed'),
                type: 'error'
            })
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
                    {{ props.method_type === 'post' ? $t('add_time_management') : $t('update_time_management') }}
                </DialogTitle>
            </DialogHeader>
            <DialogDescription :loading="loading">
                <div class="grid grid-cols-1 gap-3 py-4">
                    <InputGroup v-model="form.name" :modelValueError="form?.errors?.name" :label="$t('name')"
                        :placeholder="$t('please_enter_a_name')" type="text" :vue-error="$v?.name" />

                    <hr class="w-2/3 mx-auto my-2">

                    <h3>{{ $t('settings') }}</h3>
                    <div class="rounded-lg border p-4">
                        <div class="flex flex-row items-center justify-between ">
                            <div class="space-y-0.5">
                                <div class="text-base">
                                    {{ $t('payroll') }}
                                </div>
                                <p class="text-xs">
                                    {{ $t('message_payroll') }}
                                </p>
                            </div>
                            <Switch v-model="form.payroll" />
                        </div>
                        <p v-if="form?.errors?.payroll || $v.payroll?.$errors[0]?.$message"
                            class="text-sm text-red-500 mt-2">
                            {{ form?.errors?.payroll || $v.payroll?.$errors[0]?.$message }}
                        </p>
                    </div>
                    <div class="rounded-lg border p-4">
                        <div class="flex flex-row items-center justify-between">
                            <div class="space-y-0.5">
                                <div class="text-base">
                                    {{ $t('fingerprint_in') }}
                                </div>
                                <p class="text-xs">
                                    {{ $t('message_fingerprint_in') }}
                                </p>
                            </div>
                            <Switch v-model="form.fingerprint_in" />
                        </div>
                        <p v-if="form?.errors?.fingerprint_in || $v.fingerprint_in?.$errors[0]?.$message"
                            class="text-sm text-red-500 mt-2">
                            {{ form?.errors?.fingerprint_in || $v.fingerprint_in?.$errors[0]?.$message }}
                        </p>
                    </div>
                    <div class="rounded-lg border p-4">
                        <div class="flex flex-row items-center justify-between">
                            <div class="space-y-0.5">
                                <div class="text-base">
                                    {{ $t('fingerprint_out') }}
                                </div>
                                <p class="text-xs">
                                    {{ $t('message_fingerprint_out') }}
                                </p>
                            </div>
                            <Switch v-model="form.fingerprint_out" />
                        </div>
                        <p v-if="form?.errors?.fingerprint_out || $v.fingerprint_out?.$errors[0]?.$message"
                            class="text-sm text-red-500 mt-2">
                            {{ form?.errors?.fingerprint_out || $v.fingerprint_out?.$errors[0]?.$message }}
                        </p>
                    </div>
                    <div class="rounded-lg border p-4">
                        <div class="flex flex-row items-center justify-between">
                            <div class="space-y-0.5">
                                <div class="text-base">
                                    {{ $t('factors') }}
                                </div>
                                <p class="text-xs">
                                    {{ $t('message_factors') }}
                                </p>
                            </div>
                            <Switch v-model="form.factors" />
                        </div>
                        <p v-if="form?.errors?.factors || $v.factors?.$errors[0]?.$message"
                            class="text-sm text-red-500 mt-2">
                            {{ form?.errors?.factors || $v.factors?.$errors[0]?.$message }}
                        </p>
                    </div>
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
