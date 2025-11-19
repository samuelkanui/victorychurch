<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import MemberLayout from '@/layouts/MemberLayout.vue'
import { type BreadcrumbItemType } from '@/types'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { 
    User, 
    Edit, 
    Mail,
    Calendar,
    Users,
    MessageSquare,
    BookOpen,
    Shield,
    Phone,
    MapPin
} from 'lucide-vue-next'

interface Props {
    user?: {
        id: number
        name: string
        email: string
        phone?: string
        address?: string
        date_of_birth?: string
        bio?: string
        role: string
        email_verified_at?: string
        created_at: string
        updated_at: string
    }
    stats?: {
        groups_count: number
        prayer_requests_count: number
        assignments_completed: number
        meetings_attended: number
    }
    recentActivity?: Array<{
        id: string
        type: string
        description: string
        timestamp: string
    }>
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Profile' }
]

const formatDate = (dateString: string) => {
    if (!dateString) return 'Not provided'
    try {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        })
    } catch (error) {
        return 'Invalid date'
    }
}

const formatDateTime = (dateString: string) => {
    if (!dateString) return 'Not provided'
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

const getActivityIcon = (type: string) => {
    switch (type) {
        case 'group_join': return Users
        case 'prayer_request': return MessageSquare
        case 'assignment_submission': return BookOpen
        case 'meeting_rsvp': return Calendar
        default: return User
    }
}

const getActivityColor = (type: string) => {
    switch (type) {
        case 'group_join': return 'text-green-600'
        case 'prayer_request': return 'text-purple-600'
        case 'assignment_submission': return 'text-blue-600'
        case 'meeting_rsvp': return 'text-orange-600'
        default: return 'text-gray-600'
    }
}

const getRoleBadgeColor = (role: string) => {
    switch (role) {
        case 'admin': return 'bg-purple-100 text-purple-800'
        case 'leader': return 'bg-blue-100 text-blue-800'
        case 'member': return 'bg-green-100 text-green-800'
        default: return 'bg-gray-100 text-gray-800'
    }
}

const getRoleIcon = (role: string) => {
    switch (role) {
        case 'admin': return Shield
        case 'leader': return Shield
        case 'member': return User
        default: return User
    }
}
</script>

<template>
    <Head title="Profile - Member Dashboard" />
    
    <MemberLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-green-600 to-green-800 bg-clip-text text-transparent">
                        My Profile
                    </h1>
                    <p class="text-muted-foreground">
                        Manage your personal information and account settings
                    </p>
                </div>
                <Button as-child>
                    <Link href="/member/profile/edit">
                        <Edit class="h-4 w-4 mr-2" />
                        Edit Profile
                    </Link>
                </Button>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Profile Information -->
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <User class="h-5 w-5" />
                            Personal Information
                        </CardTitle>
                        <CardDescription>
                            Your basic profile information and contact details
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <!-- Basic Info -->
                        <div class="flex items-center gap-4">
                            <div class="h-16 w-16 rounded-full bg-green-100 flex items-center justify-center">
                                <User class="h-8 w-8 text-green-600" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold">{{ user?.name || 'Unknown User' }}</h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <Badge :class="getRoleBadgeColor(user?.role || 'member')">
                                        <component :is="getRoleIcon(user?.role || 'member')" class="h-3 w-3 mr-1" />
                                        {{ (user?.role || 'member').charAt(0).toUpperCase() + (user?.role || 'member').slice(1) }}
                                    </Badge>
                                    <Badge v-if="user?.email_verified_at" class="bg-green-100 text-green-800">
                                        Verified
                                    </Badge>
                                    <Badge v-else class="bg-yellow-100 text-yellow-800">
                                        Unverified
                                    </Badge>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700">Email Address</label>
                                <div class="flex items-center gap-2 p-3 bg-white border border-gray-200 rounded-lg">
                                    <Mail class="h-4 w-4 text-gray-500" />
                                    <span class="text-sm font-medium text-gray-900">{{ user?.email || 'Not provided' }}</span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700">Phone Number</label>
                                <div class="flex items-center gap-2 p-3 bg-white border border-gray-200 rounded-lg">
                                    <Phone class="h-4 w-4 text-gray-500" />
                                    <span class="text-sm font-medium text-gray-900">{{ user?.phone || 'Not provided' }}</span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700">Date of Birth</label>
                                <div class="flex items-center gap-2 p-3 bg-white border border-gray-200 rounded-lg">
                                    <Calendar class="h-4 w-4 text-gray-500" />
                                    <span class="text-sm font-medium text-gray-900">{{ formatDate(user?.date_of_birth || '') }}</span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700">Member Since</label>
                                <div class="flex items-center gap-2 p-3 bg-white border border-gray-200 rounded-lg">
                                    <Calendar class="h-4 w-4 text-gray-500" />
                                    <span class="text-sm font-medium text-gray-900">{{ formatDate(user?.created_at || '') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Address -->
                        <div v-if="user?.address" class="space-y-2">
                            <label class="text-sm font-medium text-gray-700">Address</label>
                            <div class="flex items-start gap-2 p-3 bg-white border border-gray-200 rounded-lg">
                                <MapPin class="h-4 w-4 text-gray-500 mt-0.5" />
                                <span class="text-sm font-medium text-gray-900">{{ user.address }}</span>
                            </div>
                        </div>

                        <!-- Bio -->
                        <div v-if="user?.bio" class="space-y-2">
                            <label class="text-sm font-medium text-gray-700">About Me</label>
                            <div class="p-3 bg-white border border-gray-200 rounded-lg">
                                <p class="text-sm font-medium text-gray-900">{{ user.bio }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Statistics & Activity -->
                <div class="space-y-6">
                    <!-- Activity Statistics -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg">Activity Summary</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <Users class="h-4 w-4 text-green-600" />
                                    <span class="text-sm">Groups Joined</span>
                                </div>
                                <span class="font-semibold">{{ stats?.groups_count || 0 }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <MessageSquare class="h-4 w-4 text-purple-600" />
                                    <span class="text-sm">Prayer Requests</span>
                                </div>
                                <span class="font-semibold">{{ stats?.prayer_requests_count || 0 }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <BookOpen class="h-4 w-4 text-blue-600" />
                                    <span class="text-sm">Assignments Completed</span>
                                </div>
                                <span class="font-semibold">{{ stats?.assignments_completed || 0 }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <Calendar class="h-4 w-4 text-orange-600" />
                                    <span class="text-sm">Meetings Attended</span>
                                </div>
                                <span class="font-semibold">{{ stats?.meetings_attended || 0 }}</span>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Recent Activity -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg">Recent Activity</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="!recentActivity || recentActivity.length === 0" class="text-center py-8 text-muted-foreground">
                                <User class="h-12 w-12 mx-auto mb-4 opacity-50" />
                                <p>No recent activity</p>
                            </div>
                            
                            <div v-else class="space-y-4">
                                <div v-for="activity in recentActivity.slice(0, 5)" :key="activity.id" 
                                     class="flex items-start gap-3">
                                    <div :class="['p-2 rounded-full', getActivityColor(activity.type)]">
                                        <component :is="getActivityIcon(activity.type)" class="h-4 w-4" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm">{{ activity.description }}</p>
                                        <p class="text-xs text-muted-foreground">{{ activity.timestamp }}</p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Quick Actions -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg">Quick Actions</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-2">
                            <Button variant="outline" class="w-full justify-start" as-child>
                                <Link href="/member/profile/edit">
                                    <Edit class="h-4 w-4 mr-2" />
                                    Edit Profile
                                </Link>
                            </Button>
                            
                            <Button variant="outline" class="w-full justify-start" as-child>
                                <Link href="/member/groups">
                                    <Users class="h-4 w-4 mr-2" />
                                    My Groups
                                </Link>
                            </Button>
                            
                            <Button variant="outline" class="w-full justify-start" as-child>
                                <Link href="/member/prayers">
                                    <MessageSquare class="h-4 w-4 mr-2" />
                                    Prayer Requests
                                </Link>
                            </Button>
                            
                            <Button variant="outline" class="w-full justify-start" as-child>
                                <Link href="/member/assignments">
                                    <BookOpen class="h-4 w-4 mr-2" />
                                    Assignments
                                </Link>
                            </Button>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </MemberLayout>
</template>
