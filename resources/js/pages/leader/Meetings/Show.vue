<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import LeaderLayout from '@/layouts/LeaderLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { 
    Calendar, 
    Edit, 
    Users, 
    Clock,
    MapPin,
    Video,
    CheckCircle,
    Play
} from 'lucide-vue-next'

interface Meeting {
    id: number
    title: string
    description: string | null
    type: string
    scheduled_at: string
    duration_minutes: number
    location: string | null
    meeting_url: string | null
    status: string
    max_attendees: number | null
    group: {
        id: number
        name: string
    }
    attendees: Array<{
        id: number
        status: string
        notes: string | null
        user: {
            id: number
            name: string
        }
    }>
}

interface Props {
    meeting: Meeting
}

const props = defineProps<Props>()

const breadcrumbs = [
    { title: 'Meetings', href: '/leader/meetings' },
    { title: props.meeting.title }
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
            month: 'long',
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
        return `${hours} hour${hours > 1 ? 's' : ''} ${mins} minute${mins !== 1 ? 's' : ''}`
    }
    return `${mins} minute${mins !== 1 ? 's' : ''}`
}
</script>

<template>
    <Head :title="`${meeting.title} - Meetings`" />
    
    <LeaderLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        {{ meeting.title }}
                    </h1>
                    <p class="text-muted-foreground">
                        Meeting details and attendance tracking
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button v-if="meeting.status === 'scheduled'" variant="outline" as-child>
                        <Link :href="`/leader/meetings/${meeting.id}/edit`">
                            <Edit class="h-4 w-4 mr-2" />
                            Edit Meeting
                        </Link>
                    </Button>
                    <Button v-if="meeting.status === 'scheduled'" class="bg-green-600 hover:bg-green-700">
                        <Play class="h-4 w-4 mr-2" />
                        Start Meeting
                    </Button>
                </div>
            </div>

            <!-- Meeting Details -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Main Details -->
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Calendar class="h-5 w-5" />
                            Meeting Details
                        </CardTitle>
                        <div class="flex items-center gap-2">
                            <Badge :class="getTypeColor(meeting.type)">
                                {{ getTypeLabel(meeting.type) }}
                            </Badge>
                            <Badge :class="getStatusColor(meeting.status)">
                                {{ meeting.status.replace('_', ' ') }}
                            </Badge>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div v-if="meeting.description">
                                <h3 class="font-medium mb-2">Description</h3>
                                <p class="text-muted-foreground">{{ meeting.description }}</p>
                            </div>
                            
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <h3 class="font-medium mb-1">Location</h3>
                                    <div class="flex items-center gap-2">
                                        <MapPin class="h-4 w-4 text-muted-foreground" />
                                        <span>{{ meeting.location || 'No location specified' }}</span>
                                    </div>
                                </div>
                                
                                <div v-if="meeting.meeting_url">
                                    <h3 class="font-medium mb-1">Virtual Meeting</h3>
                                    <div class="flex items-center gap-2">
                                        <Video class="h-4 w-4 text-muted-foreground" />
                                        <a :href="meeting.meeting_url" target="_blank" class="text-blue-600 hover:underline">
                                            Join Virtual Meeting
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Meeting Info -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Clock class="h-5 w-5" />
                            Meeting Info
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div>
                                <h3 class="font-medium mb-1">Group</h3>
                                <p class="text-muted-foreground">{{ meeting.group?.name || 'Unknown Group' }}</p>
                            </div>
                            
                            <div>
                                <h3 class="font-medium mb-1">Scheduled Time</h3>
                                <div class="flex items-center gap-2">
                                    <Calendar class="h-4 w-4 text-muted-foreground" />
                                    <span class="text-sm">{{ formatDateTime(meeting.scheduled_at) }}</span>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="font-medium mb-1">Duration</h3>
                                <div class="flex items-center gap-2">
                                    <Clock class="h-4 w-4 text-muted-foreground" />
                                    <span class="text-sm">{{ formatDuration(meeting.duration_minutes) }}</span>
                                </div>
                            </div>
                            
                            <div v-if="meeting.max_attendees">
                                <h3 class="font-medium mb-1">Max Attendees</h3>
                                <div class="flex items-center gap-2">
                                    <Users class="h-4 w-4 text-muted-foreground" />
                                    <span class="text-sm">{{ meeting.max_attendees }} people</span>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="font-medium mb-1">Status</h3>
                                <Badge :class="getStatusColor(meeting.status)">
                                    {{ meeting.status.replace('_', ' ') }}
                                </Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Attendance Overview -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Users class="h-5 w-5" />
                        Attendance Overview
                    </CardTitle>
                    <CardDescription>
                        Track meeting attendance and participation
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="!meeting.attendees || meeting.attendees.length === 0" class="text-center py-8">
                        <Users class="h-16 w-16 mx-auto mb-4 text-gray-400 opacity-50" />
                        <h3 class="text-lg font-semibold mb-2">No Attendance Records</h3>
                        <p class="text-muted-foreground">
                            Attendance tracking will be available once the meeting starts.
                        </p>
                    </div>
                    
                    <div v-else class="text-center py-8">
                        <CheckCircle class="h-16 w-16 mx-auto mb-4 text-green-600 opacity-50" />
                        <h3 class="text-lg font-semibold mb-2">Attendance Tracking Coming Soon</h3>
                        <p class="text-muted-foreground">
                            Full attendance management with present/absent tracking and notes.
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LeaderLayout>
</template>
