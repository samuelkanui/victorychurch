<script setup lang="ts">
import LeaderLayout from '@/layouts/LeaderLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItemType } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { 
    Users, 
    UserCheck, 
    Clock, 
    CheckCircle, 
    MessageSquare, 
    Calendar,
    BookOpen,
    TrendingUp,
    AlertCircle,
    Eye,
    Activity
} from 'lucide-vue-next'

interface Props {
    stats: {
        total_groups: number
        total_members: number
        pending_requests: number
        active_groups: number
    }
    groups: Array<{
        id: number
        name: string
        description: string
        is_active: boolean
        approved_members_count: number
        members_count: number
        members: Array<{
            id: number
            name: string
            pivot: {
                status: string
                joined_at: string
            }
        }>
    }>
    recentPrayers: Array<{
        id: number
        title: string
        privacy: string
        is_anonymous: boolean
        created_at: string
        user: {
            name: string
        }
    }>
    recentActivity: Array<{
        id: string
        type: string
        description: string
        user: string
        timestamp: string
        action_url?: string
        group?: string
    }>
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Leader Dashboard',
        href: '/leader/dashboard',
    },
];

const quickActions = [
    { title: 'Manage Groups', icon: Users, href: '/leader/groups', color: 'bg-blue-500' },
    { title: 'View Assignments', icon: BookOpen, href: '/leader/assignments', color: 'bg-green-500' },
    { title: 'Prayer Requests', icon: MessageSquare, href: '/leader/prayers', color: 'bg-purple-500' },
    { title: 'Schedule Meeting', icon: Calendar, href: '/leader/meetings', color: 'bg-orange-500' },
];

const getActivityIcon = (type: string) => {
    switch (type) {
        case 'member_request': return Users
        case 'prayer_request': return MessageSquare
        case 'assignment': return BookOpen
        case 'meeting': return Calendar
        default: return AlertCircle
    }
}

const getActivityColor = (type: string) => {
    switch (type) {
        case 'member_request': return 'text-blue-600'
        case 'prayer_request': return 'text-purple-600'
        case 'assignment': return 'text-green-600'
        case 'meeting': return 'text-orange-600'
        default: return 'text-gray-600'
    }
}
</script>

<template>
    <Head title="Leader Dashboard" />

    <LeaderLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        Leader Dashboard
                    </h1>
                    <p class="text-muted-foreground">
                        Manage your groups, members, and ministry activities
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <Badge v-if="props.stats.pending_requests > 0" variant="destructive" class="flex items-center gap-1">
                        <AlertCircle class="h-3 w-3" />
                        {{ props.stats.pending_requests }} Pending
                    </Badge>
                    <Badge variant="outline" class="flex items-center gap-1">
                        <Activity class="h-3 w-3" />
                        Online
                    </Badge>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">My Groups</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">{{ stats.total_groups }}</div>
                        <p class="text-xs text-muted-foreground">
                            {{ stats.active_groups }} active
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Members</CardTitle>
                        <UserCheck class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">{{ stats.total_members }}</div>
                        <p class="text-xs text-muted-foreground">
                            Approved members
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Pending Requests</CardTitle>
                        <Clock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-yellow-600">{{ stats.pending_requests }}</div>
                        <p class="text-xs text-muted-foreground">
                            Awaiting approval
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Active Groups</CardTitle>
                        <CheckCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-purple-600">{{ stats.active_groups }}</div>
                        <p class="text-xs text-muted-foreground">
                            Currently running
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Quick Actions -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <TrendingUp class="h-5 w-5" />
                        Quick Actions
                    </CardTitle>
                    <CardDescription>
                        Common tasks and shortcuts for leaders
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <Button
                            v-for="action in quickActions"
                            :key="action.title"
                            variant="outline"
                            class="h-20 flex-col gap-2"
                            as-child
                        >
                            <Link :href="action.href">
                                <div :class="['p-2 rounded-md text-white', action.color]">
                                    <component :is="action.icon" class="h-5 w-5" />
                                </div>
                                <span class="text-sm font-medium">{{ action.title }}</span>
                            </Link>
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <div class="grid gap-6 lg:grid-cols-2">
                <!-- My Groups -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Users class="h-5 w-5" />
                            My Groups
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div v-for="group in groups" :key="group.id" class="flex items-center justify-between p-4 border rounded-lg">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-medium">{{ group.name }}</h3>
                                        <Badge :class="group.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                                            {{ group.is_active ? 'Active' : 'Inactive' }}
                                        </Badge>
                                    </div>
                                    <p class="text-sm text-muted-foreground mt-1">{{ group.description }}</p>
                                    <div class="flex items-center gap-4 mt-2 text-xs text-muted-foreground">
                                        <span class="flex items-center gap-1">
                                            <UserCheck class="h-3 w-3" />
                                            {{ group.approved_members_count }} members
                                        </span>
                                        <span v-if="group.members.length > 0" class="flex items-center gap-1">
                                            <Clock class="h-3 w-3" />
                                            {{ group.members.length }} pending
                                        </span>
                                    </div>
                                </div>
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="`/leader/groups/${group.id}`">
                                        <Eye class="h-4 w-4 mr-1" />
                                        View
                                    </Link>
                                </Button>
                            </div>
                            <div v-if="groups.length === 0" class="text-center py-8 text-muted-foreground">
                                <Users class="h-12 w-12 mx-auto mb-4 opacity-50" />
                                <p>No groups assigned yet</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent Prayer Requests -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <MessageSquare class="h-5 w-5" />
                            Recent Prayer Requests
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div v-for="prayer in recentPrayers" :key="prayer.id" class="flex items-start gap-3 p-3 border rounded-lg">
                                <MessageSquare class="h-4 w-4 mt-1 text-purple-600" />
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-sm">{{ prayer.title }}</h4>
                                    <p class="text-xs text-muted-foreground">
                                        by {{ prayer.is_anonymous ? 'Anonymous' : (prayer.user?.name || 'Unknown User') }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <Badge variant="outline" class="text-xs">{{ prayer.privacy }}</Badge>
                                        <span class="text-xs text-muted-foreground">{{ prayer.created_at }}</span>
                                    </div>
                                </div>
                            </div>
                            <div v-if="recentPrayers.length === 0" class="text-center py-8 text-muted-foreground">
                                <MessageSquare class="h-12 w-12 mx-auto mb-4 opacity-50" />
                                <p>No recent prayer requests</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Recent Activity -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <TrendingUp class="h-5 w-5" />
                        Recent Activity
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div v-for="activity in recentActivity" :key="activity.id" class="flex items-start gap-3 p-3 border rounded-lg">
                            <component :is="getActivityIcon(activity.type)" :class="['h-4 w-4 mt-1', getActivityColor(activity.type)]" />
                            <div class="flex-1 min-w-0">
                                <p class="text-sm">{{ activity.description }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs text-muted-foreground">by {{ activity.user }}</span>
                                    <span v-if="activity.group" class="text-xs text-muted-foreground">• {{ activity.group }}</span>
                                    <span class="text-xs text-muted-foreground">• {{ activity.timestamp }}</span>
                                </div>
                            </div>
                            <Button v-if="activity.action_url" variant="ghost" size="sm" as-child>
                                <Link :href="activity.action_url">
                                    <Eye class="h-3 w-3" />
                                </Link>
                            </Button>
                        </div>
                        <div v-if="recentActivity.length === 0" class="text-center py-8 text-muted-foreground">
                            <TrendingUp class="h-12 w-12 mx-auto mb-4 opacity-50" />
                            <p>No recent activity</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LeaderLayout>
</template>
