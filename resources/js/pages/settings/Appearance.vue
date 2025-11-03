<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import LangTabs from '@/components/LangTabs.vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/appearance';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: t('appearance_settings'),
        href: edit().url,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head :title="$t('appearance_settings')" />

        <SettingsLayout>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-6 col-span-2 md:col-span-1">
                    <HeadingSmall :title="$t('appearance_settings')"
                        :description="$t('appearance_settings_description')" />
                    <AppearanceTabs />
                </div>
                <div class="space-y-6 col-span-2 md:col-span-1">
                    <HeadingSmall :title="$t('change_language')" :description="$t('change_language_description')" />
                    <LangTabs />
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
