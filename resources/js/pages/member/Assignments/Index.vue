<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
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
    BookOpen, 
    Eye, 
    Upload,
    Clock,
    CheckCircle,
    AlertCircle,
    Calendar,
    Users,
    Target,
    Award,
    TrendingUp,
    Filter
} from 'lucide-vue-next'
import { ref, computed } from 'vue'

interface Assignment {
    id: number
    title: string
    description: string
    type: string
    due_date: string
    max_points: number | null
    is_active: boolean
    group: {
        id: number
        name: string
    }
    submission?: {
        id: number
        status: string
        submitted_at: string
        grade?: number
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

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Assignments' }
]

// Helper functions (declared first)
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

const isOverdue = (dueDate: string) => {
    if (!dueDate) return false
    try {
        return new Date(dueDate) < new Date()
    } catch (error) {
        return false
    }
}

const truncateText = (text: string, maxLength: number = 150) => {
    if (!text) return ''
    return text.length > maxLength ? text.substring(0, maxLength) + '...' : text
}

// Reactive filter state
const selectedFilter = ref('all')

// Computed filtered assignments
const filteredAssignments = computed(() => {
    if (!props.assignments?.data) return []
    
    switch (selectedFilter.value) {
        case 'pending':
            return props.assignments.data.filter(a => !a.submission && a.is_active)
        case 'submitted':
            return props.assignments.data.filter(a => a.submission)
        case 'overdue':
            return props.assignments.data.filter(a => !a.submission && isOverdue(a.due_date))
        case 'graded':
            return props.assignments.data.filter(a => a.submission?.grade !== null && a.submission?.grade !== undefined)
        default:
            return props.assignments.data
    }
})

// Statistics computed properties
const stats = computed(() => ({
    total: props.assignments?.data?.length || 0,
    pending: props.assignments?.data?.filter(a => !a.submission && a.is_active).length || 0,
    submitted: props.assignments?.data?.filter(a => a.submission).length || 0,
    overdue: props.assignments?.data?.filter(a => !a.submission && isOverdue(a.due_date)).length || 0,
    graded: props.assignments?.data?.filter(a => a.submission?.grade !== null && a.submission?.grade !== undefined).length || 0,
    averageGrade: (() => {
        const gradedAssignments = props.assignments?.data?.filter(a => a.submission?.grade !== null && a.submission?.grade !== undefined) || []
        if (gradedAssignments.length === 0) return 0
        const totalGrade = gradedAssignments.reduce((sum, a) => sum + (a.submission?.grade || 0), 0)
        return Math.round(totalGrade / gradedAssignments.length)
    })()
}))

const filterOptions = computed(() => [
    { value: 'all', label: 'All Assignments', count: stats.value.total },
    { value: 'pending', label: 'Pending', count: stats.value.pending },
    { value: 'submitted', label: 'Submitted', count: stats.value.submitted },
    { value: 'overdue', label: 'Overdue', count: stats.value.overdue },
    { value: 'graded', label: 'Graded', count: stats.value.graded },
])

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

const getSubmissionStatusColor = (status: string) => {
    switch (status) {
        case 'submitted': return 'bg-blue-100 text-blue-800'
        case 'graded': return 'bg-green-100 text-green-800'
        case 'returned': return 'bg-yellow-100 text-yellow-800'
        default: return 'bg-gray-100 text-gray-800'
    }
}
</script>

<template>
    <Head title="Assignments - Member Dashboard" />
    
    <MemberLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-green-600 to-green-800 bg-clip-text text-transparent">
                        Assignments
                    </h1>
                    <p class="text-muted-foreground">
                        View and submit your group assignments
                    </p>
                </div>
            </div>

            <!-- Assignment Statistics -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Assignments</CardTitle>
                        <BookOpen class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">{{ stats.total }}</div>
                        <p class="text-xs text-muted-foreground">All assignments</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Pending</CardTitle>
                        <Clock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-orange-600">{{ stats.pending }}</div>
                        <p class="text-xs text-muted-foreground">Need submission</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Submitted</CardTitle>
                        <CheckCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">{{ stats.submitted }}</div>
                        <p class="text-xs text-muted-foreground">Completed</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Overdue</CardTitle>
                        <AlertCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-red-600">{{ stats.overdue }}</div>
                        <p class="text-xs text-muted-foreground">Past due</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Assignments Tabs -->
            <Tabs v-model="selectedFilter" class="w-full">
                <TabsList class="grid w-full grid-cols-5">
                    <TabsTrigger 
                        v-for="option in filterOptions" 
                        :key="option.value"
                        :value="option.value"
                        class="flex items-center gap-2"
                    >
                        <Filter class="h-4 w-4" />
                        {{ option.label }} ({{ option.count }})
                    </TabsTrigger>
                </TabsList>

