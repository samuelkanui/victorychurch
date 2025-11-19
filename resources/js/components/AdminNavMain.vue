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
        <SidebarGroupLabel class="text-purple-700 dark:text-purple-300 font-semibold">
            Administration
        </SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton 
                    as-child 
                    class="hover:bg-purple-100 hover:text-purple-900 dark:hover:bg-purple-900 dark:hover:text-purple-100 data-[active=true]:bg-purple-200 data-[active=true]:text-purple-900 dark:data-[active=true]:bg-purple-800 dark:data-[active=true]:text-purple-100"
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
