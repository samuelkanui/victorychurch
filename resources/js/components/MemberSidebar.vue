<script setup lang="ts">
import MemberNavFooter from '@/components/MemberNavFooter.vue';
import MemberNavMain from '@/components/MemberNavMain.vue';
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
    MessageSquare, 
    BookOpen,
    Calendar,
    User,
    Github,
    Folder,
    FileText
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/member/dashboard',
        icon: LayoutDashboard,
    },
    {
        title: 'My Groups',
        href: '/member/groups',
        icon: Users,
    },
    {
        title: 'Prayer Requests',
        href: '/member/prayers',
        icon: MessageSquare,
    },
    {
        title: 'Resources',
        href: '/member/resources',
        icon: FileText,
    },
    {
        title: 'Assignments',
        href: '/member/assignments',
        icon: BookOpen,
    },
    {
        title: 'Meetings',
        href: '/member/meetings',
        icon: Calendar,
    },
    {
        title: 'Profile',
        href: '/member/profile',
        icon: User,
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
    <Sidebar collapsible="icon" variant="inset" class="border-r-green-200 dark:border-r-green-800">
        <SidebarHeader class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-950 dark:to-green-900">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/member/dashboard" class="group">
                            <div class="flex items-center space-x-2">
                                <div class="rounded-lg bg-gradient-to-r from-green-600 to-green-700 p-1">
                                    <User class="h-5 w-5 text-white" />
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-green-900 dark:text-green-100">
                                        Member Portal
                                    </span>
                                    <span class="text-xs text-green-600 dark:text-green-400">
                                        Church Community
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent class="bg-gradient-to-b from-green-50/50 to-white dark:from-green-950/50 dark:to-background">
            <MemberNavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-950 dark:to-green-900">
            <MemberNavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>
