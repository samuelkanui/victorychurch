<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Database, Mail, Shield, Server, HardDrive, CheckCircle } from 'lucide-vue-next'

interface Props {
    systemInfo: {
        app_name: string
        app_env: string
        app_debug: boolean
        app_url: string
        php_version: string
        laravel_version: string
        database_driver: string
        cache_driver: string
        queue_driver: string
        mail_driver: string
    }
    databaseStats: {
        total_users: number
        total_groups: number
        total_meetings: number
        total_prayers: number
        total_resources: number
        database_size: string
    }
    emailConfig: {
        driver: string
        host: string | null
        port: string | null
        from_address: string
        from_name: string
    }
    securityFeatures: {
        two_factor_enabled: boolean
        email_verification: boolean
        password_reset: boolean
        session_timeout: number
    }
}

defineProps<Props>()

const breadcrumbs = [
    { title: 'System Settings' }
]
</script>

<template>
    <Head title="System Settings - Admin Dashboard" />
    
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent">
                        System Settings
                    </h1>
                    <p class="text-sm sm:text-base text-muted-foreground">
                        System information and configuration overview
                    </p>
                </div>
            </div>

            <!-- System Information -->
            <div class="grid gap-6 lg:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Server class="h-5 w-5 text-purple-600" />
                            System Information
                        </CardTitle>
                        <CardDescription>
                            Application and server configuration
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <span class="text-sm font-medium">Application Name</span>
                                <span class="text-sm text-muted-foreground">{{ systemInfo.app_name }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <span class="text-sm font-medium">Environment</span>
                                <Badge :variant="systemInfo.app_env === 'production' ? 'default' : 'secondary'">
                                    {{ systemInfo.app_env }}
                                </Badge>
                            </div>
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <span class="text-sm font-medium">Debug Mode</span>
                                <Badge :variant="systemInfo.app_debug ? 'destructive' : 'outline'">
                                    {{ systemInfo.app_debug ? 'Enabled' : 'Disabled' }}
                                </Badge>
                            </div>
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <span class="text-sm font-medium">PHP Version</span>
                                <span class="text-sm text-muted-foreground">{{ systemInfo.php_version }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <span class="text-sm font-medium">Laravel Version</span>
                                <span class="text-sm text-muted-foreground">{{ systemInfo.laravel_version }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <span class="text-sm font-medium">Application URL</span>
                                <span class="text-sm text-muted-foreground truncate max-w-xs">{{ systemInfo.app_url }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Database class="h-5 w-5 text-blue-600" />
                            Database Statistics
                        </CardTitle>
                        <CardDescription>
                            Database records and storage information
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <span class="text-sm font-medium">Total Users</span>
                                <span class="text-sm font-bold text-purple-600">{{ databaseStats.total_users }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <span class="text-sm font-medium">Total Groups</span>
                                <span class="text-sm font-bold text-blue-600">{{ databaseStats.total_groups }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <span class="text-sm font-medium">Total Meetings</span>
                                <span class="text-sm font-bold text-green-600">{{ databaseStats.total_meetings }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <span class="text-sm font-medium">Prayer Requests</span>
                                <span class="text-sm font-bold text-orange-600">{{ databaseStats.total_prayers }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <span class="text-sm font-medium">Resources</span>
                                <span class="text-sm font-bold text-teal-600">{{ databaseStats.total_resources }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <span class="text-sm font-medium">Database Size</span>
                                <span class="text-sm text-muted-foreground">{{ databaseStats.database_size }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Configuration Details -->
            <div class="grid gap-6 lg:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Mail class="h-5 w-5 text-green-600" />
                            Email Configuration
                        </CardTitle>
                        <CardDescription>
                            Current email service settings
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <span class="text-sm font-medium">Mail Driver</span>
                                <Badge variant="outline">{{ emailConfig.driver }}</Badge>
                            </div>
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <span class="text-sm font-medium">SMTP Host</span>
                                <span class="text-sm text-muted-foreground">{{ emailConfig.host || 'Not configured' }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <span class="text-sm font-medium">SMTP Port</span>
                                <span class="text-sm text-muted-foreground">{{ emailConfig.port || 'N/A' }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <span class="text-sm font-medium">From Address</span>
                                <span class="text-sm text-muted-foreground truncate max-w-xs">{{ emailConfig.from_address }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <span class="text-sm font-medium">From Name</span>
                                <span class="text-sm text-muted-foreground">{{ emailConfig.from_name }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Shield class="h-5 w-5 text-red-600" />
                            Security Features
                        </CardTitle>
                        <CardDescription>
                            Active security and authentication features
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div class="flex items-center gap-2">
                                    <CheckCircle class="h-4 w-4 text-green-600" />
                                    <span class="text-sm font-medium">Two-Factor Authentication</span>
                                </div>
                                <Badge variant="outline" class="text-green-600">Active</Badge>
                            </div>
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div class="flex items-center gap-2">
                                    <CheckCircle class="h-4 w-4 text-green-600" />
                                    <span class="text-sm font-medium">Email Verification</span>
                                </div>
                                <Badge variant="outline" class="text-green-600">Active</Badge>
                            </div>
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div class="flex items-center gap-2">
                                    <CheckCircle class="h-4 w-4 text-green-600" />
                                    <span class="text-sm font-medium">Password Reset</span>
                                </div>
                                <Badge variant="outline" class="text-green-600">Active</Badge>
                            </div>
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <span class="text-sm font-medium">Session Timeout</span>
                                <span class="text-sm text-muted-foreground">{{ securityFeatures.session_timeout }} minutes</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- System Drivers -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <HardDrive class="h-5 w-5 text-indigo-600" />
                        System Drivers & Services
                    </CardTitle>
                    <CardDescription>
                        Active system drivers and service configurations
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
                        <div class="flex items-center justify-between p-3 border rounded-lg">
                            <span class="text-sm font-medium">Database</span>
                            <Badge variant="outline">{{ systemInfo.database_driver }}</Badge>
                        </div>
                        <div class="flex items-center justify-between p-3 border rounded-lg">
                            <span class="text-sm font-medium">Cache</span>
                            <Badge variant="outline">{{ systemInfo.cache_driver }}</Badge>
                        </div>
                        <div class="flex items-center justify-between p-3 border rounded-lg">
                            <span class="text-sm font-medium">Queue</span>
                            <Badge variant="outline">{{ systemInfo.queue_driver }}</Badge>
                        </div>
                        <div class="flex items-center justify-between p-3 border rounded-lg">
                            <span class="text-sm font-medium">Mail</span>
                            <Badge variant="outline">{{ systemInfo.mail_driver }}</Badge>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
