<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import A from '@/components/ui/a/A.vue';

interface Props {
    form?: any
    method_type?: string
    action?: string
    cancelRoute?: string
}

const props = defineProps<Props>()

const submit = () => {
    if (!props.form || !props.action || !props.method_type) {
        console.warn('Form, action, or method_type is missing.')
        return
    }

    if (props.method_type === 'post') {
        props.form.post(props.action)
    } else if (props.method_type === 'put') {
        props.form.put(props.action)
    } else {
        console.warn(`Unsupported method_type: ${props.method_type}`)
    }
}
</script>

<template>
    <form @submit.prevent="submit">
        <slot />

        <hr class="my-2 w-5/6 mx-auto">
        <div class="flex justify-end align-center gap-2">
            <A :href="cancelRoute" class="bg-red-500/50 cursor-pointer text-white hover:bg-red-600" size="sm">Cancel</A>
            <Button class="bg-green-500/50 cursor-pointer text-white hover:bg-green-600" size="sm" type="submit">
                Save
            </Button>
        </div>
    </form>
</template>
