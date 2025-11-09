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
    permissions: AuthPermissions[]
}>()

// Import the recursive component
import SidebarModuleItem from './SidebarModuleItem.vue'

function hasPermission(module: Module) {
    if (!module.permission_title) return true;
    const permValues = props.permissions;
    return permValues.includes(module.permission_title);
}

// Check if current URL is part of this module or any of its children
function isInModulePath(module: Module): boolean {
    // Check if current URL matches this module's path
    if (page.url.startsWith(module.path)) {
        return true;
    }

    // Recursively check children
    if (module.children) {
        return module.children.some(child => isInModulePath(child));
    }

    return false;
}

// Determine if collapsible should be open by default
const shouldBeOpen = isInModulePath(props.module);
</script>

<template>
    <Collapsible v-if="module.children && module.children.length && hasPermission(module)" v-slot="{ open }"
        :defaultOpen="shouldBeOpen" class="group/collapsible">
        <SidebarMenuItem>
            <SidebarMenuButton as-child>
                <CollapsibleTrigger asChild>
                    <SidebarMenuSubItem class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i v-if="module.icon" :class="module.icon"></i>
                            <span class="text-nowrap">
                                {{ $t(module.name).substring(0, 20) }}
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
                        :permissions="permissions" />
                </SidebarMenuSub>
            </CollapsibleContent>
        </SidebarMenuItem>
    </Collapsible>

    <SidebarMenuItem v-else-if="hasPermission(module)">
        <SidebarMenuButton as-child class="w-full">
            <Link :href="module.path" :class="{
                'flex items-center gap-2 w-full': true,
                'bg-gray-200 dark:bg-gray-600/50': page.url.startsWith(module.path),
            }">
            <i v-if="module.icon" :class="module.icon"></i>
            <span class="text-nowrap">{{ $t(module.name).substring(0, 20) }}</span>
            </Link>
        </SidebarMenuButton>
    </SidebarMenuItem>
</template>
