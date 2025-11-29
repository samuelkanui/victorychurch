<script setup lang="ts">
import AdminNavFooter from '@/components/AdminNavFooter.vue';
import AdminNavMain from '@/components/AdminNavMain.vue';
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
    FileText,
    Settings,
    Shield,
    BarChart3,
    AlertTriangle,
    UserX
} from 'lucide-vue-next';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
        icon: LayoutDashboard,
    },
    {
        title: 'User Management',
        href: '/admin/users',
        icon: Users,
    },
    {
        title: 'Group Management',
        href: '/admin/groups',
        icon: BookOpen,
    },
    {
        title: 'Prayer Requests',
        href: '/admin/prayers',
        icon: MessageSquare,
    },
    {
        title: 'Meetings',
        href: '/admin/meetings',
        icon: Calendar,
    },
    {
        title: 'Resources',
        href: '/admin/resources',
        icon: FileText,
    },
    {
        title: 'Reports & Analytics',
        href: '/admin/reports',
        icon: BarChart3,
    },
    {
        title: 'Ban Appeals',
        href: '/admin/appeals',
        icon: AlertTriangle,
    },
    {
        title: 'Deletion Requests',
        href: '/admin/deletion-requests',
        icon: UserX,
    },
    {
        title: 'System Settings',
        href: '/admin/settings',
        icon: Settings,
    },
    {
        title: 'Security & Roles',
        href: '/admin/security',
        icon: Shield,
    },
];


</script>

<template>
    <Sidebar collapsible="icon" variant="inset" class="border-r-purple-200 dark:border-r-purple-800">
        <SidebarHeader class="bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-950 dark:to-purple-900">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/admin/dashboard" class="group">
                            <div class="flex items-center space-x-2">
                                <div class="rounded-lg bg-gradient-to-r from-purple-600 to-purple-700 p-1">
                                    <Shield class="h-5 w-5 text-white" />
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-purple-900 dark:text-purple-100">
                                        Church Admin
                                    </span>
                                    <span class="text-xs text-purple-600 dark:text-purple-400">
                                        Management Portal
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent class="bg-gradient-to-b from-purple-50/50 to-white dark:from-purple-950/50 dark:to-background">
            <AdminNavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter class="bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-950 dark:to-purple-900">
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>
