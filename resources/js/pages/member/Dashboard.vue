<script setup lang="ts">
import MemberLayout from '@/layouts/MemberLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItemType } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { 
    Users, 
    MessageSquare, 
    BookOpen,
    Calendar,
    Clock,
    CheckCircle,
    AlertCircle,
    Activity,
    TrendingUp,
    User,
    Eye,
    Plus
} from 'lucide-vue-next'

interface Props {
    stats: {
        my_groups: number
        pending_applications: number
        my_prayer_requests: number
        upcoming_meetings: number
        pending_assignments: number
    }
    memberGroups: Array<{
        id: number
        name: string
        description: string
        leader: {
            id: number
            name: string
        }
        pivot: {
            status: string
            joined_at: string
        }
    }>
    recentPrayers: Array<{
        id: number
        title: string
        user: {
            id: number
            name: string
        }
        is_anonymous: boolean
        created_at: string
    }>
    upcomingMeetings: Array<{
        id: number
        title: string
        scheduled_at: string
        group: {
            id: number
            name: string
        }
    }>
    pendingAssignments: Array<{
        id: number
        title: string
        due_date: string
        group: {
            id: number
            name: string
        }
    }>
    recentActivity: Array<{
        id: string
        type: string
        description: string
        timestamp: string
        action_url?: string
    }>
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Member Dashboard',
        href: '/member/dashboard',
    },
];

const quickActions = [
    { title: 'Join Groups', icon: Users, href: '/member/groups/available', color: 'bg-green-500' },
    { title: 'Create Prayer', icon: MessageSquare, href: '/member/prayers/create', color: 'bg-purple-500' },
    { title: 'View Assignments', icon: BookOpen, href: '/member/assignments', color: 'bg-blue-500' },
    { title: 'My Meetings', icon: Calendar, href: '/member/meetings', color: 'bg-orange-500' },
];

const getActivityIcon = (type: string) => {
    switch (type) {
        case 'group_join': return Users;
        case 'prayer_request': return MessageSquare;
        case 'assignment_submission': return BookOpen;
        case 'meeting_rsvp': return Calendar;
        default: return Activity;
    }
};

