<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import LeaderLayout from '@/layouts/LeaderLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { FileText, Download, Eye, Users, Search, Globe, Lock, File, Video, Link as LinkIcon, FileAudio, Image as ImageIcon, Plus, Edit, Trash2 } from 'lucide-vue-next'
import { ref } from 'vue'

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
}

interface Props {
    resources: {
        data: Resource[]
        links: any[]
        current_page: number
        last_page: number
    }
    groups: Array<{
        id: number
        name: string
    }>
    stats: {
        total_resources: number
        active_resources: number
        total_downloads: number
        public_resources: number
    }
    filters: {
        search?: string
        type?: string
        group_id?: number
    }
}

const props = defineProps<Props>()

const searchQuery = ref(props.filters.search || '')
const selectedType = ref(props.filters.type || '')
const selectedGroup = ref(props.filters.group_id || '')
const previewOpen = ref(false)
const previewResource = ref<Resource | null>(null)

const handleSearch = () => {
    router.get('/leader/resources', {
        search: searchQuery.value,
        type: selectedType.value,
        group_id: selectedGroup.value
    }, { preserveState: true })
}

const handleDelete = (id: number, title: string) => {
    if (confirm(`Are you sure you want to delete "${title}"?`)) {
        router.delete(`/leader/resources/${id}`)
    }
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
    return `/leader/resources/${resource.id}/download`
}

