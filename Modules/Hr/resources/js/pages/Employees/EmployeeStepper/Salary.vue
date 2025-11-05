<script setup lang="ts">
import { nextTick, watch, onMounted } from 'vue'
import { useDynamicForm } from '@/composables/useDynamicForm';
import InputGroup from '@/components/ui/input-group/InputGroup.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,

} from '@/components/ui/table';
import employeesRoute from '@/routes/hr/employees';
import axios from 'axios';
import { debounce } from 'lodash-es'
import ScrollArea from '@/components/ui/scroll-area/ScrollArea.vue';
import { required, minValue, helpers, maxLength } from '@vuelidate/validators';
import SelectGroup from '@/components/ui/select-group/SelectGroup.vue';


// Props
const props = defineProps<{
    item?: any,
    step: number,
    form: any,
    allowances?: {
        off_cycle: {
            id: number
            name: string
            taxable: boolean
        }[],
        recurring: {
            id: number
            name: string
            taxable: boolean
        }[]
    },
    insurance_companies?: {
        id: number
        name: string
    },
}>();

const emit = defineEmits(['update:form']);

// Vuelidate schema
const formSchema = (props: any) => ({
    formStructure: {
        net_salary: props.item?.net_salary ?? 0,
        basic_salary: props.item?.basic_salary ?? 0,
        gross_salary: props.item?.gross_salary ?? 0,
        insurance_wage: props.item?.insurance_wage ?? 0,
        insurance_wage_rounded: props.item?.insurance_wage_rounded ?? 0,
        tax_monthly: props.item?.tax_monthly ?? 0,
        social_insurance: props.item?.social_insurance ?? 0,
        martyrs_families_fund: props.item?.martyrs_families_fund ?? 0,
        company_insurance: props.item?.company_insurance ?? 0,
        salary_per_year: props.item?.salary_per_year ?? 0,
        salary_per_day: props.item?.salary_per_day ?? 0,
        salary_per_hour: props.item?.salary_per_hour ?? 0,
        total_deductions: props.item?.total_deductions ?? 0,
        recurring_allowances: props.item?.recurring_allowances ?? {},
        total_recurring_allowances: props.item?.total_recurring_allowances ?? 0,
        total_allowances_with_basic_salary: props.item?.total_allowances_with_basic_salary ?? 0,
        off_cycle_allowances: props.item?.off_cycle_allowances ?? {},
        total_off_cycle_allowances: props.item?.total_off_cycle_allowances ?? 0,
        insurance_number: props.item?.insurance_number ?? '',
        insurance_company_id: props.item?.insurance_company_id ?? '',
    },
    validationRules: {
        net_salary: { required, minValue: minValue(1000) },
        basic_salary: { required, minValue: minValue(0) },
        gross_salary: { required, minValue: minValue(0) },
        insurance_wage: { required, minValue: minValue(0) },
        insurance_wage_rounded: { required, minValue: minValue(0) },
        tax_monthly: { required, minValue: minValue(0) },
        social_insurance: { required, minValue: minValue(0) },
        martyrs_families_fund: { required, minValue: minValue(0) },
        company_insurance: { required, minValue: minValue(0) },
        salary_per_year: { required, minValue: minValue(0) },
        salary_per_day: { required, minValue: minValue(0) },
        salary_per_hour: { required, minValue: minValue(0) },
        total_deductions: { required, minValue: minValue(0) },
        recurring_allowances: {
            $each: {
                key: { required },
                value: { required, minValue: minValue(0) },
            }
        },
        total_recurring_allowances: { required, minValue: minValue(0) },
        total_allowances_with_basic_salary: {
            validTotal: helpers.withMessage(
                'Total allowances must equal basic salary + recurring allowances',
                (value) => Number(value) === Number(form.net_salary)
            )
        },
        off_cycle_allowances: {
            $each: {
                key: { required },
                value: { required, minValue: minValue(0) },
            }
        },
        total_off_cycle_allowances: { required, minValue: minValue(0) },
        insurance_number: { maxLength: maxLength(255) },
    }
})

const { form, $v } = useDynamicForm(props, formSchema)

// Methods
onMounted(() => {
    if (props.item) {
        getTotalRecurringAllowance()
        getTotalOffCycleAllowance()
    }
})

/**
 * Updates the total_allowances_with_basic_salary property of the form object
 * by adding the total_recurring_allowances to the basic_salary.
 */
const updateBasicSalaryWithAllowances = () => {
    form.total_allowances_with_basic_salary = parseFloat(form.basic_salary) + parseFloat(form.total_recurring_allowances);
}

/**
 * Calculate the total recurring allowance.
 *
 * This function iterates over the recurring allowance object and sums up the values.
 * The total is then assigned to the form.total_recurring_allowances property.
 */
