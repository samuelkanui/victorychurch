<script setup lang="ts">
import LeaderNavFooter from '@/components/LeaderNavFooter.vue';
import LeaderNavMain from '@/components/LeaderNavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { 
    LayoutDashboard, 
    Users, 
    BookOpen, 
    MessageSquare, 
    Calendar,
    BarChart3,
    Shield,
    Github,
    Folder,
    FileText
} from 'lucide-vue-next';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/leader/dashboard',
        icon: LayoutDashboard,
    },
    {
        title: 'My Groups',
        href: '/leader/groups',
        icon: Users,
    },
    {
        title: 'Assignments',
        href: '/leader/assignments',
        icon: BookOpen,
    },
    {
        title: 'Resources',
        href: '/leader/resources',
        icon: FileText,
    },
    {
        title: 'Prayer Requests',
        href: '/leader/prayers',
        icon: MessageSquare,
    },
    {
        title: 'Meetings',
        href: '/leader/meetings',
        icon: Calendar,
    },
    {
        title: 'Reports & Analytics',
        href: '/leader/reports',
        icon: BarChart3,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Github,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: Folder,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset" class="border-r-blue-200 dark:border-r-blue-800">
        <SidebarHeader class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-950 dark:to-blue-900">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/leader/dashboard" class="group">
                            <div class="flex items-center space-x-2">
                                <div class="rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 p-1">
                                    <Shield class="h-5 w-5 text-white" />
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-blue-900 dark:text-blue-100">
                                        Leader Panel
                                    </span>
                                    <span class="text-xs text-blue-600 dark:text-blue-400">
                                        Church Management
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent class="bg-gradient-to-b from-blue-50/50 to-white dark:from-blue-950/50 dark:to-background">
            <LeaderNavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-950 dark:to-blue-900">
            <LeaderNavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>
