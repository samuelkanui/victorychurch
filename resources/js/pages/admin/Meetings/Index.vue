<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Calendar, Clock, CheckCircle, XCircle, MapPin, Users } from 'lucide-vue-next'

interface Meeting {
    id: number
    title: string
    description: string | null
    scheduled_at: string
    location: string | null
    status: string
    group: {
        id: number
        name: string
    } | null
    creator: {
        id: number
        name: string
    }
}

interface Props {
    meetings: {
        data: Meeting[]
        links: any[]
        current_page: number
        last_page: number
    }
    stats: {
        total_meetings: number
        upcoming_meetings: number
        completed_meetings: number
        cancelled_meetings: number
    }
}

const props = defineProps<Props>()

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
    })
}

const getStatusVariant = (status: string) => {
    switch (status) {
        case 'completed':
            return 'default'
        case 'cancelled':
            return 'destructive'
        default:
            return 'secondary'
    }
}

const breadcrumbs = [
    { title: 'Meetings' }
]
</script>

<template>
    <Head title="Meetings - Admin Dashboard" />
    
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent">
                        Meetings Management
                    </h1>
                    <p class="text-muted-foreground">
                        Oversee all church meetings and events
                    </p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Meetings</CardTitle>
                        <Calendar class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-purple-600">{{ stats.total_meetings }}</div>
                        <p class="text-xs text-muted-foreground">All meetings</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Upcoming</CardTitle>
                        <Clock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">{{ stats.upcoming_meetings }}</div>
                        <p class="text-xs text-muted-foreground">Scheduled meetings</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Completed</CardTitle>
                        <CheckCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">{{ stats.completed_meetings }}</div>
                        <p class="text-xs text-muted-foreground">Finished meetings</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Cancelled</CardTitle>
                        <XCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-red-600">{{ stats.cancelled_meetings }}</div>
                        <p class="text-xs text-muted-foreground">Cancelled meetings</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Meetings List -->
            <Card>
                <CardHeader>
                    <CardTitle>All Meetings</CardTitle>
                    <CardDescription>
                        View and manage all church meetings and events
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="meetings.data.length > 0" class="space-y-4">
                        <div 
                            v-for="meeting in meetings.data" 
                            :key="meeting.id"
                            class="border rounded-lg p-4 hover:bg-muted/50 transition-colors"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 space-y-2">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-semibold text-lg">{{ meeting.title }}</h3>
                                        <Badge :variant="getStatusVariant(meeting.status)">
                                            {{ meeting.status }}
                                        </Badge>
                                    </div>
                                    
                                    <p v-if="meeting.description" class="text-sm text-muted-foreground">
                                        {{ meeting.description }}
                                    </p>
                                    
                                    <div class="flex flex-wrap items-center gap-4 text-sm text-muted-foreground">
                                        <span class="flex items-center gap-1">
                                            <Clock class="h-4 w-4" />
                                            {{ formatDate(meeting.scheduled_at) }}
                                        </span>
                                        
                                        <span v-if="meeting.location" class="flex items-center gap-1">
                                            <MapPin class="h-4 w-4" />
                                            {{ meeting.location }}
                                        </span>
                                        
                                        <span v-if="meeting.group" class="flex items-center gap-1">
                                            <Users class="h-4 w-4" />
                                            {{ meeting.group.name }}
                                        </span>
                                    </div>
                                    
                                    <div class="text-xs text-muted-foreground">
                                        Created by {{ meeting.creator.name }}
                                    </div>
                                </div>
                                
                                <Link :href="`/admin/meetings/${meeting.id}`">
                                    <Button variant="outline" size="sm">
                                        View Details
                                    </Button>
                                </Link>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="meetings.last_page > 1" class="flex items-center justify-center gap-2 pt-4">
                            <Link
                                v-for="(link, index) in meetings.links"
                                :key="index"
                                :href="link.url"
                                :class="[
                                    'px-3 py-1 rounded',
                                    link.active ? 'bg-primary text-primary-foreground' : 'hover:bg-muted',
                                    !link.url && 'opacity-50 cursor-not-allowed'
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                    
                    <div v-else class="text-center py-8 text-muted-foreground">
                        <Calendar class="h-12 w-12 mx-auto mb-4 opacity-50" />
                        <p>No meetings found</p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
