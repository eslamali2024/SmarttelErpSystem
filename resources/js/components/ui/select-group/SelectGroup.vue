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

interface Option {
    key: string | number
    option: string
}

interface Props {
    modelValue?: string
    modelValueError?: string
    label?: string
    placeholder?: string
    options?: Option[]
}

const props = defineProps<Props>()
const emit = defineEmits(['update:modelValue'])

// Computed property to ensure modelValue is always a string
const model = computed({
    get: () => String(props.modelValue ?? ''),
    set: (val: string) => emit('update:modelValue', String(val)),
})
</script>

<template>
    <div>
        <!-- Label -->
        <Label v-if="label">{{ label }}</Label>

        <!-- Select component -->
        <Select v-model="model">
            <SelectTrigger>
                <SelectValue :placeholder="placeholder || 'Select an option'" />
            </SelectTrigger>

            <SelectContent>
                <SelectGroup>
                    <SelectLabel v-if="label">{{ label }}</SelectLabel>

                    <!-- Render options safely -->
                    <SelectItem v-for="item in options ?? []" :key="item.key" :value="String(item.key)">
                        {{ item.option }}
                    </SelectItem>
                </SelectGroup>
            </SelectContent>
        </Select>

        <!-- Error message -->
        <p v-if="modelValueError" class="text-sm text-red-500 mt-2">
            {{ modelValueError }}
        </p>
    </div>
</template>
