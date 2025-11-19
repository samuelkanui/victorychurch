<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import LeaderLayout from '@/layouts/LeaderLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { 
    Calendar, 
    Plus, 
    Eye, 
    Edit, 
    Users, 
    Clock,
    MapPin,
    Video,
    CheckCircle,
    AlertCircle,
    Play
} from 'lucide-vue-next'

interface Meeting {
    id: number
    title: string
    description: string | null
    type: string
    meeting_type: string
    scheduled_at: string
    duration_minutes: number
    location: string | null
    meeting_url: string | null
    status: string
    max_attendees: number | null
    attendees_count: number
    group: {
        id: number
        name: string
    }
}

interface Props {
    meetings: {
        data: Meeting[]
        links: any[]
        meta: any
    }
}

const props = defineProps<Props>()

const breadcrumbs = [
    { title: 'Meetings' }
]

const getTypeColor = (type: string) => {
    switch (type) {
        case 'bible_study': return 'bg-blue-100 text-blue-800'
        case 'prayer': return 'bg-purple-100 text-purple-800'
        case 'fellowship': return 'bg-green-100 text-green-800'
        case 'service': return 'bg-orange-100 text-orange-800'
        case 'other': return 'bg-gray-100 text-gray-800'
        default: return 'bg-gray-100 text-gray-800'
    }
}

const getTypeLabel = (type: string) => {
    switch (type) {
        case 'bible_study': return 'Bible Study'
        case 'prayer': return 'Prayer Meeting'
        case 'fellowship': return 'Fellowship'
        case 'service': return 'Service Planning'
        case 'other': return 'Other'
        default: return type
    }
}

const getStatusColor = (status: string) => {
    switch (status) {
        case 'scheduled': return 'bg-blue-100 text-blue-800'
        case 'in_progress': return 'bg-green-100 text-green-800'
        case 'completed': return 'bg-gray-100 text-gray-800'
        case 'cancelled': return 'bg-red-100 text-red-800'
        default: return 'bg-gray-100 text-gray-800'
    }
}

const formatDateTime = (dateString: string) => {
    if (!dateString) return 'No date'
    try {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit'
        })
    } catch (error) {
        return 'Invalid date'
    }
}

const formatDuration = (minutes: number) => {
    const hours = Math.floor(minutes / 60)
    const mins = minutes % 60
    if (hours > 0) {
        return `${hours}h ${mins}m`
    }
    return `${mins}m`
}

const isUpcoming = (scheduledAt: string) => {
    if (!scheduledAt) return false
    try {
        return new Date(scheduledAt) > new Date()
    } catch (error) {
        return false
    }
}
</script>

