<script setup lang="ts">
import { Rocket } from 'lucide-vue-next';
import { Alert, AlertDescription, AlertTitle } from '../ui/alert';
import { Dialog, DialogContent, DialogHeader, DialogDescription, DialogFooter, DialogTitle } from '../ui/dialog';
import ButtonSubmit from '../ui/button/ButtonSubmit.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import approvalsRoute from '@/routes/approvals';
import { useToast } from '@/composables/useToast';
import { useI18n } from 'vue-i18n';

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
const action = ref<string | null>(props.action ?? (requestId !== null ? approvalsRoute.approve(requestId).url : null) ?? null);
const form = useForm({
    redirect_url: props.redirect_url
});

const submitForm = () => {
    if (!action.value) return;

    form.post(action.value, {
        onSuccess: () => {
            emit('update:show', false)
            showToast({
                title: t('approved_successfully'),
                type: 'success'
            })
        },
        onError: () => {
            showToast({
                title: t('approved_failed'),
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
                    <i class="ri ri-check-line text-lg"></i>
                    <span>{{ $t('approver_request') }}</span>
                </DialogTitle>
            </DialogHeader>
            <DialogDescription>
                <Alert>
                    <Rocket class="h-4 w-4" />
                    <AlertTitle>{{ $t('approver_request') }}</AlertTitle>
                    <AlertDescription>
                        <p>{{ $t('are_you_sure_approve_this_request') }}</p>
                    </AlertDescription>
                </Alert>
            </DialogDescription>
            <DialogFooter>
                <ButtonSubmit :loading="form.processing" :submit="submitForm"
                    :cancel="() => emit('update:show', false)">
                    {{ $t('approve') }}
                </ButtonSubmit>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
