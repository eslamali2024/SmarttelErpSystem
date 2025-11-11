<script setup lang="ts">
import {
    DateRangePickerArrow,
    DateRangePickerCalendar,
    DateRangePickerCell,
    DateRangePickerCellTrigger,
    DateRangePickerContent,
    DateRangePickerField,
    DateRangePickerGrid,
    DateRangePickerGridBody,
    DateRangePickerGridHead,
    DateRangePickerGridRow,
    DateRangePickerHeadCell,
    DateRangePickerHeader,
    DateRangePickerHeading,
    DateRangePickerInput,
    DateRangePickerNext,
    DateRangePickerPrev,
    DateRangePickerRoot,
    DateRangePickerTrigger,
    Label,
} from 'reka-ui'
import { ref, watch } from 'vue'

import { CalendarDate } from '@internationalized/date'

const props = defineProps<{
    modelValue?: { start: string | null; end: string | null }
    label?: string
    disabled?: boolean
}>()

const emit = defineEmits<{
    (e: 'update:modelValue', value: { start: string | null; end: string | null }): void
}>()

function parseDate(dateStr: string | null) {
    if (!dateStr) return null
    const [d, m, y] = dateStr.split('/').map(Number)
    return new CalendarDate(y, m, d)
}

function formatDate(dateObj: CalendarDate | null) {
    if (!dateObj) return null
    const d = String(dateObj.day).padStart(2, '0')
    const m = String(dateObj.month).padStart(2, '0')
    const y = dateObj.year
    return `${d}/${m}/${y}`
}

const modelValueInternal = ref<{
    start: CalendarDate | null
    end: CalendarDate | null
}>({
    start: parseDate(props.modelValue?.start ?? null),
    end: parseDate(props.modelValue?.end ?? null),
})

watch(modelValueInternal, (val) => {
    emit('update:modelValue', {
        start: formatDate(val.start),
        end: formatDate(val.end),
    })
}, { deep: true })
</script>

<template>
    <div>
        <Label v-if="props.label">{{ props.label }}</Label>
        <div class="w-fit mt-1">
            <DateRangePickerRoot id="date-range" v-model="modelValueInternal" :disabled="props.disabled">
                <DateRangePickerField v-slot="{ segments }"
                    class="flex select-none bg-transparent dark:bg-gray-500/50 border items-center rounded-lg text-center shadow-sm p-1">
                    <template v-for="item in segments.start" :key="item.part">
                        <DateRangePickerInput :part="item.part" type="start"
                            class="rounded-md p-0.5 focus:outline-none focus:shadow-[0_0_0_2px] focus:shadow-black">
                            {{ item.value }}
                        </DateRangePickerInput>
                    </template>
                    <span class="mx-2">-</span>
                    <template v-for="item in segments.end" :key="item.part">
                        <DateRangePickerInput :part="item.part" type="end"
                            class="rounded-md p-0.5 focus:outline-none focus:shadow-[0_0_0_2px] focus:shadow-black">
                            {{ item.value }}
                        </DateRangePickerInput>
                    </template>
                    <DateRangePickerTrigger
                        class="ml-4 focus:shadow-[0_0_0_2px] focus:shadow-black focus:outline-none rounded p-1">
                        <i class="ri-calendar-2-line"></i>
                    </DateRangePickerTrigger>
                </DateRangePickerField>
                <DateRangePickerContent :side-offset="4"
                    class="rounded-xl bg-gray-100 dark:bg-gray-500 border shadow-sm will-change-[transform,opacity]">
                    <DateRangePickerArrow class="fill-white stroke-gray-300" />
                    <DateRangePickerCalendar v-slot="{ weekDays, grid }" class="p-4">
                        <DateRangePickerHeader class="flex items-center justify-between">
                            <DateRangePickerPrev
                                class="inline-flex items-center cursor-pointer text-black justify-center rounded-md bg-transparent w-7 h-7 hover:bg-stone-100">
                                <i class="ri-arrow-left-s-line"></i>
                            </DateRangePickerPrev>
                            <DateRangePickerHeading class="text-sm text-black font-medium" />
                            <DateRangePickerNext
                                class="inline-flex items-center cursor-pointer text-black justify-center rounded-md bg-transparent w-7 h-7 hover:bg-stone-100">
                                <i class="ri-arrow-right-s-line"></i>
                            </DateRangePickerNext>
                        </DateRangePickerHeader>
                        <div class="flex flex-col space-y-4 pt-4 sm:flex-row sm:space-x-4 sm:space-y-0">
                            <DateRangePickerGrid v-for="month in grid" :key="month.value.toString()"
                                class="w-full border-collapse select-none space-y-1">
                                <DateRangePickerGridHead>
                                    <DateRangePickerGridRow class="mb-1 flex w-full justify-between">
                                        <DateRangePickerHeadCell v-for="day in weekDays" :key="day"
                                            class="w-8 rounded-md text-xs font-normal! text-black">
                                            {{ day }}
                                        </DateRangePickerHeadCell>
                                    </DateRangePickerGridRow>
                                </DateRangePickerGridHead>
                                <DateRangePickerGridBody>
                                    <DateRangePickerGridRow v-for="(weekDates, index) in month.rows"
                                        :key="`weekDate-${index}`" class="flex w-full">
                                        <DateRangePickerCell v-for="weekDate in weekDates" :key="weekDate.toString()"
                                            :date="weekDate">
                                            <DateRangePickerCellTrigger :day="weekDate" :month="month.value"
                                                class="relative flex items-center justify-center rounded-full whitespace-nowrap text-sm font-normal text-black w-8 h-8 outline-none hover:bg-gray-600 data-selected:bg-gray-600! data-selected:text-white" />
                                        </DateRangePickerCell>
                                    </DateRangePickerGridRow>
                                </DateRangePickerGridBody>
                            </DateRangePickerGrid>
                        </div>
                    </DateRangePickerCalendar>
                </DateRangePickerContent>
            </DateRangePickerRoot>
        </div>
    </div>
</template>
