<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BookOpen } from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/layout/AppLogo.vue';
import NavFooter from '@/components/navigation/NavFooter.vue';
import NavMain from '@/components/navigation/NavMain.vue';
import NavUser from '@/components/navigation/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { navigationMenu } from '@/config/navigation';
import type { NavItem, NavMenuItem } from '@/interfaces';
import { filterNavByPermissions } from '@/lib/navigation';

const authorizedNavItems = computed<NavMenuItem[]>(() =>
    filterNavByPermissions(navigationMenu),
);

const footerNavItems: NavItem[] = [
    {
        title: 'Documentación',
        href: 'https://laravel.com/docs',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="authorizedNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
