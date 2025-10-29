<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { Primitive, type PrimitiveProps } from 'reka-ui'
import { type AVariants, aVariants } from '.'
import { Link } from '@inertiajs/vue3';

interface Props extends PrimitiveProps {
    variant?: AVariants['variant']
    size?: AVariants['size']
    class?: HTMLAttributes['class'],
    href?: string
}

const props = withDefaults(defineProps<Props>(), {
    as: 'a',
})
</script>

<template>
    <Link :href="href" :class="cn(aVariants({ variant, size }), props.class)">
    <slot />
    </Link>
</template>
