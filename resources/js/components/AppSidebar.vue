<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue'
import NavUser from '@/components/NavUser.vue'
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar'
import { dashboard } from '@/routes'
import { type NavItem } from '@/types'
import { Link } from '@inertiajs/vue3'
import { BookOpen, Folder } from 'lucide-vue-next'
import AppLogo from './AppLogo.vue'
import SidebarModuleItem from './SidebarModuleItem.vue'

import { useAppStore } from '@/stores/appStore'
import { storeToRefs } from 'pinia'
import Spinner from './ui/spinner/Spinner.vue'

const appStore = useAppStore()
const { permissions, modules, loaded } = storeToRefs(appStore)

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
]
</script>


<template>

    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                        <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <div v-if="loaded">
                <SidebarModuleItem v-for="module in modules" :key="module.id" :module="module"
                    :permissions="permissions" />
            </div>
            <div v-else class="flex justify-center items-center mt-4">
                <Spinner class="size-6" />
            </div>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
