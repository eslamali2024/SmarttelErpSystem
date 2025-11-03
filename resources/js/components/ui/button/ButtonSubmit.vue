<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Loader2 } from "lucide-vue-next";

const props = defineProps<{
    loading: boolean,
    disabled?: boolean,
    submit?: () => void,
    cancel?: () => void,
}>();
</script>

<template>
    <!-- Cancel button -->
    <Button v-if="cancel" type="button" @click="cancel"
        class="bg-red-500/50 cursor-pointer text-white hover:bg-red-600">
        {{ $t('cancel') }}
    </Button>

    <!-- Submit button -->
    <Button v-if="submit" type="button" @click="submit"
        :class="['bg-green-500/50 cursor-pointer text-white hover:bg-green-600', { 'opacity-50 cursor-not-allowed': loading }]"
        :disabled="loading || disabled">
        <Loader2 v-if="loading" class="w-4 h-4 animate-spin mr-2" />
        <slot />
    </Button>
</template>
