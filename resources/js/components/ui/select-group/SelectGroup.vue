<script setup lang="ts">
import { computed } from 'vue'
import { Label } from '@/components/ui/label'
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'

interface Props {
    modelValue?: string
    modelValueError?: string
    label?: string
    placeholder?: string
    options?: Record<string, string>
}

const props = defineProps<Props>()
const emit = defineEmits(['update:modelValue'])

const model = computed({
    get: () => String(props.modelValue ?? ''),
    set: (val: string) => emit('update:modelValue', String(val)),
})

const optionsArray = props.options
    ? Object.entries(props.options).map(([value, label]) => ({ value, label }))
    : []
</script>

<template>
    <div>
        <Label v-if="props.label">{{ props.label }}</Label>

        <Select v-model="model">
            <SelectTrigger class="bg-transpartent hover:bg-gray-600/50">
                <SelectValue :placeholder="props.placeholder || 'Select an option'" />
            </SelectTrigger>

            <SelectContent class="bg-white dark:bg-gray-800">
                <SelectGroup>
                    <SelectItem v-for="option in optionsArray" :key="option.value" :value="option.value">
                        {{ option.label }}
                    </SelectItem>
                </SelectGroup>
            </SelectContent>
        </Select>

        <p v-if="props.modelValueError" class="text-sm text-red-500 mt-2">
            {{ props.modelValueError }}
        </p>
    </div>
</template>
