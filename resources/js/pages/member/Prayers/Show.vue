<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import MemberLayout from '@/layouts/MemberLayout.vue'
import { type BreadcrumbItemType } from '@/types'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Textarea } from '@/components/ui/textarea'
import { 
    Heart, 
    ArrowLeft,
    Calendar,
    User,
    Lock,
    Globe,
    Users,
    MessageSquare,
    Send,
    AlertCircle
} from 'lucide-vue-next'
import { ref } from 'vue'

interface PrayerRequest {
    id: number
    title: string
    description: string
    privacy: string
    is_urgent: boolean
    is_answered: boolean
    answered_at: string | null
    answer_description: string | null
    leader_response: string | null
    responded_at: string | null
    responded_by: number | null
    created_at: string
    user: {
        id: number
        name: string
    } | null
    responder?: {
        id: number
        name: string
        role: string
    } | null
    group?: {
        id: number
        name: string
    } | null
    prayers_count: number
    has_prayed: boolean
}

interface Props {
    prayerRequest: PrayerRequest
    canEdit: boolean
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Prayer Requests', href: '/member/prayers' },
    { title: props.prayerRequest.title }
]

const showPrayerForm = ref(false)

const prayerForm = useForm({
    prayer_request_id: props.prayerRequest.id
})

const getPrivacyIcon = (privacy: string) => {
    switch (privacy) {
        case 'public': return Globe
        case 'group': return Users
        case 'private': return Lock
        default: return Globe
    }
}

const getPrivacyColor = (privacy: string) => {
    switch (privacy) {
        case 'public': return 'bg-blue-100 text-blue-800'
        case 'group': return 'bg-purple-100 text-purple-800'
        case 'private': return 'bg-gray-100 text-gray-800'
        default: return 'bg-gray-100 text-gray-800'
    }
}

const getPrivacyLabel = (privacy: string) => {
    return privacy.charAt(0).toUpperCase() + privacy.slice(1)
}

