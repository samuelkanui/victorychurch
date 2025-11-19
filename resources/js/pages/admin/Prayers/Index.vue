<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { MessageSquare, Users, CheckCircle, Clock, Eye, Trash2, Search } from 'lucide-vue-next'
import { ref } from 'vue'
import { index as prayersIndex, show as prayersShow, destroy as prayersDestroy } from '@/routes/admin/prayers'

interface Prayer {
    id: number
    title: string
    description: string
    privacy: string
    status: string
    created_at: string
    user: {
        name: string
    }
}

interface Props {
    prayers: {
        data: Prayer[]
        links: any[]
        current_page: number
        last_page: number
    }
    stats: {
        total_prayers: number
        public_prayers: number
        answered_prayers: number
        pending_prayers: number
    }
    filters: {
        search?: string
        privacy?: string
        status?: string
    }
}

const props = defineProps<Props>()

const searchQuery = ref(props.filters.search || '')

const handleSearch = () => {
    router.get(prayersIndex(), { search: searchQuery.value }, { preserveState: true })
}

const deletePrayer = (id: number) => {
    if (confirm('Are you sure you want to delete this prayer request?')) {
        router.delete(prayersDestroy(id))
    }
}

const breadcrumbs = [
    { title: 'Prayer Requests' }
]
</script>

<template>
    <Head title="Prayer Requests - Admin Dashboard" />
    
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent">
                        Prayer Requests Management
                    </h1>
                    <p class="text-muted-foreground">
                        Manage and moderate community prayer requests
                    </p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Prayers</CardTitle>
                        <MessageSquare class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-purple-600">{{ stats.total_prayers }}</div>
                        <p class="text-xs text-muted-foreground">All prayer requests</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Public Prayers</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">{{ stats.public_prayers }}</div>
                        <p class="text-xs text-muted-foreground">Publicly visible</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Answered Prayers</CardTitle>
                        <CheckCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">{{ stats.answered_prayers }}</div>
                        <p class="text-xs text-muted-foreground">Marked as answered</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Pending Prayers</CardTitle>
                        <Clock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-yellow-600">{{ stats.pending_prayers }}</div>
                        <p class="text-xs text-muted-foreground">Awaiting response</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Search -->
            <Card>
                <CardHeader>
                    <CardTitle>Search Prayer Requests</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-2">
                        <Input
                            v-model="searchQuery"
                            placeholder="Search by title, description, or user..."
                            @keyup.enter="handleSearch"
                            class="flex-1"
                        />
                        <Button @click="handleSearch">
                            <Search class="h-4 w-4 mr-2" />
                            Search
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Prayer Requests List -->
            <Card>
                <CardHeader>
                    <CardTitle>All Prayer Requests</CardTitle>
                    <CardDescription>
                        Manage and moderate community prayer requests
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="prayers.data.length > 0" class="space-y-4">
                        <div v-for="prayer in prayers.data" :key="prayer.id" class="border rounded-lg p-4 hover:bg-muted/50 transition-colors">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 space-y-2">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-semibold text-lg">{{ prayer.title }}</h3>
                                        <Badge :variant="prayer.privacy === 'public' ? 'default' : 'secondary'">
                                            {{ prayer.privacy }}
                                        </Badge>
                                        <Badge :variant="prayer.status === 'answered' ? 'default' : prayer.status === 'pending' ? 'secondary' : 'outline'">
                                            {{ prayer.status }}
                                        </Badge>
                                    </div>
                                    <p class="text-sm text-muted-foreground line-clamp-2">{{ prayer.description }}</p>
                                    <div class="flex items-center gap-4 text-xs text-muted-foreground">
                                        <span>By {{ prayer.user.name }}</span>
                                        <span>•</span>
                                        <span>{{ new Date(prayer.created_at).toLocaleDateString() }}</span>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <Link :href="prayersShow(prayer.id)">
                                        <Button variant="outline" size="sm">
                                            <Eye class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                    <Button variant="destructive" size="sm" @click="deletePrayer(prayer.id)">
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="prayers.last_page > 1" class="flex items-center justify-center gap-2 pt-4">
                            <Link
                                v-for="(link, index) in prayers.links"
                                :key="index"
                                :href="link.url"
                                :class="[
                                    'px-3 py-1 rounded',
                                    link.active ? 'bg-primary text-primary-foreground' : 'hover:bg-muted',
                                    !link.url && 'opacity-50 cursor-not-allowed'
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-muted-foreground">
                        <MessageSquare class="h-12 w-12 mx-auto mb-4 opacity-50" />
                        <p>No prayer requests found</p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
