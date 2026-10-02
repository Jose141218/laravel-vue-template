<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronRight } from '@lucide/vue';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavMenuItem } from '@/interfaces';
import { resolveHref } from '@/lib/navigation';

defineProps<{
    items: NavMenuItem[];
    label?: string;
}>();

const page = usePage();
const { isCurrentUrl } = useCurrentUrl();

const resolveLabel = (item: NavMenuItem): string =>
    item.label ?? item.title ?? '';

const isItemActive = (item: NavMenuItem): boolean => {
    if (typeof item.isActive === 'boolean') {
        return item.isActive;
    }

    void page.url;

    if (item.route && typeof route === 'function') {
        try {
            if (route().current(item.route)) {
                return true;
            }

            const pattern = item.route.endsWith('.index')
                ? item.route.replace(/\.index$/, '.*')
                : `${item.route}.*`;

            if (route().current(pattern)) {
                return true;
            }
        } catch {

        }
    }

    return isCurrentUrl(resolveHref(item));
};

const isSubmenuActive = (item: NavMenuItem): boolean => {
    if (!item.menu) {
        return false;
    }

    return item.menu.some((sub) => isItemActive(sub));
};
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel v-if="label">{{ label }}</SidebarGroupLabel>
        <SidebarMenu>
            <template v-for="item in items" :key="resolveLabel(item)">
                <Collapsible v-if="item.menu && item.menu.length > 0" as-child :default-open="isSubmenuActive(item)"
                    class="group/collapsible">
                    <SidebarMenuItem>
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton :tooltip="resolveLabel(item)">
                                <component :is="item.icon" v-if="item.icon" />
                                <span>{{ resolveLabel(item) }}</span>
                                <ChevronRight
                                    class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem v-for="subItem in item.menu" :key="resolveLabel(subItem)">
                                    <SidebarMenuSubButton as-child :is-active="isItemActive(subItem)">
                                        <Link :href="resolveHref(subItem)">
                                            <component :is="subItem.icon" v-if="subItem.icon" />
                                            <span>{{
                                                resolveLabel(subItem)
                                                }}</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </SidebarMenuItem>
                </Collapsible>

                <SidebarMenuItem v-else>
                    <SidebarMenuButton as-child :is-active="isItemActive(item)" :tooltip="resolveLabel(item)">
                        <Link :href="resolveHref(item)">
                            <component :is="item.icon" v-if="item.icon" />
                            <span>{{ resolveLabel(item) }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>
