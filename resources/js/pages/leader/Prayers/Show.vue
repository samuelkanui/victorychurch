<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3'
import LeaderLayout from '@/layouts/LeaderLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { 
    MessageSquare, 
    User, 
    Clock, 
    Shield, 
    Globe,
    Users,
    Lock,
    AlertCircle,
    Heart,
    Flag,
    CheckCircle,
    ArrowLeft
} from 'lucide-vue-next'
import { ref } from 'vue'

interface Props {
    prayer: {
        id: number
        title: string
        description: string
        privacy: string
        is_anonymous: boolean
        is_urgent: boolean
        status: string
        created_at: string
        updated_at: string
        leader_response?: string
        responded_at?: string
        moderation_note?: string
        moderated_at?: string
        user: {
            id: number
            name: string
            groups?: Array<{
                id: number
                name: string
            }>
        } | null
    }
}

const props = defineProps<Props>()

const breadcrumbs = [
    { title: 'Prayer Requests', href: '/leader/prayers' },
    { title: props.prayer.title }
]

const showResponseForm = ref(false)
const showModerationForm = ref(false)

const responseForm = useForm({
    response: ''
})

const moderationForm = useForm({
    status: props.prayer.status,
    moderation_note: ''
})

const formatDate = (dateString: string) => {
    if (!dateString) return 'No date'
    try {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit'
        })
    } catch (error) {
        return 'Invalid date'
    }
}

const getPrivacyIcon = (privacy: string) => {
    switch (privacy) {
        case 'public': return Globe
        case 'group': return Users
        case 'private': return Lock
        default: return Lock
    }
}

const getPrivacyColor = (privacy: string) => {
    switch (privacy) {
        case 'public': return 'bg-green-100 text-green-800'
        case 'group': return 'bg-blue-100 text-blue-800'
        case 'private': return 'bg-gray-100 text-gray-800'
        default: return 'bg-gray-100 text-gray-800'
    }
}

const getPrivacyLabel = (privacy: string) => {
    switch (privacy) {
        case 'public': return 'Public'
        case 'group': return 'Group Only'
        case 'private': return 'Private'
        default: return privacy
    }
}

const getStatusColor = (status: string) => {
    switch (status) {
        case 'active': return 'bg-blue-100 text-blue-800'
        case 'answered': return 'bg-green-100 text-green-800'
        case 'flagged': return 'bg-red-100 text-red-800'
        case 'resolved': return 'bg-gray-100 text-gray-800'
        default: return 'bg-gray-100 text-gray-800'
    }
}

const submitResponse = () => {
    responseForm.post(`/leader/prayers/${props.prayer.id}/respond`, {
        onSuccess: () => {
            showResponseForm.value = false
            responseForm.reset()
        }
    })
}

const submitModeration = () => {
    moderationForm.put(`/leader/prayers/${props.prayer.id}/moderate`, {
        onSuccess: () => {
            showModerationForm.value = false
        }
    })
}
</script>

