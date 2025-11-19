<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { User, Mail, Calendar, Shield, Users, Crown } from 'lucide-vue-next'

interface Props {
    user: {
        id: number
        name: string
        email: string
        role: string
        is_active: boolean
        email_verified_at: string | null
        last_login_at: string | null
        created_at: string
        groups?: Array<{
            id: number
            name: string
            pivot: {
                status: string
                role: string
            }
        }>
        ledGroups?: Array<{
            id: number
            name: string
        }>
    }
}

const props = defineProps<Props>()

const breadcrumbs = [
    { title: 'User Management', href: '/admin/users' },
    { title: props.user.name }
]

const getRoleIcon = (role: string) => {
    switch (role) {
        case 'admin': return Crown
        case 'leader': return Shield
        case 'member': return User
        default: return User
    }
}

const getRoleColor = (role: string) => {
    switch (role) {
        case 'admin': return 'bg-red-100 text-red-800'
        case 'leader': return 'bg-blue-100 text-blue-800'
        case 'member': return 'bg-green-100 text-green-800'
        default: return 'bg-gray-100 text-gray-800'
    }
}

const formatDate = (dateString: string | null) => {
    if (!dateString) return 'Never'
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
    })
}
</script>

<template>
    <Head :title="`${user.name} - User Management`" />
    
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent">
                        User Details
                    </h1>
                    <p class="text-sm sm:text-base text-muted-foreground">
                        View and manage user information
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button variant="outline" as-child class="w-full sm:w-auto">
                        <Link :href="`/admin/users/${user.id}/edit`">
                            Edit User
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- User Information -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Basic Info -->
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <component :is="getRoleIcon(user.role)" class="h-5 w-5" />
                            {{ user.name }}
                        </CardTitle>
                        <CardDescription>
                            User account information and status
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <h3 class="font-medium mb-1">Email Address</h3>
                                    <div class="flex items-center gap-2">
                                        <Mail class="h-4 w-4 text-muted-foreground" />
                                        <span>{{ user.email }}</span>
                                        <Badge v-if="user.email_verified_at" variant="outline" class="text-green-600">
                                            Verified
                                        </Badge>
                                        <Badge v-else variant="destructive">
                                            Unverified
                                        </Badge>
                                    </div>
                                </div>
                                
                                <div>
                                    <h3 class="font-medium mb-1">Role</h3>
                                    <Badge :class="getRoleColor(user.role)" class="flex items-center gap-1 w-fit">
                                        <component :is="getRoleIcon(user.role)" class="h-3 w-3" />
                                        {{ user.role.charAt(0).toUpperCase() + user.role.slice(1) }}
                                    </Badge>
                                </div>
                                
                                <div>
                                    <h3 class="font-medium mb-1">Account Status</h3>
                                    <Badge :variant="user.is_active ? 'outline' : 'destructive'">
                                        {{ user.is_active ? 'Active' : 'Inactive' }}
                                    </Badge>
                                </div>
                                
                                <div>
                                    <h3 class="font-medium mb-1">Member Since</h3>
                                    <div class="flex items-center gap-2">
                                        <Calendar class="h-4 w-4 text-muted-foreground" />
                                        <span>{{ formatDate(user.created_at) }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="font-medium mb-1">Last Login</h3>
                                <p class="text-muted-foreground">{{ formatDate(user.last_login_at) }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Quick Stats -->
                <Card>
                    <CardHeader>
                        <CardTitle>Quick Stats</CardTitle>
                        <CardDescription>
                            User activity overview
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Groups Joined</span>
                                <Badge variant="outline">{{ user.groups?.length || 0 }}</Badge>
                            </div>
                            
                            <div v-if="user.role === 'leader'" class="flex items-center justify-between">
                                <span class="text-sm">Groups Leading</span>
                                <Badge variant="outline">{{ user.ledGroups?.length || 0 }}</Badge>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Account Age</span>
                                <span class="text-sm text-muted-foreground">
                                    {{ Math.floor((new Date().getTime() - new Date(user.created_at).getTime()) / (1000 * 60 * 60 * 24)) }} days
                                </span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Groups -->
            <div v-if="user.groups?.length || user.ledGroups?.length" class="grid gap-6 lg:grid-cols-2">
                <!-- Member Groups -->
                <Card v-if="user.groups?.length">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Users class="h-5 w-5" />
                            Group Memberships
                        </CardTitle>
                        <CardDescription>
                            Groups where this user is a member
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div v-for="group in user.groups" :key="group.id" 
                                 class="flex items-center justify-between p-3 border rounded-lg">
                                <div>
                                    <h3 class="font-medium">{{ group.name }}</h3>
                                    <p class="text-sm text-muted-foreground">{{ group.pivot.role }}</p>
                                </div>
                                <Badge :variant="group.pivot.status === 'approved' ? 'outline' : 'secondary'">
                                    {{ group.pivot.status }}
                                </Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Led Groups -->
                <Card v-if="user.ledGroups?.length">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Shield class="h-5 w-5" />
                            Leading Groups
                        </CardTitle>
                        <CardDescription>
                            Groups where this user is the leader
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div v-for="group in user.ledGroups" :key="group.id" 
                                 class="flex items-center justify-between p-3 border rounded-lg">
                                <div>
                                    <h3 class="font-medium">{{ group.name }}</h3>
                                    <p class="text-sm text-muted-foreground">Group Leader</p>
                                </div>
                                <Button size="sm" variant="outline" as-child>
                                    <Link :href="`/admin/groups/${group.id}`">
                                        View Group
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AdminLayout>
</template>