const formatDate = (dateString: string) => {
    if (!dateString) return 'N/A'
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

const handlePray = () => {
    prayerForm.post(`/member/prayers/${props.prayerRequest.id}/pray`, {
        onSuccess: () => {
            showPrayerForm.value = false
        }
    })
}
</script>

<template>
    <Head :title="`${prayerRequest.title} - Prayer Requests`" />
    
    <MemberLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-green-600 to-green-800 bg-clip-text text-transparent">
                        Prayer Request Details
                    </h1>
                    <p class="text-muted-foreground">
                        View and pray for this request
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <Button v-if="canEdit" variant="outline" as-child>
                        <Link :href="`/member/prayers/${prayerRequest.id}/edit`">
                            Edit
                        </Link>
                    </Button>
                    <Button variant="outline" @click="$inertia.visit('/member/prayers')">
                        <ArrowLeft class="h-4 w-4 mr-2" />
                        Back
                    </Button>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Prayer Request Details -->
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <CardTitle class="text-2xl mb-3">{{ prayerRequest.title }}</CardTitle>
                                <div class="flex flex-wrap items-center gap-2 mb-4">
                                    <Badge :class="getPrivacyColor(prayerRequest.privacy)">
                                        <component :is="getPrivacyIcon(prayerRequest.privacy)" class="h-3 w-3 mr-1" />
                                        {{ getPrivacyLabel(prayerRequest.privacy) }}
                                    </Badge>
                                    <Badge v-if="prayerRequest.is_urgent" class="bg-red-100 text-red-800">
                                        <AlertCircle class="h-3 w-3 mr-1" />
                                        Urgent
                                    </Badge>
                                    <Badge v-if="prayerRequest.is_answered" class="bg-green-100 text-green-800">
                                        Answered
                                    </Badge>
                                </div>
                            </div>
                        </div>
                        
                        <CardDescription>
                            <div class="grid gap-3 text-sm">
                                <div v-if="prayerRequest.user" class="flex items-center gap-2">
                                    <User class="h-4 w-4 text-muted-foreground" />
                                    <span>Requested by {{ prayerRequest.user.name }}</span>
                                </div>
                                <div v-else class="flex items-center gap-2">
                                    <User class="h-4 w-4 text-muted-foreground" />
                                    <span>Anonymous Request</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Calendar class="h-4 w-4 text-muted-foreground" />
                                    <span>{{ formatDate(prayerRequest.created_at) }}</span>
                                </div>
                                <div v-if="prayerRequest.group" class="flex items-center gap-2">
                                    <Users class="h-4 w-4 text-muted-foreground" />
                                    <span>{{ prayerRequest.group.name }}</span>
                                </div>
                            </div>
                        </CardDescription>
                    </CardHeader>
                    
                    <CardContent class="space-y-6">
                        <!-- Prayer Request Description -->
                        <div>
                            <h3 class="font-semibold mb-3">Prayer Request</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-gray-800 whitespace-pre-wrap">{{ prayerRequest.description }}</p>
                            </div>
                        </div>

                        <!-- Leader/Admin Response -->
                        <div v-if="prayerRequest.leader_response" class="border-t pt-6">
                            <h3 class="font-semibold mb-3 flex items-center gap-2">
                                <MessageSquare class="h-5 w-5 text-blue-600" />
                                Response from {{ prayerRequest.responder?.role === 'admin' ? 'Admin' : 'Leader' }}
                            </h3>
                            <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-400">
                                <p class="text-sm text-blue-700 mb-2">
                                    <strong>{{ prayerRequest.responder?.name || 'Staff' }}</strong> responded on {{ formatDate(prayerRequest.responded_at!) }}
                                </p>
                                <p class="text-blue-800 whitespace-pre-wrap">{{ prayerRequest.leader_response }}</p>
                            </div>
                        </div>

                        <!-- Answer (if answered) -->
                        <div v-if="prayerRequest.is_answered && prayerRequest.answer_description" class="border-t pt-6">
                            <h3 class="font-semibold mb-3 flex items-center gap-2">
                                <Heart class="h-5 w-5 text-green-600" />
                                Prayer Answered
                            </h3>
                            <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-400">
                                <p class="text-sm text-green-700 mb-2">
                                    <strong>Answered on:</strong> {{ formatDate(prayerRequest.answered_at!) }}
                                </p>
                                <p class="text-green-800 whitespace-pre-wrap">{{ prayerRequest.answer_description }}</p>
                            </div>
                        </div>

                        <!-- Prayer Form (only for other people's prayers) -->
                        <div v-if="!canEdit && !prayerRequest.has_prayed" class="border-t pt-6">
                            <div v-if="!showPrayerForm">
                                <Button 
                                    class="w-full bg-green-600 hover:bg-green-700"
                                    @click="showPrayerForm = true"
                                >
                                    <Heart class="h-4 w-4 mr-2" />
                                    I Prayed for This
                                </Button>
                            </div>
                            <div v-else class="space-y-4">
                                <h3 class="font-semibold">Confirm Prayer</h3>
                                <p class="text-sm text-muted-foreground">
                                    Click the button below to let others know you've prayed for this request.
                                </p>
                                <div class="flex items-center gap-3">
                                    <Button 
                                        class="bg-green-600 hover:bg-green-700"
                                        @click="handlePray"
                                        :disabled="prayerForm.processing"
                                    >
                                        <Send class="h-4 w-4 mr-2" />
                                        {{ prayerForm.processing ? 'Confirming...' : 'Confirm Prayer' }}
                                    </Button>
                                    <Button 
                                        variant="outline"
                                        @click="showPrayerForm = false"
                                        :disabled="prayerForm.processing"
                                    >
                                        Cancel
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <!-- Already Prayed (only for other people's prayers) -->
                        <div v-else-if="!canEdit" class="border-t pt-6">
                            <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-400 flex items-center gap-3">
                                <Heart class="h-5 w-5 text-green-600 fill-green-600" />
                                <div>
                                    <p class="font-medium text-green-800">You've prayed for this request</p>
                                    <p class="text-sm text-green-700">Thank you for your prayers!</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Your Own Prayer Message -->
                        <div v-if="canEdit" class="border-t pt-6">
                            <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-400 flex items-center gap-3">
                                <MessageSquare class="h-5 w-5 text-blue-600" />
                                <div>
                                    <p class="font-medium text-blue-800">This is your prayer request</p>
                                    <p class="text-sm text-blue-700">Others in your community can pray for you</p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Prayer Statistics -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg">Prayer Statistics</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="text-center">
                                <div class="text-4xl font-bold text-green-600 mb-2">
                                    {{ prayerRequest.prayers_count }}
                                </div>
                                <p class="text-sm text-muted-foreground">
                                    {{ prayerRequest.prayers_count === 1 ? 'Person has' : 'People have' }} prayed
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Request Information -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg">Request Information</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3 text-sm">
                            <div>
                                <span class="font-medium">Privacy:</span>
                                <Badge :class="getPrivacyColor(prayerRequest.privacy)" class="ml-2">
                                    {{ getPrivacyLabel(prayerRequest.privacy) }}
                                </Badge>
                            </div>
                            <div>
                                <span class="font-medium">Status:</span>
                                <Badge :class="prayerRequest.is_answered ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'" class="ml-2">
                                    {{ prayerRequest.is_answered ? 'Answered' : 'Pending' }}
                                </Badge>
                            </div>
                            <div v-if="prayerRequest.is_urgent">
                                <span class="font-medium">Priority:</span>
                                <Badge class="bg-red-100 text-red-800 ml-2">
                                    Urgent
                                </Badge>
                            </div>
                            <div class="pt-2 border-t">
                                <span class="font-medium">Created:</span>
                                <p class="text-muted-foreground mt-1">{{ formatDate(prayerRequest.created_at) }}</p>
                            </div>
                            <div v-if="prayerRequest.is_answered && prayerRequest.answered_at" class="pt-2 border-t">
                                <span class="font-medium">Answered:</span>
                                <p class="text-muted-foreground mt-1">{{ formatDate(prayerRequest.answered_at) }}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Prayer Tips -->
                    <Card v-if="!prayerRequest.has_prayed">
                        <CardHeader>
                            <CardTitle class="text-lg">How to Pray</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-2 text-sm">
                            <p class="text-muted-foreground">
                                Take a moment to pray for this request. Consider:
                            </p>
                            <ul class="list-disc list-inside space-y-1 text-muted-foreground">
                                <li>Praying for God's will</li>
                                <li>Asking for peace and comfort</li>
                                <li>Seeking wisdom and guidance</li>
                                <li>Trusting in God's timing</li>
                            </ul>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </MemberLayout>
</template>
