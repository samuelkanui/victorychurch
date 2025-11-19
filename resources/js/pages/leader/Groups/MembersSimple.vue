<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import LeaderLayout from '@/layouts/LeaderLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Users } from 'lucide-vue-next'

interface Props {
    group: {
        id: number
        name: string
    }
    membersByStatus: {
        pending: Array<any>
        approved: Array<any>
        rejected: Array<any>
        banned: Array<any>
    }
}

const props = defineProps<Props>()

const breadcrumbs = [
    { title: 'My Groups', href: '/leader/groups' },
    { title: props.group.name, href: `/leader/groups/${props.group.id}` },
    { title: 'Members' }
]
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
            </div>

            <!-- Simple Member Stats -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Users class="h-5 w-5" />
                        Member Statistics
                    </CardTitle>
                </CardHeader>
                <CardContent>
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

            <!-- Placeholder for full functionality -->
            <Card>
                <CardContent class="text-center py-12">
                    <Users class="h-16 w-16 mx-auto mb-4 text-blue-600 opacity-50" />
                    <h3 class="text-lg font-semibold mb-2">Member Management Interface</h3>
                    <p class="text-muted-foreground">
                        This is a simplified version to test the page loading. The full member management interface will be restored once the compilation issue is resolved.
                    </p>
                </CardContent>
            </Card>
        </div>
    </LeaderLayout>
</template>