const getTotalRecurringAllowance = () => {
    const values = Object.values(form.recurring_allowances || {});
    form.total_recurring_allowances = values
        .map(a => Number(a) || 0)
        .reduce((a, b) => a + b, 0);
    form.total_recurring_allowances = Number((form.total_recurring_allowances ?? 0).toFixed(3))
    updateBasicSalaryWithAllowances();
};

/**
 * Calculates the total off-cycle allowance.
 *
 * This function iterates over the off-cycle allowance object and sums up the values.
 * The total is then assigned to the form.total_off_cycle_allowances property.
 */
const getTotalOffCycleAllowance = () => {
    const values = Object.values(form.off_cycle_allowances || {});
    form.total_off_cycle_allowances = values
        .map(a => Number(a) || 0)
        .reduce((a, b) => a + b, 0);
    form.total_off_cycle_allowances = Number((form.total_off_cycle_allowances ?? 0).toFixed(3))
};

// Watch form to emit changes to parent
watch(
    [form, () => props.item],
    ([newForm, newItem], [prevForm, prevItem]) => {
        if (newItem && Object.keys(newItem).length > 0) {
            emit('update:form', newForm);
            return;
        }

        if (JSON.stringify(newForm) !== JSON.stringify(props.form)) {
            emit('update:form', newForm);
        }
    },
    { deep: true, immediate: true }
);


watch(() => form.recurring_allowances, () => {
    getTotalRecurringAllowance()
}, { deep: true })

watch(() => form.off_cycle_allowances, () => {
    getTotalOffCycleAllowance()
}, { deep: true })

watch(() => form.basic_salary, () => {
    updateBasicSalaryWithAllowances();
})


// Fetch gross salary
watch(() => form.net_salary, async () => {
    fetchGrossSalary(form)
})

const fetchGrossSalary = debounce(async (form) => {
    if (!form.net_salary) return

    const response = await axios.post(
        employeesRoute.grossUp(form.net_salary).url,
        { net_salary: form.net_salary }
    )

    if (!response.data) throw new Error('Item not found')

    const data = response.data

    form.gross_salary = Number((data.gross_salary_monthly ?? 0).toFixed(3))
    form.insurance_wage = Number(data.insurance_wage ?? 0).toFixed(3)
    form.insurance_wage_rounded = Number(data.insurance_wage_rounded ?? 0).toFixed(3)
    form.tax_monthly = Number(data.monthly_tax ?? 0).toFixed(3)
    form.social_insurance = Number(data.monthly_insurance ?? 0).toFixed(3)
    form.martyrs_families_fund = Number(data.martyrs_families_fund ?? 0).toFixed(3)
    form.company_insurance = Number(data.company_insurance ?? 0).toFixed(3)
    form.salary_per_year = Number(data.salary_per_year ?? 0).toFixed(3)
    form.salary_per_day = Number(data.salary_per_day ?? 0).toFixed(3)
    form.salary_per_hour = Number(data.salary_per_hour ?? 0).toFixed(3)
    form.total_deductions = Number(data.monthly_deductions ?? 0).toFixed(3)
}, 1000)

// Validation check
const checkValidation = async () => {
    $v.value.$touch();
    await nextTick();

    return $v.value.$errors.length === 0;
}

