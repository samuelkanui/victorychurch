<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import LeaderLayout from '@/layouts/LeaderLayout.vue'
import { type BreadcrumbItemType } from '@/types'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import Tabs from '@/components/ui/Tabs.vue'
import TabsContent from '@/components/ui/TabsContent.vue'
import TabsList from '@/components/ui/TabsList.vue'
import TabsTrigger from '@/components/ui/TabsTrigger.vue'
import { 
    Users, 
    UserCheck, 
    Clock, 
    UserX,
    Ban,
    CheckCircle,
    XCircle,
    RotateCcw,
    Trash2,
    Mail,
    Calendar
} from 'lucide-vue-next'

interface Member {
    id: number
    name: string
    email: string
    pivot: {
        status: string
        joined_at: string
        status_changed_at?: string
    }
}

interface Props {
    group: {
        id: number
        name: string
        description: string
        is_active: boolean
        max_members: number
        approved_members_count: number
    }
    membersByStatus: {
        pending: Member[]
        approved: Member[]
        rejected: Member[]
        banned: Member[]
    }
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'My Groups', href: '/leader/groups' },
    { title: props.group.name, href: `/leader/groups/${props.group.id}` },
    { title: 'Members' }
]

const approveMember = (userId: number) => {
    router.post(`/leader/groups/${props.group.id}/members/${userId}/approve`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Handle success
        }
    })
}

const rejectMember = (userId: number) => {
    router.post(`/leader/groups/${props.group.id}/members/${userId}/reject`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Handle success
        }
    })
}

const banMember = (userId: number) => {
    if (confirm('Are you sure you want to ban this member? This action can be undone later.')) {
        router.post(`/leader/groups/${props.group.id}/members/${userId}/ban`, {}, {
            preserveScroll: true,
            onSuccess: () => {
                // Handle success
            }
        })
    }
}

const unbanMember = (userId: number) => {
    router.post(`/leader/groups/${props.group.id}/members/${userId}/unban`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Handle success
        }
    })
}

const removeMember = (userId: number) => {
    if (confirm('Are you sure you want to remove this member from the group? This action cannot be undone.')) {
        router.delete(`/leader/groups/${props.group.id}/members/${userId}`, {
            preserveScroll: true,
            onSuccess: () => {
                // Handle success
            }
        })
    }
}

const getStatusColor = (status: string) => {
    switch (status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800'
        case 'approved': return 'bg-green-100 text-green-800'
        case 'rejected': return 'bg-red-100 text-red-800'
        case 'banned': return 'bg-gray-100 text-gray-800'
        default: return 'bg-gray-100 text-gray-800'
    }
}

const formatDate = (dateString: string) => {
    if (!dateString) return 'Unknown date'
    
    try {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        })
    } catch (error) {
        return 'Invalid date'
    }
}
</script>

