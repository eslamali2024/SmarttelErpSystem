<script setup lang="ts">
import { computed, ref } from "vue";
import AppLayout from "@/layouts/AppLayout.vue";
import Card from "@/components/ui/card/Card.vue";
import { CardAction, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import A from "@/components/ui/a/A.vue";
import employeesRoute from "@/routes/hr/employees";
import { Check, Circle, Dot } from "lucide-vue-next"
import { Button } from "@/components/ui/button"
import { Stepper, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from "@/components/ui/stepper"
import BasicData from "./EmployeeStepper/BasicData.vue"
import Contract from "./EmployeeStepper/Contract.vue"
import Salary from "./EmployeeStepper/Salary.vue"
import { useI18n } from 'vue-i18n'
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import { useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3';
import { useToast } from "@/composables/useToast";

const { t } = useI18n();
const { showToast } = useToast();

const props = defineProps<{
    method_type: string,
    action: string,
    item?: any,
    genders?: any,
    marital_statuess?: any,
    auto_generate_code?: string,
    insurance_companies?: {
        id: number
        name: string
    },
    time_managements?: {
        id: number
        name: string
    },
    work_schedules?: {
        id: number
        name: string
    },
    divisions?: {
        id: number
        name: string
    },
    departments?: {
        id: number
        name: string
        division_id: number
    }[],
    sections?: {
        id: number
        name: string
        department_id: number
    }[],
    positions?: {
        id: number
        name: string
        section_id: number
    }[],
    allowances?: {
        off_cycle: {
            id: number
            name: string
            taxable: boolean,
        }[],
        recurring: {
            id: number
            name: string
            taxable: boolean
        }[]
    },
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('hr'), href: null },
    { title: t('employees'), href: employeesRoute.index().url },
    { title: props.method_type === 'post' ? t('add_employee') : t('edit_employee'), href: null },
];
const stepIndex = ref(1);

const steps = ref<Array<{ step: number; title: string; status: string; form: Record<string, any> | null }>>([
    { step: 1, title: t('basic_data'), status: 'active', form: null },
    { step: 2, title: t('contract'), status: 'inactive', form: null },
    { step: 3, title: t('salary'), status: 'inactive', form: null },
]);

// Disable "Next" button if current step is not complete or it's the last step
const isNextDisabledComputed = computed(() => {
    return stepIndex.value !== steps.value.length;
});

// Handle form submission
const data = ref({});
const form = ref<any>(null);

const mergeSteps = () => {
    steps.value.forEach(step => {
        if (!step.form) step.form = {};
        if (!step.form.errors) step.form.errors = {};

        if (step.form) {
            data.value = {
                ...data.value,
                ['step_' + step.step]: { ...step.form, errors: undefined }
            };
        }
    });

    form.value = useForm(data.value);
};

/**
 * Submits the form by calling the `validateAllSteps` method and then
 * the `put` or `post` method of the `form` depending on the `method_type` prop.
 * If `validateAllSteps` returns true, the `onSuccess` option is called with a callback
 * that resets the form and clears all the errors of the steps.
 * If `validateAllSteps` returns false, the `onError` option is called with a callback
 * that sets the errors of the steps based on the server errors.
 */
const submitForm = async () => {
    mergeSteps();

    if (stepIndex.value === steps.value.length) {
        const canSubmit = await nextStep();

        if (canSubmit) {
            const options = {
                onSuccess: () => {
                    form.value.reset();
                    steps.value.forEach(step => {
                        if (step.form) step.form.errors = {};
                    });

                    showToast({
                        title: props.method_type === 'post' ? t('added_successfully') : t('updated_successfully'),
                        type: 'success'
                    })
                },
                onError: (serverErrors: any) => {
                    Object.keys(serverErrors).forEach(key => {
                        // key = "step_1.name" -> split to stepKey & field
                        const [stepKey, field] = key.split('.');
                        const stepNumber = parseInt(stepKey.replace('step_', ''), 10);
                        stepIndex.value = stepNumber;

                        let step = steps.value.find(s => s.step === stepNumber);
                        if (!step) {
                            // create a placeholder step if it doesn't exist yet
                            step = { step: stepNumber, title: '', status: 'inactive', form: { errors: {} } } as any;
                            steps.value.push(step);
                        }

                        if (!step?.form) step.form = {};
                        if (!step?.form?.errors) step.form.errors = {};

                        if (step?.form) {
                            step.form.errors = {
                                ...step.form.errors,
                                [field]: serverErrors[key]
                            };
                        }
                    });

                    showToast({
                        title: props.method_type === 'post' ? t('add_failed') : t('update_failed'),
                        type: 'error'
                    })
                }
            };

            if (props.method_type === 'post') {
                form.value?.post(props.action, options);
            } else if (props.method_type === 'put') {
                form.value?.put(props.action, options);
            }
        }
    }
};

// Validation
const step_1 = ref<{ checkValidation?: () => Promise<boolean> | boolean } | null>(null);
const step_2 = ref<{ checkValidation?: () => Promise<boolean> | boolean } | null>(null);
const step_3 = ref<{ checkValidation?: () => Promise<boolean> | boolean } | null>(null);
const stepsRef = ref([step_1, step_2, step_3]);

// Next step
const nextStep = async () => {
    const stepComponent = stepsRef.value[stepIndex.value - 1];

    const results = await (
        stepComponent?.value && typeof stepComponent.value.checkValidation === 'function'
            ? Promise.resolve(stepComponent.value.checkValidation())
            : Promise.resolve(false)
    );

    if (results) {
        if (stepIndex.value < steps.value.length) {
            stepIndex.value++;
        }
        return true;
    }

    showToast({
        title: t('validation_failed'),
        type: 'error'
    })
    return false;
};

</script>

<template>

    <Head :title="props.method_type === 'post' ? $t('add_employee') : $t('edit_employee')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card>
            <CardHeader class="flex justify-between items-center">
                <CardTitle>
                    {{ props.method_type === 'post' ? $t('add_employee') : $t('edit_employee') }}
                </CardTitle>
                <CardAction>
                    <A :href="employeesRoute.index().url"
                        class="bg-green-500 cursor-pointer hover:bg-green-600 text-white" size="sm">
                        <i class="ri ri-arrow-left-line"></i> {{ $t("back") }}
                    </A>
                </CardAction>
            </CardHeader>
            <CardContent>
                <Stepper v-slot="{ isPrevDisabled, prevStep }" v-model="stepIndex" class="block w-full">
                    <form @submit.prevent="submitForm">
                        <div class="flex w-full flex-start gap-2">
                            <StepperItem v-for="step in steps" :key="step.step" v-slot="{ state }"
                                class="relative flex w-full flex-col items-center justify-center" :step="step.step">
                                <StepperSeparator v-if="step.step !== steps[steps.length - 1].step"
                                    class="absolute left-[calc(50%+20px)] right-[calc(-50%+10px)] top-5 block h-0.5 shrink-0 rounded-full bg-muted group-data-[state=completed]:bg-primary" />

                                <StepperTrigger as-child>
                                    <Button
                                        :variant="state === 'completed' || state === 'active' ? 'default' : 'outline'"
                                        size="icon" class="z-10 rounded-full shrink-0"
                                        :class="[state === 'active' && 'ring-2 ring-ring ring-offset-2 ring-offset-background']"
                                        :disabled="step.step > stepIndex">
                                        <Check v-if="state === 'completed'" class="size-5" />
                                        <Circle v-if="state === 'active'" />
                                        <Dot v-if="state === 'inactive'" />
                                    </Button>
                                </StepperTrigger>

                                <div class="mt-1 flex flex-col items-center text-center">
                                    <StepperTitle :class="[state === 'active' && 'text-primary']"
                                        class="text-sm font-semibold transition lg:text-base">
                                        {{ step.title }}
                                    </StepperTitle>
                                </div>
                            </StepperItem>
                        </div>

                        <div class="flex flex-col gap-4 mt-4">
                            <BasicData v-show="stepIndex === 1" :step="1" v-model:form="steps[0].form" ref="step_1"
                                :item="props.item" :auto_generate_code="auto_generate_code"
                                :marital_statuess="marital_statuess" :genders="genders" />

                            <Contract v-show="stepIndex === 2" :step="2" v-model:form="steps[1].form" ref="step_2"
                                :item="props.item?.contract" :divisions="divisions" :departments="departments"
                                :sections="sections" :positions="positions" :work_schedules="work_schedules"
                                :time_managements="time_managements" />

                            <Salary v-show="stepIndex === 3" :step="3" v-model:form="steps[2].form" ref="step_3"
                                :item="props.item?.salary" :allowances="allowances"
                                :insurance_companies="insurance_companies" />
                        </div>

                        <div class="flex items-center justify-between mt-4">
                            <Button :disabled="isPrevDisabled" variant="outline" size="sm" @click="prevStep()">
                                Back
                            </Button>
                            <div class="flex items-center gap-3">
                                <Button v-if="isNextDisabledComputed" size="sm" @click="() => {
                                    nextStep()

                                }">
                                    Next
                                </Button>
                                <Button v-else size="sm" type="submit">
                                    Submit
                                </Button>
                            </div>
                        </div>
                    </form>
                </Stepper>
            </CardContent>
        </Card>
    </AppLayout>
</template>
