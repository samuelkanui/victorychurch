<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import LeaderLayout from '@/layouts/LeaderLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import { Calendar, Save, X } from 'lucide-vue-next'
import { ref } from 'vue'

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
}

interface Props {
    meeting: Meeting
    groups: Array<{
        id: number
        name: string
    }>
}

const props = defineProps<Props>()

const breadcrumbs = [
    { title: 'Meetings', href: '/leader/meetings' },
    { title: props.meeting.title, href: `/leader/meetings/${props.meeting.id}` },
    { title: 'Edit' }
]

// Format date for input field (YYYY-MM-DDTHH:MM)
const formatDateForInput = (dateString: string) => {
    if (!dateString) return ''
    try {
        const date = new Date(dateString)
        return date.toISOString().slice(0, 16)
    } catch (error) {
        return ''
    }
}

const form = useForm({
    title: props.meeting.title,
    description: props.meeting.description || '',
    type: props.meeting.type,
    scheduled_at: formatDateForInput(props.meeting.scheduled_at),
    duration_minutes: props.meeting.duration_minutes,
    location: props.meeting.location || '',
    meeting_url: props.meeting.meeting_url || '',
    max_attendees: props.meeting.max_attendees || null,
    status: props.meeting.status,
})

const meetingTypes = [
    { value: 'bible_study', label: 'Bible Study' },
    { value: 'prayer', label: 'Prayer Meeting' },
    { value: 'fellowship', label: 'Fellowship' },
    { value: 'service', label: 'Service Planning' },
    { value: 'other', label: 'Other' },
]

const statusOptions = [
    { value: 'scheduled', label: 'Scheduled' },
    { value: 'in_progress', label: 'In Progress' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' },
]

const submit = () => {
    form.put(`/leader/meetings/${props.meeting.id}`, {
        onSuccess: () => {
            // Handle success - redirect happens automatically
        }
    })
}
</script>

<template>
    <Head :title="`Edit ${meeting.title} - Meetings`" />
    
    <LeaderLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        Edit Meeting
                    </h1>
                    <p class="text-muted-foreground">
                        Update meeting details and settings
                    </p>
                </div>
            </div>

            <!-- Edit Form -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Calendar class="h-5 w-5" />
                        Meeting Details
                    </CardTitle>
                    <CardDescription>
                        Modify meeting information, schedule, and location
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-6 md:grid-cols-2">
                            <!-- Title -->
                            <div class="space-y-2">
                                <Label for="title">Meeting Title *</Label>
                                <Input 
                                    id="title"
                                    v-model="form.title"
                                    placeholder="Enter meeting title"
                                    :class="{ 'border-red-500': form.errors.title }"
                                />
                                <p v-if="form.errors.title" class="text-sm text-red-600">{{ form.errors.title }}</p>
                            </div>

                            <!-- Type -->
                            <div class="space-y-2">
                                <Label for="type">Meeting Type *</Label>
                                <select 
                                    id="type"
                                    v-model="form.type"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    :class="{ 'border-red-500': form.errors.type }"
                                >
                                    <option value="">Select meeting type</option>
                                    <option v-for="type in meetingTypes" :key="type.value" :value="type.value">
                                        {{ type.label }}
                                    </option>
                                </select>
                                <p v-if="form.errors.type" class="text-sm text-red-600">{{ form.errors.type }}</p>
                            </div>

                            <!-- Scheduled At -->
                            <div class="space-y-2">
                                <Label for="scheduled_at">Scheduled Time *</Label>
                                <Input 
                                    id="scheduled_at"
                                    v-model="form.scheduled_at"
                                    type="datetime-local"
                                    :class="{ 'border-red-500': form.errors.scheduled_at }"
                                />
                                <p v-if="form.errors.scheduled_at" class="text-sm text-red-600">{{ form.errors.scheduled_at }}</p>
                            </div>

                            <!-- Duration -->
                            <div class="space-y-2">
                                <Label for="duration_minutes">Duration (minutes) *</Label>
                                <Input 
                                    id="duration_minutes"
                                    v-model="form.duration_minutes"
                                    type="number"
                                    min="15"
                                    max="480"
                                    placeholder="60"
                                    :class="{ 'border-red-500': form.errors.duration_minutes }"
                                />
                                <p v-if="form.errors.duration_minutes" class="text-sm text-red-600">{{ form.errors.duration_minutes }}</p>
                            </div>

                            <!-- Location -->
                            <div class="space-y-2">
                                <Label for="location">Location</Label>
                                <Input 
                                    id="location"
                                    v-model="form.location"
                                    placeholder="Meeting location or address"
                                    :class="{ 'border-red-500': form.errors.location }"
                                />
                                <p v-if="form.errors.location" class="text-sm text-red-600">{{ form.errors.location }}</p>
                            </div>

                            <!-- Max Attendees -->
                            <div class="space-y-2">
                                <Label for="max_attendees">Max Attendees</Label>
                                <Input 
                                    id="max_attendees"
                                    v-model="form.max_attendees"
                                    type="number"
                                    min="1"
                                    max="200"
                                    placeholder="No limit"
                                    :class="{ 'border-red-500': form.errors.max_attendees }"
                                />
                                <p v-if="form.errors.max_attendees" class="text-sm text-red-600">{{ form.errors.max_attendees }}</p>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <Label for="description">Description</Label>
                            <textarea 
                                id="description"
                                v-model="form.description"
                                placeholder="Describe the meeting purpose and agenda"
                                rows="3"
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                :class="{ 'border-red-500': form.errors.description }"
                            ></textarea>
                            <p v-if="form.errors.description" class="text-sm text-red-600">{{ form.errors.description }}</p>
                        </div>

                        <!-- Meeting URL -->
                        <div class="space-y-2">
                            <Label for="meeting_url">Virtual Meeting URL</Label>
                            <Input 
                                id="meeting_url"
                                v-model="form.meeting_url"
                                type="url"
                                placeholder="https://zoom.us/j/... or other meeting link"
                                :class="{ 'border-red-500': form.errors.meeting_url }"
                            />
                            <p v-if="form.errors.meeting_url" class="text-sm text-red-600">{{ form.errors.meeting_url }}</p>
                        </div>

                        <!-- Status -->
                        <div class="space-y-2">
                            <Label for="status">Meeting Status *</Label>
                            <select 
                                id="status"
                                v-model="form.status"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                :class="{ 'border-red-500': form.errors.status }"
                            >
                                <option v-for="status in statusOptions" :key="status.value" :value="status.value">
                                    {{ status.label }}
                                </option>
                            </select>
                            <p v-if="form.errors.status" class="text-sm text-red-600">{{ form.errors.status }}</p>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center gap-4 pt-4">
                            <Button 
                                type="submit" 
                                :disabled="form.processing"
                                class="bg-blue-600 hover:bg-blue-700"
                            >
                                <Save class="h-4 w-4 mr-2" />
                                {{ form.processing ? 'Saving...' : 'Save Changes' }}
                            </Button>
                            
                            <Button 
                                type="button" 
                                variant="outline"
                                @click="$inertia.visit(`/leader/meetings/${meeting.id}`)"
                            >
                                <X class="h-4 w-4 mr-2" />
                                Cancel
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </LeaderLayout>
</template>
