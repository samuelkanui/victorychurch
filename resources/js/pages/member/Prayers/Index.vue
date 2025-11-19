<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import MemberLayout from '@/layouts/MemberLayout.vue'
import { type BreadcrumbItemType } from '@/types'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import Tabs from '@/components/ui/Tabs.vue'
import TabsContent from '@/components/ui/TabsContent.vue'
import TabsList from '@/components/ui/TabsList.vue'
import TabsTrigger from '@/components/ui/TabsTrigger.vue'
import { 
    MessageSquare, 
    Plus, 
    Eye, 
    Edit,
    Trash2,
    Globe,
    Users,
    Lock,
    AlertCircle,
    Calendar,
    Heart,
    Filter
} from 'lucide-vue-next'
import { ref } from 'vue'

interface PrayerRequest {
    id: number
    title: string
    description: string
    privacy: string
    is_anonymous: boolean
    is_urgent: boolean
    status: string
    created_at: string
    updated_at: string
    user?: {
        id: number
        name: string
    }
}

interface Props {
    prayers: {
        data: PrayerRequest[]
        links: any[]
        meta: any
    }
    stats: {
        total_prayers: number
        active_prayers: number
        answered_prayers: number
        urgent_prayers: number
        community_prayers: number
    }
    currentTab?: string
    filters?: any
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Prayer Requests' }
]

const statusFilter = ref(props.filters?.status || '')
const privacyFilter = ref(props.filters?.privacy || '')

const changeTab = (tab: string) => {
    router.get('/member/prayers', { tab }, { preserveState: true })
}

const applyFilters = () => {
    const params: any = { tab: props.currentTab || 'my_prayers' }
    if (statusFilter.value) params.status = statusFilter.value
    if (privacyFilter.value && props.currentTab === 'my_prayers') params.privacy = privacyFilter.value
    router.get('/member/prayers', params, { preserveState: true })
}

const clearFilters = () => {
    statusFilter.value = ''
    privacyFilter.value = ''
    router.get('/member/prayers', { tab: props.currentTab || 'my_prayers' }, { preserveState: true })
}

const getPrivacyIcon = (privacy: string) => {
    switch (privacy) {
        case 'public': return Globe
        case 'group': return Users
        case 'private': return Lock
        default: return Lock
    }
}

const getPrivacyColor = (privacy: string) => {
    switch (privacy) {
        case 'public': return 'bg-green-100 text-green-800'
        case 'group': return 'bg-blue-100 text-blue-800'
        case 'private': return 'bg-gray-100 text-gray-800'
        default: return 'bg-gray-100 text-gray-800'
    }
}

const getPrivacyLabel = (privacy: string) => {
    switch (privacy) {
        case 'public': return 'Public'
        case 'group': return 'Group Only'
        case 'private': return 'Private'
        default: return privacy
    }
}

const getStatusColor = (status: string) => {
    switch (status) {
        case 'active': return 'bg-blue-100 text-blue-800'
        case 'answered': return 'bg-green-100 text-green-800'
        case 'closed': return 'bg-gray-100 text-gray-800'
        default: return 'bg-gray-100 text-gray-800'
    }
}

const formatDate = (dateString: string) => {
    if (!dateString) return 'No date'
    try {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit'
        })
    } catch (error) {
        return 'Invalid date'
    }
}

const truncateText = (text: string, maxLength: number = 100) => {
    if (!text) return ''
    return text.length > maxLength ? text.substring(0, maxLength) + '...' : text
}
</script>

