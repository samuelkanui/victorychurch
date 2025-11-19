<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import MemberLayout from '@/layouts/MemberLayout.vue'
import { type BreadcrumbItemType } from '@/types'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { 
    Calendar, 
    Eye, 
    Clock,
    MapPin,
    Video,
    Users,
    CheckCircle,
    AlertCircle,
    UserCheck
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
    rsvp_status: string | null
    attendance_status: string | null
    group: {
        id: number
        name: string
    }
    creator?: {
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
    stats: {
        upcoming_meetings: number
        meetings_attended: number
        meetings_rsvp_yes: number
        total_meetings: number
    }
    currentStatus: string
    filters: {
        type?: string
    }
}

defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
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

const getRsvpColor = (status: string) => {
    switch (status) {
        case 'yes': return 'bg-green-100 text-green-800'
        case 'no': return 'bg-red-100 text-red-800'
        case 'maybe': return 'bg-yellow-100 text-yellow-800'
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
    } catch {
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
    } catch {
        return false
    }
}

const truncateText = (text: string, maxLength: number = 150) => {
    if (!text) return ''
    return text.length > maxLength ? text.substring(0, maxLength) + '...' : text
}
</script>

<template>
    <Head title="Meetings - Member Dashboard" />
    
    <MemberLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-green-600 to-green-800 bg-clip-text text-transparent">
                        Meetings
                    </h1>
                    <p class="text-muted-foreground">
                        View and RSVP to group meetings
                    </p>
                </div>
            </div>

            <!-- Statistics -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Meetings</CardTitle>
                        <Calendar class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">{{ stats?.total_meetings || 0 }}</div>
                        <p class="text-xs text-muted-foreground">All meetings</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Upcoming</CardTitle>
                        <Clock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">{{ stats?.upcoming_meetings || 0 }}</div>
                        <p class="text-xs text-muted-foreground">This week</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">RSVP'd Yes</CardTitle>
                        <UserCheck class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-purple-600">{{ stats?.meetings_rsvp_yes || 0 }}</div>
                        <p class="text-xs text-muted-foreground">Confirmed</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Attended</CardTitle>
                        <CheckCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-orange-600">{{ stats?.meetings_attended || 0 }}</div>
                        <p class="text-xs text-muted-foreground">Past meetings</p>
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
                        View meeting details and manage your attendance
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="!meetings?.data || meetings.data.length === 0" class="text-center py-12">
                        <Calendar class="h-16 w-16 mx-auto mb-4 text-gray-400 opacity-50" />
                        <h3 class="text-lg font-semibold mb-2">No Meetings Scheduled</h3>
                        <p class="text-muted-foreground">
                            No meetings have been scheduled for your groups yet.
                        </p>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="meeting in meetings.data" :key="meeting.id" 
                             class="p-6 border rounded-lg hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-4">
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
                                        <Badge v-if="meeting.rsvp_status" :class="getRsvpColor(meeting.rsvp_status)">
                                            {{ meeting.rsvp_status.replace('_', ' ') }}
                                        </Badge>
                                        <Badge v-else-if="meeting.status === 'scheduled' && isUpcoming(meeting.scheduled_at)" class="bg-yellow-100 text-yellow-800">
                                            <AlertCircle class="h-3 w-3 mr-1" />
                                            RSVP Needed
                                        </Badge>
                                    </div>
                                    
                                    <div class="flex items-center gap-6 text-sm text-muted-foreground mb-3 flex-wrap">
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
                                    </div>
                                </div>
                                
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 ml-4">
                                    <!-- Join Meeting Button for Online Meetings -->
                                    <Button 
                                        v-if="meeting.meeting_type === 'online' && meeting.meeting_url && meeting.status === 'scheduled' && isUpcoming(meeting.scheduled_at)" 
                                        size="sm" 
                                        class="bg-purple-600 hover:bg-purple-700"
                                        as="a"
                                        :href="meeting.meeting_url"
                                        target="_blank"
                                    >
                                        <Video class="h-4 w-4 mr-1" />
                                        Join Meeting
                                    </Button>
                                    
                                    <Button variant="outline" size="sm" as-child>
                                        <Link :href="`/member/meetings/${meeting.id}`">
                                            <Eye class="h-4 w-4 mr-1" />
                                            View
                                        </Link>
                                    </Button>
                                    
                                    <Button v-if="meeting.status === 'scheduled' && isUpcoming(meeting.scheduled_at)" size="sm" as-child>
                                        <Link :href="`/member/meetings/${meeting.id}`">
                                            <UserCheck class="h-4 w-4 mr-1" />
                                            {{ meeting.rsvp_status ? 'Update RSVP' : 'RSVP' }}
                                        </Link>
                                    </Button>
                                </div>
                            </div>
                            
                            <div v-if="meeting.description" class="bg-gray-50 p-4 rounded-lg mb-4">
                                <p class="text-sm text-gray-700">{{ truncateText(meeting.description) }}</p>
                                <Link v-if="meeting.description.length > 150" 
                                      :href="`/member/meetings/${meeting.id}`" 
                                      class="text-blue-600 hover:underline text-sm mt-2 inline-block">
                                    Read more...
                                </Link>
                            </div>

                            <div v-if="meeting.rsvp_status" class="p-3 bg-blue-50 rounded-lg border-l-4 border-blue-400">
                                <p class="text-sm text-blue-800">
                                    <strong>RSVP Status:</strong> {{ meeting.rsvp_status.replace('_', ' ') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </MemberLayout>
</template>
