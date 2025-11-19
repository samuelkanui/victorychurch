<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { 
    BookOpen, 
    Users, 
    Crown, 
    Calendar, 
    Mail, 
    Edit,
    UserCheck,
    Clock,
    UserX,
    Shield
} from 'lucide-vue-next'

interface Props {
    group: {
        id: number
        name: string
        description: string
        leader: {
            id: number
            name: string
            email: string
        }
        is_active: boolean
        max_members: number
        meeting_schedule: string | null
        created_at: string
        members: Array<{
            id: number
            name: string
            email: string
            pivot: {
                status: string
                role: string
                joined_at: string
                status_changed_at: string | null
            }
        }>
    }
}

const props = defineProps<Props>()

const breadcrumbs = [
    { title: 'Group Management', href: '/admin/groups' },
    { title: props.group.name }
]

const getStatusColor = (status: string) => {
    switch (status) {
        case 'approved': return 'bg-green-100 text-green-800'
        case 'pending': return 'bg-yellow-100 text-yellow-800'
        case 'rejected': return 'bg-red-100 text-red-800'
        case 'banned': return 'bg-red-100 text-red-800'
        default: return 'bg-gray-100 text-gray-800'
    }
}

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'approved': return UserCheck
        case 'pending': return Clock
        case 'rejected': return UserX
        case 'banned': return UserX
        default: return Users
    }
}

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
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
    <Head :title="`${group.name} - Group Management`" />
    
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent">
                        {{ group.name }}
                    </h1>
                    <p class="text-sm sm:text-base text-muted-foreground">
                        Group details and member management
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button variant="outline" as-child class="w-full sm:w-auto">
                        <Link :href="`/admin/groups/${group.id}/edit`">
                            <Edit class="h-4 w-4 mr-2" />
                            Edit Group
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Group Information -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Basic Info -->
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <BookOpen class="h-5 w-5" />
                            Group Information
                        </CardTitle>
                        <CardDescription>
                            Basic details about this group
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
                                    <h3 class="font-medium mb-1">Group Leader</h3>
                                    <div class="flex items-center gap-2">
                                        <Crown class="h-4 w-4 text-blue-600" />
                                        <div>
                                            <p class="font-medium">{{ group.leader.name }}</p>
                                            <p class="text-sm text-muted-foreground flex items-center gap-1">
                                                <Mail class="h-3 w-3" />
                                                {{ group.leader.email }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
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
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Member Stats -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Users class="h-5 w-5" />
                            Member Statistics
                        </CardTitle>
                        <CardDescription>
                            Group membership overview
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Total Capacity</span>
                                <Badge variant="outline">{{ group.max_members }}</Badge>
                            </div>
                            
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
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Members List -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Users class="h-5 w-5" />
                        Group Members
                    </CardTitle>
                    <CardDescription>
                        All members and their status in this group
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="group.members.length === 0" class="text-center py-8 text-muted-foreground">
                        <Users class="h-12 w-12 mx-auto mb-4 opacity-50" />
                        <p>No members in this group yet</p>
                    </div>
                    
                    <div v-else class="space-y-4">
                        <div v-for="member in group.members" :key="member.id" 
                             class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-4 border rounded-lg">
                            <div class="flex items-start sm:items-center gap-3 flex-1">
                                <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                                    <component :is="getStatusIcon(member.pivot.status)" class="h-5 w-5 text-purple-600" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-medium truncate">{{ member.name }}</h3>
                                    <p class="text-sm text-muted-foreground truncate">{{ member.email }}</p>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <Badge :class="getStatusColor(member.pivot.status)" class="text-xs">
                                            {{ member.pivot.status }}
                                        </Badge>
                                        <span class="text-xs text-muted-foreground">
                                            {{ member.pivot.role }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col sm:text-right gap-2">
                                <p class="text-xs sm:text-sm text-muted-foreground">
                                    Joined {{ formatDate(member.pivot.joined_at) }}
                                </p>
                                <Button size="sm" variant="outline" as-child class="w-full sm:w-auto">
                                    <Link :href="`/admin/users/${member.id}`">
                                        View Profile
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
