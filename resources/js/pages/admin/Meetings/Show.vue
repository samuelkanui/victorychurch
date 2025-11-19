<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Calendar, User, Clock, MapPin, Video, Users, ArrowLeft, CheckCircle, XCircle, HelpCircle } from 'lucide-vue-next'

interface Attendance {
    id: number
    rsvp_status: string
    user: {
        id: number
        name: string
    }
}

interface Props {
    meeting: {
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
        group: {
            id: number
            name: string
        }
        creator: {
            id: number
            name: string
        }
        attendances: Attendance[]
    }
    attendanceStats: {
        total: number
        confirmed: number
        declined: number
        maybe: number
    }
}

const props = defineProps<Props>()

const breadcrumbs = [
    { title: 'Meetings', href: '/admin/meetings' },
    { title: props.meeting.title }
]

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
    })
}

const getTypeLabel = (type: string) => {
    const labels: Record<string, string> = {
        'bible_study': 'Bible Study',
        'prayer': 'Prayer Meeting',
        'fellowship': 'Fellowship',
        'service': 'Service Planning',
        'other': 'Other'
    }
    return labels[type] || type
}

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        'scheduled': 'bg-blue-100 text-blue-800',
        'in_progress': 'bg-green-100 text-green-800',
        'completed': 'bg-gray-100 text-gray-800',
        'cancelled': 'bg-red-100 text-red-800'
    }
    return colors[status] || 'bg-gray-100 text-gray-800'
}

const getRsvpIcon = (status: string) => {
    const icons: Record<string, any> = {
        'yes': CheckCircle,
        'no': XCircle,
        'maybe': HelpCircle
    }
    return icons[status] || HelpCircle
}

const getRsvpColor = (status: string) => {
    const colors: Record<string, string> = {
        'yes': 'bg-green-100 text-green-800',
        'no': 'bg-red-100 text-red-800',
        'maybe': 'bg-yellow-100 text-yellow-800'
    }
    return colors[status] || 'bg-gray-100 text-gray-800'
}
</script>

