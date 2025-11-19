<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import MemberLayout from '@/layouts/MemberLayout.vue'
import { type BreadcrumbItemType } from '@/types'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { 
    BookOpen, 
    ArrowLeft,
    Upload,
    Calendar,
    Users,
    User,
    Clock,
    CheckCircle,
    AlertCircle,
    Target,
    Eye
} from 'lucide-vue-next'

interface Assignment {
    id: number
    title: string
    description: string
    type: string
    due_date: string
    max_points: number | null
    instructions: string | null
    is_active: boolean
    group: {
        id: number
        name: string
    }
    creator: {
        id: number
        name: string
    }
}

interface Submission {
    id: number
    content: string
    file_path: string | null
    grade: number | null
    feedback: string | null
    submitted_at: string
    graded_at: string | null
    status: string
}

interface Props {
    assignment: Assignment
    submission: Submission | null
    submissionStatus: string
    canSubmit: boolean
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Assignments', href: '/member/assignments' },
    { title: props.assignment.title }
]

const getTypeColor = (type: string) => {
    switch (type) {
        case 'bible_study': return 'bg-blue-100 text-blue-800'
        case 'reflection': return 'bg-purple-100 text-purple-800'
        case 'memorization': return 'bg-green-100 text-green-800'
        case 'research': return 'bg-orange-100 text-orange-800'
        default: return 'bg-gray-100 text-gray-800'
    }
}

const getTypeLabel = (type: string) => {
    switch (type) {
        case 'bible_study': return 'Bible Study'
        case 'reflection': return 'Reflection'
        case 'memorization': return 'Memorization'
        case 'research': return 'Research'
        default: return type
    }
}

const getStatusColor = (status: string) => {
    switch (status) {
        case 'pending': return 'bg-orange-100 text-orange-800'
        case 'submitted': return 'bg-blue-100 text-blue-800'
        case 'graded': return 'bg-green-100 text-green-800'
        case 'overdue': return 'bg-red-100 text-red-800'
        default: return 'bg-gray-100 text-gray-800'
    }
}

const formatDateTime = (dateString: string) => {
    if (!dateString) return 'No date'
    try {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit'
        })
    } catch (error) {
        return 'Invalid date'
    }
}

const isOverdue = (dueDate: string) => {
    if (!dueDate) return false
    try {
        return new Date(dueDate) < new Date()
    } catch (error) {
        return false
    }
}
</script>

