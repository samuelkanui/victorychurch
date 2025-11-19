<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import LeaderLayout from '@/layouts/LeaderLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { 
    BookOpen, 
    Users, 
    CheckCircle,
    Clock,
    Eye,
    Star
} from 'lucide-vue-next'

interface Assignment {
    id: number
    title: string
    description: string
    type: string
    due_date: string
    max_points: number | null
    group: {
        id: number
        name: string
    }
    submissions: Array<{
        id: number
        content: string
        submitted_at: string
        points: number | null
        feedback: string | null
        graded_at: string | null
        user: {
            id: number
            name: string
            email: string
        }
    }>
}

interface Props {
    assignment: Assignment
}

const props = defineProps<Props>()

const breadcrumbs = [
    { title: 'Assignments', href: '/leader/assignments' },
    { title: props.assignment.title, href: `/leader/assignments/${props.assignment.id}` },
    { title: 'Submissions' }
]

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

const getGradeColor = (points: number | null, maxPoints: number | null) => {
    if (points === null || maxPoints === null) return 'bg-gray-100 text-gray-800'
    const percentage = (points / maxPoints) * 100
    if (percentage >= 90) return 'bg-green-100 text-green-800'
    if (percentage >= 80) return 'bg-blue-100 text-blue-800'
    if (percentage >= 70) return 'bg-yellow-100 text-yellow-800'
    return 'bg-red-100 text-red-800'
}
</script>

<template>
    <Head :title="`${assignment.title} Submissions - Assignments`" />
    
    <LeaderLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        Assignment Submissions
                    </h1>
                    <p class="text-muted-foreground">
                        Review and grade student submissions for "{{ assignment.title }}"
                    </p>
                </div>
                <Button variant="outline" as-child>
                    <Link :href="`/leader/assignments/${assignment.id}`">
                        <Eye class="h-4 w-4 mr-2" />
                        View Assignment
                    </Link>
                </Button>
            </div>

            <!-- Assignment Info -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <BookOpen class="h-5 w-5" />
                        {{ assignment.title }}
                    </CardTitle>
                    <CardDescription>
                        {{ assignment.group?.name || 'Unknown Group' }} • Due {{ formatDate(assignment.due_date) }}
                        <span v-if="assignment.max_points"> • {{ assignment.max_points }} points</span>
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ assignment.submissions?.length || 0 }}</div>
                            <p class="text-sm text-blue-800">Total Submissions</p>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">
                                {{ assignment.submissions?.filter(s => s.graded_at).length || 0 }}
                            </div>
                            <p class="text-sm text-green-800">Graded</p>
                        </div>
                        <div class="text-center p-4 bg-yellow-50 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600">
                                {{ assignment.submissions?.filter(s => !s.graded_at).length || 0 }}
                            </div>
                            <p class="text-sm text-yellow-800">Pending Review</p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Submissions List -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Users class="h-5 w-5" />
                        Student Submissions
                    </CardTitle>
                    <CardDescription>
                        Review and provide feedback on student work
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="!assignment.submissions || assignment.submissions.length === 0" class="text-center py-12">
                        <Users class="h-16 w-16 mx-auto mb-4 text-gray-400 opacity-50" />
                        <h3 class="text-lg font-semibold mb-2">No Submissions Yet</h3>
                        <p class="text-muted-foreground">
                            Students haven't submitted their work for this assignment yet.
                        </p>
                    </div>
                    
                    <div v-else class="space-y-4">
                        <div v-for="submission in assignment.submissions" :key="submission.id" 
                             class="p-6 border rounded-lg">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="font-semibold text-lg">{{ submission.user?.name || 'Unknown User' }}</h3>
                                    <p class="text-sm text-muted-foreground">{{ submission.user?.email || 'No email' }}</p>
                                    <p class="text-sm text-muted-foreground">
                                        Submitted {{ formatDate(submission.submitted_at) }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Badge v-if="submission.graded_at" :class="getGradeColor(submission.points, assignment.max_points)">
                                        <Star class="h-3 w-3 mr-1" />
                                        {{ submission.points }}/{{ assignment.max_points || 100 }}
                                    </Badge>
                                    <Badge v-else class="bg-yellow-100 text-yellow-800">
                                        <Clock class="h-3 w-3 mr-1" />
                                        Pending Review
                                    </Badge>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <h4 class="font-medium mb-2">Submission Content</h4>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm whitespace-pre-wrap">{{ submission.content }}</p>
                                </div>
                            </div>
                            
                            <div v-if="submission.feedback" class="mb-4">
                                <h4 class="font-medium mb-2">Feedback</h4>
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <p class="text-sm">{{ submission.feedback }}</p>
                                    <p class="text-xs text-muted-foreground mt-2">
                                        Graded {{ formatDate(submission.graded_at || '') }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <Button variant="outline" size="sm">
                                    <CheckCircle class="h-4 w-4 mr-1" />
                                    {{ submission.graded_at ? 'Update Grade' : 'Grade Submission' }}
                                </Button>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LeaderLayout>
</template>
