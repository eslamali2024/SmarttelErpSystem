<script setup lang="ts">
import { ref, computed, watch } from "vue"
import { Check, ChevronsUpDown, Search } from "lucide-vue-next"
import { Button } from "@/components/ui/button"
import {
    Combobox,
    ComboboxAnchor,
    ComboboxEmpty,
    ComboboxGroup,
    ComboboxInput,
    ComboboxItem,
    ComboboxItemIndicator,
    ComboboxList,
    ComboboxTrigger,
} from "@/components/ui/combobox"
import { Label } from "@/components/ui/label"

const props = defineProps<{
    options: { label: string; value: string }[]
    modelValue?: string
    modelValueError?: string
    label?: string
    placeholder?: string
    disabled?: boolean
}>()

const emit = defineEmits(["update:modelValue"])

const internalValue = ref(props.modelValue ?? "")

// Sync prop updates → local state
watch(() => props.modelValue, val => {
    internalValue.value = val ?? ""
})

// Emit updates to parent
watch(internalValue, val => {
    emit("update:modelValue", val)
})

const selectedItem = computed(() =>
    props.options.find(i => i.value === internalValue.value)
)
</script>

<template>
    <div class="flex flex-col gap-2">
        <Label v-if="props.label">{{ props.label }}</Label>

        <Combobox v-model="internalValue" by="value">
            <ComboboxAnchor as-child>
                <ComboboxTrigger as-child>
                    <Button :disabled="props.disabled" variant="outline" class="justify-between w-full">
                        {{ selectedItem?.label ?? props.placeholder ?? 'Select option' }}
                        <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                    </Button>
                </ComboboxTrigger>
            </ComboboxAnchor>

            <ComboboxList>
                <div class="relative w-full max-w-sm items-center">
                    <ComboboxInput class="pl-9 focus-visible:ring-0 border-0 border-b rounded-none h-10 w-full"
                        :placeholder="props.placeholder" />
                    <span class="absolute start-0 inset-y-0 flex items-center justify-center px-3">
                        <Search class="size-4 text-muted-foreground" />
                    </span>
                </div>

                <ComboboxEmpty>
                    {{ $t?.('no_results') ?? 'No results found' }}
                </ComboboxEmpty>

                <ComboboxGroup>
                    <ComboboxItem v-for="option in props.options" :key="option.value" :value="option.value">
                        {{ option.label }}
                        <ComboboxItemIndicator>
                            <Check class="ml-auto h-4 w-4" />
                        </ComboboxItemIndicator>
                    </ComboboxItem>
                </ComboboxGroup>
            </ComboboxList>
        </Combobox>

        <p v-if="props.modelValueError" class="text-red-500 text-sm">
            {{ props.modelValueError }}
        </p>
    </div>
</template>