<template>
    <Head :title="`${assignment.title} - Assignments`" />
    
    <MemberLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-green-600 to-green-800 bg-clip-text text-transparent">
                        Assignment Details
                    </h1>
                    <p class="text-muted-foreground">
                        View assignment requirements and submit your work
                    </p>
                </div>
                <Button variant="outline" @click="$inertia.visit('/member/assignments')">
                    <ArrowLeft class="h-4 w-4 mr-2" />
                    Back to Assignments
                </Button>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Assignment Details -->
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <CardTitle class="text-2xl mb-3">{{ assignment.title }}</CardTitle>
                                <div class="flex flex-wrap items-center gap-2 mb-4">
                                    <Badge :class="getTypeColor(assignment.type)">
                                        {{ getTypeLabel(assignment.type) }}
                                    </Badge>
                                    <Badge :class="getStatusColor(submissionStatus)">
                                        <component :is="submissionStatus === 'overdue' ? AlertCircle : submissionStatus === 'submitted' ? CheckCircle : Clock" class="h-3 w-3 mr-1" />
                                        {{ submissionStatus.charAt(0).toUpperCase() + submissionStatus.slice(1) }}
                                    </Badge>
                                    <Badge v-if="isOverdue(assignment.due_date) && !submission" class="bg-red-100 text-red-800">
                                        <AlertCircle class="h-3 w-3 mr-1" />
                                        Overdue
                                    </Badge>
                                </div>
                            </div>
                            
                            <!-- Grade Display -->
                            <div v-if="submission?.grade !== null && submission?.grade !== undefined" 
                                 class="text-right">
                                <div class="text-3xl font-bold text-green-600">
                                    {{ submission.grade }}
                                </div>
                                <div class="text-sm text-muted-foreground">
                                    / {{ assignment.max_points || 100 }}
                                </div>
                            </div>
                        </div>
                        
                        <CardDescription>
                            <div class="grid gap-4 md:grid-cols-2 text-sm">
                                <div class="flex items-center gap-2">
                                    <Users class="h-4 w-4 text-muted-foreground" />
                                    <span>{{ assignment.group?.name || 'Unknown Group' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Calendar class="h-4 w-4 text-muted-foreground" />
                                    <span>Due {{ formatDateTime(assignment.due_date) }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <User class="h-4 w-4 text-muted-foreground" />
                                    <span>{{ assignment.creator?.name || 'Unknown Creator' }}</span>
                                </div>
                                <div v-if="assignment.max_points" class="flex items-center gap-2">
                                    <Target class="h-4 w-4 text-muted-foreground" />
                                    <span>{{ assignment.max_points }} points</span>
                                </div>
                            </div>
                        </CardDescription>
                    </CardHeader>
                    
                    <CardContent class="space-y-6">
                        <!-- Assignment Description -->
                        <div>
                            <h3 class="font-semibold mb-3">Assignment Description</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-gray-800 whitespace-pre-wrap">{{ assignment.description }}</p>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div v-if="assignment.instructions">
                            <h3 class="font-semibold mb-3">Instructions</h3>
                            <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-400">
                                <p class="text-blue-800 whitespace-pre-wrap">{{ assignment.instructions }}</p>
                            </div>
                        </div>

                        <!-- Submission Details -->
                        <div v-if="submission" class="border-t pt-6">
                            <h3 class="font-semibold mb-3">Your Submission</h3>
                            <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-400">
                                <div class="mb-3">
                                    <p class="text-sm text-green-700 mb-2">
                                        <strong>Submitted:</strong> {{ formatDateTime(submission.submitted_at) }}
                                    </p>
                                    <p v-if="submission.graded_at" class="text-sm text-green-700">
                                        <strong>Graded:</strong> {{ formatDateTime(submission.graded_at) }}
                                    </p>
                                </div>
                                
                                <div class="space-y-3">
                                    <div>
                                        <h4 class="font-medium text-green-800 mb-2">Submission Content:</h4>
                                        <p class="text-green-800 whitespace-pre-wrap">{{ submission.content }}</p>
                                    </div>
                                    
                                    <div v-if="submission.file_path" class="flex items-center gap-2">
                                        <BookOpen class="h-4 w-4 text-green-600" />
                                        <a :href="`/storage/${submission.file_path}`" target="_blank" 
                                           class="text-green-600 hover:underline">
                                            View Attached File
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Feedback -->
                        <div v-if="submission?.feedback" class="border-t pt-6">
                            <h3 class="font-semibold mb-3">Instructor Feedback</h3>
                            <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-400">
                                <p class="text-purple-800 whitespace-pre-wrap">{{ submission.feedback }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Actions Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg">Actions</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <Button 
                                v-if="canSubmit"
                                class="w-full bg-green-600 hover:bg-green-700"
                                as-child
                            >
                                <Link :href="`/member/assignments/${assignment.id}/submission`">
                                    <Upload class="h-4 w-4 mr-2" />
                                    Submit Assignment
                                </Link>
                            </Button>
                            
                            <Button 
                                v-else-if="submission"
                                variant="outline" 
                                class="w-full"
                                as-child
                            >
                                <Link :href="`/member/assignments/${assignment.id}/submission`">
                                    <Eye class="h-4 w-4 mr-2" />
                                    View Submission
                                </Link>
                            </Button>
                            
                            <Button 
                                v-else
                                variant="outline" 
                                disabled
                                class="w-full"
                            >
                                <AlertCircle class="h-4 w-4 mr-2" />
                                {{ isOverdue(assignment.due_date) ? 'Assignment Overdue' : 'Cannot Submit' }}
                            </Button>
                        </CardContent>
                    </Card>

                    <!-- Assignment Info -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg">Assignment Information</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3 text-sm">
                            <div>
                                <span class="font-medium">Type:</span>
                                <Badge :class="getTypeColor(assignment.type)" class="ml-2">
                                    {{ getTypeLabel(assignment.type) }}
                                </Badge>
                            </div>
                            <div>
                                <span class="font-medium">Status:</span>
                                <Badge :class="getStatusColor(submissionStatus)" class="ml-2">
                                    {{ submissionStatus.charAt(0).toUpperCase() + submissionStatus.slice(1) }}
                                </Badge>
                            </div>
                            <div>
                                <span class="font-medium">Due Date:</span>
                                <span class="ml-2">{{ formatDateTime(assignment.due_date) }}</span>
                            </div>
                            <div v-if="assignment.max_points">
                                <span class="font-medium">Points:</span>
                                <span class="ml-2">{{ assignment.max_points }} points</span>
                            </div>
                            <div>
                                <span class="font-medium">Group:</span>
                                <span class="ml-2">{{ assignment.group?.name || 'Unknown Group' }}</span>
                            </div>
                            <div v-if="submission?.grade !== null && submission?.grade !== undefined">
                                <span class="font-medium">Grade:</span>
                                <span class="ml-2 font-semibold text-green-600">{{ submission.grade }}/{{ assignment.max_points || 100 }}</span>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </MemberLayout>
</template>
