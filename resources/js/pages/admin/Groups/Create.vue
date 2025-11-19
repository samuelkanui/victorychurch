<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { BookOpen, Users, Calendar } from 'lucide-vue-next'
import { ref, computed } from 'vue'

interface Props {
    leaders: Array<{
        id: number
        name: string
        email: string
    }>
    availableMembers: Array<{
        id: number
        name: string
        email: string
    }>
}

const props = defineProps<Props>()

const breadcrumbs = [
    { title: 'Group Management', href: '/admin/groups' },
    { title: 'Create Group' }
]

const form = useForm({
    name: '',
    description: '',
    leader_id: '',
    max_members: 12,
    meeting_schedule: '',
    member_ids: [] as number[],
})

const searchQuery = ref('')

const filteredMembers = computed(() => {
    if (!searchQuery.value) return props.availableMembers
    
    const query = searchQuery.value.toLowerCase()
    return props.availableMembers.filter(member => 
        member.name.toLowerCase().includes(query) || 
        member.email.toLowerCase().includes(query)
    )
})

const toggleMember = (memberId: number) => {
    const index = form.member_ids.indexOf(memberId)
    if (index > -1) {
        form.member_ids.splice(index, 1)
    } else {
        form.member_ids.push(memberId)
    }
}

const isMemberSelected = (memberId: number) => {
    return form.member_ids.includes(memberId)
}

const submit = () => {
    form.post('/admin/groups', {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Create Group - Group Management" />
    
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent">
                        Create New Group
                    </h1>
                    <p class="text-muted-foreground">
                        Set up a new Bible study group with leader assignment
                    </p>
                </div>
                <Button variant="outline" as-child>
                    <Link href="/admin/groups">
                        Cancel
                    </Link>
                </Button>
            </div>

            <!-- Create Form -->
            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <BookOpen class="h-5 w-5" />
                        Group Information
                    </CardTitle>
                    <CardDescription>
                        Enter the details for the new group
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Group Name -->
                        <div class="space-y-2">
                            <Label for="name">Group Name</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Enter group name"
                                :class="{ 'border-red-500': form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="text-sm text-red-600">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <Label for="description">Description</Label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                placeholder="Enter group description (optional)"
                                class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                :class="{ 'border-red-500': form.errors.description }"
                            ></textarea>
                            <p v-if="form.errors.description" class="text-sm text-red-600">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- Group Leader -->
                        <div class="space-y-2">
                            <Label for="leader_id">Group Leader</Label>
                            <select
                                id="leader_id"
                                v-model="form.leader_id"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                :class="{ 'border-red-500': form.errors.leader_id }"
                            >
                                <option value="">Select a leader</option>
                                <option v-for="leader in leaders" :key="leader.id" :value="leader.id">
                                    {{ leader.name }} ({{ leader.email }})
                                </option>
                            </select>
                            <p v-if="form.errors.leader_id" class="text-sm text-red-600">
                                {{ form.errors.leader_id }}
                            </p>
                        </div>

                        <!-- Max Members -->
                        <div class="space-y-2">
                            <Label for="max_members">Maximum Members</Label>
                            <div class="relative">
                                <Users class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                                <Input
                                    id="max_members"
                                    v-model.number="form.max_members"
                                    type="number"
                                    min="1"
                                    max="100"
                                    placeholder="12"
                                    class="pl-10"
                                    :class="{ 'border-red-500': form.errors.max_members }"
                                />
                            </div>
                            <p v-if="form.errors.max_members" class="text-sm text-red-600">
                                {{ form.errors.max_members }}
                            </p>
                        </div>

                        <!-- Meeting Schedule -->
                        <div class="space-y-2">
                            <Label for="meeting_schedule">Meeting Schedule</Label>
                            <div class="relative">
                                <Calendar class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                                <Input
                                    id="meeting_schedule"
                                    v-model="form.meeting_schedule"
                                    type="text"
                                    placeholder="e.g., Wednesdays 7:00 PM"
                                    class="pl-10"
                                    :class="{ 'border-red-500': form.errors.meeting_schedule }"
                                />
                            </div>
                            <p v-if="form.errors.meeting_schedule" class="text-sm text-red-600">
                                {{ form.errors.meeting_schedule }}
                            </p>
                        </div>

                        <!-- Add Members -->
                        <div class="space-y-2">
                            <Label>Add Members (Optional)</Label>
                            <p class="text-sm text-muted-foreground">Select members to add to this group</p>
                            
                            <!-- Search -->
                            <Input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search members by name or email..."
                                class="mb-2"
                            />
                            
                            <!-- Selected Count -->
                            <div v-if="form.member_ids.length > 0" class="mb-2">
                                <Badge variant="secondary">
                                    {{ form.member_ids.length }} member(s) selected
                                </Badge>
                            </div>
                            
                            <!-- Member List -->
                            <div class="border rounded-lg max-h-60 overflow-y-auto">
                                <div
                                    v-for="member in filteredMembers"
                                    :key="member.id"
                                    @click="toggleMember(member.id)"
                                    class="flex items-center gap-3 p-3 hover:bg-muted/50 cursor-pointer border-b last:border-b-0"
                                    :class="{ 'bg-purple-50 dark:bg-purple-900/20': isMemberSelected(member.id) }"
                                >
                                    <input
                                        type="checkbox"
                                        :checked="isMemberSelected(member.id)"
                                        class="rounded border-gray-300 text-purple-600"
                                        @click.stop
                                    />
                                    <div class="flex-1">
                                        <p class="font-medium text-sm">{{ member.name }}</p>
                                        <p class="text-xs text-muted-foreground">{{ member.email }}</p>
                                    </div>
                                </div>
                                <div v-if="filteredMembers.length === 0" class="p-4 text-center text-sm text-muted-foreground">
                                    No members found
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-2 pt-4">
                            <Button 
                                type="submit" 
                                :disabled="form.processing"
                                class="bg-purple-600 hover:bg-purple-700"
                            >
                                {{ form.processing ? 'Creating...' : 'Create Group' }}
                            </Button>
                            <Button type="button" variant="outline" as-child>
                                <Link href="/admin/groups">
                                    Cancel
                                </Link>
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
