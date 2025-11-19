<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { BarChart3, Users, BookOpen, MessageSquare, Calendar, FileText, TrendingUp, Activity, Clock } from 'lucide-vue-next'

interface Props {
    stats: {
        total_users: number
        total_groups: number
        total_prayers: number
        total_meetings: number
        total_resources: number
        active_groups: number
        recent_signups: number
        system_health: string
    }
    userGrowth: Array<{
        month: string
        count: number
    }>
    groupActivity: Array<{
        name: string
        members: number
        is_active: boolean
    }>
    recentPrayers: Array<{
        title: string
        user: string
        created_at: string
    }>
    upcomingMeetings: Array<{
        title: string
        group: string
        scheduled_at: string
    }>
}

const props = defineProps<Props>()

const breadcrumbs = [
    { title: 'Reports & Analytics' }
]
</script>

<template>
    <Head title="Reports & Analytics - Admin Dashboard" />
    
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent">
                        Reports & Analytics
                    </h1>
                    <p class="text-sm sm:text-base text-muted-foreground">
                        Comprehensive insights into church management system usage
                    </p>
                </div>
            </div>

            <!-- Overview Stats -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Users</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-purple-600">{{ stats.total_users }}</div>
                        <p class="text-xs text-muted-foreground">Registered members</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Active Groups</CardTitle>
                        <BookOpen class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">{{ stats.active_groups }}</div>
                        <p class="text-xs text-muted-foreground">Out of {{ stats.total_groups }} total</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Prayer Requests</CardTitle>
                        <MessageSquare class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">{{ stats.total_prayers }}</div>
                        <p class="text-xs text-muted-foreground">Community prayers</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Recent Signups</CardTitle>
                        <TrendingUp class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-orange-600">{{ stats.recent_signups }}</div>
                        <p class="text-xs text-muted-foreground">Last 30 days</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Additional Stats -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Meetings</CardTitle>
                        <Calendar class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-indigo-600">{{ stats.total_meetings }}</div>
                        <p class="text-xs text-muted-foreground">Scheduled events</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Resources Shared</CardTitle>
                        <FileText class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-teal-600">{{ stats.total_resources }}</div>
                        <p class="text-xs text-muted-foreground">Files and links</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">System Health</CardTitle>
                        <BarChart3 class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold" :class="{
                            'text-green-600': stats.system_health === 'Excellent',
                            'text-blue-600': stats.system_health === 'Good',
                            'text-yellow-600': stats.system_health === 'Fair',
                            'text-red-600': stats.system_health === 'Needs Attention'
                        }">{{ stats.system_health }}</div>
                        <p class="text-xs text-muted-foreground">Based on system activity</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Advanced Analytics -->
            <div class="grid gap-4 md:grid-cols-2">
                <!-- User Growth Trend -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <TrendingUp class="h-5 w-5 text-purple-600" />
                            User Growth (Last 6 Months)
                        </CardTitle>
                        <CardDescription>Monthly user registration trends</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div v-for="data in userGrowth" :key="data.month" class="flex items-center justify-between">
                                <span class="text-sm font-medium">{{ data.month }}</span>
                                <div class="flex items-center gap-2">
                                    <div class="h-2 bg-purple-600 rounded-full" :style="{ width: `${Math.max(data.count * 10, 20)}px` }"></div>
                                    <span class="text-sm text-muted-foreground w-8 text-right">{{ data.count }}</span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Top Groups by Members -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Activity class="h-5 w-5 text-blue-600" />
                            Top Groups by Members
                        </CardTitle>
                        <CardDescription>Most active groups in the community</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div v-for="group in groupActivity" :key="group.name" class="flex items-center justify-between">
                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                    <span class="text-sm font-medium truncate">{{ group.name }}</span>
                                    <Badge v-if="group.is_active" variant="outline" class="text-xs">Active</Badge>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Users class="h-4 w-4 text-muted-foreground" />
                                    <span class="text-sm font-semibold">{{ group.members }}</span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent Prayer Requests -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <MessageSquare class="h-5 w-5 text-green-600" />
                            Recent Prayer Requests
                        </CardTitle>
                        <CardDescription>Latest community prayer needs</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div v-for="prayer in recentPrayers" :key="prayer.title" class="border-l-2 border-green-600 pl-3">
                                <p class="text-sm font-medium">{{ prayer.title }}</p>
                                <p class="text-xs text-muted-foreground">by {{ prayer.user }} • {{ prayer.created_at }}</p>
                            </div>
                            <p v-if="recentPrayers.length === 0" class="text-sm text-muted-foreground text-center py-4">
                                No recent prayer requests
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Upcoming Meetings -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Clock class="h-5 w-5 text-orange-600" />
                            Upcoming Meetings
                        </CardTitle>
                        <CardDescription>Scheduled events and gatherings</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div v-for="meeting in upcomingMeetings" :key="meeting.title" class="border-l-2 border-orange-600 pl-3">
                                <p class="text-sm font-medium">{{ meeting.title }}</p>
                                <p class="text-xs text-muted-foreground">{{ meeting.group }} • {{ meeting.scheduled_at }}</p>
                            </div>
                            <p v-if="upcomingMeetings.length === 0" class="text-sm text-muted-foreground text-center py-4">
                                No upcoming meetings scheduled
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AdminLayout>
</template>
