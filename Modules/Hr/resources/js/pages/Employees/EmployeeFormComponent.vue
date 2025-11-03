<script setup lang="ts">
import { computed, ref, watch } from "vue";
import AppLayout from "@/layouts/AppLayout.vue";
import Card from "@/components/ui/card/Card.vue";
import { CardAction, CardContent, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
import A from "@/components/ui/a/A.vue";
import employeesRoute from "@/routes/hr/employees";
import { Check, Circle, Dot } from "lucide-vue-next"
import { Button } from "@/components/ui/button"
import { Stepper, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from "@/components/ui/stepper"
import BasicData from "./EmployeeStepper/BasicData.vue"
import Contract from "./EmployeeStepper/Contract.vue"
import { useI18n } from 'vue-i18n'
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import { useForm } from '@inertiajs/vue3'


const { t } = useI18n()

const props = defineProps<{
    method_type: string,
    action: string,
    item?: any,
    genders?: any,
    marital_statuess?: any
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('dashboard'), href: dashboard().url },
    { title: t('hr'), href: null },
    { title: t('employees'), href: employeesRoute.index().url },
    { title: props.method_type === 'post' ? t('add_employee') : t('edit_employee'), href: null },
];
const stepIndex = ref(1);

const steps = ref([
    { step: 1, title: t('basic_data'), status: 'active', form: null },
    { step: 2, title: t('contract'), status: 'inactive', form: null },
    { step: 3, title: t('salary'), status: 'inactive', form: null },
]);

// Disable "Next" button if current step is not complete or it's the last step
const isNextDisabledComputed = computed(() => {
    const currentStep = steps.value.find(s => s.step === stepIndex.value);
    return currentStep?.status !== 'complete' || stepIndex.value > steps.value.length;
});

// Handle form submission
const data = ref({});
const form = ref(null);

const mergeSteps = () => {
    steps.value.forEach(step => {
        if (step.form) {
            step.form = {
                ...step.form,
                errors: {}
            };

            data.value = {
                ...data.value,
                ['step_' + step.step]: { ...step.form }
            };
        }
    });

    form.value = useForm(data.value);
};

const submitForm = () => {
    mergeSteps();

    if (stepIndex.value === steps.value.length) {

        const options = {
            onSuccess: () => {
                form.value?.reset();
                steps.value.forEach(step => {
                    if (step.form) step.form.errors = {};
                });
            },
            onError: (serverErrors) => {
                Object.keys(serverErrors).forEach(key => {
                    // key = "step_1.name" -> split to stepKey & field
                    const [stepKey, field] = key.split('.');
                    const stepNumber = parseInt(stepKey.replace('step_', ''), 10);

                    const step = steps.value.find(s => s.step === stepNumber);
                    if (step && step.form) {
                        if (!step.form.errors) step.form.errors = {};
                        step.form.errors[field] = serverErrors[key];
                    }
                });

                console.log('Step-wise errors:', steps.value);
            }
        };

        if (props.method_type === 'post') {
            form.value?.post(props.action, options);
        } else if (props.method_type === 'put') {
            form.value?.put(props.action, options);
        }
    }
};

// Track validated steps
const checkValidatedStep = ref(null);
const pendingValidation = ref({});


// Validate a step
const validateStep = (index, data) => {
    const step = steps.value.find(s => s.step === index);
    if (!step) return;

    step.status = data.status ? 'complete' : 'active';

    step.form = data.form;

    // Update the next step to active if current is complete
    if (data.status && index < steps.value.length) {
        const nextStep = steps.value.find(s => s.step === index + 1);
        if (nextStep && nextStep.status === 'inactive') {
            nextStep.status = 'active';
        }
    }
};

watch(checkValidatedStep, (newVal) => {
    if (pendingValidation.value.step === newVal) {
        validateStep(pendingValidation.value.step, pendingValidation.value);
    }
});
</script>

<template>
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
                <Stepper v-slot="{ isPrevDisabled, nextStep, prevStep }" v-model="stepIndex" class="block w-full">
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
                            <BasicData v-if="stepIndex === 1" :step="1" :form="steps[0].form"
                                :checkValidatedStep="checkValidatedStep"
                                @validation="val => pendingValidation = { step: 1, ...val }"
                                :marital_statuess="marital_statuess" :genders="genders" />

                            <Contract v-if="stepIndex === 2" :step="2" :form="steps[1].form"
                                :checkValidatedStep="checkValidatedStep"
                                @validation="val => pendingValidation = { step: 2, ...val }"
                                :marital_statuess="marital_statuess" :genders="genders" />

                            <!-- <BasicData v-if="stepIndex === 2" />
                            <BasicData v-if="stepIndex === 3" /> -->
                        </div>

                        <div class="flex items-center justify-between mt-4">
                            <Button :disabled="isPrevDisabled" variant="outline" size="sm" @click="prevStep()">
                                Back
                            </Button>
                            <div class="flex items-center gap-3">
                                <Button v-if="stepIndex !== steps.length" size="sm" @click="() => {
                                    checkValidatedStep = stepIndex
                                    if (isNextDisabledComputed) return
                                    nextStep()
                                }">
                                    Next
                                </Button>
                                <Button v-if="stepIndex === steps.length" size="sm" type="submit">
                                    Submit
                                </Button>
                            </div>
                        </div>
                    </form>
                </Stepper>
            </CardContent>
            <CardFooter>

            </CardFooter>
        </Card>
    </AppLayout>
</template>
