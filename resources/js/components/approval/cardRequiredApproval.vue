<script setup lang="ts">
import Button from '../ui/button/Button.vue';
import { Card, CardContent, CardHeader, CardTitle } from '../ui/card';

defineProps<{
    step?: {
        id: number,
        name: string,
        created_at: string
    }
}>();

</script>

<template>
    <Card class="bg-gray-500/50 gap-2 border border-yellow-100/50" v-if="step">
        <CardHeader class="mb-0">
            <CardTitle class="flex gap-2 items-center">
                <i class="ri ri-user-3-line"></i>
                <span>{{ step?.name }}</span>
            </CardTitle>
        </CardHeader>
        <CardContent>
            <hr class="mb-4 mx-auto">
            <div class="flex justify-between items-center gap-2">
                <div class="text-sm font-bold">
                    <p>{{ $t('are_you_sure_approve_this_request') }}</p>
                    <p>
                        <span>{{ $t('requested_at') }}: </span>
                        <span>{{ step?.created_at }}</span>
                    </p>
                </div>
                <div class="flex gap-2 items-center">
                    <Button variant="green" v-on:click="$emit('showApproverModal')">{{ $t('approve') }}</Button>
                    <Button variant="red" v-on:click="$emit('showRejectModal')">{{ $t('reject') }}</Button>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
