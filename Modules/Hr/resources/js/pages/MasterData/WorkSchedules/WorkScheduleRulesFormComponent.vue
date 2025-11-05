<script setup lang="ts">
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import {
    Card, CardHeader, CardTitle, CardContent, CardAction,
} from '@/components/ui/card';
import {
    Table, TableBody, TableCell, TableEmpty, TableHead, TableHeader, TableRow,
} from '@/components/ui/table';
import Button from '@/components/ui/button/Button.vue';
import { watch } from 'vue';
import { required, minValue } from '@vuelidate/validators';
import { useForm } from '@inertiajs/vue3';
import useVuelidate from '@vuelidate/core';

interface Rule {
    id: number;
    deducation_after_time: number | string;
    deducation_percentage: number | string;
}

const props = defineProps<{ rules?: Rule[], form?: any, isDisabled?: boolean }>();
const emit = defineEmits(['update:rules']);

const form = useForm({
    rules: props.form?.rules ?? props.rules ?? [],
});

const rulesValidations = {
    rules: {
        $each: {
            id: { required },
            deducation_after_time: { required, minValue: minValue(10) },
            deducation_percentage: { required },
        },
    },
};

const $v = useVuelidate(rulesValidations, form);

watch(() => form.rules, (newVal) => {
    emit('update:rules', newVal);
}, { deep: true });

const addRule = () => {
    form.rules.push({
        id: form.rules.length + 1,
        deducation_after_time: '',
        deducation_percentage: '',
    });
};

const removeRule = (id: number) => {
    form.rules = (form.rules as Rule[]).filter((rule: Rule) => rule.id !== id);
};
</script>

<template>
    <Card class="mt-4">
        <CardHeader>
            <CardTitle>{{ $t('work_schedule_rules') }}</CardTitle>
            <CardAction v-if="!isDisabled">
                <Button type="button" class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm"
                    @click="addRule">
                    <i class="ri ri-add-line"></i>
                </Button>
            </CardAction>
        </CardHeader>

        <CardContent>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead class="text-center w-[100px]">{{ $t('no') }}</TableHead>
                        <TableHead class="text-center">{{ $t('deducation_after_time') }}</TableHead>
                        <TableHead class="text-center">{{ $t('deducation_percentage') }}</TableHead>
                        <TableHead class="text-center" v-if="!isDisabled">{{ $t('action') }}</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <TableRow v-for="(rule, index) in form.rules" :key="rule.id">
                        <TableCell class="text-center">{{ index + 1 }}</TableCell>
                        <TableCell class="text-center">
                            <InputGroup v-model="rule.deducation_after_time" type="number"
                                :modelValueError="props.form?.errors?.['rules.' + index + '.deducation_after_time']"
                                :vue-error="$v.rules?.[index]?.deducation_after_time" :disabled="isDisabled"
                                :placeholder="$t('please_enter_a_deducation_after_time')" />
                        </TableCell>
                        <TableCell class="text-center">
                            <InputGroup v-model="rule.deducation_percentage" type="number"
                                :modelValueError="props.form?.errors?.['rules.' + index + '.deducation_percentage']"
                                :vue-error="$v.rules?.[index]?.deducation_percentage" :disabled="isDisabled"
                                :placeholder="$t('please_enter_a_deducation_percentage')" />
                        </TableCell>
                        <TableCell class="text-center" v-if="!isDisabled">
                            <Button type="button" variant="destructive" class="cursor-pointer" size="sm"
                                @click="removeRule(rule.id)">
                                <i class="ri ri-delete-bin-line"></i>
                            </Button>
                        </TableCell>
                    </TableRow>

                    <TableEmpty v-if="!form.rules?.length" :colspan="4">
                        {{ $t('no_data') }}
                    </TableEmpty>
                </TableBody>
            </Table>
        </CardContent>
    </Card>
</template>
