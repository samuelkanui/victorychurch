<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import MemberLayout from '@/layouts/MemberLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { FileText, Download, Eye, Search, Globe, Lock, File, Video, Link as LinkIcon, FileAudio, Image as ImageIcon, CheckCircle, Clock, PlayCircle } from 'lucide-vue-next'
import { ref, computed } from 'vue'

interface Resource {
    id: number
    title: string
    description: string | null
    type: string
    file_name: string | null
    file_size: number | null
    formatted_file_size: string | null
    mime_type: string | null
    visibility: string
    download_count: number
    published_at: string
    group: {
        id: number
        name: string
    }
    uploader: {
        id: number
        name: string
    }
    user_progress: {
        status: string
        percentage: number
        started_at: string
        completed_at: string | null
    } | null
}

interface Props {
    resources: {
        data: Resource[]
        links: any[]
        current_page: number
        last_page: number
    }
    categories: string[]
    stats: {
        available_resources: number
        completed_resources: number
        in_progress_resources: number
        downloaded_resources: number
    }
    filters: {
        search?: string
        type?: string
        category?: string
        progress?: string
    }
}

const props = defineProps<Props>()

const searchQuery = ref(props.filters.search || '')
const selectedType = ref(props.filters.type || '')
const selectedCategory = ref(props.filters.category || '')
const selectedProgress = ref(props.filters.progress || '')
const previewOpen = ref(false)
const previewResource = ref<Resource | null>(null)

const handleSearch = () => {
    router.get('/member/resources', {
        search: searchQuery.value,
        type: selectedType.value,
        category: selectedCategory.value,
        progress: selectedProgress.value
    }, { preserveState: true })
}

const openPreview = (resource: Resource) => {
    previewResource.value = resource
    previewOpen.value = true
}

const closePreview = () => {
    previewOpen.value = false
    previewResource.value = null
}

const getPreviewUrl = (resource: Resource) => {
    return `/member/resources/${resource.id}/download`
}

const canPreview = (resource: Resource) => {
    if (resource.type === 'link') return false
    if (!resource.mime_type) return true
    
    const previewableMimes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'image/', 'video/', 'audio/',
        'text/'
    ]
    return previewableMimes.some(mime => resource.mime_type?.includes(mime))
}

const getFileIcon = (type: string, mimeType: string | null) => {
    if (type === 'link') return LinkIcon
    if (type === 'video' || mimeType?.startsWith('video/')) return Video
    if (mimeType?.startsWith('audio/')) return FileAudio
    if (mimeType?.startsWith('image/')) return ImageIcon
    return File
}

const getProgressBadge = (progress: Resource['user_progress']) => {
    if (!progress) return { label: 'Not Started', variant: 'secondary' as const, icon: Clock }
    if (progress.status === 'completed') return { label: 'Completed', variant: 'default' as const, icon: CheckCircle }
    if (progress.status === 'downloaded') return { label: 'Downloaded', variant: 'default' as const, icon: Download }
    return { label: 'In Progress', variant: 'outline' as const, icon: PlayCircle }
}

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const breadcrumbs = [
    { title: 'Resources' }
]
</script>

