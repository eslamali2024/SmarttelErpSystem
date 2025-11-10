<script setup lang="ts">
import { ApprovalStatusEnum } from '@/enums/ApprovalStatusEnum';
import Button from '../ui/button/Button.vue';
import { Card, CardContent, CardHeader, CardTitle } from '../ui/card';
import approvalsRoute from '@/routes/approvals';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { useToast } from '@/composables/useToast';

const { t } = useI18n();
const { showToast } = useToast();

const props = defineProps<{
    cancel_approval: object | boolean,
    last_request_action_approval: {
        action: {
            id: number,
            created_at: string,
            action_type: number,
            approver: {
                name: string
            }
        },
        request: {
            id: number | string
        }
    }
    action?: string
    redirect_url?: string
}>();

const rawRequestId = props.last_request_action_approval.request ? (typeof props.last_request_action_approval.request.id === 'string' ? parseInt(props.last_request_action_approval.request.id, 10) : props.last_request_action_approval.request.id) : null;
const requestId = rawRequestId !== null && !Number.isNaN(rawRequestId as number) ? (rawRequestId as number) : null;
const action = ref<string | null>(props.action ?? (requestId !== null ? approvalsRoute.cancelApproval(requestId).url : null) ?? null);
const form = useForm({
    redirect_url: props.redirect_url
});


const submitForm = () => {
    if (!action.value) return;

    form.post(action.value, {
        onSuccess: () => {
            showToast({
                title: props.last_request_action_approval?.action?.action_type == ApprovalStatusEnum.APPROVED ? t('cancel_rejection_successfully') : t('cancel_approval_successfully'),
                type: 'success'
            })
        }, onError: () => {
            showToast({
                title: props.last_request_action_approval?.action?.action_type == ApprovalStatusEnum.APPROVED ? t('cancel_rejection_failed') : t('cancel_approval_failed'),
                type: 'error'
            })
        }
    });
}

</script>
<template>
    <Card class="bg-gray-500/50 gap-2 border border-yellow-100/50" v-if="cancel_approval">
        <CardHeader class="mb-0">
            <CardTitle class="flex gap-2 items-center">
                <i class="ri ri-user-3-line"></i>
                <span>
                    {{ last_request_action_approval?.action?.action_type == ApprovalStatusEnum.APPROVED ?
                        $t('cancel_approval') : $t('cancel_rejection') }}
                </span>
            </CardTitle>
        </CardHeader>
        <CardContent>
            <hr class="mb-4 mx-auto">
            <div class="flex justify-between items-center gap-2">
                <div class="text-sm font-bold">
                    <p>{{ $t('are_you_sure_want_to_cancel_last_action') }}</p>
                    <p>
                        <span>{{ $t('requested_at') }}: </span>
                        <span>{{ last_request_action_approval.action.created_at }}</span>
                    </p>
                    <p>
                        <span>{{ $t('reviewed_by') }}: </span>
                        <span>{{ last_request_action_approval?.action?.approver?.name ?? '-' }}</span>
                    </p>
                </div>
                <div class="flex gap-2 items-center">
                    <Button variant="outline" v-on:click="submitForm">{{ $t('cancel') }}</Button>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
