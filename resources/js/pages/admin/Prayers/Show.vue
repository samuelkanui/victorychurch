<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import { Badge } from '@/components/ui/badge'
import { MessageSquare, User, Clock, Shield, Save, Trash2 } from 'lucide-vue-next'

interface Props {
    prayer: {
        id: number
        title: string
        description: string
        status: string
        privacy: string
        is_anonymous: boolean
        admin_notes?: string
        created_at: string
        user: {
            id: number
            name: string
        }
    }
}

const props = defineProps<Props>()

const form = useForm({
    status: props.prayer.status,
    admin_notes: props.prayer.admin_notes || ''
})

const handleUpdate = () => {
    form.put(`/admin/prayers/${props.prayer.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            // Success message will be shown via flash
        }
    })
}

const handleDelete = () => {
    if (confirm('Are you sure you want to delete this prayer request? This action cannot be undone.')) {
        router.delete(`/admin/prayers/${props.prayer.id}`)
    }
}

const breadcrumbs = [
    { title: 'Prayer Requests', href: '/admin/prayers' },
    { title: props.prayer.title }
]

const formatDate = (dateString: string) => {
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
    <Head :title="`${prayer.title} - Prayer Requests`" />
    
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent">
                        Prayer Request Details
                    </h1>
                    <p class="text-muted-foreground">
                        Review and manage prayer request
                    </p>
                </div>
            </div>

            <!-- Prayer Request Details -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <MessageSquare class="h-5 w-5" />
                        {{ prayer.title }}
                    </CardTitle>
                    <CardDescription>
                        <div class="flex items-center gap-4 text-sm">
                            <span class="flex items-center gap-1">
                                <User class="h-4 w-4" />
                                {{ prayer.is_anonymous ? 'Anonymous' : prayer.user.name }}
                            </span>
                            <span class="flex items-center gap-1">
                                <Clock class="h-4 w-4" />
                                {{ formatDate(prayer.created_at) }}
                            </span>
                            <span class="flex items-center gap-1">
                                <Shield class="h-4 w-4" />
                                {{ prayer.privacy }} • {{ prayer.status }}
                            </span>
                        </div>
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-6">
                        <div>
                            <h3 class="font-medium mb-2">Prayer Request</h3>
                            <p class="text-muted-foreground whitespace-pre-wrap">{{ prayer.description }}</p>
                        </div>
                        
                        <div class="border-t pt-6">
                            <h3 class="font-medium mb-4">Manage Prayer Request</h3>
                            <form @submit.prevent="handleUpdate" class="space-y-4">
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div class="space-y-2">
                                        <Label for="status">Status</Label>
                                        <select
                                            id="status"
                                            v-model="form.status"
                                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            <option value="pending">Pending</option>
                                            <option value="answered">Answered</option>
                                            <option value="closed">Closed</option>
                                        </select>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <Label>Current Status</Label>
                                        <div class="flex items-center h-10">
                                            <Badge :variant="prayer.status === 'answered' ? 'default' : prayer.status === 'pending' ? 'secondary' : 'outline'">
                                                {{ prayer.status }}
                                            </Badge>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="admin_notes">Admin Notes (Internal)</Label>
                                    <textarea
                                        id="admin_notes"
                                        v-model="form.admin_notes"
                                        placeholder="Add internal notes about this prayer request..."
                                        rows="4"
                                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    />
                                    <p class="text-xs text-muted-foreground">These notes are only visible to administrators</p>
                                </div>

                                <div class="flex items-center justify-between pt-4">
                                    <Button
                                        type="button"
                                        variant="destructive"
                                        @click="handleDelete"
                                    >
                                        <Trash2 class="h-4 w-4 mr-2" />
                                        Delete Prayer
                                    </Button>
                                    
                                    <Button
                                        type="submit"
                                        :disabled="form.processing"
                                    >
                                        <Save class="h-4 w-4 mr-2" />
                                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                                    </Button>
                                </div>
                            </form>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
