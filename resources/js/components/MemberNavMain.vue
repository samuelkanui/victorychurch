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
        <SidebarGroupLabel class="text-green-700 dark:text-green-300 font-semibold">
            Member Tools
        </SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton 
                    as-child 
                    class="hover:bg-green-100 hover:text-green-900 dark:hover:bg-green-900 dark:hover:text-green-100 data-[active=true]:bg-green-200 data-[active=true]:text-green-900 dark:data-[active=true]:bg-green-800 dark:data-[active=true]:text-green-100"
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