<template>
    <Head :title="`${group.name} Members - Leader Dashboard`" />
    
    <LeaderLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        {{ group.name }} Members
                    </h1>
                    <p class="text-muted-foreground">
                        Manage group membership and member requests
                    </p>
                </div>
                <Button variant="outline" as-child>
                    <Link :href="`/leader/groups/${group.id}`">
                        Back to Group
                    </Link>
                </Button>
            </div>

            <!-- Group Info -->
            <Card>
                <CardContent class="pt-6">
                    <div class="grid gap-4 md:grid-cols-4">
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ membersByStatus.approved.length }}</div>
                            <p class="text-sm text-green-800">Approved Members</p>
                        </div>
                        <div class="text-center p-4 bg-yellow-50 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600">{{ membersByStatus.pending.length }}</div>
                            <p class="text-sm text-yellow-800">Pending Requests</p>
                        </div>
                        <div class="text-center p-4 bg-red-50 rounded-lg">
                            <div class="text-2xl font-bold text-red-600">{{ membersByStatus.rejected.length }}</div>
                            <p class="text-sm text-red-800">Rejected</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-gray-600">{{ membersByStatus.banned.length }}</div>
                            <p class="text-sm text-gray-800">Banned</p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Members Tabs -->
            <Tabs default-value="pending" class="w-full">
                <TabsList class="grid w-full grid-cols-4">
                    <TabsTrigger value="pending" class="flex items-center gap-2">
                        <Clock class="h-4 w-4" />
                        Pending ({{ membersByStatus.pending.length }})
                    </TabsTrigger>
                    <TabsTrigger value="approved" class="flex items-center gap-2">
                        <UserCheck class="h-4 w-4" />
                        Approved ({{ membersByStatus.approved.length }})
                    </TabsTrigger>
                    <TabsTrigger value="rejected" class="flex items-center gap-2">
                        <UserX class="h-4 w-4" />
                        Rejected ({{ membersByStatus.rejected.length }})
                    </TabsTrigger>
                    <TabsTrigger value="banned" class="flex items-center gap-2">
                        <Ban class="h-4 w-4" />
                        Banned ({{ membersByStatus.banned.length }})
                    </TabsTrigger>
                </TabsList>

                <!-- Pending Members -->
                <TabsContent value="pending" class="space-y-4">
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Clock class="h-5 w-5 text-yellow-600" />
                                Pending Membership Requests
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div v-for="member in membersByStatus.pending" :key="member.id" 
                                     class="flex items-center justify-between p-4 border rounded-lg">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                            <Users class="h-5 w-5 text-yellow-600" />
                                        </div>
                                        <div>
                                            <h3 class="font-medium">{{ member.name }}</h3>
                                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                                <Mail class="h-3 w-3" />
                                                <span>{{ member.email }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-muted-foreground mt-1">
                                                <Calendar class="h-3 w-3" />
                                                <span>Requested {{ formatDate(member.pivot?.joined_at || '') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <Button size="sm" @click="approveMember(member.id)" class="bg-green-600 hover:bg-green-700">
                                            <CheckCircle class="h-4 w-4 mr-1" />
                                            Approve
                                        </Button>
                                        <Button size="sm" variant="outline" @click="rejectMember(member.id)">
                                            <XCircle class="h-4 w-4 mr-1" />
                                            Reject
                                        </Button>
                                    </div>
                                </div>
                                <div v-if="membersByStatus.pending.length === 0" class="text-center py-8 text-muted-foreground">
                                    <Clock class="h-12 w-12 mx-auto mb-4 opacity-50" />
                                    <p>No pending membership requests</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </TabsContent>

                <!-- Approved Members -->
                <TabsContent value="approved" class="space-y-4">
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <UserCheck class="h-5 w-5 text-green-600" />
                                Approved Members
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div v-for="member in membersByStatus.approved" :key="member.id" 
                                     class="flex items-center justify-between p-4 border rounded-lg">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                            <UserCheck class="h-5 w-5 text-green-600" />
                                        </div>
                                        <div>
                                            <h3 class="font-medium">{{ member.name }}</h3>
                                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                                <Mail class="h-3 w-3" />
                                                <span>{{ member.email }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-muted-foreground mt-1">
                                                <Calendar class="h-3 w-3" />
                                                <span>Joined {{ formatDate(member.pivot?.joined_at || '') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <Button size="sm" variant="outline" @click="banMember(member.id)">
                                            <Ban class="h-4 w-4 mr-1" />
                                            Ban
                                        </Button>
                                        <Button size="sm" variant="destructive" @click="removeMember(member.id)">
                                            <Trash2 class="h-4 w-4 mr-1" />
                                            Remove
                                        </Button>
                                    </div>
                                </div>
                                <div v-if="membersByStatus.approved.length === 0" class="text-center py-8 text-muted-foreground">
                                    <UserCheck class="h-12 w-12 mx-auto mb-4 opacity-50" />
                                    <p>No approved members yet</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </TabsContent>

                <!-- Rejected Members -->
                <TabsContent value="rejected" class="space-y-4">
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <UserX class="h-5 w-5 text-red-600" />
                                Rejected Members
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div v-for="member in membersByStatus.rejected" :key="member.id" 
                                     class="flex items-center justify-between p-4 border rounded-lg">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                            <UserX class="h-5 w-5 text-red-600" />
                                        </div>
                                        <div>
                                            <h3 class="font-medium">{{ member.name }}</h3>
                                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                                <Mail class="h-3 w-3" />
                                                <span>{{ member.email }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-muted-foreground mt-1">
                                                <Calendar class="h-3 w-3" />
                                                <span>Rejected {{ formatDate(member.pivot?.status_changed_at || member.pivot?.joined_at || '') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <Button size="sm" @click="approveMember(member.id)" class="bg-green-600 hover:bg-green-700">
                                            <CheckCircle class="h-4 w-4 mr-1" />
                                            Approve
                                        </Button>
                                        <Button size="sm" variant="outline" @click="removeMember(member.id)">
                                            <Trash2 class="h-4 w-4 mr-1" />
                                            Remove
                                        </Button>
                                    </div>
                                </div>
                                <div v-if="membersByStatus.rejected.length === 0" class="text-center py-8 text-muted-foreground">
                                    <UserX class="h-12 w-12 mx-auto mb-4 opacity-50" />
                                    <p>No rejected members</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </TabsContent>

                <!-- Banned Members -->
                <TabsContent value="banned" class="space-y-4">
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Ban class="h-5 w-5 text-gray-600" />
                                Banned Members
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div v-for="member in membersByStatus.banned" :key="member.id" 
                                     class="flex items-center justify-between p-4 border rounded-lg">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                            <Ban class="h-5 w-5 text-gray-600" />
                                        </div>
                                        <div>
                                            <h3 class="font-medium">{{ member.name }}</h3>
                                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                                <Mail class="h-3 w-3" />
                                                <span>{{ member.email }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-muted-foreground mt-1">
                                                <Calendar class="h-3 w-3" />
                                                <span>Banned {{ formatDate(member.pivot?.status_changed_at || member.pivot?.joined_at || '') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <Button size="sm" @click="unbanMember(member.id)" class="bg-blue-600 hover:bg-blue-700">
                                            <RotateCcw class="h-4 w-4 mr-1" />
                                            Unban
                                        </Button>
                                        <Button size="sm" variant="outline" @click="removeMember(member.id)">
                                            <Trash2 class="h-4 w-4 mr-1" />
                                            Remove
                                        </Button>
                                    </div>
                                </div>
                                <div v-if="membersByStatus.banned.length === 0" class="text-center py-8 text-muted-foreground">
                                    <Ban class="h-12 w-12 mx-auto mb-4 opacity-50" />
                                    <p>No banned members</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </TabsContent>
            </Tabs>
        </div>
    </LeaderLayout>
</template>