defineExpose({ checkValidation })
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 py-4">
        <div class="flex flex-col gap-3">
            <InputGroup v-model="form.net_salary" :modelValueError="props.form?.errors?.net_salary"
                :label="$t('net_salary')" :placeholder="$t('please_enter_a_net_salary')" type="number" :minValue="0"
                :vue-error="$v?.net_salary" />

            <SelectGroup v-model="form.insurance_company_id" :modelValueError="props.form?.errors?.insurance_company_id"
                :label="$t('insurance_company')" :placeholder="$t('please_select_a_insurance_company')"
                :vue-error="$v?.insurance_company_id" :options="props.insurance_companies" />

            <InputGroup v-model="form.insurance_number" :modelValueError="props.form?.errors?.insurance_number"
                :label="$t('insurance_number')" :placeholder="$t('please_enter_a_insurance_number')" type="text"
                :vue-error="$v?.insurance_number" />
        </div>
        <div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-[100px] text-center"></TableHead>
                        <TableHead class="text-center">{{ $t('amount') }}</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <TableRow>
                        <TableCell class="text-center text-nowrap">{{ $t('net_salary') }}</TableCell>
                        <TableCell class="text-center text-nowrap" v-text="form.net_salary"></TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell class="text-center text-nowrap">{{ $t('gross_salary') }}</TableCell>
                        <TableCell class="text-center text-nowrap" v-text="form.gross_salary"></TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell class="text-center text-nowrap">{{ $t('insurance_wage') }}</TableCell>
                        <TableCell class="text-center text-nowrap" v-text="form.insurance_wage"></TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell class="text-center text-nowrap">{{ $t('insurance_wage_rounded') }}</TableCell>
                        <TableCell class="text-center text-nowrap" v-text="form.insurance_wage_rounded"></TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell class="text-center text-nowrap">{{ $t('tax_monthly') }}</TableCell>
                        <TableCell class="text-center text-nowrap" v-text="form.tax_monthly"></TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell class="text-center text-nowrap">{{ $t('social_insurance') }}</TableCell>
                        <TableCell class="text-center text-nowrap" v-text="form.social_insurance"></TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell class="text-center text-nowrap">{{ $t('martyrs_families_fund') }}</TableCell>
                        <TableCell class="text-center text-nowrap" v-text="form.martyrs_families_fund"></TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell class="text-center text-nowrap">{{ $t('company_insurance') }}</TableCell>
                        <TableCell class="text-center text-nowrap" v-text="form.company_insurance"></TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell class="text-center text-nowrap">{{ $t('salary_per_year') }}</TableCell>
                        <TableCell class="text-center text-nowrap" v-text="form.salary_per_year"></TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell class="text-center text-nowrap">{{ $t('salary_per_day') }}</TableCell>
                        <TableCell class="text-center text-nowrap" v-text="form.salary_per_day"></TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell class="text-center text-nowrap">{{ $t('salary_per_hour') }}</TableCell>
                        <TableCell class="text-center text-nowrap" v-text="form.salary_per_hour"></TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell class="text-center text-nowrap">{{ $t('total_deductions') }}</TableCell>
                        <TableCell class="text-center text-nowrap" v-text="form.total_deductions"></TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
        <div>
            <div class="flex gap-2 justify-between items-end mb-2">
                <InputGroup v-model="form.basic_salary" :modelValueError="props.form?.errors?.basic_salary"
                    :label="$t('basic_salary')" :placeholder="$t('basic_salary')" type="number" :minValue="0"
                    :vue-error="$v?.basic_salary" />

                <InputGroup v-model="form.total_allowances_with_basic_salary"
                    :modelValueError="props.form?.errors?.total_allowances_with_basic_salary" :disabled="true"
                    :placeholder="$t('please_enter_a_total_allowances_with_basic_salary')" type="number" :minValue="0"
                    :vue-error="$v?.total_allowances_with_basic_salary" />
            </div>
            <ScrollArea class="h-[400px] w-full rounded-md1">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class=" w-[100px] text-center text-nowrap" colspan="2">
                                {{ $t('total_allowances') }}
                            </TableHead>
                            <TableHead class="text-center text-nowrap" v-text="form.total_recurring_allowances">
                            </TableHead>
                        </TableRow>
                        <TableRow>
                            <TableHead class="text-center text-nowrap">{{ $t('name') }}</TableHead>
                            <TableHead class="text-center text-nowrap">{{ $t('taxable') }}</TableHead>
                            <TableHead class="text-center text-nowrap">{{ $t('amount') }}</TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow v-for="allowance in allowances?.recurring" :key="allowance.id">
                            <TableCell class="text-center text-nowrap">{{ allowance.name }}</TableCell>
                            <TableCell class="text-center text-nowrap">{{ allowance.taxable ? $t('yes') : $t('no') }}
                            </TableCell>
                            <TableCell class="text-center text-nowrap">
                                <InputGroup v-model="form.recurring_allowances[allowance.id]"
                                    :modelValueError="props.form?.errors?.recurring_allowances?.[allowance.id]"
                                    :placeholder="$t('please_enter_a_amount')" type="number" :minValue="0"
                                    :vue-error="$v?.recurring_allowances?.[allowance.id]" />
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </ScrollArea>
        </div>

        <div class="col-span-full mt-4">
            <h3 class="mb-4 text-xl border-b pb-1 ">- {{ $t('off_cycle_allowances') }}</h3>
            <ScrollArea class="h-[400px] w-full rounded-md1">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class=" w-[100px] text-center text-nowrap">
                                {{ $t('total_off_cycle_allowances') }}
                            </TableHead>
                            <TableHead class="text-center text-nowrap" v-text="form.total_off_cycle_allowances">
                            </TableHead>
                        </TableRow>
                        <TableRow>
                            <TableHead class="text-center text-nowrap">{{ $t('name') }}</TableHead>
                            <TableHead class="text-center text-nowrap">{{ $t('amount') }}</TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow v-for="allowance in allowances?.off_cycle" :key="allowance.id">
                            <TableCell class="text-center text-nowrap">{{ allowance.name }}</TableCell>
                            <TableCell class="text-center text-nowrap">
                                <InputGroup v-model="form.off_cycle_allowances[allowance.id]"
                                    :modelValueError="props.form?.errors?.off_cycle_allowances?.[allowance.id]"
                                    :placeholder="$t('please_enter_a_amount')" type="number" :minValue="0"
                                    :vue-error="$v?.off_cycle_allowances?.[allowance.id]" />
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </ScrollArea>
        </div>
    </div>
</template>
