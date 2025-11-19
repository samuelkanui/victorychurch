<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import MemberLayout from '@/layouts/MemberLayout.vue'
import { type BreadcrumbItemType } from '@/types'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { 
    Calendar, 
    Clock,
    MapPin,
    Video,
    Users,
    User,
    CheckCircle,
    XCircle,
    HelpCircle,
    AlertCircle,
    ArrowLeft
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
    group: {
        id: number
        name: string
    }
    creator?: {
        id: number
        name: string
    }
}

interface Attendance {
    id: number
    rsvp_status: string | null
    status: string | null
    notes: string | null
    rsvp_at: string | null
}

interface Attendee {
    id: number
    user: {
        id: number
        name: string
    }
    rsvp_status: string
}

interface Props {
    meeting: Meeting
    attendance: Attendance | null
    attendees: Attendee[]
    canRsvp: boolean
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Meetings', href: '/member/meetings' },
    { title: props.meeting.title }
]

const rsvpForm = useForm({
    rsvp_status: props.attendance?.rsvp_status || '',
    notes: props.attendance?.notes || ''
})

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

const getRsvpIcon = (status: string) => {
    switch (status) {
        case 'yes': return CheckCircle
        case 'no': return XCircle
        case 'maybe': return HelpCircle
        default: return HelpCircle
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
        return `${hours}h ${mins}m`
    }
    return `${mins}m`
}

const submitRsvp = () => {
    rsvpForm.post(`/member/meetings/${props.meeting.id}/rsvp`, {
        onSuccess: () => {
            // Success handled by Inertia
        }
    })
}
</script>

