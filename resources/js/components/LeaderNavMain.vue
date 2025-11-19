<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';

interface Props {
    items: NavItem[];
}

defineProps<Props>();
</script>

<template>
    <SidebarGroup>
        <SidebarGroupLabel class="text-blue-700 dark:text-blue-300 font-semibold">
            Leader Tools
        </SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton 
                    as-child 
                    class="hover:bg-blue-100 hover:text-blue-900 dark:hover:bg-blue-900 dark:hover:text-blue-100 data-[active=true]:bg-blue-200 data-[active=true]:text-blue-900 dark:data-[active=true]:bg-blue-800 dark:data-[active=true]:text-blue-100"
                >
                    <Link :href="item.href">
                        <component :is="item.icon" class="h-4 w-4" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
