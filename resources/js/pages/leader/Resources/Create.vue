<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import LeaderLayout from '@/layouts/LeaderLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { FileText, Upload, Globe, Lock } from 'lucide-vue-next'
import { ref } from 'vue'

interface Props {
    groups: Array<{
        id: number
        name: string
    }>
}

const props = defineProps<Props>()

const breadcrumbs = [
    { title: 'Resources', href: '/leader/resources' },
    { title: 'Upload Resource' }
]

const form = useForm({
    title: '',
    description: '',
    type: 'file',
    group_id: '',
    visibility: 'group',
    external_url: '',
    file: null as File | null,
    categories: [] as string[],
})

const fileInput = ref<HTMLInputElement | null>(null)

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement
    if (target.files && target.files[0]) {
        form.file = target.files[0]
    }
}

const submit = () => {
    form.post('/leader/resources', {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Upload Resource - Leader Dashboard" />
    
    <LeaderLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        Upload Resource
                    </h1>
                    <p class="text-muted-foreground">
                        Share files, documents, and links with your group
                    </p>
                </div>
                <Button variant="outline" as-child>
                    <Link href="/leader/resources">
                        Cancel
                    </Link>
                </Button>
            </div>

            <!-- Upload Form -->
            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <FileText class="h-5 w-5" />
                        Resource Information
                    </CardTitle>
                    <CardDescription>
                        Upload a file or share a link with your group members
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Resource Title -->
                        <div class="space-y-2">
                            <Label for="title">Resource Title *</Label>
                            <Input
                                id="title"
                                v-model="form.title"
                                type="text"
                                placeholder="Enter resource title"
                                :class="{ 'border-red-500': form.errors.title }"
                            />
                            <p v-if="form.errors.title" class="text-sm text-red-600">
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <Label for="description">Description</Label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                placeholder="Describe this resource (optional)"
                                class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                :class="{ 'border-red-500': form.errors.description }"
                            ></textarea>
                            <p v-if="form.errors.description" class="text-sm text-red-600">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- Resource Type -->
                        <div class="space-y-2">
                            <Label for="type">Resource Type *</Label>
                            <select
                                id="type"
                                v-model="form.type"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                :class="{ 'border-red-500': form.errors.type }"
                            >
                                <option value="file">File Upload</option>
                                <option value="document">Document</option>
                                <option value="video">Video</option>
                                <option value="audio">Audio</option>
                                <option value="link">External Link</option>
                            </select>
                            <p v-if="form.errors.type" class="text-sm text-red-600">
                                {{ form.errors.type }}
                            </p>
                        </div>

                        <!-- Group Selection -->
                        <div class="space-y-2">
                            <Label for="group_id">
                                Select Group *
                                <span v-if="form.visibility === 'public'" class="text-xs text-muted-foreground ml-2">(Not required for public resources)</span>
                            </Label>
                            <select
                                id="group_id"
                                v-model="form.group_id"
                                :disabled="form.visibility === 'public'"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                :class="{ 
                                    'border-red-500': form.errors.group_id,
                                    'opacity-50 cursor-not-allowed': form.visibility === 'public'
                                }"
                            >
                                <option value="">{{ form.visibility === 'public' ? 'All groups (public resource)' : 'Select a group' }}</option>
                                <option v-for="group in groups" :key="group.id" :value="group.id">
                                    {{ group.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.group_id" class="text-sm text-red-600">
                                {{ form.errors.group_id }}
                            </p>
                        </div>

                        <!-- Visibility -->
                        <div class="space-y-2">
                            <Label for="visibility">Visibility *</Label>
                            <div class="space-y-3">
                                <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-muted/50" :class="{ 'border-blue-600 bg-blue-50 dark:bg-blue-950': form.visibility === 'group' }">
                                    <input
                                        type="radio"
                                        v-model="form.visibility"
                                        value="group"
                                        class="text-blue-600"
                                    />
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <Lock class="h-4 w-4" />
                                            <span class="font-medium">Group Only</span>
                                        </div>
                                        <p class="text-sm text-muted-foreground">Only members of the selected group can access</p>
                                    </div>
                                </label>
                                
                                <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-muted/50" :class="{ 'border-blue-600 bg-blue-50 dark:bg-blue-950': form.visibility === 'public' }">
                                    <input
                                        type="radio"
                                        v-model="form.visibility"
                                        value="public"
                                        class="text-blue-600"
                                    />
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <Globe class="h-4 w-4" />
                                            <span class="font-medium">Public</span>
                                        </div>
                                        <p class="text-sm text-muted-foreground">All groups can access this resource</p>
                                    </div>
                                </label>
                            </div>
                            <p v-if="form.errors.visibility" class="text-sm text-red-600">
                                {{ form.errors.visibility }}
                            </p>
                        </div>

                        <!-- External URL (for links) -->
                        <div v-if="form.type === 'link'" class="space-y-2">
                            <Label for="external_url">External URL *</Label>
                            <Input
                                id="external_url"
                                v-model="form.external_url"
                                type="url"
                                placeholder="https://example.com"
                                :class="{ 'border-red-500': form.errors.external_url }"
                            />
                            <p v-if="form.errors.external_url" class="text-sm text-red-600">
                                {{ form.errors.external_url }}
                            </p>
                        </div>

                        <!-- File Upload (for files) -->
                        <div v-else class="space-y-2">
                            <Label for="file">Upload File *</Label>
                            <div class="border-2 border-dashed rounded-lg p-6 text-center hover:border-blue-600 transition-colors">
                                <input
                                    ref="fileInput"
                                    type="file"
                                    @change="handleFileChange"
                                    class="hidden"
                                    id="file"
                                />
                                <Upload class="h-12 w-12 mx-auto mb-4 text-muted-foreground" />
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="fileInput?.click()"
                                >
                                    Choose File
                                </Button>
                                <p class="text-sm text-muted-foreground mt-2">
                                    {{ form.file ? form.file.name : 'Max file size: 50MB' }}
                                </p>
                            </div>
                            <p v-if="form.errors.file" class="text-sm text-red-600">
                                {{ form.errors.file }}
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-2 pt-4">
                            <Button 
                                type="submit" 
                                :disabled="form.processing"
                                class="bg-blue-600 hover:bg-blue-700"
                            >
                                {{ form.processing ? 'Uploading...' : 'Upload Resource' }}
                            </Button>
                            <Button type="button" variant="outline" as-child>
                                <Link href="/leader/resources">
                                    Cancel
                                </Link>
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </LeaderLayout>
</template>