const canPreview = (resource: Resource) => {
    // Don't show preview for external links
    if (resource.type === 'link') return false
    
    // If no mime_type, show preview button anyway (will handle in modal)
    if (!resource.mime_type) return true
    
    // Check if mime type is previewable
    const previewableMimes = [
        'application/pdf',
        'application/msword',  // .doc
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',  // .docx
        'application/vnd.ms-excel',  // .xls
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',  // .xlsx
        'application/vnd.ms-powerpoint',  // .ppt
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',  // .pptx
        'image/', 'video/', 'audio/',  // Check prefixes
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
    <Head title="Resources - Leader Dashboard" />
    
    <LeaderLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        Resources Management
                    </h1>
                    <p class="text-muted-foreground">
                        Upload and manage resources for your groups
                    </p>
                </div>
                <Button as-child class="bg-blue-600 hover:bg-blue-700">
                    <Link href="/leader/resources/create">
                        <Plus class="h-4 w-4 mr-2" />
                        Upload Resource
                    </Link>
                </Button>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Resources</CardTitle>
                        <FileText class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">{{ stats.total_resources }}</div>
                        <p class="text-xs text-muted-foreground">Your resources</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Active Resources</CardTitle>
                        <Eye class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">{{ stats.active_resources }}</div>
                        <p class="text-xs text-muted-foreground">Currently available</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Downloads</CardTitle>
                        <Download class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-purple-600">{{ stats.total_downloads }}</div>
                        <p class="text-xs text-muted-foreground">Files downloaded</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Public Resources</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-orange-600">{{ stats.public_resources }}</div>
                        <p class="text-xs text-muted-foreground">Shared publicly</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Search & Filters -->
            <Card>
                <CardHeader>
                    <CardTitle>Search & Filter Resources</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-3">
                        <Input
                            v-model="searchQuery"
                            placeholder="Search resources..."
                            @keyup.enter="handleSearch"
                        />
                        
                        <select
                            v-model="selectedType"
                            @change="handleSearch"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="">All Types</option>
                            <option value="file">File</option>
                            <option value="document">Document</option>
                            <option value="video">Video</option>
                            <option value="link">Link</option>
                        </select>
                        
                        <select
                            v-model="selectedGroup"
                            @change="handleSearch"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="">All Groups</option>
                            <option v-for="group in groups" :key="group.id" :value="group.id">
                                {{ group.name }}
                            </option>
                        </select>
                    </div>
                </CardContent>
            </Card>

            <!-- Resources List -->
            <Card>
                <CardHeader>
                    <CardTitle>Your Resources</CardTitle>
                    <CardDescription>
                        Manage resources for your groups
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="resources.data.length > 0" class="space-y-4">
                        <div 
                            v-for="resource in resources.data" 
                            :key="resource.id"
                            class="border rounded-lg p-4 hover:bg-muted/50 transition-colors"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 space-y-2">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <component :is="getFileIcon(resource.type, resource.mime_type)" class="h-5 w-5 text-blue-600" />
                                        <h3 class="font-semibold text-lg">{{ resource.title }}</h3>
                                        <Badge :variant="resource.visibility === 'public' ? 'default' : 'secondary'">
                                            <Globe v-if="resource.visibility === 'public'" class="h-3 w-3 mr-1" />
                                            <Lock v-else class="h-3 w-3 mr-1" />
                                            {{ resource.visibility }}
                                        </Badge>
                                        <Badge variant="outline">{{ resource.type }}</Badge>
                                    </div>
                                    
                                    <p v-if="resource.description" class="text-sm text-muted-foreground">
                                        {{ resource.description }}
                                    </p>
                                    
                                    <div class="flex flex-wrap items-center gap-4 text-sm text-muted-foreground">
                                        <span class="flex items-center gap-1">
                                            <Users class="h-4 w-4" />
                                            {{ resource.group.name }}
                                        </span>
                                        
                                        <span v-if="resource.file_size">
                                            {{ resource.formatted_file_size }}
                                        </span>
                                        
                                        <span>
                                            {{ formatDate(resource.published_at) }}
                                        </span>
                                        
                                        <span class="flex items-center gap-1">
                                            <Download class="h-4 w-4" />
                                            {{ resource.download_count }} downloads
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="flex flex-col gap-2">
                                    <Button 
                                        v-if="canPreview(resource)"
                                        variant="outline" 
                                        size="sm"
                                        @click="openPreview(resource)"
                                    >
                                        <Eye class="h-4 w-4 mr-2" />
                                        Preview
                                    </Button>
                                    <a 
                                        v-if="resource.type !== 'link'"
                                        :href="`/leader/resources/${resource.id}/download`"
                                    >
                                        <Button variant="outline" size="sm">
                                            <Download class="h-4 w-4 mr-2" />
                                            Download
                                        </Button>
                                    </a>
                                    <Link :href="`/leader/resources/${resource.id}/edit`">
                                        <Button variant="outline" size="sm">
                                            <Edit class="h-4 w-4 mr-2" />
                                            Edit
                                        </Button>
                                    </Link>
                                    <Button 
                                        variant="destructive" 
                                        size="sm"
                                        @click="handleDelete(resource.id, resource.title)"
                                    >
                                        <Trash2 class="h-4 w-4 mr-2" />
                                        Delete
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="resources.last_page > 1" class="flex items-center justify-center gap-2 pt-4">
                            <Link
                                v-for="(link, index) in resources.links"
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
                        <FileText class="h-12 w-12 mx-auto mb-4 opacity-50" />
                        <p class="mb-4">No resources found</p>
                        <Button as-child class="bg-blue-600 hover:bg-blue-700">
                            <Link href="/leader/resources/create">
                                <Plus class="h-4 w-4 mr-2" />
                                Upload Your First Resource
                            </Link>
                        </Button>
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
                            
                            <!-- Office Documents Preview (Word, Excel, PowerPoint) -->
                            <div v-else-if="previewResource.mime_type?.includes('msword') || 
                                           previewResource.mime_type?.includes('wordprocessingml') ||
                                           previewResource.mime_type?.includes('ms-excel') ||
                                           previewResource.mime_type?.includes('spreadsheetml') ||
                                           previewResource.mime_type?.includes('ms-powerpoint') ||
                                           previewResource.mime_type?.includes('presentationml')" 
                                 class="p-8 text-center">
                                <File class="h-16 w-16 mx-auto mb-4 text-blue-600" />
                                <h3 class="text-lg font-semibold mb-2">{{ previewResource.file_name }}</h3>
                                <p class="text-muted-foreground mb-4">
                                    Office documents (Word, Excel, PowerPoint) cannot be previewed directly in the browser.
                                </p>
                                <div class="flex gap-2 justify-center">
                                    <Button as-child>
                                        <a :href="`/leader/resources/${previewResource.id}/download`" download>
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
                                    <a :href="`/leader/resources/${previewResource.id}/download`">
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
                                    <span class="text-muted-foreground">Group:</span>
                                    <span class="font-medium">{{ previewResource.group.name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground">Downloads:</span>
                                    <span class="font-medium">{{ previewResource.download_count }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-2 pt-4 border-t">
                        <Button variant="outline" @click="closePreview">
                            Close
                        </Button>
                        <Button as-child class="bg-blue-600 hover:bg-blue-700">
                            <a :href="`/leader/resources/${previewResource?.id}/download`">
                                <Download class="h-4 w-4 mr-2" />
                                Download
                            </a>
                        </Button>
                    </div>
                </DialogContent>
            </Dialog>
        </div>
    </LeaderLayout>
</template>
