<script setup lang="ts">
import { Label } from '@/components/ui/label'
import { type PrimitiveProps } from 'reka-ui'
import { Textarea } from '@/components/ui/textarea'
import { computed } from 'vue'


interface Props extends PrimitiveProps {
    modelValue?: string,
    modelValueError?: string
    label?: string,
    placeholder?: string
    type?: string,
    placeholder_message?: string,
    disabled?: boolean,
    vueError?: any
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
        <Label :for="model">{{ props.label }}</Label>
        <Textarea :id="model" :placeholder="props.placeholder" v-model="model" :disabled="props.disabled" />
        <p class="text-sm text-muted-foreground">
            {{ props.placeholder_message }}
        </p>
        <p v-if="props.modelValueError" class="text-sm text-red-500 mt-2">
            {{ props.modelValueError || vueError?.$errors[0]?.$message }}
        </p>

    </div>
</template>
