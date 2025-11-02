<script setup lang="ts">
import { useFilter } from "reka-ui"
import { computed, ref } from "vue"
import {
    Combobox,
    ComboboxAnchor,
    ComboboxEmpty,
    ComboboxGroup,
    ComboboxInput,
    ComboboxItem,
    ComboboxList
} from "@/components/ui/combobox"
import {
    TagsInput,
    TagsInputInput,
    TagsInputItem,
    TagsInputItemDelete,
    TagsInputItemText
} from "@/components/ui/tags-input"
import { Label } from "@/components/ui/label"

const props = defineProps<{
    items: { label: string; value: string }[]
    modelValue?: string[]
    modelValueError?: string
    label?: string
    placeholder?: string
    disabled?: boolean
}>()

const emit = defineEmits(["update:modelValue"])

const internalValue = computed<string[]>({
    get: () => props.modelValue ?? [],
    set: (val) => emit("update:modelValue", val)
})

const open = ref(false)
const searchTerm = ref("")

const { contains } = useFilter({ sensitivity: "base" })

const filteredFrameworks = computed(() => {
    const options = props.items.filter(i => !internalValue.value.includes(i.label))
    return searchTerm.value
        ? options.filter(option => contains(option.label, searchTerm.value))
        : options
})
</script>

<template>
    <Combobox v-model="internalValue" v-model:open="open" :ignore-filter="true">
        <ComboboxAnchor as-child>
            <Label>{{ props.label }}</Label>

            <TagsInput v-model="internalValue" class="px-2 gap-2 w-full">
                <div class="flex gap-2 flex-wrap items-center">
                    <TagsInputItem v-for="item in internalValue" :key="item" :value="item">
                        <TagsInputItemText />
                        <TagsInputItemDelete />
                    </TagsInputItem>
                </div>

                <ComboboxInput v-model="searchTerm" :disabled="props.disabled" as-child>
                    <TagsInputInput :placeholder="props.placeholder"
                        class="min-w-[200px] w-full p-0 border-none focus-visible:ring-0 h-auto"
                        @keydown.enter.prevent />
                </ComboboxInput>
            </TagsInput>

            <p v-if="props.modelValueError" class="text-sm text-red-500 mt-2">
                {{ props.modelValueError }}
            </p>

            <ComboboxList align="start" class="w-[--reka-popper-anchor-width] h-[300px]">
                <ComboboxEmpty>
                    {{ $t('no_results') }}
                </ComboboxEmpty>
                <ComboboxGroup>
                    <ComboboxItem v-for="item in filteredFrameworks" :key="item.value" :value="item.label"
                        @select.prevent="(ev) => {
                            if (typeof ev.detail.value === 'string') {
                                searchTerm = ''
                                internalValue = [...internalValue, ev.detail.value]
                            }

                            if (filteredFrameworks.length === 0) {
                                open = false
                            }
                        }">
                        {{ item.label }}
                    </ComboboxItem>
                </ComboboxGroup>
            </ComboboxList>
        </ComboboxAnchor>
    </Combobox>
</template>
