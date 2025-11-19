<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
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
    FileText,
    Send
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
    submission?: Submission | null
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Assignments', href: '/member/assignments' },
    { title: props.assignment.title, href: `/member/assignments/${props.assignment.id}` },
    { title: props.submission ? 'View Submission' : 'Submit Assignment' }
]

const form = useForm({
    content: props.submission?.content || '',
    file: null as File | null
})

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

const canSubmit = !props.submission && !isOverdue(props.assignment.due_date) && props.assignment.is_active

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement
    if (target.files && target.files[0]) {
        form.file = target.files[0]
    }
}

const submitAssignment = () => {
    form.post(`/member/assignments/${props.assignment.id}/submit`, {
        onSuccess: () => {
            // Success handled by Inertia
        }
    })
}
</script>

<template>
    <Head :title="`${assignment.title} - ${submission ? 'View Submission' : 'Submit Assignment'}`" />
    
    <MemberLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-green-600 to-green-800 bg-clip-text text-transparent">
                        {{ submission ? 'View Submission' : 'Submit Assignment' }}
                    </h1>
                    <p class="text-muted-foreground">
                        {{ submission ? 'Review your submitted work and feedback' : 'Complete and submit your assignment' }}
                    </p>
                </div>
                <Button variant="outline" @click="$inertia.visit(`/member/assignments/${assignment.id}`)">
                    <ArrowLeft class="h-4 w-4 mr-2" />
                    Back to Assignment
                </Button>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Assignment Summary -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-3">
                                <BookOpen class="h-5 w-5 text-green-600" />
                                {{ assignment.title }}
                            </CardTitle>
                            <CardDescription>
                                <div class="flex flex-wrap items-center gap-2 mt-2">
                                    <Badge :class="getTypeColor(assignment.type)">
                                        {{ getTypeLabel(assignment.type) }}
                                    </Badge>
                                    <Badge v-if="isOverdue(assignment.due_date)" class="bg-red-100 text-red-800">
                                        <AlertCircle class="h-3 w-3 mr-1" />
                                        Overdue
                                    </Badge>
                                </div>
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
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
                        </CardContent>
                    </Card>

                    <!-- Viewing Existing Submission -->
                    <Card v-if="submission">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-3">
                                <CheckCircle class="h-5 w-5 text-green-600" />
                                Your Submission
                            </CardTitle>
                            <CardDescription>
                                Submitted on {{ formatDateTime(submission.submitted_at) }}
                                <span v-if="submission.graded_at" class="ml-2">
                                    • Graded on {{ formatDateTime(submission.graded_at) }}
                                </span>
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <!-- Grade Display -->
                            <div v-if="submission.grade !== null && submission.grade !== undefined" 
                                 class="bg-green-50 p-4 rounded-lg border-l-4 border-green-400">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-semibold text-green-800">Grade Received</h4>
                                        <p class="text-sm text-green-700">Your assignment has been graded</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-3xl font-bold text-green-600">
                                            {{ submission.grade }}
                                        </div>
                                        <div class="text-sm text-green-600">
                                            / {{ assignment.max_points || 100 }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submission Content -->
                            <div>
                                <h4 class="font-semibold mb-2">Submission Content</h4>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="whitespace-pre-wrap">{{ submission.content }}</p>
                                </div>
                            </div>

                            <!-- Attached File -->
                            <div v-if="submission.file_path" class="flex items-center gap-2 p-3 bg-blue-50 rounded-lg">
                                <FileText class="h-5 w-5 text-blue-600" />
                                <div>
                                    <p class="font-medium text-blue-800">Attached File</p>
                                    <a :href="`/storage/${submission.file_path}`" target="_blank" 
                                       class="text-blue-600 hover:underline text-sm">
                                        View File
                                    </a>
                                </div>
                            </div>

                            <!-- Feedback -->
                            <div v-if="submission.feedback" class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-400">
                                <h4 class="font-semibold text-purple-800 mb-2">Instructor Feedback</h4>
                                <p class="text-purple-800 whitespace-pre-wrap">{{ submission.feedback }}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Submission Form -->
                    <Card v-else-if="canSubmit">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-3">
                                <Upload class="h-5 w-5 text-green-600" />
                                Submit Your Work
                            </CardTitle>
                            <CardDescription>
                                Complete the form below to submit your assignment
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form @submit.prevent="submitAssignment" class="space-y-6">
                                <!-- Content Field -->
                                <div>
                                    <label class="block text-sm font-medium mb-2">
                                        Assignment Response <span class="text-red-500">*</span>
                                    </label>
                                    <textarea 
                                        v-model="form.content"
                                        placeholder="Enter your assignment response here..."
                                        rows="10"
                                        required
                                        class="w-full p-3 border rounded-lg resize-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        :class="{ 'border-red-500': form.errors.content }"
                                    ></textarea>
                                    <p v-if="form.errors.content" class="text-sm text-red-600 mt-1">
                                        {{ form.errors.content }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Provide a detailed response to the assignment requirements
                                    </p>
                                </div>

                                <!-- File Upload -->
                                <div>
                                    <label class="block text-sm font-medium mb-2">
                                        Attach File (Optional)
                                    </label>
                                    <input 
                                        type="file"
                                        @change="handleFileChange"
                                        accept=".pdf,.doc,.docx,.txt"
                                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        :class="{ 'border-red-500': form.errors.file }"
                                    />
                                    <p v-if="form.errors.file" class="text-sm text-red-600 mt-1">
                                        {{ form.errors.file }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Supported formats: PDF, DOC, DOCX, TXT (Max: 10MB)
                                    </p>
                                </div>

                                <!-- Submit Button -->
                                <div class="flex items-center gap-3 pt-4 border-t">
                                    <Button 
                                        type="submit" 
                                        :disabled="form.processing || !form.content"
                                        class="bg-green-600 hover:bg-green-700"
                                    >
                                        <Send class="h-4 w-4 mr-2" />
                                        {{ form.processing ? 'Submitting...' : 'Submit Assignment' }}
                                    </Button>
                                    <Button 
                                        type="button"
                                        variant="outline"
                                        @click="$inertia.visit(`/member/assignments/${assignment.id}`)"
                                    >
                                        Cancel
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>

                    <!-- Cannot Submit -->
                    <Card v-else>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-3">
                                <AlertCircle class="h-5 w-5 text-red-600" />
                                Cannot Submit
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-400">
                                <p class="text-red-800">
                                    {{ isOverdue(assignment.due_date) 
                                        ? 'This assignment is overdue and can no longer be submitted.' 
                                        : 'This assignment is not available for submission.' }}
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Assignment Guidelines -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg">Assignment Guidelines</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3 text-sm">
                            <div>
                                <span class="font-medium">Due Date:</span>
                                <span class="ml-2" :class="{ 'text-red-600 font-semibold': isOverdue(assignment.due_date) }">
                                    {{ formatDateTime(assignment.due_date) }}
                                </span>
                            </div>
                            <div v-if="assignment.max_points">
                                <span class="font-medium">Points:</span>
                                <span class="ml-2">{{ assignment.max_points }} points</span>
                            </div>
                            <div>
                                <span class="font-medium">Type:</span>
                                <Badge :class="getTypeColor(assignment.type)" class="ml-2">
                                    {{ getTypeLabel(assignment.type) }}
                                </Badge>
                            </div>
                            <div class="pt-2 border-t">
                                <p class="text-xs text-gray-600">
                                    Make sure to read the assignment description and instructions carefully before submitting.
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Submission Tips -->
                    <Card v-if="!submission">
                        <CardHeader>
                            <CardTitle class="text-lg">Submission Tips</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-2 text-sm">
                            <div class="flex items-start gap-2">
                                <CheckCircle class="h-4 w-4 text-green-600 mt-0.5 flex-shrink-0" />
                                <span>Review the assignment requirements thoroughly</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <CheckCircle class="h-4 w-4 text-green-600 mt-0.5 flex-shrink-0" />
                                <span>Provide detailed and thoughtful responses</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <CheckCircle class="h-4 w-4 text-green-600 mt-0.5 flex-shrink-0" />
                                <span>Check your work before submitting</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <CheckCircle class="h-4 w-4 text-green-600 mt-0.5 flex-shrink-0" />
                                <span>Submit before the due date</span>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </MemberLayout>
</template>
