<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import LeaderLayout from '@/layouts/LeaderLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { 
    BookOpen, 
    Plus, 
    Eye, 
    Edit, 
    Trash2, 
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
    created_at: string
    submissions_count: number
    group: {
        id: number
        name: string
    }
}

interface Props {
    assignments: {
        data: Assignment[]
        links: any[]
        meta: any
    }
}

const props = defineProps<Props>()

const breadcrumbs = [
    { title: 'Assignments' }
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
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const isOverdue = (dueDateString: string) => {
    return new Date(dueDateString) < new Date()
}
</script>

<template>
    <Head title="Assignments - Leader Dashboard" />
    
    <LeaderLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        Assignments
                    </h1>
                    <p class="text-muted-foreground">
                        Create and manage Bible study assignments for your groups
                    </p>
                </div>
                <Button class="bg-blue-600 hover:bg-blue-700" as-child>
                    <Link href="/leader/assignments/create">
                        <Plus class="h-4 w-4 mr-2" />
                        Create Assignment
                    </Link>
                </Button>
            </div>

            <!-- Statistics -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Assignments</CardTitle>
                        <BookOpen class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">{{ assignments.data.length }}</div>
                        <p class="text-xs text-muted-foreground">All assignments</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Active</CardTitle>
                        <CheckCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">
                            {{ assignments.data.filter(a => a.is_active).length }}
                        </div>
                        <p class="text-xs text-muted-foreground">Currently active</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Overdue</CardTitle>
                        <AlertCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-red-600">
                            {{ assignments.data.filter(a => a.is_active && isOverdue(a.due_date)).length }}
                        </div>
                        <p class="text-xs text-muted-foreground">Past due date</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Submissions</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-purple-600">
                            {{ assignments.data.reduce((sum, a) => sum + a.submissions_count, 0) }}
                        </div>
                        <p class="text-xs text-muted-foreground">Student submissions</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Assignments List -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <BookOpen class="h-5 w-5" />
                        All Assignments
                    </CardTitle>
                    <CardDescription>
                        Manage your Bible study assignments and track student progress
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="assignments.data.length === 0" class="text-center py-12">
                        <BookOpen class="h-16 w-16 mx-auto mb-4 text-gray-400 opacity-50" />
                        <h3 class="text-lg font-semibold mb-2">No Assignments Yet</h3>
                        <p class="text-muted-foreground mb-4">
                            Create your first assignment to start engaging your group members with Bible study activities.
                        </p>
                        <Button as-child>
                            <Link href="/leader/assignments/create">
                                <Plus class="h-4 w-4 mr-2" />
                                Create First Assignment
                            </Link>
                        </Button>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="assignment in assignments.data" :key="assignment.id" 
                             class="flex items-center justify-between p-4 border rounded-lg hover:shadow-md transition-shadow">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="font-semibold text-lg">{{ assignment.title }}</h3>
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
                                
                                <p class="text-muted-foreground mb-3 line-clamp-2">
                                    {{ assignment.description }}
                                </p>
                                
                                <div class="flex items-center gap-6 text-sm text-muted-foreground">
                                    <div class="flex items-center gap-1">
                                        <Users class="h-4 w-4" />
                                        <span>{{ assignment.group.name }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <Calendar class="h-4 w-4" />
                                        <span>Due {{ formatDate(assignment.due_date) }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <CheckCircle class="h-4 w-4" />
                                        <span>{{ assignment.submissions_count }} submissions</span>
                                    </div>
                                    <div v-if="assignment.max_points" class="flex items-center gap-1">
                                        <Clock class="h-4 w-4" />
                                        <span>{{ assignment.max_points }} points</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2 ml-4">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="`/leader/assignments/${assignment.id}`">
                                        <Eye class="h-4 w-4 mr-1" />
                                        View
                                    </Link>
                                </Button>
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="`/leader/assignments/${assignment.id}/edit`">
                                        <Edit class="h-4 w-4 mr-1" />
                                        Edit
                                    </Link>
                                </Button>
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="`/leader/assignments/${assignment.id}/submissions`">
                                        <Users class="h-4 w-4 mr-1" />
                                        Submissions
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LeaderLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
