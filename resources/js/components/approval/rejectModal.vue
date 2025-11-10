<script setup lang="ts">
import { GitPullRequestClosed } from 'lucide-vue-next';
import { Alert, AlertDescription, AlertTitle } from '../ui/alert';
import { Dialog, DialogContent, DialogHeader, DialogDescription, DialogFooter, DialogTitle } from '../ui/dialog';
import ButtonSubmit from '../ui/button/ButtonSubmit.vue';
import TextareaGroup from '../ui/textarea-group/TextareaGroup.vue';
import { useDynamicForm } from '@/composables/useDynamicForm';
import { maxLength, minLength, required } from '@vuelidate/validators';
import { ref } from 'vue';
import approvalsRoute from '@/routes/approvals';
import { useI18n } from 'vue-i18n';
import { useToast } from '@/composables/useToast';

const { t } = useI18n();
const { showToast } = useToast();

const props = defineProps<{
    show: boolean,
    item?: any
    action?: string
    request?: {
        id: number | string
    },
    redirect_url?: string
}>();

const emit = defineEmits(['update:show']);
const rawRequestId = props.request ? (typeof props.request.id === 'string' ? parseInt(props.request.id, 10) : props.request.id) : null;
const requestId = rawRequestId !== null && !Number.isNaN(rawRequestId as number) ? (rawRequestId as number) : null;
const action = ref<string | null>(props.action ?? (requestId !== null ? approvalsRoute.reject(requestId).url : null) ?? null);

const formSchema = (props: any) => ({
    formStructure: {
        comment: props.item?.comment ?? '',
        redirect_url: props.redirect_url ?? null,
    },
    validationRules: {
        comment: { required, minLength: minLength(5), maxLength: maxLength(2000) },
    }
})

const { form, $v } = useDynamicForm(props, formSchema)


const submitForm = () => {
    if (!action.value) return;

    form.post(action.value, {
        onSuccess: () => {
            emit('update:show', false)

            showToast({
                title: t('rejected_successfully'),
                type: 'success'
            })
        },
        onError: () => {
            showToast({
                title: t('rejected_failed'),
                type: 'error'
            })
        }
    });
}
</script>

<template>
    <Dialog :open="show" @update:open="val => emit('update:show', val)" v-if="request">
        <DialogContent class="sm:max-w-[425px] md:max-w-[600px] lg:max-w-5xl">
            <DialogHeader>
                <DialogTitle class="flex items-center gap-2">
                    <GitPullRequestClosed class="h-5 w-5" />
                    <span>{{ $t('reject_request') }}</span>
                </DialogTitle>
            </DialogHeader>
            <DialogDescription>
                <Alert>
                    <GitPullRequestClosed class="h-4 w-4" />
                    <AlertTitle>{{ $t('reject_request') }}</AlertTitle>
                    <AlertDescription>
                        <p>{{ $t('are_you_sure_reject_this_request') }}</p>
                    </AlertDescription>
                </Alert>

                <TextareaGroup v-model="form.comment" :label="$t('comment')" :modelValueError="form.errors.comment"
                    :placeholder="$t('please_enter_your_comment')" class="mt-4" :vue-error="$v?.comment" />

            </DialogDescription>
            <DialogFooter>
                <ButtonSubmit :loading="form.processing" :submit="submitForm"
                    :cancel="() => emit('update:show', false)">
                    {{ $t('reject') }}
                </ButtonSubmit>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
