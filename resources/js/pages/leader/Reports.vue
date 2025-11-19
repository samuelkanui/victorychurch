<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import LeaderLayout from '@/layouts/LeaderLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { BarChart3, Users, MessageSquare, Calendar, TrendingUp, Activity, BookOpen } from 'lucide-vue-next'

interface Group {
    id: number
    name: string
    members_count: number
    approved_members_count: number
    members: Array<{
        id: number
        name: string
        pivot: {
            status: string
            joined_at: string
        }
    }>
}

interface GrowthData {
    group: Group
    growth: Array<{
        month: string
        count: number
    }>
}

interface PrayerAnalytics {
    month: string
    count: number
}

interface Props {
    groups: Group[]
    groupGrowth: GrowthData[]
    prayerAnalytics: PrayerAnalytics[]
}

const props = defineProps<Props>()

const breadcrumbs = [
    { title: 'Reports & Analytics' }
]

// Calculate total statistics
const totalMembers = props.groups.reduce((sum, group) => sum + group.members_count, 0)
const totalApprovedMembers = props.groups.reduce((sum, group) => sum + group.approved_members_count, 0)
const totalPrayerRequests = props.prayerAnalytics.reduce((sum, month) => sum + month.count, 0)

// Calculate engagement rate
const engagementRate = totalMembers > 0 ? Math.round((totalApprovedMembers / totalMembers) * 100) : 0

// Calculate growth rate (comparing last 2 months if available)
const calculateGrowthRate = () => {
    const allGrowthData = props.groupGrowth.flatMap(g => g.growth)
    if (allGrowthData.length < 2) return 0
    
    const sortedData = allGrowthData.sort((a, b) => a.month.localeCompare(b.month))
    const lastMonth = sortedData[sortedData.length - 1]
    const previousMonth = sortedData[sortedData.length - 2]
    
    if (!previousMonth || previousMonth.count === 0) return 0
    
    return Math.round(((lastMonth.count - previousMonth.count) / previousMonth.count) * 100)
}

const growthRate = calculateGrowthRate()

// Get recent months for prayer analytics
const recentPrayerRequests = props.prayerAnalytics.slice(-1)[0]?.count || 0

// Calculate average meeting attendance (placeholder - would need actual meeting data)
const averageAttendance = Math.round(engagementRate * 0.9) // Approximation based on engagement

const formatMonth = (monthString: string) => {
    if (!monthString) return 'Unknown'
    try {
        const [year, month] = monthString.split('-')
        const date = new Date(parseInt(year), parseInt(month) - 1)
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short' })
    } catch (error) {
        return monthString
    }
}
</script>

<template>
    <Head title="Reports & Analytics - Leader Dashboard" />
    
    <LeaderLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        Reports & Analytics
                    </h1>
                    <p class="text-muted-foreground">
                        View detailed analytics and reports for your groups and ministry activities
                    </p>
                </div>
            </div>

            <!-- Analytics Overview -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Group Growth</CardTitle>
                        <TrendingUp class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold" :class="growthRate >= 0 ? 'text-green-600' : 'text-red-600'">
                            {{ growthRate >= 0 ? '+' : '' }}{{ growthRate }}%
                        </div>
                        <p class="text-xs text-muted-foreground">vs last month</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Active Members</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">{{ engagementRate }}%</div>
                        <p class="text-xs text-muted-foreground">{{ totalApprovedMembers }} of {{ totalMembers }} members</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Prayer Requests</CardTitle>
                        <MessageSquare class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-purple-600">{{ totalPrayerRequests }}</div>
                        <p class="text-xs text-muted-foreground">{{ recentPrayerRequests }} this month</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Engagement Rate</CardTitle>
                        <Activity class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-orange-600">{{ averageAttendance }}%</div>
                        <p class="text-xs text-muted-foreground">estimated activity</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Group Performance -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Users class="h-5 w-5" />
                        Group Performance Overview
                    </CardTitle>
                    <CardDescription>
                        Detailed breakdown of your groups and their growth
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="groups.length === 0" class="text-center py-8">
                        <Users class="h-16 w-16 mx-auto mb-4 text-gray-400 opacity-50" />
                        <h3 class="text-lg font-semibold mb-2">No Groups Assigned</h3>
                        <p class="text-muted-foreground">
                            You don't have any groups assigned yet.
                        </p>
                    </div>
                    
                    <div v-else class="space-y-4">
                        <div v-for="group in groups" :key="group.id" 
                             class="flex items-center justify-between p-4 border rounded-lg">
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg">{{ group.name }}</h3>
                                <div class="flex items-center gap-4 mt-2">
                                    <div class="flex items-center gap-2">
                                        <Users class="h-4 w-4 text-muted-foreground" />
                                        <span class="text-sm">{{ group.members_count }} total members</span>
                                    </div>
                                    <Badge class="bg-green-100 text-green-800">
                                        {{ group.approved_members_count }} active
                                    </Badge>
                                    <div class="text-sm text-muted-foreground">
                                        {{ group.members_count > 0 ? Math.round((group.approved_members_count / group.members_count) * 100) : 0 }}% engagement
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Growth Analytics -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Monthly Growth Trends -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <TrendingUp class="h-5 w-5" />
                            Monthly Growth Trends
                        </CardTitle>
                        <CardDescription>
                            Member growth patterns across your groups
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="groupGrowth.length === 0 || groupGrowth.every(g => g.growth.length === 0)" 
                             class="text-center py-8">
                            <BarChart3 class="h-16 w-16 mx-auto mb-4 text-blue-600 opacity-50" />
                            <h3 class="text-lg font-semibold mb-2">No Growth Data Yet</h3>
                            <p class="text-muted-foreground">
                                Growth trends will appear as members join your groups over time.
                            </p>
                        </div>
                        
                        <div v-else class="space-y-4">
                            <div v-for="groupData in groupGrowth.filter(g => g.growth.length > 0)" :key="groupData.group.id">
                                <h4 class="font-medium mb-2">{{ groupData.group.name }}</h4>
                                <div class="space-y-2">
                                    <div v-for="monthData in groupData.growth" :key="monthData.month" 
                                         class="flex items-center justify-between text-sm">
                                        <span>{{ formatMonth(monthData.month) }}</span>
                                        <Badge class="bg-blue-100 text-blue-800">
                                            +{{ monthData.count }} members
                                        </Badge>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Prayer Request Analytics -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <MessageSquare class="h-5 w-5" />
                            Prayer Request Trends
                        </CardTitle>
                        <CardDescription>
                            Monthly prayer request activity from your groups
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="prayerAnalytics.length === 0" class="text-center py-8">
                            <MessageSquare class="h-16 w-16 mx-auto mb-4 text-purple-600 opacity-50" />
                            <h3 class="text-lg font-semibold mb-2">No Prayer Data Yet</h3>
                            <p class="text-muted-foreground">
                                Prayer request analytics will appear as members submit prayer requests.
                            </p>
                        </div>
                        
                        <div v-else class="space-y-2">
                            <div v-for="monthData in prayerAnalytics" :key="monthData.month" 
                                 class="flex items-center justify-between text-sm p-2 border rounded">
                                <span>{{ formatMonth(monthData.month) }}</span>
                                <Badge class="bg-purple-100 text-purple-800">
                                    {{ monthData.count }} requests
                                </Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </LeaderLayout>
</template>
