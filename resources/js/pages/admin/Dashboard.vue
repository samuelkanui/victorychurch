<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { 
    Users, 
    UserCheck, 
    UserPlus, 
    Shield, 
    BookOpen, 
    MessageSquare, 
    Calendar,
    TrendingUp,
    Activity,
    AlertTriangle
} from 'lucide-vue-next';

interface Props {
    stats: {
        totalUsers: number;
        activeUsers: number;
        newUsersThisMonth: number;
        totalGroups: number;
        activeGroups: number;
        totalAssignments: number;
        pendingSubmissions: number;
        prayerRequests: number;
        upcomingMeetings: number;
        systemAlerts: number;
    };
    recentActivity: Array<{
        id: number;
        type: string;
        description: string;
        user: string;
        timestamp: string;
    }>;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: '/admin/dashboard',
    },
];

const quickActions = [
    { title: 'Manage Users', icon: Users, href: '/admin/users', color: 'bg-purple-500' },
    { title: 'View Groups', icon: BookOpen, href: '/admin/groups', color: 'bg-blue-500' },
    { title: 'System Settings', icon: Shield, href: '/admin/settings', color: 'bg-green-500' },
    { title: 'Reports', icon: TrendingUp, href: '/admin/reports', color: 'bg-orange-500' },
];

const getActivityIcon = (type: string) => {
    switch (type) {
        case 'user_registered': return UserPlus;
        case 'group_created': return BookOpen;
        case 'prayer_request': return MessageSquare;
        case 'meeting_scheduled': return Calendar;
        default: return Activity;
    }
};

const getActivityColor = (type: string) => {
    switch (type) {
        case 'user_registered': return 'text-green-600';
        case 'group_created': return 'text-blue-600';
        case 'prayer_request': return 'text-purple-600';
        case 'meeting_scheduled': return 'text-orange-600';
        default: return 'text-gray-600';
    }
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent">
                        Admin Dashboard
                    </h1>
                    <p class="text-muted-foreground">
                        Manage your church community and monitor system activity
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <Badge v-if="props.stats.systemAlerts > 0" variant="destructive" class="flex items-center gap-1">
                        <AlertTriangle class="h-3 w-3" />
                        {{ props.stats.systemAlerts }} Alerts
                    </Badge>
                    <Badge variant="outline" class="flex items-center gap-1">
                        <Activity class="h-3 w-3" />
                        System Active
                    </Badge>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Total Users -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Users</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ props.stats.totalUsers }}</div>
                        <p class="text-xs text-muted-foreground">
                            {{ props.stats.activeUsers }} active this month
                        </p>
                    </CardContent>
                </Card>

                <!-- New Users -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">New Users</CardTitle>
                        <UserPlus class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ props.stats.newUsersThisMonth }}</div>
                        <p class="text-xs text-muted-foreground">
                            This month
                        </p>
                    </CardContent>
                </Card>

                <!-- Groups -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Groups</CardTitle>
                        <BookOpen class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ props.stats.totalGroups }}</div>
                        <p class="text-xs text-muted-foreground">
                            {{ props.stats.activeGroups }} active
                        </p>
                    </CardContent>
                </Card>

                <!-- Prayer Requests -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Prayer Requests</CardTitle>
                        <MessageSquare class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ props.stats.prayerRequests }}</div>
                        <p class="text-xs text-muted-foreground">
                            Active requests
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Quick Actions -->
            <Card>
                <CardHeader>
                    <CardTitle>Quick Actions</CardTitle>
                    <CardDescription>
                        Common administrative tasks
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <Button
                            v-for="action in quickActions"
                            :key="action.title"
                            variant="outline"
                            class="h-20 flex-col space-y-2"
                            as-child
                        >
                            <a :href="action.href">
                                <div :class="[action.color, 'rounded-full p-2 text-white']">
                                    <component :is="action.icon" class="h-5 w-5" />
                                </div>
                                <span class="text-sm font-medium">{{ action.title }}</span>
                            </a>
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Recent Activity & System Overview -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Recent Activity -->
                <Card>
                    <CardHeader>
                        <CardTitle>Recent Activity</CardTitle>
                        <CardDescription>
                            Latest system events and user actions
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div
                                v-for="activity in props.recentActivity"
                                :key="activity.id"
                                class="flex items-start space-x-3"
                            >
                                <div :class="[getActivityColor(activity.type), 'mt-1']">
                                    <component :is="getActivityIcon(activity.type)" class="h-4 w-4" />
                                </div>
                                <div class="flex-1 space-y-1">
                                    <p class="text-sm font-medium">{{ activity.description }}</p>
                                    <p class="text-xs text-muted-foreground">
                                        by {{ activity.user }} • {{ activity.timestamp }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- System Overview -->
                <Card>
                    <CardHeader>
                        <CardTitle>System Overview</CardTitle>
                        <CardDescription>
                            Current system status and metrics
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <!-- Assignments -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <BookOpen class="h-4 w-4 text-blue-600" />
                                    <span class="text-sm font-medium">Assignments</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-bold">{{ props.stats.totalAssignments }}</div>
                                    <div class="text-xs text-muted-foreground">
                                        {{ props.stats.pendingSubmissions }} pending
                                    </div>
                                </div>
                            </div>

                            <!-- Meetings -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <Calendar class="h-4 w-4 text-green-600" />
                                    <span class="text-sm font-medium">Upcoming Meetings</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-bold">{{ props.stats.upcomingMeetings }}</div>
                                    <div class="text-xs text-muted-foreground">This week</div>
                                </div>
                            </div>

                            <!-- Active Users -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <UserCheck class="h-4 w-4 text-purple-600" />
                                    <span class="text-sm font-medium">Active Users</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-bold">{{ props.stats.activeUsers }}</div>
                                    <div class="text-xs text-muted-foreground">Last 30 days</div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AdminLayout>
</template>
