<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { toUrl, urlIsActive } from '@/lib/utils';
import { bonuses, deductions, contracts } from '@/routes/hr/employees/activity';

import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';
import Can from '@/components/ui/Auth/Can.vue';

const { t } = useI18n();

const props = defineProps<{
    employee: any,
}>();


const sidebarNavItems = computed<NavItem[]>(() => [
    {
        title: t('bonuses'),
        href: bonuses(props.employee.id),
        permission: 'bonus_access',
    },
    {
        title: t('deductions'),
        href: deductions(props.employee.id),
        permission: 'deduction_access',
    },
    {
        title: t('contracts'),
        href: contracts(props.employee.id),
        permission: 'employee_contract_access',
    },

]);
const currentPath = typeof window !== undefined ? window.location.pathname : '';
</script>

<template>
    <div class="px-4 py-6">
        <Heading :title="$t('employee_activity')" :description="t('employee_activity')" />

        <div class="flex flex-col lg:flex-row lg:space-x-12">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-y-1 space-x-0">
                    <template v-for="item in sidebarNavItems" :key="toUrl(item.href)">
                        <Can :permissions="item.permission">
                            <Button variant="ghost" :class="[
                                'w-full justify-start',
                                { 'bg-muted': urlIsActive(item.href, currentPath) },
                            ]" as-child>
                                <Link :href="item.href">
                                <component :is="item.icon" class="h-4 w-4" />
                                {{ item.title }}
                                </Link>
                            </Button>
                        </Can>
                    </template>
                </nav>
            </aside>

            <Separator class="my-6 lg:hidden" />

            <div class="flex-1 md:max-w-2xl xl:max-w-full">
                <section class=" space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
