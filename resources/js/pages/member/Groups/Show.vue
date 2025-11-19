<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import MemberLayout from '@/layouts/MemberLayout.vue'
import { type BreadcrumbItemType } from '@/types'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { 
    Users, 
    ArrowLeft,
    Calendar,
    MapPin,
    User,
    Clock,
    CheckCircle,
    BookOpen,
    MessageSquare,
    UserPlus
} from 'lucide-vue-next'

interface Group {
    id: number
    name: string
    description: string
    category: string
    meeting_day: string | null
    meeting_time: string | null
    location: string | null
    max_members: number | null
    is_active: boolean
    leader: {
        id: number
        name: string
        email: string
    }
    members_count: number
    membership?: {
        status: string
        role: string
        joined_at: string
    }
}

interface Props {
    group: Group
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Groups', href: '/member/groups' },
    { title: props.group.name }
]

const getCategoryColor = (category: string) => {
    switch (category) {
        case 'bible_study': return 'bg-blue-100 text-blue-800'
        case 'prayer': return 'bg-purple-100 text-purple-800'
        case 'fellowship': return 'bg-green-100 text-green-800'
        case 'service': return 'bg-orange-100 text-orange-800'
        case 'youth': return 'bg-pink-100 text-pink-800'
        case 'worship': return 'bg-indigo-100 text-indigo-800'
        default: return 'bg-gray-100 text-gray-800'
    }
}

const getCategoryLabel = (category: string) => {
    if (!category) return 'Uncategorized'
    return category.split('_').map(word => 
        word.charAt(0).toUpperCase() + word.slice(1)
    ).join(' ')
}

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
    if (!dateString) return 'N/A'
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
</script>

