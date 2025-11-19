<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import LeaderLayout from '@/layouts/LeaderLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { 
    MessageSquare, 
    Eye, 
    Heart,
    Lock,
    Globe,
    Users,
    Calendar,
    CheckCircle,
    AlertCircle,
    Clock
} from 'lucide-vue-next'

interface PrayerRequest {
    id: number
    title: string
    description: string
    is_anonymous: boolean
    privacy: string
    is_urgent: boolean
    status: string
    created_at: string
    updated_at: string
    user: {
        id: number
        name: string
        groups?: Array<{
            id: number
            name: string
        }>
    } | null
    group: {
        id: number
        name: string
    } | null
}

interface Props {
    prayerRequests: {
        data: PrayerRequest[]
        links: any[]
        meta: any
    }
}

const props = defineProps<Props>()

const breadcrumbs = [
    { title: 'Prayer Requests' }
]

const getPrivacyColor = (privacy: string) => {
    switch (privacy) {
        case 'public': return 'bg-green-100 text-green-800'
        case 'group': return 'bg-blue-100 text-blue-800'
        case 'private': return 'bg-gray-100 text-gray-800'
        default: return 'bg-gray-100 text-gray-800'
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
        case 'closed': return 'bg-gray-100 text-gray-800'
        default: return 'bg-gray-100 text-gray-800'
    }
}

const formatDate = (dateString: string) => {
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

const truncateText = (text: string, maxLength: number = 150) => {
    if (!text) return ''
    return text.length > maxLength ? text.substring(0, maxLength) + '...' : text
}
</script>

<template>
    <Head title="Prayer Requests - Leader Dashboard" />
    
    <LeaderLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        Prayer Requests
                    </h1>
                    <p class="text-muted-foreground">
                        View and moderate prayer requests from your group members
                    </p>
                </div>
            </div>

            <!-- Statistics -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Requests</CardTitle>
                        <MessageSquare class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-purple-600">{{ prayerRequests?.data?.length || 0 }}</div>
                        <p class="text-xs text-muted-foreground">All prayer requests</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Active</CardTitle>
                        <Clock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">
                            {{ prayerRequests?.data?.filter(p => p.status === 'active').length || 0 }}
                        </div>
                        <p class="text-xs text-muted-foreground">Awaiting prayer</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Urgent</CardTitle>
                        <AlertCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-red-600">
                            {{ prayerRequests?.data?.filter(p => p.is_urgent).length || 0 }}
                        </div>
                        <p class="text-xs text-muted-foreground">Need immediate prayer</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Answered</CardTitle>
                        <CheckCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">
                            {{ prayerRequests?.data?.filter(p => p.status === 'answered').length || 0 }}
                        </div>
                        <p class="text-xs text-muted-foreground">Prayers answered</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Prayer Requests List -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <MessageSquare class="h-5 w-5" />
                        Prayer Requests
                    </CardTitle>
                    <CardDescription>
                        Monitor and respond to prayer requests from your group members
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="!prayerRequests?.data || prayerRequests.data.length === 0" class="text-center py-12">
                        <MessageSquare class="h-16 w-16 mx-auto mb-4 text-gray-400 opacity-50" />
                        <h3 class="text-lg font-semibold mb-2">No Prayer Requests</h3>
                        <p class="text-muted-foreground">
                            No prayer requests have been submitted by your group members yet.
                        </p>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="prayer in prayerRequests.data" :key="prayer.id" 
                             class="p-6 border rounded-lg hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="font-semibold text-lg">{{ prayer.title }}</h3>
                                        <Badge v-if="prayer.is_urgent" class="bg-red-100 text-red-800">
                                            <AlertCircle class="h-3 w-3 mr-1" />
                                            Urgent
                                        </Badge>
                                        <Badge :class="getStatusColor(prayer.status)">
                                            {{ prayer.status.charAt(0).toUpperCase() + prayer.status.slice(1) }}
                                        </Badge>
                                    </div>
                                    
                                    <div class="flex items-center gap-4 text-sm text-muted-foreground mb-3">
                                        <div class="flex items-center gap-1">
                                            <component :is="getPrivacyIcon(prayer.privacy)" class="h-4 w-4" />
                                            <Badge :class="getPrivacyColor(prayer.privacy)" class="text-xs">
                                                {{ getPrivacyLabel(prayer.privacy) }}
                                            </Badge>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <Calendar class="h-4 w-4" />
                                            <span>{{ formatDate(prayer.created_at) }}</span>
                                        </div>
                                        <div v-if="!prayer.is_anonymous && prayer.user" class="flex items-center gap-1">
                                            <Users class="h-4 w-4" />
                                            <span>{{ prayer.user.name }}</span>
                                        </div>
                                        <div v-else class="flex items-center gap-1">
                                            <Users class="h-4 w-4" />
                                            <span>Anonymous</span>
                                        </div>
                                        <div v-if="prayer.user && prayer.user.groups && prayer.user.groups.length > 0" class="flex items-center gap-1">
                                            <Users class="h-4 w-4" />
                                            <span>{{ prayer.user.groups[0].name }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2 ml-4">
                                    <Button variant="outline" size="sm" as-child>
                                        <Link :href="`/leader/prayers/${prayer.id}`">
                                            <Eye class="h-4 w-4 mr-1" />
                                            View
                                        </Link>
                                    </Button>
                                    <Button v-if="prayer.status === 'active'" variant="outline" size="sm">
                                        <Heart class="h-4 w-4 mr-1" />
                                        Respond
                                    </Button>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-700">{{ truncateText(prayer.description) }}</p>
                                <Link v-if="prayer.description.length > 150" 
                                      :href="`/leader/prayers/${prayer.id}`" 
                                      class="text-blue-600 hover:underline text-sm mt-2 inline-block">
                                    Read more...
                                </Link>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LeaderLayout>
</template>