<template>
    <Head title="Prayer Requests - Member Dashboard" />
    
    <MemberLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight bg-gradient-to-r from-green-600 to-green-800 bg-clip-text text-transparent">
                        Prayer Requests
                    </h1>
                    <p class="text-sm sm:text-base text-muted-foreground">
                        Submit and manage your prayer requests
                    </p>
                </div>
                <Button as-child class="w-full sm:w-auto">
                    <Link href="/member/prayers/create">
                        <Plus class="h-4 w-4 mr-2" />
                        New Prayer Request
                    </Link>
                </Button>
            </div>

            <!-- Statistics -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">My Prayers</CardTitle>
                        <MessageSquare class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-purple-600">{{ stats.total_prayers }}</div>
                        <p class="text-xs text-muted-foreground">Total requests</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Active</CardTitle>
                        <AlertCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">{{ stats.active_prayers }}</div>
                        <p class="text-xs text-muted-foreground">Awaiting prayer</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Answered</CardTitle>
                        <MessageSquare class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">{{ stats.answered_prayers }}</div>
                        <p class="text-xs text-muted-foreground">Prayers answered</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Community</CardTitle>
                        <Heart class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-orange-600">{{ stats.community_prayers }}</div>
                        <p class="text-xs text-muted-foreground">From others</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Tabs and Filters -->
            <Card>
                <CardHeader>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <CardTitle class="flex items-center gap-2">
                                <MessageSquare class="h-5 w-5" />
                                Prayer Requests
                            </CardTitle>
                            <CardDescription>
                                View and manage prayer requests
                            </CardDescription>
                        </div>
                        
                        <!-- Filters -->
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="flex items-center gap-2">
                                <Filter class="h-4 w-4 text-muted-foreground" />
                                <select 
                                    v-model="statusFilter" 
                                    @change="applyFilters"
                                    class="px-3 py-2 text-sm border rounded-md bg-background"
                                >
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="answered">Answered</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>

                            <div v-if="currentTab === 'my_prayers'" class="flex items-center gap-2">
                                <Filter class="h-4 w-4 text-muted-foreground" />
                                <select 
                                    v-model="privacyFilter" 
                                    @change="applyFilters"
                                    class="px-3 py-2 text-sm border rounded-md bg-background"
                                >
                                    <option value="">All Privacy</option>
                                    <option value="public">Public</option>
                                    <option value="group">Group Only</option>
                                    <option value="private">Private</option>
                                </select>
                            </div>

                            <Button v-if="statusFilter || privacyFilter" variant="outline" size="sm" @click="clearFilters">
                                Clear Filters
                            </Button>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <Tabs :default-value="currentTab || 'my_prayers'" class="w-full">
                        <TabsList class="grid w-full grid-cols-2">
                            <TabsTrigger value="my_prayers" @click="changeTab('my_prayers')">
                                My Prayers ({{ stats.total_prayers }})
                            </TabsTrigger>
                            <TabsTrigger value="community" @click="changeTab('community')">
                                Community ({{ stats.community_prayers }})
                            </TabsTrigger>
                        </TabsList>

                        <!-- My Prayers Tab -->
                        <TabsContent value="my_prayers" class="mt-6">
                            <div v-if="!prayers?.data || prayers.data.length === 0" class="text-center py-12">
                                <MessageSquare class="h-16 w-16 mx-auto mb-4 text-gray-400 opacity-50" />
                                <h3 class="text-lg font-semibold mb-2">No Prayer Requests</h3>
                                <p class="text-muted-foreground mb-4">
                                    You haven't submitted any prayer requests yet.
                                </p>
                                <Button as-child>
                                    <Link href="/member/prayers/create">
                                        <Plus class="h-4 w-4 mr-2" />
                                        Create Your First Prayer Request
                                    </Link>
                                </Button>
                            </div>

                            <div v-else class="space-y-4">
                                <div v-for="prayer in prayers.data" :key="prayer.id" 
                                     class="p-4 sm:p-6 border rounded-lg hover:shadow-md transition-shadow">
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                                <h3 class="font-semibold text-lg truncate">{{ prayer.title }}</h3>
                                                <Badge v-if="prayer.is_urgent" class="bg-red-100 text-red-800 flex-shrink-0">
                                                    <AlertCircle class="h-3 w-3 mr-1" />
                                                    Urgent
                                                </Badge>
                                                <Badge :class="getStatusColor(prayer.status)" class="flex-shrink-0">
                                                    {{ prayer.status.charAt(0).toUpperCase() + prayer.status.slice(1) }}
                                                </Badge>
                                            </div>
                                            
                                            <div class="flex flex-wrap items-center gap-3 text-sm text-muted-foreground mb-3">
                                                <div class="flex items-center gap-1">
                                                    <component :is="getPrivacyIcon(prayer.privacy)" class="h-4 w-4" />
                                                    <Badge :class="getPrivacyColor(prayer.privacy)" class="text-xs">
                                                        {{ getPrivacyLabel(prayer.privacy) }}
                                                    </Badge>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <Calendar class="h-4 w-4" />
                                                    <span class="text-xs sm:text-sm">{{ formatDate(prayer.created_at) }}</span>
                                                </div>
                                                <div v-if="prayer.is_anonymous" class="flex items-center gap-1">
                                                    <Lock class="h-4 w-4" />
                                                    <span class="text-xs sm:text-sm">Anonymous</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex flex-wrap items-center gap-2">
                                            <Button variant="outline" size="sm" as-child class="flex-1 sm:flex-none">
                                                <Link :href="`/member/prayers/${prayer.id}`">
                                                    <Eye class="h-4 w-4 mr-1" />
                                                    View
                                                </Link>
                                            </Button>
                                            <Button v-if="prayer.status === 'active'" variant="outline" size="sm" as-child class="flex-1 sm:flex-none">
                                                <Link :href="`/member/prayers/${prayer.id}/edit`">
                                                    <Edit class="h-4 w-4 mr-1" />
                                                    Edit
                                                </Link>
                                            </Button>
                                            <Button variant="outline" size="sm" as-child class="flex-1 sm:flex-none">
                                                <Link :href="`/member/prayers/${prayer.id}`" method="delete" as="button">
                                                    <Trash2 class="h-4 w-4 mr-1" />
                                                    Delete
                                                </Link>
                                            </Button>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ truncateText(prayer.description) }}</p>
                                        <Link v-if="prayer.description.length > 100" 
                                              :href="`/member/prayers/${prayer.id}`" 
                                              class="text-blue-600 hover:underline text-sm mt-2 inline-block">
                                            Read more...
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </TabsContent>

                        <!-- Community Prayers Tab -->
                        <TabsContent value="community" class="mt-6">
                            <div v-if="!prayers?.data || prayers.data.length === 0" class="text-center py-12">
                                <Heart class="h-16 w-16 mx-auto mb-4 text-gray-400 opacity-50" />
                                <h3 class="text-lg font-semibold mb-2">No Community Prayers</h3>
                                <p class="text-muted-foreground mb-4">
                                    No prayer requests from your community yet.
                                </p>
                            </div>

                            <div v-else class="space-y-4">
                                <div v-for="prayer in prayers.data" :key="prayer.id" 
                                     class="p-4 sm:p-6 border rounded-lg hover:shadow-md transition-shadow">
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                                <h3 class="font-semibold text-lg truncate">{{ prayer.title }}</h3>
                                                <Badge v-if="prayer.is_urgent" class="bg-red-100 text-red-800 flex-shrink-0">
                                                    <AlertCircle class="h-3 w-3 mr-1" />
                                                    Urgent
                                                </Badge>
                                                <Badge :class="getStatusColor(prayer.status)" class="flex-shrink-0">
                                                    {{ prayer.status.charAt(0).toUpperCase() + prayer.status.slice(1) }}
                                                </Badge>
                                            </div>
                                            
                                            <div class="flex flex-wrap items-center gap-3 text-sm text-muted-foreground mb-3">
                                                <div v-if="prayer.user && !prayer.is_anonymous" class="flex items-center gap-1">
                                                    <Users class="h-4 w-4" />
                                                    <span class="text-xs sm:text-sm">{{ prayer.user.name }}</span>
                                                </div>
                                                <div v-if="prayer.is_anonymous" class="flex items-center gap-1">
                                                    <Lock class="h-4 w-4" />
                                                    <span class="text-xs sm:text-sm">Anonymous</span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <component :is="getPrivacyIcon(prayer.privacy)" class="h-4 w-4" />
                                                    <Badge :class="getPrivacyColor(prayer.privacy)" class="text-xs">
                                                        {{ getPrivacyLabel(prayer.privacy) }}
                                                    </Badge>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <Calendar class="h-4 w-4" />
                                                    <span class="text-xs sm:text-sm">{{ formatDate(prayer.created_at) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center gap-2">
                                            <Button variant="outline" size="sm" as-child class="w-full sm:w-auto">
                                                <Link :href="`/member/prayers/${prayer.id}`">
                                                    <Eye class="h-4 w-4 mr-1" />
                                                    View & Pray
                                                </Link>
                                            </Button>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ truncateText(prayer.description) }}</p>
                                        <Link v-if="prayer.description.length > 100" 
                                              :href="`/member/prayers/${prayer.id}`" 
                                              class="text-blue-600 hover:underline text-sm mt-2 inline-block">
                                            Read more...
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </TabsContent>
                    </Tabs>

                    <!-- Pagination -->
                    <div v-if="prayers.links && prayers.links.length > 3" class="mt-6 flex justify-center">
                        <nav class="flex items-center gap-2">
                            <Link v-for="(link, index) in prayers.links" :key="index"
                                  :href="link.url || '#'"
                                  :class="[
                                      'px-3 py-2 text-sm rounded-md transition-colors',
                                      link.active ? 'bg-green-600 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-700',
                                      !link.url ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                                  ]"
                                  v-html="link.label"
                                  :disabled="!link.url"
                            />
                        </nav>
                    </div>
                </CardContent>
            </Card>
        </div>
    </MemberLayout>
</template>