<template>
    <Head :title="`${meeting.title} - Meetings`" />
    
    <MemberLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-green-600 to-green-800 bg-clip-text text-transparent">
                        Meeting Details
                    </h1>
                    <p class="text-muted-foreground">
                        View meeting information and manage your attendance
                    </p>
                </div>
                <Button variant="outline" @click="$inertia.visit('/member/meetings')">
                    <ArrowLeft class="h-4 w-4 mr-2" />
                    Back to Meetings
                </Button>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Meeting Details -->
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <CardTitle class="text-2xl mb-3">{{ meeting.title }}</CardTitle>
                                <div class="flex flex-wrap items-center gap-2 mb-4">
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
                                    <Badge v-if="attendance?.rsvp_status" :class="getRsvpColor(attendance.rsvp_status)">
                                        <component :is="getRsvpIcon(attendance.rsvp_status)" class="h-3 w-3 mr-1" />
                                        {{ attendance.rsvp_status.toUpperCase() }}
                                    </Badge>
                                    <Badge v-else-if="canRsvp" class="bg-yellow-100 text-yellow-800">
                                        <AlertCircle class="h-3 w-3 mr-1" />
                                        RSVP Needed
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
                                    <span>{{ formatDateTime(meeting.scheduled_at) }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Clock class="h-4 w-4 text-muted-foreground" />
                                    <span>{{ formatDuration(meeting.duration_minutes) }}</span>
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
                        <div v-if="attendees.length > 0" class="mb-6">
                            <h3 class="font-semibold mb-3">Attendees ({{ attendees.length }})</h3>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div v-for="attendee in attendees" :key="attendee.id" 
                                     class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <span class="font-medium">{{ attendee.user.name }}</span>
                                    <Badge :class="getRsvpColor(attendee.rsvp_status)" class="text-xs">
                                        <component :is="getRsvpIcon(attendee.rsvp_status)" class="h-3 w-3 mr-1" />
                                        {{ attendee.rsvp_status.toUpperCase() }}
                                    </Badge>
                                </div>
                            </div>
                        </div>

                        <!-- Current RSVP Status -->
                        <div v-if="attendance" class="p-4 bg-blue-50 rounded-lg border-l-4 border-blue-400">
                            <h4 class="font-semibold text-blue-900 mb-2">Your RSVP Status</h4>
                            <div class="flex items-center gap-2 mb-2">
                                <Badge :class="getRsvpColor(attendance.rsvp_status || 'none')">
                                    <component :is="getRsvpIcon(attendance.rsvp_status || 'none')" class="h-3 w-3 mr-1" />
                                    {{ attendance.rsvp_status?.toUpperCase() || 'NO RESPONSE' }}
                                </Badge>
                                <span class="text-sm text-blue-700">
                                    {{ attendance.rsvp_at ? `Responded on ${formatDateTime(attendance.rsvp_at)}` : '' }}
                                </span>
                            </div>
                            <p v-if="attendance.notes" class="text-sm text-blue-800">
                                <strong>Notes:</strong> {{ attendance.notes }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- RSVP Form -->
                <div class="space-y-6">
                    <Card v-if="canRsvp">
                        <CardHeader>
                            <CardTitle class="text-lg">RSVP to Meeting</CardTitle>
                            <CardDescription>
                                Let us know if you'll be attending
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form @submit.prevent="submitRsvp" class="space-y-4">
                                <div>
                                    <label class="text-sm font-medium mb-3 block">Will you attend?</label>
                                    <div class="space-y-2">
                                        <label class="flex items-center space-x-3 cursor-pointer">
                                            <input 
                                                v-model="rsvpForm.rsvp_status" 
                                                type="radio" 
                                                value="yes"
                                                class="text-green-600 focus:ring-green-500"
                                            />
                                            <div class="flex items-center gap-2">
                                                <CheckCircle class="h-4 w-4 text-green-600" />
                                                <span>Yes, I'll be there</span>
                                            </div>
                                        </label>
                                        <label class="flex items-center space-x-3 cursor-pointer">
                                            <input 
                                                v-model="rsvpForm.rsvp_status" 
                                                type="radio" 
                                                value="maybe"
                                                class="text-yellow-600 focus:ring-yellow-500"
                                            />
                                            <div class="flex items-center gap-2">
                                                <HelpCircle class="h-4 w-4 text-yellow-600" />
                                                <span>Maybe</span>
                                            </div>
                                        </label>
                                        <label class="flex items-center space-x-3 cursor-pointer">
                                            <input 
                                                v-model="rsvpForm.rsvp_status" 
                                                type="radio" 
                                                value="no"
                                                class="text-red-600 focus:ring-red-500"
                                            />
                                            <div class="flex items-center gap-2">
                                                <XCircle class="h-4 w-4 text-red-600" />
                                                <span>No, I can't attend</span>
                                            </div>
                                        </label>
                                    </div>
                                    <p v-if="rsvpForm.errors.rsvp_status" class="text-sm text-red-600 mt-1">
                                        {{ rsvpForm.errors.rsvp_status }}
                                    </p>
                                </div>

                                <div>
                                    <label class="text-sm font-medium mb-2 block">Notes (Optional)</label>
                                    <textarea 
                                        v-model="rsvpForm.notes"
                                        placeholder="Any additional notes or comments..."
                                        rows="3"
                                        class="w-full p-3 border rounded-lg resize-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    ></textarea>
                                    <p v-if="rsvpForm.errors.notes" class="text-sm text-red-600 mt-1">
                                        {{ rsvpForm.errors.notes }}
                                    </p>
                                </div>

                                <Button 
                                    type="submit" 
                                    :disabled="rsvpForm.processing || !rsvpForm.rsvp_status"
                                    class="w-full bg-green-600 hover:bg-green-700"
                                >
                                    {{ rsvpForm.processing ? 'Submitting...' : (attendance ? 'Update RSVP' : 'Submit RSVP') }}
                                </Button>
                            </form>
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
                                <Badge :class="getTypeColor(meeting.type)" class="ml-2">
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
                                <span class="ml-2">{{ formatDateTime(meeting.scheduled_at) }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Duration:</span>
                                <span class="ml-2">{{ formatDuration(meeting.duration_minutes) }}</span>
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
                            <div v-if="attendees.length > 0">
                                <span class="font-medium">Attendees:</span>
                                <span class="ml-2">{{ attendees.filter(a => a.rsvp_status === 'yes').length }} confirmed</span>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </MemberLayout>
</template>
