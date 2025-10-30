<script setup lang="ts">
import Collapsible from './ui/collapsible/Collapsible.vue'
import CollapsibleTrigger from './ui/collapsible/CollapsibleTrigger.vue'
import CollapsibleContent from './ui/collapsible/CollapsibleContent.vue'
import SidebarMenuSub from './ui/sidebar/SidebarMenuSub.vue'
import SidebarMenuSubItem from './ui/sidebar/SidebarMenuSubItem.vue'
import {
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar'
import { Link } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import { AuthPermissions, Module } from '@/types'

const page = usePage();

const props = defineProps<{
    module: Module,
    auth_permissions: AuthPermissions[]
}>()


function hasPermission(module: Module) {
    if (!module.permission_title) return true;

    const permValues = props.auth_permissions;
    return permValues.includes(module.permission_title);
}
</script>

<template>
    <Collapsible v-if="module.children && module.children.length && hasPermission(module)" v-slot="{ open }" defaultOpen
        class="group/collapsible">
        <SidebarMenuItem>
            <SidebarMenuButton as-child>
                <CollapsibleTrigger asChild>
                    <SidebarMenuSubItem class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i v-if="module.icon" :class="module.icon"></i>
                            <span class="text-nowrap">
                                {{ $t(module.name) }}
                            </span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-200"
                            :class="{ 'rotate-90': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </SidebarMenuSubItem>
                </CollapsibleTrigger>
            </SidebarMenuButton>

            <CollapsibleContent>
                <SidebarMenuSub>
                    <SidebarModuleItem v-for="child in module.children" :key="child.id" :module="child"
                        :auth_permissions="auth_permissions" />
                </SidebarMenuSub>
            </CollapsibleContent>
        </SidebarMenuItem>
    </Collapsible>

    <SidebarMenuItem v-else-if="hasPermission(module)">
        <SidebarMenuButton as-child class="w-full">
            <Link :href="module.path" :class="{
                'flex items-center gap-2 w-full': true,
                'bg-gray-200 dark:bg-gray-600/50': page.url.includes(module.path),
            }">
            <i v-if="module.icon" :class="module.icon"></i>
            {{ $t(module.name) }}
            </Link>
        </SidebarMenuButton>
    </SidebarMenuItem>
</template>
