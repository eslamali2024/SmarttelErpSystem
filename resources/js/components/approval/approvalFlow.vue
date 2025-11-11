<script setup lang="ts">
import { ApprovalStatusEnum } from '@/enums/ApprovalStatusEnum';
import { Card, CardContent, CardHeader, CardTitle } from '../ui/card';

defineProps<{
    approval_steps: {
        count: {
            current: number,
            total: number
        },
        items: Array<{
            action: {
                id: number,
                created_at: string,
                action_type: number,
                approver: {
                    name: string
                }
                comment: string
            },
            step: {
                name: string,
            }
        }>
    }
}>();
</script>

<template>
    <Card class="bg-gray-500/50 gap-2 border border-yellow-100/50"
        v-if="approval_steps.items && approval_steps.items.length > 0">
        <CardHeader class="mb-0">
            <CardTitle class="flex gap-2 items-center">
                <i class="ri ri-user-3-line"></i>
                <span>
                    {{ $t('approval_flow') }}
                </span>
            </CardTitle>
        </CardHeader>
        <CardContent>
            <hr class="mb-4 mx-auto">

            <ul class="flex flex-wrap md:flex-nowrap justify-between items-stretch">
                <template v-for="(item, index) in approval_steps.items" :key="'flow-' + item.step.id + index">
                    <li :class="{
                        'flex-1 text-center p-4 rounded-lg relative mb-3 md:mb-0 md:mx-1 transition-all duration-300 shadow-sm font-medium': true,
                        'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300':
                            !item.action && (!approval_steps.count || approval_steps.count.current != index),
                        'bg-blue-100 text-blue-900 ring-2 ring-blue-400 dark:bg-blue-900 dark:text-blue-100 dark:ring-blue-600':
                            approval_steps.count && approval_steps.count.current == index && !item.action,
                        'bg-emerald-500 text-white dark:bg-emerald-600 dark:text-emerald-50':
                            item.action && item.action.action_type == ApprovalStatusEnum.APPROVED,
                        'bg-rose-500 text-white dark:bg-rose-600 dark:text-rose-50':
                            item.action && item.action.action_type == ApprovalStatusEnum.REJECTED
                    }">
                        <span class="text-lg font-bold block mb-2">{{ index + 1 }}</span>
                        <p class="font-medium">{{ item.step.name }}</p>
                        <div v-if="item.action">
                            <small>
                                {{ item.action.approver.name }} - {{ item.action.created_at }}
                            </small>
                            <small class="block text-gray-700 dark:text-gray-200 text-sm mt-1"
                                v-if="item.action.comment">
                                <p class="font-semibold">{{ $t('comment') }}:</p>
                                <p>{{ item.action.comment ?? $t('no_comment') }}</p>
                            </small>
                        </div>
                    </li>

                </template>
            </ul>
        </CardContent>
    </Card>
</template>
