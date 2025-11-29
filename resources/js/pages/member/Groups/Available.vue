<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import MemberLayout from '@/layouts/MemberLayout.vue'
import { type BreadcrumbItemType } from '@/types'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { 
    Users, 
    UserPlus, 
    Eye,
    Search,
    Filter,
    MapPin,
    Calendar,
    User
} from 'lucide-vue-next'

interface Group {
    id: number
    name: string
    description: string
    location?: string
    meeting_day?: string
    meeting_time?: string
    max_members?: number
    current_members_count: number
    leader: {
        id: number
        name: string
    }
    is_member?: boolean
    membership_status?: string
}

interface Props {
    availableGroups: Group[]
    filters?: {
        search?: string
        location?: string
        meeting_day?: string
    }
}

defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'My Groups', href: '/member/groups' },
    { title: 'Available Groups' }
]

const getMembershipStatusColor = (status: string) => {
    switch (status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800'
        case 'approved': return 'bg-green-100 text-green-800'
        case 'rejected': return 'bg-red-100 text-red-800'
        default: return 'bg-gray-100 text-gray-800'
    }
}

const formatMeetingTime = (day: string, time: string) => {
    if (!day && !time) return 'Not specified'
    if (!time) return day
    if (!day) return time
    return `${day}s at ${time}`
}

const getCapacityColor = (current: number, max: number | null) => {
    if (!max) return 'text-gray-600'
    const percentage = (current / max) * 100
    if (percentage >= 90) return 'text-red-600'
    if (percentage >= 75) return 'text-orange-600'
    return 'text-green-600'
}

const isGroupFull = (current: number, max: number | null) => {
    if (!max) return false
    return current >= max
}
</script>

<template>
    <Head title="Available Groups - Member Dashboard" />
    
    <MemberLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-green-600 to-green-800 bg-clip-text text-transparent">
                        Available Groups
                    </h1>
                    <p class="text-muted-foreground">
                        Browse and join groups in your church community
                    </p>
                </div>
            </div>

            <!-- Search and Filters -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Search class="h-5 w-5" />
                        Find Groups
                    </CardTitle>
                    <CardDescription>
                        Search for groups by name, location, or meeting schedule
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Search Groups</label>
                            <div class="relative">
                                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                                <input 
                                    type="text" 
                                    placeholder="Search by name or description..."
                                    class="pl-10 w-full p-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500"
                                    :value="filters?.search || ''"
                                />
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Location</label>
                            <select class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                <option value="">All Locations</option>
                                <option value="main_building">Main Building</option>
                                <option value="fellowship_hall">Fellowship Hall</option>
                                <option value="youth_center">Youth Center</option>
                                <option value="online">Online</option>
                            </select>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Meeting Day</label>
                            <select class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                <option value="">Any Day</option>
                                <option value="sunday">Sunday</option>
                                <option value="monday">Monday</option>
                                <option value="tuesday">Tuesday</option>
                                <option value="wednesday">Wednesday</option>
                                <option value="thursday">Thursday</option>
                                <option value="friday">Friday</option>
                                <option value="saturday">Saturday</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex gap-2 mt-4">
                        <Button>
                            <Search class="h-4 w-4 mr-2" />
                            Search Groups
                        </Button>
                        <Button variant="outline">
                            <Filter class="h-4 w-4 mr-2" />
                            Clear Filters
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Available Groups -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Users class="h-5 w-5" />
                        Available Groups ({{ availableGroups?.length || 0 }})
                    </CardTitle>
                    <CardDescription>
                        Groups you can join in your church community
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="!availableGroups || availableGroups.length === 0" class="text-center py-12">
                        <Users class="h-16 w-16 mx-auto mb-4 text-gray-400 opacity-50" />
                        <h3 class="text-lg font-semibold mb-2">No Available Groups</h3>
                        <p class="text-muted-foreground">
                            There are no groups available to join at the moment. Check back later or contact your church administrator.
                        </p>
                    </div>

                    <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <div v-for="group in availableGroups" :key="group.id" 
                             class="border rounded-lg p-6 hover:shadow-md transition-shadow">
                            <div class="space-y-4">
                                <!-- Group Header -->
                                <div>
                                    <h3 class="font-semibold text-lg mb-2">{{ group.name }}</h3>
                                    <p class="text-sm text-muted-foreground line-clamp-3">
                                        {{ group.description }}
                                    </p>
                                </div>

                                <!-- Group Details -->
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center gap-2">
                                        <User class="h-4 w-4 text-gray-500" />
                                        <span>Led by {{ group.leader?.name || 'Unknown Leader' }}</span>
                                    </div>
                                    
                                    <div v-if="group.location" class="flex items-center gap-2">
                                        <MapPin class="h-4 w-4 text-gray-500" />
                                        <span>{{ group.location }}</span>
                                    </div>
                                    
                                    <div v-if="group.meeting_day || group.meeting_time" class="flex items-center gap-2">
                                        <Calendar class="h-4 w-4 text-gray-500" />
                                        <span>{{ formatMeetingTime(group.meeting_day || '', group.meeting_time || '') }}</span>
                                    </div>
                                    
                                    <div class="flex items-center gap-2">
                                        <Users class="h-4 w-4 text-gray-500" />
                                        <span :class="getCapacityColor(group.current_members_count, group.max_members)">
                                            {{ group.current_members_count }}{{ group.max_members ? `/${group.max_members}` : '' }} members
                                        </span>
                                    </div>
                                </div>

                                <!-- Membership Status -->
                                <div v-if="group.is_member" class="flex items-center gap-2">
                                    <Badge :class="getMembershipStatusColor(group.membership_status || 'unknown')">
                                        {{ (group.membership_status || 'unknown').charAt(0).toUpperCase() + (group.membership_status || 'unknown').slice(1) }}
                                    </Badge>
                                </div>

                                <!-- Actions -->
                                <div class="flex gap-2 pt-2">
                                    <Button variant="outline" size="sm" as-child class="flex-1">
                                        <Link :href="`/member/groups/${group.id}`">
                                            <Eye class="h-4 w-4 mr-1" />
                                            View Details
                                        </Link>
                                    </Button>
                                    
                                    <Button 
                                        v-if="!group.is_member && !isGroupFull(group.current_members_count, group.max_members)"
                                        size="sm" 
                                        as-child 
                                        class="flex-1"
                                    >
                                        <Link :href="`/member/groups/${group.id}/join`" method="post" as="button">
                                            <UserPlus class="h-4 w-4 mr-1" />
                                            Join Group
                                        </Link>
                                    </Button>
                                    
                                    <Button 
                                        v-else-if="!group.is_member && isGroupFull(group.current_members_count, group.max_members)"
                                        size="sm" 
                                        variant="outline" 
                                        disabled
                                        class="flex-1"
                                    >
                                        Group Full
                                    </Button>
                                    
                                    <Button 
                                        v-else-if="group.membership_status === 'pending'"
                                        size="sm" 
                                        variant="outline" 
                                        disabled
                                        class="flex-1"
                                    >
                                        Application Pending
                                    </Button>
                                    
                                    <Button 
                                        v-else-if="group.membership_status === 'approved'"
                                        size="sm" 
                                        variant="outline" 
                                        disabled
                                        class="flex-1"
                                    >
                                        Already Member
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </MemberLayout>
</template>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
