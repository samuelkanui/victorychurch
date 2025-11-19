<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import LeaderLayout from '@/layouts/LeaderLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { 
    Users, 
    UserCheck, 
    Calendar, 
    BookOpen,
    MessageSquare,
    Settings
} from 'lucide-vue-next'

interface Props {
    group: {
        id: number
        name: string
        description: string
        is_active: boolean
        max_members: number
        meeting_schedule: string | null
        created_at: string
        approved_members_count: number
        members_count: number
        members: Array<{
            id: number
            name: string
            email: string
            pivot: {
                status: string
                joined_at: string
            }
        }>
    }
}

const props = defineProps<Props>()

const breadcrumbs = [
    { title: 'My Groups', href: '/leader/groups' },
    { title: props.group.name }
]

const getStatusColor = (status: string) => {
    switch (status) {
        case 'approved': return 'bg-green-100 text-green-800'
        case 'pending': return 'bg-yellow-100 text-yellow-800'
        case 'rejected': return 'bg-red-100 text-red-800'
        case 'banned': return 'bg-gray-100 text-gray-800'
        default: return 'bg-gray-100 text-gray-800'
    }
}

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const membersByStatus = {
    approved: props.group.members.filter(m => m.pivot.status === 'approved'),
    pending: props.group.members.filter(m => m.pivot.status === 'pending'),
    rejected: props.group.members.filter(m => m.pivot.status === 'rejected'),
    banned: props.group.members.filter(m => m.pivot.status === 'banned'),
}
</script>

