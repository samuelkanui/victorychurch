<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { 
    Users, 
    MessageSquare, 
    BookOpen,
    Calendar,
    Clock,
    AlertCircle,
    Activity,
    TrendingUp,
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

const props = defineProps<Props>()

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
    })
}
</script>

<template>
    <Head title="Member Dashboard" />
    
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-green-100">
        <!-- Simple Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div>
                        <h1 class="text-2xl font-bold text-green-800">Member Dashboard</h1>
                        <p class="text-green-600">Welcome to your church community portal</p>
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
            </div>
        </header>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-5 mb-8">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">My Groups</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">{{ stats.my_groups }}</div>
                        <p class="text-xs text-muted-foreground">Active memberships</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Pending Applications</CardTitle>
                        <Clock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-yellow-600">{{ stats.pending_applications }}</div>
                        <p class="text-xs text-muted-foreground">Awaiting approval</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Prayer Requests</CardTitle>
                        <MessageSquare class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-purple-600">{{ stats.my_prayer_requests }}</div>
                        <p class="text-xs text-muted-foreground">My requests</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Upcoming Meetings</CardTitle>
                        <Calendar class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-orange-600">{{ stats.upcoming_meetings }}</div>
                        <p class="text-xs text-muted-foreground">This week</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Pending Assignments</CardTitle>
                        <BookOpen class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">{{ stats.pending_assignments }}</div>
                        <p class="text-xs text-muted-foreground">Need submission</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Quick Actions -->
            <Card class="mb-8">
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
                        <Button variant="outline" class="h-20 flex-col gap-2" as-child>
                            <Link href="/member/groups/available">
                                <div class="p-2 rounded-md text-white bg-green-500">
                                    <Users class="h-5 w-5" />
                                </div>
                                <span class="text-sm font-medium">Join Groups</span>
                            </Link>
                        </Button>
                        
                        <Button variant="outline" class="h-20 flex-col gap-2" as-child>
                            <Link href="/member/prayers/create">
                                <div class="p-2 rounded-md text-white bg-purple-500">
                                    <MessageSquare class="h-5 w-5" />
                                </div>
                                <span class="text-sm font-medium">Create Prayer</span>
                            </Link>
                        </Button>
                        
                        <Button variant="outline" class="h-20 flex-col gap-2" as-child>
                            <Link href="/member/assignments">
                                <div class="p-2 rounded-md text-white bg-blue-500">
                                    <BookOpen class="h-5 w-5" />
                                </div>
                                <span class="text-sm font-medium">View Assignments</span>
                            </Link>
                        </Button>
                        
                        <Button variant="outline" class="h-20 flex-col gap-2" as-child>
                            <Link href="/member/meetings">
                                <div class="p-2 rounded-md text-white bg-orange-500">
                                    <Calendar class="h-5 w-5" />
                                </div>
                                <span class="text-sm font-medium">My Meetings</span>
                            </Link>
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Content Grid -->
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
                                    <p class="text-sm text-muted-foreground">Led by {{ group.leader.name }}</p>
                                    <p class="text-xs text-muted-foreground">
                                        Joined {{ formatDate(group.pivot.joined_at) }}
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
                                        by {{ prayer.is_anonymous ? 'Anonymous' : prayer.user.name }}
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
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
