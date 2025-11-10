<script setup lang="ts">
import { ref } from 'vue';
import ApproverModal from './approverModal.vue';
import CardCancelApproval from './cardCancelApproval.vue';
import CardRequiredApproval from './cardRequiredApproval.vue';
import RejectModal from './rejectModal.vue';
import ApprovalFlow from './approvalFlow.vue';

const props = defineProps<{
    approval?: any
    cancel_approval: object | boolean,
    last_request_action_approval: any,
    approval_steps: any
    route_approval?: string
    route_cancel?: string
    redirect_url?: string
    model?: object
}>()

const redirectUrl = window.location.origin + props?.redirect_url;

const showApproverModal = ref(false);
const showRejectModal = ref(false);
</script>
<template>

    <div class="mb-8">
        <CardRequiredApproval :route_approval="route_approval" :model="model" :redirect_url="redirectUrl"
            @showApproverModal="showApproverModal = true" @showRejectModal="showRejectModal = true"
            :step="approval?.step" />

        <ApproverModal v-model:show="showApproverModal" :action="route_approval" :request="approval?.request"
            :redirect_url="redirectUrl" />

        <RejectModal v-model:show="showRejectModal" :action="route_cancel" :request="approval?.request"
            :redirect_url="redirectUrl" />

        <CardCancelApproval :cancel_approval="cancel_approval" class="mt-4"
            :last_request_action_approval="last_request_action_approval" :redirect_url="redirectUrl" />

        <ApprovalFlow class="mt-4" :approval_steps="approval_steps" />
    </div>
</template>
