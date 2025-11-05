<script setup lang="ts">
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { computed } from 'vue'

interface Props {
    modelValue?: string | number
    modelValueError?: string
    label?: string
    placeholder?: string
    type?: string
    disabled?: boolean
    vueError?: any
    minValue?: number
}

const props = defineProps<Props>()
const emit = defineEmits(['update:modelValue'])

const model = computed({
    get: () => props.modelValue ?? '',
    set: (val: string) => emit('update:modelValue', val),
})
</script>

<template>
    <div class="w-full">
        <Label>{{ props.label }}</Label>
        <Input :type="props.type || 'text'" :placeholder="props.placeholder" v-model="model" :disabled="props.disabled"
            :min="props.minValue" @blur="vueError ? vueError?.$touch() : ''" />
        <p v-if="props.modelValueError || vueError?.$errors[0]?.$message" class="text-sm text-red-500 mt-2">
            {{ props.modelValueError || vueError?.$errors[0]?.$message }}
        </p>
    </div>
</template>