const getActivityColor = (type: string) => {
    switch (type) {
        case 'group_join': return 'text-green-600';
        case 'prayer_request': return 'text-purple-600';
        case 'assignment_submission': return 'text-blue-600';
        case 'meeting_rsvp': return 'text-orange-600';
        default: return 'text-gray-600';
    }
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Member Dashboard" />

    <MemberLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-green-600 to-green-800 bg-clip-text text-transparent">
                        Member Dashboard
                    </h1>
                    <p class="text-muted-foreground">
                        Welcome to your church community portal
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <Badge v-if="props.stats.pending_assignments > 0" variant="destructive" class="flex items-center gap-1">
                        <AlertCircle class="h-3 w-3" />
                        {{ props.stats.pending_assignments }} Due
                    </Badge>
                    <Badge variant="outline" class="flex items-center gap-1">
                        <Activity class="h-3 w-3" />
                        Online
                    </Badge>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-5">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">My Groups</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">{{ stats.my_groups }}</div>
                        <p class="text-xs text-muted-foreground">
                            Active memberships
                        </p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Pending Applications</CardTitle>
                        <Clock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-yellow-600">{{ stats.pending_applications }}</div>
                        <p class="text-xs text-muted-foreground">
                            Awaiting approval
                        </p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Prayer Requests</CardTitle>
                        <MessageSquare class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-purple-600">{{ stats.my_prayer_requests }}</div>
                        <p class="text-xs text-muted-foreground">
                            My requests
                        </p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Upcoming Meetings</CardTitle>
                        <Calendar class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-orange-600">{{ stats.upcoming_meetings }}</div>
                        <p class="text-xs text-muted-foreground">
                            This week
                        </p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Pending Assignments</CardTitle>
                        <BookOpen class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">{{ stats.pending_assignments }}</div>
                        <p class="text-xs text-muted-foreground">
                            Need submission
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
                        Common tasks and activities for members
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
                            <div v-for="group in memberGroups.slice(0, 3)" :key="group.id" 
                                 class="flex items-center justify-between p-3 border rounded-lg">
                                <div>
                                    <h3 class="font-medium">{{ group.name }}</h3>
                                    <p class="text-sm text-muted-foreground">Led by {{ group.leader?.name || 'Unknown Leader' }}</p>
                                    <p class="text-xs text-muted-foreground">
                                        Joined {{ group.pivot?.joined_at ? formatDate(group.pivot.joined_at) : 'Unknown date' }}
                                    </p>
                                </div>
                                <Button size="sm" variant="outline" as-child>
                                    <Link :href="`/member/groups/${group.id}`">
                                        <Eye class="h-4 w-4 mr-1" />
                                        View
                                    </Link>
                                </Button>
                            </div>
                            <div v-if="memberGroups.length === 0" class="text-center py-8 text-muted-foreground">
                                <Users class="h-12 w-12 mx-auto mb-4 opacity-50" />
                                <p>You haven't joined any groups yet</p>
                                <Button class="mt-2" as-child>
                                    <Link href="/member/groups/available">
                                        <Plus class="h-4 w-4 mr-1" />
                                        Browse Groups
                                    </Link>
                                </Button>
                            </div>
                            <div v-else-if="memberGroups.length > 3" class="text-center">
                                <Button variant="outline" as-child>
                                    <Link href="/member/groups">View All Groups</Link>
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent Community Prayers -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <MessageSquare class="h-5 w-5" />
                            Recent Community Prayers
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div v-for="prayer in recentPrayers.slice(0, 3)" :key="prayer.id" 
                                 class="flex items-start justify-between p-3 border rounded-lg">
                                <div class="flex-1">
                                    <h3 class="font-medium">{{ prayer.title }}</h3>
                                    <p class="text-sm text-muted-foreground">
                                        by {{ prayer.is_anonymous ? 'Anonymous' : (prayer.user?.name || 'Unknown User') }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ formatDate(prayer.created_at) }}
                                    </p>
                                </div>
                                <Button size="sm" variant="outline" as-child>
                                    <Link :href="`/member/prayers/${prayer.id}`">
                                        <Eye class="h-4 w-4" />
                                    </Link>
                                </Button>
                            </div>
                            <div v-if="recentPrayers.length === 0" class="text-center py-8 text-muted-foreground">
                                <MessageSquare class="h-12 w-12 mx-auto mb-4 opacity-50" />
                                <p>No recent prayer requests</p>
                            </div>
                            <div v-else class="text-center">
                                <Button variant="outline" as-child>
                                    <Link href="/member/prayers">View All Prayers</Link>
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Upcoming Meetings -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Calendar class="h-5 w-5" />
                            Upcoming Meetings
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div v-for="meeting in upcomingMeetings" :key="meeting.id" 
                                 class="flex items-center justify-between p-3 border rounded-lg">
                                <div>
                                    <h3 class="font-medium">{{ meeting.title }}</h3>
                                    <p class="text-sm text-muted-foreground">{{ meeting.group?.name || 'Unknown Group' }}</p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ formatDate(meeting.scheduled_at) }}
                                    </p>
                                </div>
                                <Button size="sm" variant="outline" as-child>
                                    <Link :href="`/member/meetings/${meeting.id}`">
                                        <Eye class="h-4 w-4 mr-1" />
                                        RSVP
                                    </Link>
                                </Button>
                            </div>
                            <div v-if="upcomingMeetings.length === 0" class="text-center py-8 text-muted-foreground">
                                <Calendar class="h-12 w-12 mx-auto mb-4 opacity-50" />
                                <p>No upcoming meetings</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Pending Assignments -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <BookOpen class="h-5 w-5" />
                            Pending Assignments
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div v-for="assignment in pendingAssignments" :key="assignment.id" 
                                 class="flex items-center justify-between p-3 border rounded-lg">
                                <div>
                                    <h3 class="font-medium">{{ assignment.title }}</h3>
                                    <p class="text-sm text-muted-foreground">{{ assignment.group?.name || 'Unknown Group' }}</p>
                                    <p class="text-xs text-red-600">
                                        Due {{ formatDate(assignment.due_date) }}
                                    </p>
                                </div>
                                <Button size="sm" as-child>
                                    <Link :href="`/member/assignments/${assignment.id}`">
                                        Submit
                                    </Link>
                                </Button>
                            </div>
                            <div v-if="pendingAssignments.length === 0" class="text-center py-8 text-muted-foreground">
                                <CheckCircle class="h-12 w-12 mx-auto mb-4 opacity-50 text-green-500" />
                                <p>All assignments completed!</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Recent Activity -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Activity class="h-5 w-5" />
                        Recent Activity
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div v-for="activity in recentActivity.slice(0, 5)" :key="activity.id" 
                             class="flex items-center gap-4 p-3 border rounded-lg">
                            <div :class="['p-2 rounded-full', getActivityColor(activity.type)]">
                                <component :is="getActivityIcon(activity.type)" class="h-4 w-4" />
                            </div>
                            <div class="flex-1">
                                <p class="text-sm">{{ activity.description }}</p>
                                <p class="text-xs text-muted-foreground">{{ activity.timestamp }}</p>
                            </div>
                            <Button v-if="activity.action_url" size="sm" variant="outline" as-child>
                                <Link :href="activity.action_url">
                                    <Eye class="h-4 w-4" />
                                </Link>
                            </Button>
                        </div>
                        <div v-if="recentActivity.length === 0" class="text-center py-8 text-muted-foreground">
                            <Activity class="h-12 w-12 mx-auto mb-4 opacity-50" />
                            <p>No recent activity</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </MemberLayout>
</template>
