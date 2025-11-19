<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import LeaderLayout from '@/layouts/LeaderLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { 
    BookOpen, 
    Edit, 
    Users, 
    Calendar,
    Clock,
    CheckCircle,
    AlertCircle
} from 'lucide-vue-next'

interface Assignment {
    id: number
    title: string
    description: string
    type: string
    due_date: string
    max_points: number | null
    is_active: boolean
    instructions: string | null
    created_at: string
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
        }
    }>
}

interface Props {
    assignment: Assignment
}

const props = defineProps<Props>()

const breadcrumbs = [
    { title: 'Assignments', href: '/leader/assignments' },
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

const formatDate = (dateString: string) => {
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

const isOverdue = (dueDateString: string) => {
    if (!dueDateString) return false
    try {
        return new Date(dueDateString) < new Date()
    } catch (error) {
        return false
    }
}
</script>

<template>
    <Head :title="`${assignment.title} - Assignments`" />
    
    <LeaderLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        {{ assignment.title }}
                    </h1>
                    <p class="text-muted-foreground">
                        Assignment details and submission tracking
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button variant="outline" as-child>
                        <Link :href="`/leader/assignments/${assignment.id}/edit`">
                            <Edit class="h-4 w-4 mr-2" />
                            Edit Assignment
                        </Link>
                    </Button>
                    <Button variant="outline" as-child>
                        <Link :href="`/leader/assignments/${assignment.id}/submissions`">
                            <Users class="h-4 w-4 mr-2" />
                            View Submissions
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Assignment Details -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Main Details -->
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <BookOpen class="h-5 w-5" />
                            Assignment Details
                        </CardTitle>
                        <div class="flex items-center gap-2">
                            <Badge :class="getTypeColor(assignment.type)">
                                {{ getTypeLabel(assignment.type) }}
                            </Badge>
                            <Badge v-if="!assignment.is_active" variant="secondary">
                                Inactive
                            </Badge>
                            <Badge v-if="assignment.is_active && isOverdue(assignment.due_date)" 
                                   class="bg-red-100 text-red-800">
                                Overdue
                            </Badge>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div>
                                <h3 class="font-medium mb-2">Description</h3>
                                <p class="text-muted-foreground">{{ assignment.description }}</p>
                            </div>
                            
                            <div v-if="assignment.instructions && assignment.instructions.trim()">
                                <h3 class="font-medium mb-2">Instructions</h3>
                                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                                    <p class="text-sm text-gray-800 whitespace-pre-wrap font-medium">{{ assignment.instructions }}</p>
                                </div>
                            </div>
                            
                            <!-- Debug: Show if instructions exist but are empty -->
                            <div v-else-if="assignment.instructions !== null && assignment.instructions !== undefined" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                                <h3 class="font-medium mb-2 text-yellow-800">Instructions</h3>
                                <p class="text-sm text-yellow-700">Instructions field exists but appears to be empty or contains only whitespace.</p>
                                <p class="text-xs text-yellow-600 mt-1">Raw value: "{{ assignment.instructions }}"</p>
                            </div>
                            
                            <!-- Show when no instructions at all -->
                            <div v-else class="bg-gray-50 border-l-4 border-gray-300 p-4 rounded-lg">
                                <h3 class="font-medium mb-2 text-gray-600">Instructions</h3>
                                <p class="text-sm text-gray-500">No specific instructions provided for this assignment.</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Assignment Info -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Calendar class="h-5 w-5" />
                            Assignment Info
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div>
                                <h3 class="font-medium mb-1">Group</h3>
                                <p class="text-muted-foreground">{{ assignment.group?.name || 'Unknown Group' }}</p>
                            </div>
                            
                            <div>
                                <h3 class="font-medium mb-1">Due Date</h3>
                                <div class="flex items-center gap-2">
                                    <Calendar class="h-4 w-4 text-muted-foreground" />
                                    <span class="text-sm">{{ formatDate(assignment.due_date) }}</span>
                                </div>
                            </div>
                            
                            <div v-if="assignment.max_points">
                                <h3 class="font-medium mb-1">Max Points</h3>
                                <div class="flex items-center gap-2">
                                    <Clock class="h-4 w-4 text-muted-foreground" />
                                    <span class="text-sm">{{ assignment.max_points }} points</span>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="font-medium mb-1">Created</h3>
                                <p class="text-sm text-muted-foreground">{{ formatDate(assignment.created_at) }}</p>
                            </div>
                            
                            <div>
                                <h3 class="font-medium mb-1">Status</h3>
                                <Badge :variant="assignment.is_active ? 'default' : 'secondary'">
                                    {{ assignment.is_active ? 'Active' : 'Inactive' }}
                                </Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Submissions Overview -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Users class="h-5 w-5" />
                        Submissions Overview
                    </CardTitle>
                    <CardDescription>
                        Recent submissions from group members
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="assignment.submissions?.length === 0" class="text-center py-8">
                        <Users class="h-16 w-16 mx-auto mb-4 text-gray-400 opacity-50" />
                        <h3 class="text-lg font-semibold mb-2">No Submissions Yet</h3>
                        <p class="text-muted-foreground">
                            Group members haven't submitted their work for this assignment yet.
                        </p>
                    </div>
                    
                    <div v-else class="space-y-4">
                        <div v-for="submission in assignment.submissions?.slice(0, 5)" :key="submission.id" 
                             class="flex items-center justify-between p-4 border rounded-lg">
                            <div>
                                <h3 class="font-medium">{{ submission.user?.name || 'Unknown User' }}</h3>
                                <p class="text-sm text-muted-foreground">
                                    Submitted {{ formatDate(submission.submitted_at) }}
                                </p>
                                <div class="flex items-center gap-2 mt-1">
                                    <Badge v-if="submission.graded_at" class="bg-green-100 text-green-800">
                                        Graded: {{ submission.points }}/{{ assignment.max_points || 100 }}
                                    </Badge>
                                    <Badge v-else class="bg-yellow-100 text-yellow-800">
                                        Pending Review
                                    </Badge>
                                </div>
                            </div>
                        </div>
                        
                        <div v-if="assignment.submissions && assignment.submissions.length > 5" class="text-center">
                            <Button variant="outline" as-child>
                                <Link :href="`/leader/assignments/${assignment.id}/submissions`">
                                    View All Submissions ({{ assignment.submissions.length }})
                                </Link>
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LeaderLayout>
</template>
