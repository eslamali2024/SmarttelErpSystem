<script setup lang="ts">
import { cn } from '@/lib/utils'
import { DialogDescription, type DialogDescriptionProps, useForwardProps } from 'reka-ui'
import Spinner from '@/components/ui/spinner/Spinner.vue';
import { computed, type HTMLAttributes } from 'vue'

const props = defineProps<DialogDescriptionProps & { class?: HTMLAttributes['class'] } & { loading?: boolean }>()

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props
    return delegated
})

const forwardedProps = useForwardProps(delegatedProps)

</script>

<template>
    <DialogDescription data-slot="dialog-description" v-bind="forwardedProps"
        :class="cn('text-muted-foreground text-sm', props.class)">
        <slot v-if="!props.loading" />

        <div class="grid grid-cols-1 gap-3 py-4" v-else>
            <div class="flex items-center justify-center">
                <Spinner />
            </div>
        </div>
    </DialogDescription>
</template>
