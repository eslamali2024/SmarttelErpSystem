<script setup lang="ts">
import { computed } from 'vue';
import { useLang } from '@/composables/useLang';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const { lang, updateLang } = useLang();
const tabs = computed(() => [
    { value: 'en', label: t('english') },
    { value: 'ar', label: t('arabic') },
]);
</script>

<template>
    <div class="inline-flex gap-1 rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800">
        <button v-for="{ value, label } in tabs" :key="value" @click="updateLang(value)" :class="[
            'flex items-center rounded-md px-3.5 py-1.5 transition-colors',
            lang === value
                ? 'bg-white shadow-xs dark:bg-neutral-700 dark:text-neutral-100'
                : 'text-neutral-500 hover:bg-neutral-200/60 hover:text-black dark:text-neutral-400 dark:hover:bg-neutral-700/60',
        ]">
            <span class="ml-1.5 text-sm">{{ label }}</span>
        </button>
    </div>
</template>
