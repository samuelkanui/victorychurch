<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import LeaderLayout from '@/layouts/LeaderLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import { BookOpen, Save, X } from 'lucide-vue-next'

interface Assignment {
    id: number
    title: string
    description: string
    type: string
    due_date: string
    max_points: number | null
    is_active: boolean
    instructions: string | null
}

interface Props {
    assignment: Assignment
    groups: Array<{
        id: number
        name: string
    }>
}

const props = defineProps<Props>()

const breadcrumbs = [
    { title: 'Assignments', href: '/leader/assignments' },
    { title: props.assignment.title, href: `/leader/assignments/${props.assignment.id}` },
    { title: 'Edit' }
]

// Format date for input field (YYYY-MM-DDTHH:MM)
const formatDateForInput = (dateString: string) => {
    if (!dateString) return ''
    try {
        const date = new Date(dateString)
        return date.toISOString().slice(0, 16)
    } catch {
        return ''
    }
}

const form = useForm({
    title: props.assignment.title,
    description: props.assignment.description,
    type: props.assignment.type,
    due_date: formatDateForInput(props.assignment.due_date),
    max_points: props.assignment.max_points || 100,
    instructions: props.assignment.instructions || '',
    is_active: props.assignment.is_active,
})


const assignmentTypes = [
    { value: 'bible_study', label: 'Bible Study' },
    { value: 'reflection', label: 'Reflection' },
    { value: 'memorization', label: 'Memorization' },
    { value: 'research', label: 'Research' },
]

const submit = () => {
    form.put(`/leader/assignments/${props.assignment.id}`, {
        onSuccess: () => {
            // Handle success - redirect happens automatically
        }
    })
}
</script>

<template>
    <Head :title="`Edit ${assignment.title} - Assignments`" />
    
    <LeaderLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        Edit Assignment
                    </h1>
                    <p class="text-muted-foreground">
                        Update assignment details and settings
                    </p>
                </div>
            </div>

            <!-- Edit Form -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <BookOpen class="h-5 w-5" />
                        Assignment Details
                    </CardTitle>
                    <CardDescription>
                        Modify assignment information, due dates, and instructions
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-6 md:grid-cols-2">
                            <!-- Title -->
                            <div class="space-y-2">
                                <Label for="title">Assignment Title *</Label>
                                <Input 
                                    id="title"
                                    v-model="form.title"
                                    placeholder="Enter assignment title"
                                    :class="{ 'border-red-500': form.errors.title }"
                                />
                                <p v-if="form.errors.title" class="text-sm text-red-600">{{ form.errors.title }}</p>
                            </div>

                            <!-- Type -->
                            <div class="space-y-2">
                                <Label for="type">Assignment Type *</Label>
                                <select 
                                    id="type"
                                    v-model="form.type"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    :class="{ 'border-red-500': form.errors.type }"
                                >
                                    <option value="">Select assignment type</option>
                                    <option v-for="type in assignmentTypes" :key="type.value" :value="type.value">
                                        {{ type.label }}
                                    </option>
                                </select>
                                <p v-if="form.errors.type" class="text-sm text-red-600">{{ form.errors.type }}</p>
                            </div>

                            <!-- Due Date -->
                            <div class="space-y-2">
                                <Label for="due_date">Due Date *</Label>
                                <Input 
                                    id="due_date"
                                    v-model="form.due_date"
                                    type="datetime-local"
                                    :class="{ 'border-red-500': form.errors.due_date }"
                                />
                                <p v-if="form.errors.due_date" class="text-sm text-red-600">{{ form.errors.due_date }}</p>
                            </div>

                            <!-- Max Points -->
                            <div class="space-y-2">
                                <Label for="max_points">Maximum Points</Label>
                                <Input 
                                    id="max_points"
                                    v-model="form.max_points"
                                    type="number"
                                    min="1"
                                    max="100"
                                    placeholder="100"
                                    :class="{ 'border-red-500': form.errors.max_points }"
                                />
                                <p v-if="form.errors.max_points" class="text-sm text-red-600">{{ form.errors.max_points }}</p>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <Label for="description">Description *</Label>
                            <textarea 
                                id="description"
                                v-model="form.description"
                                placeholder="Describe the assignment objectives and requirements"
                                rows="3"
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                :class="{ 'border-red-500': form.errors.description }"
                            ></textarea>
                            <p v-if="form.errors.description" class="text-sm text-red-600">{{ form.errors.description }}</p>
                        </div>

                        <!-- Instructions -->
                        <div class="space-y-2">
                            <Label for="instructions">Detailed Instructions</Label>
                            <textarea 
                                id="instructions"
                                v-model="form.instructions"
                                placeholder="Provide detailed instructions for completing this assignment"
                                rows="4"
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                :class="{ 'border-red-500': form.errors.instructions }"
                            ></textarea>
                            <p v-if="form.errors.instructions" class="text-sm text-red-600">{{ form.errors.instructions }}</p>
                        </div>

                        <!-- Active Status -->
                        <div class="flex items-center space-x-2">
                            <input 
                                id="is_active"
                                v-model="form.is_active"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            />
                            <Label for="is_active">Assignment is active</Label>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center gap-4 pt-4">
                            <Button 
                                type="submit" 
                                :disabled="form.processing"
                                class="bg-blue-600 hover:bg-blue-700"
                            >
                                <Save class="h-4 w-4 mr-2" />
                                {{ form.processing ? 'Saving...' : 'Save Changes' }}
                            </Button>
                            
                            <Button 
                                type="button" 
                                variant="outline"
                                @click="$inertia.visit(`/leader/assignments/${assignment.id}`)"
                            >
                                <X class="h-4 w-4 mr-2" />
                                Cancel
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </LeaderLayout>
</template>
