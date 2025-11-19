<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import LeaderLayout from '@/layouts/LeaderLayout.vue'
import { type BreadcrumbItemType } from '@/types'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { 
    Users, 
    UserCheck, 
    Clock, 
    Eye,
    Settings,
    Calendar,
    MapPin,
    AlertCircle
} from 'lucide-vue-next'

interface Props {
    groups: Array<{
        id: number
        name: string
        description: string
        is_active: boolean
        max_members: number
        meeting_schedule: string
        approved_members_count: number
        members_count: number
        members: Array<{
            id: number
            name: string
            pivot: {
                status: string
                joined_at: string
            }
        }>
        created_at: string
        updated_at: string
    }>
}

defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'My Groups' }
]
</script>

<template>
    <Head title="My Groups - Leader Dashboard" />
    
    <LeaderLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        My Groups
                    </h1>
                    <p class="text-muted-foreground">
                        Manage your Bible study groups and members
                    </p>
                </div>
            </div>

            <!-- Groups Grid -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="group in groups" :key="group.id" class="hover:shadow-lg transition-shadow">
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <CardTitle class="text-lg">{{ group.name }}</CardTitle>
                                <div class="flex items-center gap-2 mt-2">
                                    <Badge :class="group.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                                        {{ group.is_active ? 'Active' : 'Inactive' }}
                                    </Badge>
                                    <Badge variant="outline">
                                        {{ group.approved_members_count }}/{{ group.max_members }} members
                                    </Badge>
                                </div>
                            </div>
                            <Button variant="ghost" size="sm" as-child>
                                <Link :href="`/leader/groups/${group.id}`">
                                    <Eye class="h-4 w-4" />
                                </Link>
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <!-- Description -->
                            <p class="text-sm text-muted-foreground line-clamp-2">
                                {{ group.description || 'No description provided' }}
                            </p>

                            <!-- Meeting Schedule -->
                            <div v-if="group.meeting_schedule" class="flex items-center gap-2 text-sm text-muted-foreground">
                                <Calendar class="h-4 w-4" />
                                <span>{{ group.meeting_schedule }}</span>
                            </div>

                            <!-- Member Statistics -->
                            <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                                <div class="text-center">
                                    <div class="flex items-center justify-center gap-1 text-green-600">
                                        <UserCheck class="h-4 w-4" />
                                        <span class="font-semibold">{{ group.approved_members_count }}</span>
                                    </div>
                                    <p class="text-xs text-muted-foreground">Approved</p>
                                </div>
                                <div class="text-center">
                                    <div class="flex items-center justify-center gap-1 text-yellow-600">
                                        <Clock class="h-4 w-4" />
                                        <span class="font-semibold">{{ group.members.length }}</span>
                                    </div>
                                    <p class="text-xs text-muted-foreground">Pending</p>
                                </div>
                            </div>

                            <!-- Pending Members Alert -->
                            <div v-if="group.members.length > 0" class="flex items-center gap-2 p-2 bg-yellow-50 border border-yellow-200 rounded-md">
                                <AlertCircle class="h-4 w-4 text-yellow-600" />
                                <span class="text-sm text-yellow-800">
                                    {{ group.members.length }} member{{ group.members.length > 1 ? 's' : '' }} awaiting approval
                                </span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2 pt-2">
                                <Button variant="outline" size="sm" class="flex-1" as-child>
                                    <Link :href="`/leader/groups/${group.id}`">
                                        <Eye class="h-3 w-3 mr-1" />
                                        View Details
                                    </Link>
                                </Button>
                                <Button variant="outline" size="sm" class="flex-1" as-child>
                                    <Link :href="`/leader/groups/${group.id}/members`">
                                        <Users class="h-3 w-3 mr-1" />
                                        Members
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Empty State -->
            <div v-if="groups.length === 0" class="text-center py-12">
                <Users class="h-16 w-16 mx-auto mb-4 text-gray-400" />
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Groups Assigned</h3>
                <p class="text-gray-500 mb-4">
                    You haven't been assigned to lead any groups yet. Contact your administrator to get started.
                </p>
            </div>

            <!-- Quick Stats Summary -->
            <Card v-if="groups.length > 0">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Users class="h-5 w-5" />
                        Summary Statistics
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-4">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ groups.length }}</div>
                            <p class="text-sm text-blue-800">Total Groups</p>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">
                                {{ groups.filter(g => g.is_active).length }}
                            </div>
                            <p class="text-sm text-green-800">Active Groups</p>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">
                                {{ groups.reduce((sum, g) => sum + g.approved_members_count, 0) }}
                            </div>
                            <p class="text-sm text-purple-800">Total Members</p>
                        </div>
                        <div class="text-center p-4 bg-yellow-50 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600">
                                {{ groups.reduce((sum, g) => sum + g.members.length, 0) }}
                            </div>
                            <p class="text-sm text-yellow-800">Pending Requests</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LeaderLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