                <!-- Assignment List -->
                <div class="mt-6">
                    <div v-if="!filteredAssignments || filteredAssignments.length === 0" class="text-center py-12">
                        <BookOpen class="h-16 w-16 mx-auto mb-4 text-gray-400 opacity-50" />
                        <h3 class="text-lg font-semibold mb-2">No Assignments</h3>
                        <p class="text-muted-foreground">
                            {{ selectedFilter === 'all' 
                                ? 'No assignments have been created for your groups yet.' 
                                : `No assignments match the ${selectedFilter} filter.` }}
                        </p>
                    </div>

                    <div v-else class="space-y-4">
                        <Card 
                            v-for="assignment in filteredAssignments" 
                            :key="assignment.id" 
                            class="hover:shadow-md transition-shadow"
                        >
                            <CardContent class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="font-semibold text-lg">{{ assignment.title }}</h3>
                                            <Badge :class="getTypeColor(assignment.type)">
                                                {{ getTypeLabel(assignment.type) }}
                                            </Badge>
                                            <Badge v-if="assignment.submission" :class="getSubmissionStatusColor(assignment.submission.status)">
                                                {{ assignment.submission.status.charAt(0).toUpperCase() + assignment.submission.status.slice(1) }}
                                            </Badge>
                                            <Badge v-else-if="!assignment.submission && isOverdue(assignment.due_date)" class="bg-red-100 text-red-800">
                                                <AlertCircle class="h-3 w-3 mr-1" />
                                                Overdue
                                            </Badge>
                                            <Badge v-else-if="!assignment.submission" class="bg-orange-100 text-orange-800">
                                                <Clock class="h-3 w-3 mr-1" />
                                                Pending
                                            </Badge>
                                        </div>
                                        
                                        <div class="flex items-center gap-4 text-sm text-muted-foreground mb-3">
                                            <div class="flex items-center gap-1">
                                                <Users class="h-4 w-4" />
                                                <span>{{ assignment.group?.name || 'Unknown Group' }}</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <Calendar class="h-4 w-4" />
                                                <span>Due {{ formatDate(assignment.due_date) }}</span>
                                            </div>
                                            <div v-if="assignment.max_points" class="flex items-center gap-1">
                                                <BookOpen class="h-4 w-4" />
                                                <span>{{ assignment.max_points }} points</span>
                                            </div>
                                            <div v-if="assignment.submission?.grade !== null && assignment.submission?.grade !== undefined" class="flex items-center gap-1">
                                                <CheckCircle class="h-4 w-4" />
                                                <span>Grade: {{ assignment.submission.grade }}/{{ assignment.max_points || 100 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-2 ml-4">
                                        <Button variant="outline" size="sm" as-child>
                                            <Link :href="`/member/assignments/${assignment.id}`">
                                                <Eye class="h-4 w-4 mr-1" />
                                                View
                                            </Link>
                                        </Button>
                                        <Button v-if="!assignment.submission && assignment.is_active" size="sm" as-child>
                                            <Link :href="`/member/assignments/${assignment.id}/submission`">
                                                <Upload class="h-4 w-4 mr-1" />
                                                Submit
                                            </Link>
                                        </Button>
                                        <Button v-else-if="assignment.submission" variant="outline" size="sm" as-child>
                                            <Link :href="`/member/assignments/${assignment.id}/submission`">
                                                <Eye class="h-4 w-4 mr-1" />
                                                View Submission
                                            </Link>
                                        </Button>
                                    </div>
                                </div>
                                
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm text-gray-700">{{ truncateText(assignment.description) }}</p>
                                    <Link v-if="assignment.description.length > 150" 
                                          :href="`/member/assignments/${assignment.id}`" 
                                          class="text-blue-600 hover:underline text-sm mt-2 inline-block">
                                        Read more...
                                    </Link>
                                </div>

                                <div v-if="assignment.submission" class="mt-4 p-3 bg-blue-50 rounded-lg border-l-4 border-blue-400">
                                    <p class="text-sm text-blue-800">
                                        <strong>Submitted:</strong> {{ formatDate(assignment.submission.submitted_at) }}
                                    </p>
                                    <p v-if="assignment.submission.grade !== null && assignment.submission.grade !== undefined" class="text-sm text-blue-800 mt-1">
                                        <strong>Grade:</strong> {{ assignment.submission.grade }}/{{ assignment.max_points || 100 }}
                                    </p>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </Tabs>
        </div>
    </MemberLayout>
</template>
