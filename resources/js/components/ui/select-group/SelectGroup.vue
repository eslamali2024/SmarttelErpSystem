<script setup lang="ts">
import { computed, watch, ref, nextTick } from 'vue'
import { Label } from '@/components/ui/label'
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'

interface Props {
    modelValue?: string | number,
    modelValueError?: string,
    label?: string,
    placeholder?: string,
    options?: Record<string, string> | Array<{ value: string | number, label: string }> | undefined | any[],
    vueError?: any
}

const props = defineProps<Props>()
const emit = defineEmits(['update:modelValue'])

// Computed for sending value
const model = computed({
    get: () => {
        const val = props.modelValue != null ? String(props.modelValue) : ''
        if (!optionsArray.value.find(opt => opt.value === val)) return ''
        return val
    },
    set: (val: string) => emit('update:modelValue', val)
})

// This For Convert Object To Array
const optionsArray = computed(() => {
    if (!props.options) return []

    if (Array.isArray(props.options)) {
        return props.options.map(opt => ({ value: String(opt.value), label: opt.label }))
    } else {
        return Object.entries(props.options).map(([value, label]) => ({ value, label }))
    }
})

//
watch(optionsArray, (newOptions) => {
    if (!newOptions.find(opt => opt.value === model.value)) {
        model.value = ''
    }
})

// We add This For Warning User If Select Is Open and empty
const isOpen = ref(false)
watch(isOpen, (open) => {
    props.vueError?.$touch()
    if (!open) {
        nextTick(() => {
            const active = document.activeElement as HTMLElement | null
            if (active && active.tagName === 'BUTTON') active.blur()
        })
    }
})
</script>
<template>
    <div>
        <Label v-if="props.label">{{ props.label }}</Label>

        <Select v-model="model" @open-change="isOpen = $event">
            <SelectTrigger class="bg-transparent hover:bg-gray-600/20 cursor-pointer" :disabled="!optionsArray.length">
                <SelectValue :placeholder="props.placeholder || 'Select an option'" />
            </SelectTrigger>

            <SelectContent class="bg-white dark:bg-gray-800" v-if="optionsArray.length > 0">
                <SelectGroup>
                    <SelectItem v-for="option in optionsArray" :key="option.value" :value="option.value"
                        class="cursor-pointer">
                        {{ option.label }}
                    </SelectItem>
                </SelectGroup>
            </SelectContent>
        </Select>

        <p v-if="props.modelValueError || vueError?.$errors[0]?.$message" class="text-sm text-red-500 mt-2">
            {{ props.modelValueError || vueError?.$errors[0]?.$message }}
        </p>
    </div>
</template>
