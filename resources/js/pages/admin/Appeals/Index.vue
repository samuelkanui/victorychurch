<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { AlertTriangle, Clock, CheckCircle, XCircle, Users } from 'lucide-vue-next'

const breadcrumbs = [
    { title: 'Ban Appeals' }
]
</script>

<template>
    <Head title="Ban Appeals - Admin Dashboard" />
    
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent">
                        Ban Appeals Management
                    </h1>
                    <p class="text-muted-foreground">
                        Review and manage member ban appeals and disciplinary actions
                    </p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Pending Appeals</CardTitle>
                        <Clock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-yellow-600">0</div>
                        <p class="text-xs text-muted-foreground">Awaiting review</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Approved Appeals</CardTitle>
                        <CheckCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">0</div>
                        <p class="text-xs text-muted-foreground">Successfully resolved</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Rejected Appeals</CardTitle>
                        <XCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-red-600">0</div>
                        <p class="text-xs text-muted-foreground">Appeal denied</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Banned Members</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-purple-600">0</div>
                        <p class="text-xs text-muted-foreground">Currently banned</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Appeal Process -->
            <div class="grid gap-6 lg:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <AlertTriangle class="h-5 w-5" />
                            Appeal Process
                        </CardTitle>
                        <CardDescription>
                            How ban appeals are handled in the system
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="p-1 rounded-full bg-blue-100 mt-1">
                                    <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                </div>
                                <div>
                                    <p class="font-medium">1. Member Submits Appeal</p>
                                    <p class="text-sm text-muted-foreground">Banned member can submit a formal appeal with explanation</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="p-1 rounded-full bg-yellow-100 mt-1">
                                    <div class="w-2 h-2 bg-yellow-600 rounded-full"></div>
                                </div>
                                <div>
                                    <p class="font-medium">2. Admin Review</p>
                                    <p class="text-sm text-muted-foreground">Administrators review the appeal and evidence</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="p-1 rounded-full bg-green-100 mt-1">
                                    <div class="w-2 h-2 bg-green-600 rounded-full"></div>
                                </div>
                                <div>
                                    <p class="font-medium">3. Decision & Action</p>
                                    <p class="text-sm text-muted-foreground">Appeal is approved or rejected with notification</p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Users class="h-5 w-5" />
                            Moderation Guidelines
                        </CardTitle>
                        <CardDescription>
                            Community standards and moderation policies
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-sm font-medium text-red-800">Serious Violations</p>
                                <p class="text-xs text-red-600">Harassment, inappropriate content, or repeated policy violations</p>
                            </div>
                            
                            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-sm font-medium text-yellow-800">Minor Violations</p>
                                <p class="text-xs text-yellow-600">Spam, off-topic discussions, or minor disruptions</p>
                            </div>
                            
                            <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-sm font-medium text-green-800">Appeal Consideration</p>
                                <p class="text-xs text-green-600">Genuine remorse, understanding of violation, commitment to change</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Appeals List -->
            <Card>
                <CardHeader>
                    <CardTitle>Ban Appeals</CardTitle>
                    <CardDescription>
                        Ban appeal system is under development. Full appeal management will be available soon.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="text-center py-8 text-muted-foreground">
                        <AlertTriangle class="h-12 w-12 mx-auto mb-4 opacity-50" />
                        <p>No ban appeals at this time</p>
                        <p class="text-sm mt-2">The ban appeal system will allow members to formally request review of disciplinary actions.</p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