<template>
    <Head :title="`${prayer.title} - Prayer Requests`" />
    
    <LeaderLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        Prayer Request Details
                    </h1>
                    <p class="text-muted-foreground">
                        Review and respond to prayer request
                    </p>
                </div>
                <Button variant="outline" as-child>
                    <Link href="/leader/prayers">
                        <ArrowLeft class="h-4 w-4 mr-2" />
                        Back to Prayers
                    </Link>
                </Button>
            </div>

            <!-- Prayer Request Details -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Main Content -->
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <CardTitle class="flex items-center gap-2 mb-2">
                                    <MessageSquare class="h-5 w-5" />
                                    {{ prayer.title }}
                                </CardTitle>
                                <div class="flex items-center gap-2 mb-2">
                                    <Badge v-if="prayer.is_urgent" class="bg-red-100 text-red-800">
                                        <AlertCircle class="h-3 w-3 mr-1" />
                                        Urgent
                                    </Badge>
                                    <Badge :class="getStatusColor(prayer.status)">
                                        {{ prayer.status.charAt(0).toUpperCase() + prayer.status.slice(1) }}
                                    </Badge>
                                    <Badge :class="getPrivacyColor(prayer.privacy)">
                                        <component :is="getPrivacyIcon(prayer.privacy)" class="h-3 w-3 mr-1" />
                                        {{ getPrivacyLabel(prayer.privacy) }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                        <CardDescription>
                            <div class="flex items-center gap-4 text-sm">
                                <span class="flex items-center gap-1">
                                    <User class="h-4 w-4" />
                                    {{ prayer.is_anonymous ? 'Anonymous' : (prayer.user?.name || 'Unknown User') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <Clock class="h-4 w-4" />
                                    {{ formatDate(prayer.created_at) }}
                                </span>
                                <span v-if="prayer.user && prayer.user.groups && prayer.user.groups.length > 0" class="flex items-center gap-1">
                                    <Users class="h-4 w-4" />
                                    {{ prayer.user.groups[0].name }}
                                </span>
                            </div>
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-6">
                            <div>
                                <h3 class="font-medium mb-3">Prayer Request</h3>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-gray-800 whitespace-pre-wrap">{{ prayer.description }}</p>
                                </div>
                            </div>

                            <!-- Leader Response -->
                            <div v-if="prayer.leader_response" class="border-t pt-6">
                                <h3 class="font-medium mb-3">Leader Response</h3>
                                <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-400">
                                    <p class="text-blue-800 mb-2">{{ prayer.leader_response }}</p>
                                    <p class="text-xs text-blue-600">
                                        Responded on {{ formatDate(prayer.responded_at || '') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Moderation Note -->
                            <div v-if="prayer.moderation_note" class="border-t pt-6">
                                <h3 class="font-medium mb-3">Moderation Note</h3>
                                <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-400">
                                    <p class="text-yellow-800 mb-2">{{ prayer.moderation_note }}</p>
                                    <p class="text-xs text-yellow-600">
                                        Moderated on {{ formatDate(prayer.moderated_at || '') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Actions Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg">Actions</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <Button 
                                v-if="prayer.status === 'active' && !prayer.leader_response"
                                @click="showResponseForm = !showResponseForm"
                                class="w-full bg-blue-600 hover:bg-blue-700"
                            >
                                <Heart class="h-4 w-4 mr-2" />
                                Respond to Prayer
                            </Button>
                            
                            <Button 
                                @click="showModerationForm = !showModerationForm"
                                variant="outline" 
                                class="w-full"
                            >
                                <Flag class="h-4 w-4 mr-2" />
                                Moderate Request
                            </Button>

                            <Button 
                                v-if="prayer.status === 'active'"
                                @click="moderationForm.status = 'answered'; submitModeration()"
                                variant="outline" 
                                class="w-full"
                                :disabled="moderationForm.processing"
                            >
                                <CheckCircle class="h-4 w-4 mr-2" />
                                Mark as Answered
                            </Button>
                        </CardContent>
                    </Card>

                    <!-- Prayer Info -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg">Prayer Information</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3 text-sm">
                            <div>
                                <span class="font-medium">Status:</span>
                                <Badge :class="getStatusColor(prayer.status)" class="ml-2">
                                    {{ prayer.status.charAt(0).toUpperCase() + prayer.status.slice(1) }}
                                </Badge>
                            </div>
                            <div>
                                <span class="font-medium">Privacy:</span>
                                <Badge :class="getPrivacyColor(prayer.privacy)" class="ml-2">
                                    {{ getPrivacyLabel(prayer.privacy) }}
                                </Badge>
                            </div>
                            <div>
                                <span class="font-medium">Submitted:</span>
                                <span class="ml-2">{{ formatDate(prayer.created_at) }}</span>
                            </div>
                            <div v-if="prayer.updated_at !== prayer.created_at">
                                <span class="font-medium">Updated:</span>
                                <span class="ml-2">{{ formatDate(prayer.updated_at) }}</span>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Response Form -->
            <Card v-if="showResponseForm">
                <CardHeader>
                    <CardTitle>Respond to Prayer Request</CardTitle>
                    <CardDescription>
                        Provide encouragement, scripture, or pastoral guidance
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submitResponse" class="space-y-4">
                        <div>
                            <textarea 
                                v-model="responseForm.response"
                                placeholder="Write your response to this prayer request..."
                                rows="4"
                                class="w-full p-3 border rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'border-red-500': responseForm.errors.response }"
                            ></textarea>
                            <p v-if="responseForm.errors.response" class="text-sm text-red-600 mt-1">
                                {{ responseForm.errors.response }}
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <Button type="submit" :disabled="responseForm.processing">
                                {{ responseForm.processing ? 'Sending...' : 'Send Response' }}
                            </Button>
                            <Button type="button" variant="outline" @click="showResponseForm = false">
                                Cancel
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

            <!-- Moderation Form -->
            <Card v-if="showModerationForm">
                <CardHeader>
                    <CardTitle>Moderate Prayer Request</CardTitle>
                    <CardDescription>
                        Update status or add moderation notes
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submitModeration" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Status</label>
                            <select 
                                v-model="moderationForm.status"
                                class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="active">Active</option>
                                <option value="answered">Answered</option>
                                <option value="flagged">Flagged</option>
                                <option value="resolved">Resolved</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Moderation Note (Optional)</label>
                            <textarea 
                                v-model="moderationForm.moderation_note"
                                placeholder="Add any moderation notes..."
                                rows="3"
                                class="w-full p-3 border rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            ></textarea>
                        </div>
                        <div class="flex gap-2">
                            <Button type="submit" :disabled="moderationForm.processing">
                                {{ moderationForm.processing ? 'Updating...' : 'Update Prayer' }}
                            </Button>
                            <Button type="button" variant="outline" @click="showModerationForm = false">
                                Cancel
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </LeaderLayout>
</template>
