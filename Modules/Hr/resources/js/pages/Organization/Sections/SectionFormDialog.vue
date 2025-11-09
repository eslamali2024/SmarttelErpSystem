<script setup lang="ts">
import { computed } from "vue";
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import TextareaGroup from '@/components/ui/textarea-group/TextareaGroup.vue';
import SelectGroup from '@/components/ui/select-group/SelectGroup.vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import ButtonSubmit from '@/components/ui/button/ButtonSubmit.vue';
import { required, minLength, maxLength } from '@vuelidate/validators'
import { useDynamicForm } from '@/composables/useDynamicForm';
import { useI18n } from "vue-i18n";
import { useToast } from "@/composables/useToast";

const props = defineProps<{
    show: boolean,
    method_type: string,
    action: string,
    item?: any,
    divisions?: {
        id: number
        name: string
    },
    departments?: {
        id: number
        name: string
        division_id: number
    }[],
    managers?: {
        id: number
        name: string
    },
    loading: boolean
}>();

const { t } = useI18n();
const { showToast } = useToast();
const emit = defineEmits(['update:show']);

// Vuelidate
const formSchema = (props: any) => ({
    formStructure: {
        name: props.item?.name ?? '',
        division_id: props.item?.division_id ?? '',
        department_id: props.item?.department_id ?? '',
        manager_id: props.item?.manager_id ?? '',
        description: props.item?.description ?? '',
    },
    validationRules: {
        name: { required, minLength: minLength(5), maxLength: maxLength(255) },
        description: { maxLength: maxLength(2000) },
        division_id: { required },
        department_id: { required },
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

// Watch for changes in division_id to update department options
const departmentOptions = computed(() => {
    const result = props.departments
        ?.filter(d => String(d.division_id) === String(form.division_id))
        .map(d => ({
            value: d.id,
            label: d.name
        })) ?? [];

    return result;
});
</script>

<template>
    <Dialog :open="show" @update:open="val => emit('update:show', val)">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>
                    {{ props.method_type === 'post' ? $t('add_section') : $t('update_section') }}
                </DialogTitle>
            </DialogHeader>
            <DialogDescription :loading="loading">
                <div class="grid grid-cols-1 gap-3 py-4">
                    <InputGroup v-model="form.name" :modelValueError="form.errors.name" :label="$t('name')"
                        :placeholder="$t('please_enter_a_name')" type="text" :vue-error="$v?.name" />

                    <SelectGroup v-model="form.division_id" :modelValueError="form.errors.division_id"
                        :label="$t('division')" :placeholder="$t('please_select_a_division')"
                        :options="props.divisions || []" :vue-error="$v?.division_id" />

                    <SelectGroup v-model="form.department_id" :modelValueError="form.errors.department_id"
                        :label="$t('department')" :placeholder="$t('please_select_a_department')"
                        :options="departmentOptions || []" :vue-error="$v?.department_id" />

                    <SelectGroup v-model="form.manager_id" :modelValueError="form.errors.manager_id"
                        :label="$t('manager')" :placeholder="$t('please_select_a_manager')"
                        :options="props.managers || []" :vue-error="$v?.manager_id" />

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
