<script setup lang="ts">
import { computed, watch } from 'vue';
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import { Dialog, DialogScrollContent, DialogHeader, DialogTitle, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import ButtonSubmit from '@/components/ui/button/ButtonSubmit.vue';
import { useI18n } from 'vue-i18n';
import Button from '@/components/ui/button/Button.vue';
import { required, minLength, maxLength, integer } from '@vuelidate/validators'
import { useDynamicForm } from '@/composables/useDynamicForm';
import { useToast } from '@/composables/useToast';
import SelectGroup from '@/components/ui/select-group/SelectGroup.vue';
import { ApprovalTypeEnum } from '@/enums/approvalTypeEnum';
import SelectTags from '@/components/ui/tags-input/SelectTags.vue';

const props = defineProps<{
    show: boolean,
    method_type: string,
    action: string,
    item?: any,
    data?: any,
    loading?: boolean
}>();


const { t } = useI18n();
const { showToast } = useToast();
const emit = defineEmits(['update:show']);
const isReadOnly = computed(() => props.method_type === 'show')

// Vuelidate
const formSchema = (props: any) => ({
    formStructure: {
        name: props.item?.name ?? '',
        approver_type: props.item?.approver_type ?? '',
        order: props.item?.order ?? '',
        user_id: props.item?.user_id ?? '',
        roles: props.item?.roles ?? [],
        permissions: props.item?.permissions ?? [],
        manager_column: props.item?.manager_column ?? '',
        approver_column: props.item?.approver_column ?? '',
    },
    validationRules: {
        name: { required, minLength: minLength(5), maxLength: maxLength(255) },
        order: { required, integer },
        approver_type: { required },
        user_id: { integer },
        roles: { $each: { required } },
        permissions: { $each: { required } },
    }
})

const { form, $v } = useDynamicForm(props, formSchema)

watch(
    () => [props.item?.approver_type, form.approver_type],
    ([newProp, newForm]) => {
        if (newProp !== newForm) {
            form.user_id = '';
            form.roles = [];
            form.permissions = [];
            form.manager_column = '';
            form.approver_column = '';
        }
    }
);

/**
 * Submits the form data to the server.
 * If the form is in read-only mode, does nothing.
 * If the form is in create mode, sends a POST request to the server.
 * If the form is in update mode, sends a PUT request to the server.
 * On success, emits an event to close the dialog and resets the form data.
 * On error, logs the error to the console.
 * Preserves the scroll position of the page.
 */
const submitForm = () => {
    $v.value.$touch()
    if (isReadOnly.value || $v.value.$invalid) return;
    const currentPage = new URLSearchParams(window.location.search).get('page') || 1;

    const options = {
        onSuccess: () => {
            emit('update:show', false);
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
        },
        preserveScroll: true,
    };

    const actionUrl = props.action.includes('?')
        ? `${props.action}&page=${currentPage}`
        : `${props.action}?page=${currentPage}`;

    if (props.method_type === 'post') {
        form.post(actionUrl, options);
    } else if (props.method_type === 'put') {
        form.put(actionUrl, options);
    }
};

// Get Title Dialog
const title = computed(() => {
    switch (props.method_type) {
        case 'post':
            return t('add_approval_flow')
        case 'put':
            return t('update_approval_flow')
        case 'show':
            return t('show_approval_flow')
        default:
            return t('add_approval_flow')
    }
})
</script>

<template>
    <form @action.prevent="submitForm">

        <Dialog :open="show" @update:open="val => emit('update:show', val)">
            <DialogScrollContent class="sm:max-w-[1000px]">
                <DialogHeader>
                    <DialogTitle>
                        {{ title }}
                    </DialogTitle>
                </DialogHeader>
                <DialogDescription :loading="props.loading">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 py-4">
                        <InputGroup v-model="form.name" :modelValueError="form.errors.name" :label="$t('name')"
                            :placeholder="$t('please_enter_a_name')" type="text" :disabled="isReadOnly"
                            :vue-error="$v?.name" />

                        <SelectGroup v-model="form.approver_type" :modelValueError="form.errors.approver_type"
                            :label="$t('approver_type')" :options="props?.data?.approver_types"
                            :placeholder="$t('please_enter_a_approver_type')" type="text" :disabled="isReadOnly"
                            :vue-error="$v?.approver_type" />

                        <div class="col-span-2">
                            <SelectGroup v-if="ApprovalTypeEnum.USER == form.approver_type" v-model="form.user_id"
                                :modelValueError="form.errors.user_id" :label="$t('user')" :options="props?.data?.users"
                                :placeholder="$t('please_enter_a_user')" type="text" :disabled="isReadOnly"
                                :vue-error="$v?.user_id" />

                            <SelectTags v-if="ApprovalTypeEnum.ROLE == form.approver_type" v-model="form.roles"
                                :modelValueError="form.errors.roles" :label="$t('roles')" :options="props?.data?.roles "
                                :placeholder="$t('please_enter_roles')" type="text" :disabled="isReadOnly"
                                :vue-error="$v?.roles" />

                            <SelectTags v-if="ApprovalTypeEnum.PERMISSIONS == form.approver_type"
                                v-model="form.permissions" :modelValueError="form.errors.permissions"
                                :label="$t('permissions')" :options="props?.data?.permissions"
                                :placeholder="$t('please_enter_permissions')" type="text" :disabled="isReadOnly"
                                :vue-error="$v?.permissions" />

                            <InputGroup
                                v-if="[ApprovalTypeEnum.DIVISION, ApprovalTypeEnum.DEPARTMENT, ApprovalTypeEnum.SECTION, ApprovalTypeEnum.DEPARTMENT_REQUEST, ApprovalTypeEnum.DEPARTMENT_APPROVAL].includes(Number(form.approver_type))"
                                v-model="form.manager_column" :modelValueError="form.errors.manager_column"
                                :label="$t('manager_column')" :placeholder="$t('please_enter_a_manager_column')"
                                type="text" :disabled="isReadOnly" class="col-span-2" :vue-error="$v?.manager_column" />

                            <InputGroup
                                v-if="[ApprovalTypeEnum.DEPARTMENT_REQUEST, ApprovalTypeEnum.DEPARTMENT_APPROVAL].includes(Number(form.approver_type))"
                                v-model="form.approver_column" :modelValueError="form.errors.approver_column"
                                :label="$t('approver_column')" :placeholder="$t('please_enter_a_approver_column')"
                                type="text" :disabled="isReadOnly" class="col-span-2"
                                :vue-error="$v?.approver_column" />
                        </div>

                        <InputGroup v-model="form.order" :modelValueError="form.errors.order" :label="$t('order')"
                            :placeholder="$t('please_enter_a_order')" type="text" :disabled="isReadOnly"
                            class="col-span-2" :vue-error="$v?.order" />
                    </div>
                </DialogDescription>
                <DialogFooter>
                    <ButtonSubmit :loading="form.processing" :submit="submitForm" v-if="!isReadOnly"
                        :cancel="() => emit('update:show', false)" :disabled="props.loading">
                        {{ $t('save') }}
                    </ButtonSubmit>
                    <Button v-else type="button" @click="() => emit('update:show', false)" class="cursor-pointer">
                        {{ $t('close') }}</Button>
                </DialogFooter>
            </DialogScrollContent>
        </Dialog>
    </form>

</template>