<template>
    <Head :title="`${group.name} - Groups`" />
    
    <MemberLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-green-600 to-green-800 bg-clip-text text-transparent">
                        {{ group.name }}
                    </h1>
                    <p class="text-muted-foreground">
                        View group details and information
                    </p>
                </div>
                <Button variant="outline" @click="$inertia.visit('/member/groups')">
                    <ArrowLeft class="h-4 w-4 mr-2" />
                    Back to Groups
                </Button>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Group Details -->
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <CardTitle class="text-2xl mb-3">{{ group.name }}</CardTitle>
                                <div class="flex flex-wrap items-center gap-2 mb-4">
                                    <Badge :class="getCategoryColor(group.category)">
                                        {{ getCategoryLabel(group.category) }}
                                    </Badge>
                                    <Badge v-if="group.membership" :class="getStatusColor(group.membership.status)">
                                        {{ group.membership.status.charAt(0).toUpperCase() + group.membership.status.slice(1) }}
                                    </Badge>
                                    <Badge v-if="group.is_active" class="bg-green-100 text-green-800">
                                        Active
                                    </Badge>
                                    <Badge v-else class="bg-gray-100 text-gray-800">
                                        Inactive
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </CardHeader>
                    
                    <CardContent class="space-y-6">
                        <!-- Description -->
                        <div>
                            <h3 class="font-semibold mb-3">About This Group</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-gray-800 whitespace-pre-wrap">{{ group.description }}</p>
                            </div>
                        </div>

                        <!-- Meeting Information -->
                        <div v-if="group.meeting_day || group.meeting_time || group.location" class="border-t pt-6">
                            <h3 class="font-semibold mb-3">Meeting Information</h3>
                            <div class="grid gap-4 md:grid-cols-2">
                                <div v-if="group.meeting_day" class="flex items-center gap-3">
                                    <Calendar class="h-5 w-5 text-green-600" />
                                    <div>
                                        <p class="text-sm text-muted-foreground">Meeting Day</p>
                                        <p class="font-medium">{{ group.meeting_day }}</p>
                                    </div>
                                </div>
                                <div v-if="group.meeting_time" class="flex items-center gap-3">
                                    <Clock class="h-5 w-5 text-green-600" />
                                    <div>
                                        <p class="text-sm text-muted-foreground">Meeting Time</p>
                                        <p class="font-medium">{{ group.meeting_time }}</p>
                                    </div>
                                </div>
                                <div v-if="group.location" class="flex items-center gap-3 md:col-span-2">
                                    <MapPin class="h-5 w-5 text-green-600" />
                                    <div>
                                        <p class="text-sm text-muted-foreground">Location</p>
                                        <p class="font-medium">{{ group.location }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Leader Information -->
                        <div class="border-t pt-6">
                            <h3 class="font-semibold mb-3">Group Leader</h3>
                            <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                                <User class="h-8 w-8 text-blue-600" />
                                <div>
                                    <p class="font-medium text-blue-900">{{ group.leader.name }}</p>
                                    <p class="text-sm text-blue-700">{{ group.leader.email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Membership Info -->
                        <div v-if="group.membership" class="border-t pt-6">
                            <h3 class="font-semibold mb-3">Your Membership</h3>
                            <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-400">
                                <div class="grid gap-3">
                                    <div>
                                        <span class="text-sm text-green-700 font-medium">Status:</span>
                                        <Badge :class="getStatusColor(group.membership.status)" class="ml-2">
                                            {{ group.membership.status.charAt(0).toUpperCase() + group.membership.status.slice(1) }}
                                        </Badge>
                                    </div>
                                    <div>
                                        <span class="text-sm text-green-700 font-medium">Role:</span>
                                        <span class="ml-2 text-green-800">{{ group.membership.role }}</span>
                                    </div>
                                    <div>
                                        <span class="text-sm text-green-700 font-medium">Joined:</span>
                                        <span class="ml-2 text-green-800">{{ formatDate(group.membership.joined_at) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Stats -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg">Group Statistics</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <Users class="h-4 w-4 text-muted-foreground" />
                                    <span class="text-sm">Members</span>
                                </div>
                                <span class="font-semibold">
                                    {{ group.members_count }}{{ group.max_members ? `/${group.max_members}` : '' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <BookOpen class="h-4 w-4 text-muted-foreground" />
                                    <span class="text-sm">Category</span>
                                </div>
                                <Badge :class="getCategoryColor(group.category)" class="text-xs">
                                    {{ getCategoryLabel(group.category) }}
                                </Badge>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Actions -->
                    <Card v-if="!group.membership">
                        <CardHeader>
                            <CardTitle class="text-lg">Join This Group</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground mb-4">
                                Become a member of this group to participate in activities and events.
                            </p>
                            <Button 
                                class="w-full bg-green-600 hover:bg-green-700"
                                @click="$inertia.post(`/member/groups/${group.id}/join`)"
                            >
                                <UserPlus class="h-4 w-4 mr-2" />
                                Request to Join
                            </Button>
                        </CardContent>
                    </Card>

                    <Card v-else-if="group.membership.status === 'approved'">
                        <CardHeader>
                            <CardTitle class="text-lg">Group Actions</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <Button variant="outline" class="w-full" as-child>
                                <Link href="/member/assignments">
                                    <BookOpen class="h-4 w-4 mr-2" />
                                    View Assignments
                                </Link>
                            </Button>
                            <Button variant="outline" class="w-full" as-child>
                                <Link href="/member/meetings">
                                    <Calendar class="h-4 w-4 mr-2" />
                                    View Meetings
                                </Link>
                            </Button>
                            <Button 
                                variant="outline" 
                                class="w-full text-red-600 hover:text-red-700 hover:bg-red-50"
                                @click="$inertia.delete(`/member/groups/${group.id}/leave`)"
                            >
                                Leave Group
                            </Button>
                        </CardContent>
                    </Card>

                    <Card v-else-if="group.membership.status === 'pending'">
                        <CardHeader>
                            <CardTitle class="text-lg">Pending Approval</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground mb-4">
                                Your request to join this group is pending approval from the group leader.
                            </p>
                            <Button 
                                variant="outline" 
                                class="w-full"
                                @click="$inertia.delete(`/member/groups/${group.id}/leave`)"
                            >
                                Cancel Request
                            </Button>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </MemberLayout>
</template>
