<script setup lang="ts">
import { computed } from 'vue';
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import { Dialog, DialogScrollContent, DialogHeader, DialogTitle, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import ButtonSubmit from '@/components/ui/button/ButtonSubmit.vue';
import { useI18n } from 'vue-i18n';
import Button from '@/components/ui/button/Button.vue';
import { required, minLength, maxLength } from '@vuelidate/validators'
import { useDynamicForm } from '@/composables/useDynamicForm';
import { useToast } from '@/composables/useToast';
import SelectGroup from '@/components/ui/select-group/SelectGroup.vue';

const props = defineProps<{
    show: boolean,
    method_type: string,
    action: string,
    item?: any,
    models?: any,
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
        redirect_route: props.item?.redirect_route ?? '',
        approvable_type: props.item?.approvable_type ?? '',
    },
    validationRules: {
        name: { required, minLength: minLength(5), maxLength: maxLength(255) },
        redirect_route: { required, minLength: minLength(5), maxLength: maxLength(255) },
        approvable_type: { required }
    }
})

const { form, $v } = useDynamicForm(props, formSchema)

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
                    <div class="grid grid-cols-1 gap-3 py-4">
                        <InputGroup v-model="form.name" :modelValueError="form.errors.name" :label="$t('name')"
                            :placeholder="$t('please_enter_a_name')" type="text" :disabled="isReadOnly"
                            :vue-error="$v?.name" />

                        <InputGroup v-model="form.redirect_route" :modelValueError="form.errors.redirect_route"
                            :label="$t('redirect_route')" :placeholder="$t('please_enter_a_redirect_route')" type="text"
                            :disabled="isReadOnly" :vue-error="$v?.redirect_route" />

                        <SelectGroup v-model="form.approvable_type" :modelValueError="form.errors.approvable_type"
                            :label="$t('approvable_type')" :options="props.models"
                            :placeholder="$t('please_enter_a_approvable_type')" type="text" :disabled="isReadOnly"
                            :vue-error="$v?.approvable_type" />

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
