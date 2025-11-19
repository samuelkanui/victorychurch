<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import MemberLayout from '@/layouts/MemberLayout.vue'
import { type BreadcrumbItemType } from '@/types'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { 
    Users, 
    UserCheck, 
    Clock, 
    UserX,
    Eye,
    Plus,
    LogOut
} from 'lucide-vue-next'

interface Group {
    id: number
    name: string
    description: string
    leader: {
        id: number
        name: string
    }
    pivot: {
        status: string
        role: string
        joined_at: string
        status_changed_at?: string
    }
}

interface Props {
    groupsByStatus: {
        approved: Group[]
        pending: Group[]
        rejected: Group[]
    }
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'My Groups' }
]

const getStatusColor = (status: string) => {
    switch (status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800'
        case 'approved': return 'bg-green-100 text-green-800'
        case 'rejected': return 'bg-red-100 text-red-800'
        default: return 'bg-gray-100 text-gray-800'
    }
}

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}
</script>

<template>
    <Head title="My Groups - Member Dashboard" />
    
    <MemberLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-green-600 to-green-800 bg-clip-text text-transparent">
                        My Groups
                    </h1>
                    <p class="text-muted-foreground">
                        Manage your group memberships and applications
                    </p>
                </div>
                <Button as-child>
                    <Link href="/member/groups/available">
                        <Plus class="h-4 w-4 mr-2" />
                        Browse Groups
                    </Link>
                </Button>
            </div>

            <!-- Group Statistics -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Active Groups</CardTitle>
                        <UserCheck class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">{{ groupsByStatus.approved.length }}</div>
                        <p class="text-xs text-muted-foreground">
                            Current memberships
                        </p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Pending Applications</CardTitle>
                        <Clock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-yellow-600">{{ groupsByStatus.pending.length }}</div>
                        <p class="text-xs text-muted-foreground">
                            Awaiting approval
                        </p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Rejected Applications</CardTitle>
                        <UserX class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-red-600">{{ groupsByStatus.rejected.length }}</div>
                        <p class="text-xs text-muted-foreground">
                            Not approved
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Groups Tabs -->
            <Tabs default-value="approved" class="w-full">
                <TabsList class="grid w-full grid-cols-3">
                    <TabsTrigger value="approved" class="flex items-center gap-2">
                        <UserCheck class="h-4 w-4" />
                        Active ({{ groupsByStatus.approved.length }})
                    </TabsTrigger>
                    <TabsTrigger value="pending" class="flex items-center gap-2">
                        <Clock class="h-4 w-4" />
                        Pending ({{ groupsByStatus.pending.length }})
                    </TabsTrigger>
                    <TabsTrigger value="rejected" class="flex items-center gap-2">
                        <UserX class="h-4 w-4" />
                        Rejected ({{ groupsByStatus.rejected.length }})
                    </TabsTrigger>
                </TabsList>

                <!-- Active Groups -->
                <TabsContent value="approved" class="space-y-4">
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <UserCheck class="h-5 w-5 text-green-600" />
                                Active Group Memberships
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div v-for="group in groupsByStatus.approved" :key="group.id" 
                                     class="flex items-center justify-between p-4 border rounded-lg">
                                    <div class="flex items-center gap-4">
                                        <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                                            <Users class="h-6 w-6 text-green-600" />
                                        </div>
                                        <div>
                                            <h3 class="font-semibold">{{ group.name }}</h3>
                                            <p class="text-sm text-muted-foreground">{{ group.description }}</p>
                                            <div class="flex items-center gap-4 mt-2">
                                                <span class="text-xs text-muted-foreground">
                                                    Led by {{ group.leader.name }}
                                                </span>
                                                <Badge :class="getStatusColor(group.pivot.status)">
                                                    {{ group.pivot.role }}
                                                </Badge>
                                                <span class="text-xs text-muted-foreground">
                                                    Joined {{ formatDate(group.pivot.joined_at) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <Button size="sm" variant="outline" as-child>
                                            <Link :href="`/member/groups/${group.id}`">
                                                <Eye class="h-4 w-4 mr-1" />
                                                View
                                            </Link>
                                        </Button>
                                        <Button size="sm" variant="destructive" as-child>
                                            <Link :href="`/member/groups/${group.id}/leave`" method="delete" as="button">
                                                <LogOut class="h-4 w-4 mr-1" />
                                                Leave
                                            </Link>
                                        </Button>
                                    </div>
                                </div>
                                <div v-if="groupsByStatus.approved.length === 0" class="text-center py-8 text-muted-foreground">
                                    <UserCheck class="h-12 w-12 mx-auto mb-4 opacity-50" />
                                    <p>You haven't joined any groups yet</p>
                                    <Button class="mt-4" as-child>
                                        <Link href="/member/groups/available">
                                            <Plus class="h-4 w-4 mr-2" />
                                            Browse Available Groups
                                        </Link>
                                    </Button>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </TabsContent>

                <!-- Pending Applications -->
                <TabsContent value="pending" class="space-y-4">
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Clock class="h-5 w-5 text-yellow-600" />
                                Pending Applications
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div v-for="group in groupsByStatus.pending" :key="group.id" 
                                     class="flex items-center justify-between p-4 border rounded-lg">
                                    <div class="flex items-center gap-4">
                                        <div class="h-12 w-12 rounded-full bg-yellow-100 flex items-center justify-center">
                                            <Clock class="h-6 w-6 text-yellow-600" />
                                        </div>
                                        <div>
                                            <h3 class="font-semibold">{{ group.name }}</h3>
                                            <p class="text-sm text-muted-foreground">{{ group.description }}</p>
                                            <div class="flex items-center gap-4 mt-2">
                                                <span class="text-xs text-muted-foreground">
                                                    Led by {{ group.leader.name }}
                                                </span>
                                                <Badge :class="getStatusColor(group.pivot.status)">
                                                    {{ group.pivot.status }}
                                                </Badge>
                                                <span class="text-xs text-muted-foreground">
                                                    Applied {{ formatDate(group.pivot.joined_at) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <Button size="sm" variant="outline" disabled>
                                            <Clock class="h-4 w-4 mr-1" />
                                            Awaiting Approval
                                        </Button>
                                    </div>
                                </div>
                                <div v-if="groupsByStatus.pending.length === 0" class="text-center py-8 text-muted-foreground">
                                    <Clock class="h-12 w-12 mx-auto mb-4 opacity-50" />
                                    <p>No pending applications</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </TabsContent>

                <!-- Rejected Applications -->
                <TabsContent value="rejected" class="space-y-4">
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <UserX class="h-5 w-5 text-red-600" />
                                Rejected Applications
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div v-for="group in groupsByStatus.rejected" :key="group.id" 
                                     class="flex items-center justify-between p-4 border rounded-lg">
                                    <div class="flex items-center gap-4">
                                        <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                                            <UserX class="h-6 w-6 text-red-600" />
                                        </div>
                                        <div>
                                            <h3 class="font-semibold">{{ group.name }}</h3>
                                            <p class="text-sm text-muted-foreground">{{ group.description }}</p>
                                            <div class="flex items-center gap-4 mt-2">
                                                <span class="text-xs text-muted-foreground">
                                                    Led by {{ group.leader.name }}
                                                </span>
                                                <Badge :class="getStatusColor(group.pivot.status)">
                                                    {{ group.pivot.status }}
                                                </Badge>
                                                <span class="text-xs text-muted-foreground">
                                                    Rejected {{ formatDate(group.pivot.status_changed_at || group.pivot.joined_at) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <Button size="sm" variant="outline" disabled>
                                            Application Rejected
                                        </Button>
                                    </div>
                                </div>
                                <div v-if="groupsByStatus.rejected.length === 0" class="text-center py-8 text-muted-foreground">
                                    <UserX class="h-12 w-12 mx-auto mb-4 opacity-50" />
                                    <p>No rejected applications</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </TabsContent>
            </Tabs>
        </div>
    </MemberLayout>
</template>