<template>
    <Head title="Meetings - Leader Dashboard" />
    
    <LeaderLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        Meetings
                    </h1>
                    <p class="text-muted-foreground">
                        Schedule and manage meetings for your groups
                    </p>
                </div>
                <Button class="bg-blue-600 hover:bg-blue-700" as-child>
                    <Link href="/leader/meetings/create">
                        <Plus class="h-4 w-4 mr-2" />
                        Schedule Meeting
                    </Link>
                </Button>
            </div>

            <!-- Statistics -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Meetings</CardTitle>
                        <Calendar class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">{{ meetings?.data?.length || 0 }}</div>
                        <p class="text-xs text-muted-foreground">All meetings</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Upcoming</CardTitle>
                        <Clock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">
                            {{ meetings?.data?.filter(m => m.status === 'scheduled' && isUpcoming(m.scheduled_at)).length || 0 }}
                        </div>
                        <p class="text-xs text-muted-foreground">Scheduled ahead</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Completed</CardTitle>
                        <CheckCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-purple-600">
                            {{ meetings?.data?.filter(m => m.status === 'completed').length || 0 }}
                        </div>
                        <p class="text-xs text-muted-foreground">Finished meetings</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Attendees</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-orange-600">
                            {{ meetings?.data?.reduce((sum, m) => sum + (m.attendees_count || 0), 0) || 0 }}
                        </div>
                        <p class="text-xs text-muted-foreground">Total attendance</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Meetings List -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Calendar class="h-5 w-5" />
                        All Meetings
                    </CardTitle>
                    <CardDescription>
                        Manage your scheduled meetings and track attendance
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="!meetings?.data || meetings.data.length === 0" class="text-center py-12">
                        <Calendar class="h-16 w-16 mx-auto mb-4 text-gray-400 opacity-50" />
                        <h3 class="text-lg font-semibold mb-2">No Meetings Scheduled</h3>
                        <p class="text-muted-foreground mb-4">
                            Schedule your first meeting to start organizing group gatherings and Bible studies.
                        </p>
                        <Button as-child>
                            <Link href="/leader/meetings/create">
                                <Plus class="h-4 w-4 mr-2" />
                                Schedule First Meeting
                            </Link>
                        </Button>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="meeting in meetings.data" :key="meeting.id" 
                             class="flex items-center justify-between p-4 border rounded-lg hover:shadow-md transition-shadow">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2 flex-wrap">
                                    <h3 class="font-semibold text-lg">{{ meeting.title }}</h3>
                                    <Badge :class="getTypeColor(meeting.type)">
                                        {{ getTypeLabel(meeting.type) }}
                                    </Badge>
                                    <Badge :class="meeting.meeting_type === 'online' ? 'bg-purple-100 text-purple-800' : 'bg-teal-100 text-teal-800'">
                                        <Video v-if="meeting.meeting_type === 'online'" class="h-3 w-3 mr-1" />
                                        <MapPin v-else class="h-3 w-3 mr-1" />
                                        {{ meeting.meeting_type === 'online' ? 'Online' : 'Physical' }}
                                    </Badge>
                                    <Badge :class="getStatusColor(meeting.status)">
                                        {{ meeting.status.replace('_', ' ') }}
                                    </Badge>
                                    <Badge v-if="meeting.status === 'scheduled' && !isUpcoming(meeting.scheduled_at)" 
                                           class="bg-red-100 text-red-800">
                                        Overdue
                                    </Badge>
                                </div>
                                
                                <p v-if="meeting.description" class="text-muted-foreground mb-3 line-clamp-2">
                                    {{ meeting.description }}
                                </p>
                                
                                <div class="flex items-center gap-6 text-sm text-muted-foreground flex-wrap">
                                    <div class="flex items-center gap-1">
                                        <Users class="h-4 w-4" />
                                        <span>{{ meeting.group?.name || 'Unknown Group' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <Calendar class="h-4 w-4" />
                                        <span>{{ formatDateTime(meeting.scheduled_at) }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <Clock class="h-4 w-4" />
                                        <span>{{ formatDuration(meeting.duration_minutes) }}</span>
                                    </div>
                                    <div v-if="meeting.meeting_type === 'physical' && meeting.location" class="flex items-center gap-1">
                                        <MapPin class="h-4 w-4" />
                                        <span>{{ meeting.location }}</span>
                                    </div>
                                    <div v-if="meeting.meeting_type === 'online' && meeting.meeting_url" class="flex items-center gap-1">
                                        <a :href="meeting.meeting_url" target="_blank" 
                                           class="flex items-center gap-1 text-blue-600 hover:text-blue-800 hover:underline">
                                            <Video class="h-4 w-4" />
                                            <span>Join Online Meeting</span>
                                        </a>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <Users class="h-4 w-4" />
                                        <span>{{ meeting.attendees_count || 0 }} attendees</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 ml-4">
                                <!-- Start Meeting Button (Green) -->
                                <Button 
                                    v-if="meeting.status === 'scheduled' && isUpcoming(meeting.scheduled_at)" 
                                    size="sm" 
                                    class="bg-green-600 hover:bg-green-700"
                                >
                                    <Play class="h-4 w-4 mr-1" />
                                    Start Meeting
                                </Button>
                                
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="`/leader/meetings/${meeting.id}`">
                                        <Eye class="h-4 w-4 mr-1" />
                                        View
                                    </Link>
                                </Button>
                                <Button v-if="meeting.status === 'scheduled'" variant="outline" size="sm" as-child>
                                    <Link :href="`/leader/meetings/${meeting.id}/edit`">
                                        <Edit class="h-4 w-4 mr-1" />
                                        Edit
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LeaderLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
