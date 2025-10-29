<script setup lang="ts">
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationItem,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination'
import { type PrimitiveProps } from 'reka-ui'
import { router } from '@inertiajs/vue3'


interface Props extends PrimitiveProps {
    items: any,
    currentPage: number,
    total: number,
    itemsPerPage: number,
    defaultPage: number,
}

const props = defineProps<Props>();
let items = props.items.splice(1, props.items.length - 2);

// methods
const goTo = (url: string) => {
    router.visit(url);
}
</script>

<template>
    <Pagination :items-per-page="props.itemsPerPage" :total="props.total" :default-page="props.defaultPage">
        <PaginationContent>
            <PaginationPrevious v-if="items[props.currentPage - 2]"
                v-on:click="goTo(items[props.currentPage - 1].url)" />

            <template v-for="(item, index) in items" :key="index">
                <PaginationItem :value="parseInt(item.label)" :is-active="item.active" v-on:click="goTo(item.url)">
                    {{ item.label }}
                </PaginationItem>
            </template>

            <PaginationNext v-if="items[props.currentPage]" v-on:click="goTo(items[props.currentPage].url)" />
        </PaginationContent>
    </Pagination>
</template>