<template>
    <Head title="Resources - Member Portal" />
    
    <MemberLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-green-600 to-green-800 bg-clip-text text-transparent">
                    Resources
                </h1>
                <p class="text-muted-foreground">
                    Access study materials, documents, and media from your groups
                </p>
            </div>

            <!-- Statistics -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Available</CardTitle>
                        <FileText class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.available_resources }}</div>
                        <p class="text-xs text-muted-foreground">Resources you can access</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Completed</CardTitle>
                        <CheckCircle class="h-4 w-4 text-green-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.completed_resources }}</div>
                        <p class="text-xs text-muted-foreground">Resources completed</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">In Progress</CardTitle>
                        <PlayCircle class="h-4 w-4 text-blue-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.in_progress_resources }}</div>
                        <p class="text-xs text-muted-foreground">Currently viewing</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Downloaded</CardTitle>
                        <Download class="h-4 w-4 text-purple-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.downloaded_resources }}</div>
                        <p class="text-xs text-muted-foreground">Resources downloaded</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Search and Filters -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Search class="h-5 w-5" />
                        Search & Filter
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-4">
                        <div class="space-y-2">
                            <Input
                                v-model="searchQuery"
                                placeholder="Search resources..."
                                @keyup.enter="handleSearch"
                            />
                        </div>
                        <div class="space-y-2">
                            <select
                                v-model="selectedType"
                                @change="handleSearch"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                            >
                                <option value="">All Types</option>
                                <option value="file">File</option>
                                <option value="document">Document</option>
                                <option value="video">Video</option>
                                <option value="audio">Audio</option>
                                <option value="link">Link</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <select
                                v-model="selectedCategory"
                                @change="handleSearch"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                            >
                                <option value="">All Categories</option>
                                <option v-for="category in categories" :key="category" :value="category">
                                    {{ category }}
                                </option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <select
                                v-model="selectedProgress"
                                @change="handleSearch"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                            >
                                <option value="">All Progress</option>
                                <option value="not_started">Not Started</option>
                                <option value="viewed">In Progress</option>
                                <option value="downloaded">Downloaded</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Resources List -->
            <Card>
                <CardHeader>
                    <CardTitle>Available Resources</CardTitle>
                    <CardDescription>
                        {{ resources.data.length }} resource(s) found
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="resources.data.length > 0" class="space-y-4">
                        <div
                            v-for="resource in resources.data"
                            :key="resource.id"
                            class="flex items-start gap-4 p-4 border rounded-lg hover:bg-muted/50 transition-colors"
                        >
                            <component :is="getFileIcon(resource.type, resource.mime_type)" class="h-10 w-10 text-green-600 flex-shrink-0 mt-1" />
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-lg">{{ resource.title }}</h3>
                                        <p v-if="resource.description" class="text-sm text-muted-foreground mt-1">
                                            {{ resource.description }}
                                        </p>
                                        <div class="flex flex-wrap items-center gap-2 mt-2">
                                            <Badge :variant="resource.visibility === 'public' ? 'default' : 'secondary'">
                                                <component :is="resource.visibility === 'public' ? Globe : Lock" class="h-3 w-3 mr-1" />
                                                {{ resource.visibility === 'public' ? 'Public' : 'Group Only' }}
                                            </Badge>
                                            <Badge variant="outline">{{ resource.type }}</Badge>
                                            <Badge :variant="getProgressBadge(resource.user_progress).variant">
                                                <component :is="getProgressBadge(resource.user_progress).icon" class="h-3 w-3 mr-1" />
                                                {{ getProgressBadge(resource.user_progress).label }}
                                            </Badge>
                                        </div>
                                        <div class="flex items-center gap-4 mt-2 text-xs text-muted-foreground">
                                            <span>{{ resource.group.name }}</span>
                                            <span>•</span>
                                            <span>{{ resource.uploader.name }}</span>
                                            <span>•</span>
                                            <span>{{ formatDate(resource.published_at) }}</span>
                                            <span v-if="resource.formatted_file_size">•</span>
                                            <span v-if="resource.formatted_file_size">{{ resource.formatted_file_size }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col gap-2 flex-shrink-0">
                                <Button v-if="canPreview(resource)" variant="outline" size="sm" @click="openPreview(resource)">
                                    <Eye class="h-4 w-4 mr-2" />
                                    Preview
                                </Button>
                                <Button v-if="resource.type !== 'link'" variant="outline" size="sm" as-child>
                                    <a :href="`/member/resources/${resource.id}/download`">
                                        <Download class="h-4 w-4 mr-2" />
                                        Download
                                    </a>
                                </Button>
                                <Button v-else variant="outline" size="sm" as-child>
                                    <a :href="resource.external_url" target="_blank">
                                        <LinkIcon class="h-4 w-4 mr-2" />
                                        Open Link
                                    </a>
                                </Button>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="text-center py-8 text-muted-foreground">
                        <FileText class="h-12 w-12 mx-auto mb-4 opacity-50" />
                        <p>No resources found</p>
                    </div>
                </CardContent>
            </Card>

            <!-- Preview Modal -->
            <Dialog :open="previewOpen" @update:open="closePreview">
                <DialogContent class="max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
                    <DialogHeader>
                        <DialogTitle class="flex items-center gap-2">
                            <component :is="getFileIcon(previewResource?.type || '', previewResource?.mime_type || null)" class="h-5 w-5" />
                            {{ previewResource?.title }}
                        </DialogTitle>
                        <DialogDescription>
                            {{ previewResource?.description || 'Resource preview' }}
                        </DialogDescription>
                    </DialogHeader>
                    
                    <div class="flex-1 overflow-auto">
                        <div v-if="previewResource" class="space-y-4">
                            <!-- PDF Preview -->
                            <iframe
                                v-if="previewResource.mime_type?.includes('pdf')"
                                :src="getPreviewUrl(previewResource)"
                                class="w-full h-[600px] border rounded"
                            ></iframe>
                            
                            <!-- Image Preview -->
                            <img
                                v-else-if="previewResource.mime_type?.startsWith('image/')"
                                :src="getPreviewUrl(previewResource)"
                                :alt="previewResource.title"
                                class="w-full h-auto rounded"
                            />
                            
                            <!-- Video Preview -->
                            <video
                                v-else-if="previewResource.mime_type?.startsWith('video/')"
                                :src="getPreviewUrl(previewResource)"
                                controls
                                class="w-full rounded"
                            ></video>
                            
                            <!-- Audio Preview -->
                            <div v-else-if="previewResource.mime_type?.startsWith('audio/')" class="p-8 text-center">
                                <FileAudio class="h-16 w-16 mx-auto mb-4 text-muted-foreground" />
                                <audio
                                    :src="getPreviewUrl(previewResource)"
                                    controls
                                    class="w-full"
                                ></audio>
                            </div>
                            
                            <!-- Office Documents Preview -->
                            <div v-else-if="previewResource.mime_type?.includes('msword') || 
                                           previewResource.mime_type?.includes('wordprocessingml') ||
                                           previewResource.mime_type?.includes('ms-excel') ||
                                           previewResource.mime_type?.includes('spreadsheetml') ||
                                           previewResource.mime_type?.includes('ms-powerpoint') ||
                                           previewResource.mime_type?.includes('presentationml')" 
                                 class="p-8 text-center">
                                <File class="h-16 w-16 mx-auto mb-4 text-green-600" />
                                <h3 class="text-lg font-semibold mb-2">{{ previewResource.file_name }}</h3>
                                <p class="text-muted-foreground mb-4">
                                    Office documents (Word, Excel, PowerPoint) cannot be previewed directly in the browser.
                                </p>
                                <div class="flex gap-2 justify-center">
                                    <Button as-child>
                                        <a :href="`/member/resources/${previewResource.id}/download`" download>
                                            <Download class="h-4 w-4 mr-2" />
                                            Download to View
                                        </a>
                                    </Button>
                                </div>
                                <p class="text-xs text-muted-foreground mt-4">
                                    Download the file to open it in Microsoft Word, Excel, or PowerPoint
                                </p>
                            </div>
                            
                            <!-- Fallback -->
                            <div v-else class="p-8 text-center text-muted-foreground">
                                <FileText class="h-16 w-16 mx-auto mb-4 opacity-50" />
                                <p>Preview not available for this file type</p>
                                <Button as-child class="mt-4">
                                    <a :href="`/member/resources/${previewResource.id}/download`">
                                        <Download class="h-4 w-4 mr-2" />
                                        Download to view
                                    </a>
                                </Button>
                            </div>
                            
                            <!-- Resource Info -->
                            <div class="border-t pt-4 space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground">File name:</span>
                                    <span class="font-medium">{{ previewResource.file_name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground">File size:</span>
                                    <span class="font-medium">{{ previewResource.formatted_file_size }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground">Type:</span>
                                    <Badge variant="outline">{{ previewResource.type }}</Badge>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground">Visibility:</span>
                                    <Badge :variant="previewResource.visibility === 'public' ? 'default' : 'secondary'">
                                        {{ previewResource.visibility }}
                                    </Badge>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground">Downloads:</span>
                                    <span class="font-medium">{{ previewResource.download_count }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </DialogContent>
            </Dialog>
        </div>
    </MemberLayout>
</template>