<template>
    <Head :title="`${meeting.title} - Meetings`" />
    
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent">
                        Meeting Details
                    </h1>
                    <p class="text-muted-foreground">
                        Review and manage meeting information
                    </p>
                </div>
                <Button variant="outline" as-child>
                    <Link href="/admin/meetings">
                        <ArrowLeft class="h-4 w-4 mr-2" />
                        Back to Meetings
                    </Link>
                </Button>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Meeting Details -->
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <CardTitle class="text-2xl mb-3">{{ meeting.title }}</CardTitle>
                                <div class="flex flex-wrap items-center gap-2">
                                    <Badge class="bg-blue-100 text-blue-800">
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
                                </div>
                            </div>
                        </div>
                        
                        <CardDescription>
                            <div class="grid gap-4 md:grid-cols-2 text-sm">
                                <div class="flex items-center gap-2">
                                    <Users class="h-4 w-4 text-muted-foreground" />
                                    <span>{{ meeting.group?.name || 'Unknown Group' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Calendar class="h-4 w-4 text-muted-foreground" />
                                    <span>{{ formatDate(meeting.scheduled_at) }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Clock class="h-4 w-4 text-muted-foreground" />
                                    <span>{{ meeting.duration_minutes }} minutes</span>
                                </div>
                                <div v-if="meeting.creator" class="flex items-center gap-2">
                                    <User class="h-4 w-4 text-muted-foreground" />
                                    <span>{{ meeting.creator.name }}</span>
                                </div>
                                <div v-if="meeting.meeting_type === 'physical' && meeting.location" class="flex items-center gap-2">
                                    <MapPin class="h-4 w-4 text-muted-foreground" />
                                    <span>{{ meeting.location }}</span>
                                </div>
                                <div v-if="meeting.meeting_type === 'online' && meeting.meeting_url" class="flex items-center gap-2">
                                    <Video class="h-4 w-4 text-muted-foreground" />
                                    <a :href="meeting.meeting_url" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                        Join Online Meeting
                                    </a>
                                </div>
                            </div>
                        </CardDescription>
                    </CardHeader>
                    
                    <CardContent>
                        <div v-if="meeting.description" class="mb-6">
                            <h3 class="font-semibold mb-3">Description</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-gray-700 whitespace-pre-wrap">{{ meeting.description }}</p>
                            </div>
                        </div>

                        <!-- Attendees List -->
                        <div v-if="meeting.attendances && meeting.attendances.length > 0" class="mb-6">
                            <h3 class="font-semibold mb-3">Attendees ({{ meeting.attendances.length }})</h3>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div v-for="attendance in meeting.attendances" :key="attendance.id" 
                                     class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <span class="font-medium">{{ attendance.user.name }}</span>
                                    <Badge :class="getRsvpColor(attendance.rsvp_status)" class="text-xs">
                                        <component :is="getRsvpIcon(attendance.rsvp_status)" class="h-3 w-3 mr-1" />
                                        {{ attendance.rsvp_status.toUpperCase() }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                        
                        <div v-else class="text-center py-8 text-muted-foreground border-t">
                            <Users class="h-12 w-12 mx-auto mb-4 opacity-50" />
                            <p>No RSVPs yet</p>
                            <p class="text-sm mt-2">Members haven't responded to this meeting yet.</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Attendance Stats -->
                <div class="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg">Attendance Statistics</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium">Total RSVPs:</span>
                                <span class="text-2xl font-bold">{{ attendanceStats.total }}</span>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="flex items-center gap-2">
                                        <CheckCircle class="h-4 w-4 text-green-600" />
                                        Confirmed
                                    </span>
                                    <span class="font-semibold text-green-600">{{ attendanceStats.confirmed }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="flex items-center gap-2">
                                        <HelpCircle class="h-4 w-4 text-yellow-600" />
                                        Maybe
                                    </span>
                                    <span class="font-semibold text-yellow-600">{{ attendanceStats.maybe }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="flex items-center gap-2">
                                        <XCircle class="h-4 w-4 text-red-600" />
                                        Declined
                                    </span>
                                    <span class="font-semibold text-red-600">{{ attendanceStats.declined }}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Meeting Info -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg">Meeting Information</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3 text-sm">
                            <div>
                                <span class="font-medium">Status:</span>
                                <Badge :class="getStatusColor(meeting.status)" class="ml-2">
                                    {{ meeting.status.replace('_', ' ') }}
                                </Badge>
                            </div>
                            <div>
                                <span class="font-medium">Type:</span>
                                <Badge class="bg-blue-100 text-blue-800 ml-2">
                                    {{ getTypeLabel(meeting.type) }}
                                </Badge>
                            </div>
                            <div>
                                <span class="font-medium">Format:</span>
                                <Badge :class="meeting.meeting_type === 'online' ? 'bg-purple-100 text-purple-800' : 'bg-teal-100 text-teal-800'" class="ml-2">
                                    <Video v-if="meeting.meeting_type === 'online'" class="h-3 w-3 mr-1" />
                                    <MapPin v-else class="h-3 w-3 mr-1" />
                                    {{ meeting.meeting_type === 'online' ? 'Online' : 'Physical' }}
                                </Badge>
                            </div>
                            <div>
                                <span class="font-medium">Scheduled:</span>
                                <span class="ml-2">{{ formatDate(meeting.scheduled_at) }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Duration:</span>
                                <span class="ml-2">{{ meeting.duration_minutes }} minutes</span>
                            </div>
                            <div v-if="meeting.meeting_type === 'physical' && meeting.location">
                                <span class="font-medium">Location:</span>
                                <span class="ml-2">{{ meeting.location }}</span>
                            </div>
                            <div v-if="meeting.meeting_type === 'online' && meeting.meeting_url">
                                <span class="font-medium">Meeting Link:</span>
                                <a :href="meeting.meeting_url" target="_blank" class="ml-2 text-blue-600 hover:underline">
                                    Join Online
                                </a>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
