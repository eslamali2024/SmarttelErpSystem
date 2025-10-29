<script setup lang="ts">
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { computed } from 'vue'

interface Props {
    modelValue?: string
    modelValueError?: string
    label?: string
    placeholder?: string
    type?: string
    disabled?: boolean
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
        <Input :type="props.type || 'text'" :placeholder="props.placeholder" v-model="model"
            :disabled="props.disabled" />
        <p v-if="props.modelValueError" class="text-sm text-red-500 mt-2">
            {{ props.modelValueError }}
        </p>
    </div>
</template>
