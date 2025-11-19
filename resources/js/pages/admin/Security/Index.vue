<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Shield, Users, Lock, Key, AlertTriangle, Eye, UserCheck, UserX, Clock, CheckCircle, XCircle, Crown } from 'lucide-vue-next'
import { Badge } from '@/components/ui/badge'

interface Props {
    stats: {
        admin_count: number
        leader_count: number
        member_count: number
        total_users: number
        inactive_users: number
        verified_users: number
        unverified_users: number
    }
    recentUsers: Array<{
        id: number
        name: string
        email: string
        role: string
        created_at: string
        is_active: boolean
        email_verified_at: string | null
    }>
    roleDistribution: {
        admin: number
        leader: number
        member: number
    }
    securityMetrics: {
        active_users: number
        inactive_users: number
        verified_rate: number
        unverified_count: number
        recent_signups: number
        recent_logins: number
    }
}

const props = defineProps<Props>()

const breadcrumbs = [
    { title: 'Security & Roles' }
]

const getSecurityStatus = () => {
    // Calculate security status based on verification rate
    const verificationRate = props.stats.total_users > 0 
        ? (props.stats.verified_users / props.stats.total_users) * 100 
        : 0
    
    if (verificationRate >= 90) return { status: 'Excellent', color: 'text-green-600' }
    if (verificationRate >= 70) return { status: 'Good', color: 'text-blue-600' }
    if (verificationRate >= 50) return { status: 'Fair', color: 'text-yellow-600' }
    return { status: 'Needs Attention', color: 'text-red-600' }
}

const securityStatus = getSecurityStatus()

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
    })
}

const getRoleIcon = (role: string) => {
    switch (role) {
        case 'admin': return Shield
        case 'leader': return Crown
        default: return Users
    }
}

const getRoleColor = (role: string) => {
    switch (role) {
        case 'admin': return 'bg-red-100 text-red-800'
        case 'leader': return 'bg-blue-100 text-blue-800'
        default: return 'bg-green-100 text-green-800'
    }
}
</script>

