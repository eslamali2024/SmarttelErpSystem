<script setup lang="ts">
import { computed } from "vue"
import { Check, Search } from "lucide-vue-next"
import { Combobox, ComboboxAnchor, ComboboxEmpty, ComboboxGroup, ComboboxInput, ComboboxItem, ComboboxItemIndicator, ComboboxList } from "@/components/ui/combobox"
import { Label } from "@/components/ui/label"
import ScrollArea from "../scroll-area/ScrollArea.vue"

interface Props {
    modelValue?: string | number
    modelValueError?: string
    label?: string
    placeholder?: string
    options?: Record<string, string> | Array<{ value: string | number; label: string }> | { id: number; name: string };
    vueError?: any
}

const props = defineProps<Props>()
const emit = defineEmits(['update:modelValue'])

// Convert options to array of objects
const optionsArray = computed(() => {
    if (!props.options) return []
    if (Array.isArray(props.options)) {
        return props.options.map(opt => ({ value: String(opt.value), label: opt.label }))
    } else {
        return Object.entries(props.options).map(([value, label]) => ({ value, label }))
    }
})

const selectedValue = computed({
    get() {
        return optionsArray.value.find(opt => opt.value === String(props.modelValue)) ?? null
    },
    set(val: { value: string; label: string } | null) {
        emit('update:modelValue', val?.value ?? null)
    }
})
</script>

<template>
    <Combobox by="label" :model-value="selectedValue" @update:model-value="val => selectedValue = val">
        <Label v-if="props.label">{{ props.label }}</Label>
        <ComboboxAnchor>
            <div class="relative w-full items-center">
                <ComboboxInput class="pl-9 w-full" :display-value="(val) => val?.label ?? ''"
                    :placeholder="props.placeholder ?? 'Select framework'" />
                <span class="absolute start-0 inset-y-0 flex items-center justify-center px-3">
                    <Search class="size-4 text-muted-foreground" />
                </span>
            </div>
        </ComboboxAnchor>

        <ComboboxList>
            <ComboboxEmpty>
                {{ $t?.('no_results') ?? 'No results found' }}
            </ComboboxEmpty>

            <ScrollArea class="max-h-[300px] w-full">
                <ComboboxGroup class=" w-full">
                    <ComboboxItem v-for="option in optionsArray" :key="option.value" :value="option">
                        {{ option.label }}

                        <ComboboxItemIndicator>
                            <Check class="ml-auto h-4 w-4" />
                        </ComboboxItemIndicator>
                    </ComboboxItem>
                </ComboboxGroup>
            </ScrollArea>
        </ComboboxList>

        <p v-if="props.modelValueError || vueError?.$errors?.[0]?.$message" class="text-sm text-red-500 mt-2">
            {{ props.modelValueError || vueError?.$errors?.[0]?.$message }}
        </p>
    </Combobox>
</template>
