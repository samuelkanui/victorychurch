<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import LeaderLayout from '@/layouts/LeaderLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import { Calendar, Plus, X } from 'lucide-vue-next'

interface Props {
    groups: Array<{
        id: number
        name: string
    }>
}

const props = defineProps<Props>()

const breadcrumbs = [
    { title: 'Meetings', href: '/leader/meetings' },
    { title: 'Schedule Meeting' }
]

// Get tomorrow's date at 10:00 AM as default
const getDefaultDateTime = () => {
    const tomorrow = new Date()
    tomorrow.setDate(tomorrow.getDate() + 1)
    tomorrow.setHours(10, 0, 0, 0)
    return tomorrow.toISOString().slice(0, 16)
}

const form = useForm({
    group_id: '',
    title: '',
    description: '',
    type: 'bible_study',
    meeting_type: 'physical',
    scheduled_at: getDefaultDateTime(),
    duration_minutes: 60,
    location: '',
    meeting_url: '',
    max_attendees: null,
    is_recurring: false,
    recurrence_pattern: '',
})

const meetingTypes = [
    { value: 'bible_study', label: 'Bible Study' },
    { value: 'prayer', label: 'Prayer Meeting' },
    { value: 'fellowship', label: 'Fellowship' },
    { value: 'service', label: 'Service Planning' },
    { value: 'other', label: 'Other' },
]

const recurrenceOptions = [
    { value: '', label: 'No recurrence' },
    { value: 'weekly', label: 'Weekly' },
    { value: 'biweekly', label: 'Bi-weekly' },
    { value: 'monthly', label: 'Monthly' },
]

const submit = () => {
    form.post('/leader/meetings', {
        onSuccess: () => {
            // Handle success - redirect happens automatically
        }
    })
}
</script>

<template>
    <Head title="Schedule Meeting - Leader Dashboard" />
    
    <LeaderLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        Schedule Meeting
                    </h1>
                    <p class="text-muted-foreground">
                        Create a new meeting for your group members
                    </p>
                </div>
            </div>

            <!-- Create Form -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Calendar class="h-5 w-5" />
                        Meeting Details
                    </CardTitle>
                    <CardDescription>
                        Schedule a new meeting with your group members
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-6 md:grid-cols-2">
                            <!-- Group Selection -->
                            <div class="space-y-2">
                                <Label for="group_id">Select Group *</Label>
                                <select 
                                    id="group_id"
                                    v-model="form.group_id"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    :class="{ 'border-red-500': form.errors.group_id }"
                                >
                                    <option value="">Select a group</option>
                                    <option v-for="group in groups" :key="group.id" :value="group.id">
                                        {{ group.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.group_id" class="text-sm text-red-600">{{ form.errors.group_id }}</p>
                                <p v-if="groups.length === 0" class="text-sm text-yellow-600">No active groups available. You need to have active groups to schedule meetings.</p>
                            </div>

                            <!-- Meeting Title -->
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

                            <!-- Meeting Type -->
                            <div class="space-y-2">
                                <Label for="type">Meeting Type *</Label>
                                <select 
                                    id="type"
                                    v-model="form.type"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    :class="{ 'border-red-500': form.errors.type }"
                                >
                                    <option v-for="type in meetingTypes" :key="type.value" :value="type.value">
                                        {{ type.label }}
                                    </option>
                                </select>
                                <p v-if="form.errors.type" class="text-sm text-red-600">{{ form.errors.type }}</p>
                            </div>

                            <!-- Scheduled Time -->
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
                                <select 
                                    id="duration_minutes"
                                    v-model="form.duration_minutes"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    :class="{ 'border-red-500': form.errors.duration_minutes }"
                                >
                                    <option :value="30">30 minutes</option>
                                    <option :value="45">45 minutes</option>
                                    <option :value="60">1 hour</option>
                                    <option :value="90">1.5 hours</option>
                                    <option :value="120">2 hours</option>
                                    <option :value="180">3 hours</option>
                                </select>
                                <p v-if="form.errors.duration_minutes" class="text-sm text-red-600">{{ form.errors.duration_minutes }}</p>
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

                        <!-- Meeting Type (Physical/Online) -->
                        <div class="space-y-2">
                            <Label>Meeting Format *</Label>
                            <div class="flex gap-4">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input 
                                        type="radio"
                                        v-model="form.meeting_type"
                                        value="physical"
                                        class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                                    />
                                    <span class="text-sm font-medium">Physical Meeting</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input 
                                        type="radio"
                                        v-model="form.meeting_type"
                                        value="online"
                                        class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                                    />
                                    <span class="text-sm font-medium">Online Meeting</span>
                                </label>
                            </div>
                            <p v-if="form.errors.meeting_type" class="text-sm text-red-600">{{ form.errors.meeting_type }}</p>
                        </div>

                        <!-- Conditional Location/Link Fields -->
                        <div class="space-y-2" v-if="form.meeting_type === 'physical'">
                            <Label for="location">Physical Location *</Label>
                            <Input 
                                id="location"
                                v-model="form.location"
                                placeholder="Enter meeting room, address, or venue"
                                :class="{ 'border-red-500': form.errors.location }"
                            />
                            <p v-if="form.errors.location" class="text-sm text-red-600">{{ form.errors.location }}</p>
                            <p class="text-xs text-muted-foreground">Specify where the physical meeting will take place</p>
                        </div>

                        <div class="space-y-2" v-if="form.meeting_type === 'online'">
                            <Label for="meeting_url">Online Meeting Link *</Label>
                            <Input 
                                id="meeting_url"
                                v-model="form.meeting_url"
                                type="url"
                                placeholder="https://zoom.us/j/... or other meeting link"
                                :class="{ 'border-red-500': form.errors.meeting_url }"
                            />
                            <p v-if="form.errors.meeting_url" class="text-sm text-red-600">{{ form.errors.meeting_url }}</p>
                            <p class="text-xs text-muted-foreground">Paste the Zoom, Google Meet, or other video conferencing link</p>
                        </div>

                        <!-- Recurring Meeting Options -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-2">
                                <input 
                                    id="is_recurring"
                                    v-model="form.is_recurring"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                />
                                <Label for="is_recurring">This is a recurring meeting</Label>
                            </div>

                            <div v-if="form.is_recurring" class="space-y-2">
                                <Label for="recurrence_pattern">Recurrence Pattern</Label>
                                <select 
                                    id="recurrence_pattern"
                                    v-model="form.recurrence_pattern"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    :class="{ 'border-red-500': form.errors.recurrence_pattern }"
                                >
                                    <option v-for="option in recurrenceOptions.slice(1)" :key="option.value" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                                <p v-if="form.errors.recurrence_pattern" class="text-sm text-red-600">{{ form.errors.recurrence_pattern }}</p>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center gap-4 pt-4">
                            <Button 
                                type="submit" 
                                :disabled="form.processing || groups.length === 0"
                                class="bg-blue-600 hover:bg-blue-700"
                            >
                                <Plus class="h-4 w-4 mr-2" />
                                {{ form.processing ? 'Scheduling...' : 'Schedule Meeting' }}
                            </Button>
                            
                            <Button 
                                type="button" 
                                variant="outline"
                                @click="$inertia.visit('/leader/meetings')"
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