<template>
    <Head title="Security & Roles - Admin Dashboard" />
    
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent">
                        Security & Roles Management
                    </h1>
                    <p class="text-sm sm:text-base text-muted-foreground">
                        Manage user roles, permissions, and security settings
                    </p>
                </div>
            </div>

            <!-- Security Overview -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Admin Users</CardTitle>
                        <Shield class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-red-600">{{ stats.admin_count }}</div>
                        <p class="text-xs text-muted-foreground">System administrators</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Leader Users</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">{{ stats.leader_count }}</div>
                        <p class="text-xs text-muted-foreground">Group leaders</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Security Status</CardTitle>
                        <Lock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold" :class="securityStatus.color">{{ securityStatus.status }}</div>
                        <p class="text-xs text-muted-foreground">{{ stats.verified_users }}/{{ stats.total_users }} verified</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Role Management -->
            <div class="grid gap-6 lg:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Users class="h-5 w-5" />
                            Role Management
                        </CardTitle>
                        <CardDescription>
                            Manage user roles and permissions
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-full bg-red-100">
                                        <Shield class="h-4 w-4 text-red-600" />
                                    </div>
                                    <div>
                                        <p class="font-medium">Administrator</p>
                                        <p class="text-sm text-muted-foreground">Full system access</p>
                                    </div>
                                </div>
                                <span class="text-sm text-muted-foreground">{{ stats.admin_count }} {{ stats.admin_count === 1 ? 'user' : 'users' }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-full bg-blue-100">
                                        <Users class="h-4 w-4 text-blue-600" />
                                    </div>
                                    <div>
                                        <p class="font-medium">Leader</p>
                                        <p class="text-sm text-muted-foreground">Group management access</p>
                                    </div>
                                </div>
                                <span class="text-sm text-muted-foreground">{{ stats.leader_count }} {{ stats.leader_count === 1 ? 'user' : 'users' }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-full bg-green-100">
                                        <Users class="h-4 w-4 text-green-600" />
                                    </div>
                                    <div>
                                        <p class="font-medium">Member</p>
                                        <p class="text-sm text-muted-foreground">Standard member access</p>
                                    </div>
                                </div>
                                <span class="text-sm text-muted-foreground">{{ stats.member_count }} {{ stats.member_count === 1 ? 'user' : 'users' }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Lock class="h-5 w-5" />
                            Security Features
                        </CardTitle>
                        <CardDescription>
                            Current security implementations
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-full bg-green-100">
                                        <Key class="h-4 w-4 text-green-600" />
                                    </div>
                                    <div>
                                        <p class="font-medium">Two-Factor Authentication</p>
                                        <p class="text-sm text-muted-foreground">Available for all users</p>
                                    </div>
                                </div>
                                <span class="text-sm text-green-600">Active</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-full bg-green-100">
                                        <Shield class="h-4 w-4 text-green-600" />
                                    </div>
                                    <div>
                                        <p class="font-medium">Role-Based Access Control</p>
                                        <p class="text-sm text-muted-foreground">Middleware protection</p>
                                    </div>
                                </div>
                                <span class="text-sm text-green-600">Active</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-full bg-green-100">
                                        <Eye class="h-4 w-4 text-green-600" />
                                    </div>
                                    <div>
                                        <p class="font-medium">Email Verification</p>
                                        <p class="text-sm text-muted-foreground">Required for new accounts</p>
                                    </div>
                                </div>
                                <span class="text-sm text-green-600">Active</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Advanced Security Management -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Security Metrics -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <AlertTriangle class="h-5 w-5 text-orange-600" />
                            Security Metrics
                        </CardTitle>
                        <CardDescription>
                            System-wide security and user activity metrics
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-full bg-green-100">
                                        <UserCheck class="h-4 w-4 text-green-600" />
                                    </div>
                                    <div>
                                        <p class="font-medium">Email Verification Rate</p>
                                        <p class="text-sm text-muted-foreground">{{ securityMetrics.verified_rate }}% of users verified</p>
                                    </div>
                                </div>
                                <span class="text-lg font-bold" :class="{
                                    'text-green-600': securityMetrics.verified_rate >= 90,
                                    'text-blue-600': securityMetrics.verified_rate >= 70 && securityMetrics.verified_rate < 90,
                                    'text-yellow-600': securityMetrics.verified_rate >= 50 && securityMetrics.verified_rate < 70,
                                    'text-red-600': securityMetrics.verified_rate < 50
                                }">{{ securityMetrics.verified_rate }}%</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-full bg-blue-100">
                                        <Users class="h-4 w-4 text-blue-600" />
                                    </div>
                                    <div>
                                        <p class="font-medium">Recent Signups</p>
                                        <p class="text-sm text-muted-foreground">Last 7 days</p>
                                    </div>
                                </div>
                                <span class="text-lg font-bold text-blue-600">{{ securityMetrics.recent_signups }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-full bg-purple-100">
                                        <Clock class="h-4 w-4 text-purple-600" />
                                    </div>
                                    <div>
                                        <p class="font-medium">Recent Logins</p>
                                        <p class="text-sm text-muted-foreground">Last 7 days</p>
                                    </div>
                                </div>
                                <span class="text-lg font-bold text-purple-600">{{ securityMetrics.recent_logins }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-full bg-orange-100">
                                        <UserX class="h-4 w-4 text-orange-600" />
                                    </div>
                                    <div>
                                        <p class="font-medium">Unverified Users</p>
                                        <p class="text-sm text-muted-foreground">Require email verification</p>
                                    </div>
                                </div>
                                <span class="text-lg font-bold text-orange-600">{{ securityMetrics.unverified_count }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-full bg-red-100">
                                        <UserX class="h-4 w-4 text-red-600" />
                                    </div>
                                    <div>
                                        <p class="font-medium">Inactive Users</p>
                                        <p class="text-sm text-muted-foreground">Deactivated accounts</p>
                                    </div>
                                </div>
                                <span class="text-lg font-bold text-red-600">{{ securityMetrics.inactive_users }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent User Activity -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Clock class="h-5 w-5 text-blue-600" />
                            Recent User Activity
                        </CardTitle>
                        <CardDescription>
                            Latest user registrations and account status
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div v-if="recentUsers.length === 0" class="text-center py-8 text-muted-foreground">
                                <Users class="h-12 w-12 mx-auto mb-4 opacity-50" />
                                <p>No recent user activity</p>
                            </div>
                            
                            <div v-else v-for="user in recentUsers" :key="user.id" 
                                 class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 border rounded-lg hover:bg-muted/50 transition-colors">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center flex-shrink-0">
                                        <component :is="getRoleIcon(user.role)" class="h-5 w-5 text-white" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-medium truncate">{{ user.name }}</h3>
                                        <p class="text-sm text-muted-foreground truncate">{{ user.email }}</p>
                                        <div class="flex flex-wrap items-center gap-2 mt-1">
                                            <Badge :class="getRoleColor(user.role)" class="text-xs">
                                                {{ user.role }}
                                            </Badge>
                                            <Badge v-if="user.email_verified_at" variant="outline" class="text-xs text-green-600">
                                                <CheckCircle class="h-3 w-3 mr-1" />
                                                Verified
                                            </Badge>
                                            <Badge v-else variant="outline" class="text-xs text-orange-600">
                                                <XCircle class="h-3 w-3 mr-1" />
                                                Unverified
                                            </Badge>
                                            <Badge v-if="!user.is_active" variant="destructive" class="text-xs">
                                                Inactive
                                            </Badge>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-xs text-muted-foreground sm:text-right">
                                    {{ formatDate(user.created_at) }}
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AdminLayout>
</template>