<template>
    <Head :title="`${group.name} - My Groups`" />
    
    <LeaderLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        {{ group.name }}
                    </h1>
                    <p class="text-muted-foreground">
                        Manage your group members and activities
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button variant="outline" as-child>
                        <Link :href="`/leader/groups/${group.id}/members`">
                            <Users class="h-4 w-4 mr-2" />
                            Manage Members
                        </Link>
                    </Button>
                    <Button variant="outline">
                        <Settings class="h-4 w-4 mr-2" />
                        Group Settings
                    </Button>
                </div>
            </div>

            <!-- Group Overview -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Group Info -->
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Users class="h-5 w-5" />
                            Group Information
                        </CardTitle>
                        <CardDescription>
                            Basic details about your group
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div>
                                <h3 class="font-medium mb-1">Description</h3>
                                <p class="text-muted-foreground">{{ group.description || 'No description provided' }}</p>
                            </div>
                            
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <h3 class="font-medium mb-1">Meeting Schedule</h3>
                                    <div class="flex items-center gap-2">
                                        <Calendar class="h-4 w-4 text-muted-foreground" />
                                        <span>{{ group.meeting_schedule || 'Not scheduled' }}</span>
                                    </div>
                                </div>
                                
                                <div>
                                    <h3 class="font-medium mb-1">Status</h3>
                                    <Badge :variant="group.is_active ? 'default' : 'secondary'">
                                        {{ group.is_active ? 'Active' : 'Inactive' }}
                                    </Badge>
                                </div>
                                
                                <div>
                                    <h3 class="font-medium mb-1">Created</h3>
                                    <p class="text-muted-foreground">{{ formatDate(group.created_at) }}</p>
                                </div>
                                
                                <div>
                                    <h3 class="font-medium mb-1">Capacity</h3>
                                    <p class="text-muted-foreground">{{ group.approved_members_count }}/{{ group.max_members }} members</p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Member Statistics -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <UserCheck class="h-5 w-5" />
                            Member Statistics
                        </CardTitle>
                        <CardDescription>
                            Group membership overview
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Active Members</span>
                                <Badge class="bg-green-100 text-green-800">{{ membersByStatus.approved.length }}</Badge>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Pending Approval</span>
                                <Badge class="bg-yellow-100 text-yellow-800">{{ membersByStatus.pending.length }}</Badge>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Available Spots</span>
                                <span class="text-sm text-muted-foreground">
                                    {{ group.max_members - membersByStatus.approved.length }}
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Total Requests</span>
                                <span class="text-sm text-muted-foreground">{{ group.members_count }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Quick Actions -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardContent class="p-6">
                        <div class="flex items-center gap-4">
                            <div class="p-3 rounded-full bg-blue-100">
                                <Users class="h-6 w-6 text-blue-600" />
                            </div>
                            <div>
                                <h3 class="font-semibold">Manage Members</h3>
                                <p class="text-sm text-muted-foreground">Approve, reject, or ban members</p>
                            </div>
                        </div>
                        <Button class="w-full mt-4" variant="outline" as-child>
                            <Link :href="`/leader/groups/${group.id}/members`">
                                View Members
                            </Link>
                        </Button>
                    </CardContent>
                </Card>

                <Card>
                    <CardContent class="p-6">
                        <div class="flex items-center gap-4">
                            <div class="p-3 rounded-full bg-green-100">
                                <BookOpen class="h-6 w-6 text-green-600" />
                            </div>
                            <div>
                                <h3 class="font-semibold">Assignments</h3>
                                <p class="text-sm text-muted-foreground">Create and manage assignments</p>
                            </div>
                        </div>
                        <Button class="w-full mt-4" variant="outline" as-child>
                            <Link href="/leader/assignments">
                                View Assignments
                            </Link>
                        </Button>
                    </CardContent>
                </Card>

                <Card>
                    <CardContent class="p-6">
                        <div class="flex items-center gap-4">
                            <div class="p-3 rounded-full bg-purple-100">
                                <MessageSquare class="h-6 w-6 text-purple-600" />
                            </div>
                            <div>
                                <h3 class="font-semibold">Prayer Requests</h3>
                                <p class="text-sm text-muted-foreground">View group prayer requests</p>
                            </div>
                        </div>
                        <Button class="w-full mt-4" variant="outline" as-child>
                            <Link href="/leader/prayers">
                                View Prayers
                            </Link>
                        </Button>
                    </CardContent>
                </Card>

                <Card>
                    <CardContent class="p-6">
                        <div class="flex items-center gap-4">
                            <div class="p-3 rounded-full bg-orange-100">
                                <Calendar class="h-6 w-6 text-orange-600" />
                            </div>
                            <div>
                                <h3 class="font-semibold">Meetings</h3>
                                <p class="text-sm text-muted-foreground">Schedule group meetings</p>
                            </div>
                        </div>
                        <Button class="w-full mt-4" variant="outline" as-child>
                            <Link href="/leader/meetings">
                                Schedule Meeting
                            </Link>
                        </Button>
                    </CardContent>
                </Card>
            </div>

            <!-- Recent Members -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Users class="h-5 w-5" />
                        Recent Members
                    </CardTitle>
                    <CardDescription>
                        Latest member activity in this group
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="group.members.length === 0" class="text-center py-8 text-muted-foreground">
                        <Users class="h-12 w-12 mx-auto mb-4 opacity-50" />
                        <p>No members in this group yet</p>
                    </div>
                    
                    <div v-else class="space-y-4">
                        <div v-for="member in group.members.slice(0, 5)" :key="member.id" 
                             class="flex items-center justify-between p-3 border rounded-lg">
                            <div>
                                <h3 class="font-medium">{{ member.name }}</h3>
                                <p class="text-sm text-muted-foreground">{{ member.email }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <Badge :class="getStatusColor(member.pivot.status)" class="text-xs">
                                        {{ member.pivot.status }}
                                    </Badge>
                                    <span class="text-xs text-muted-foreground">
                                        Joined {{ formatDate(member.pivot.joined_at) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div v-if="group.members.length > 5" class="text-center">
                            <Button variant="outline" as-child>
                                <Link :href="`/leader/groups/${group.id}/members`">
                                    View All Members ({{ group.members.length }})
                                </Link>
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LeaderLayout>
</template>
