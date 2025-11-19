<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { BookOpen, Crown, Users, Calendar, User, UserMinus, UserPlus, Search } from 'lucide-vue-next'

interface Props {
    group: {
        id: number
        name: string
        description: string
        leader_id: number
        max_members: number
        meeting_schedule: string | null
        is_active: boolean
        members?: Array<{
            id: number
            name: string
            email: string
            pivot: {
                status: string
                joined_at: string
            }
        }>
    }
    leaders: Array<{
        id: number
        name: string
        email: string
    }>
    availableMembers?: Array<{
        id: number
        name: string
        email: string
    }>
}

const props = defineProps<Props>()

const breadcrumbs = [
    { title: 'Group Management', href: '/admin/groups' },
    { title: props.group.name, href: `/admin/groups/${props.group.id}` },
    { title: 'Edit' }
]

const form = useForm({
    name: props.group.name,
    description: props.group.description || '',
    leader_id: props.group.leader_id.toString(),
    max_members: props.group.max_members,
    meeting_schedule: props.group.meeting_schedule || '',
    is_active: props.group.is_active,
})

const submit = () => {
    form.put(`/admin/groups/${props.group.id}`, {
        preserveScroll: true,
    })
}

const removeMember = (memberId: number, memberName: string) => {
    if (confirm(`Are you sure you want to remove ${memberName} from this group?`)) {
        router.delete(`/admin/groups/${props.group.id}/members/${memberId}`, {
            preserveScroll: true,
            onSuccess: () => {
                // Success message will be shown via flash
            }
        })
    }
}

// Member addition functionality
const searchQuery = ref('')
const selectedMembers = ref<number[]>([])
const showAddMembers = ref(false)

const filteredAvailableMembers = computed(() => {
    if (!props.availableMembers) return []
    if (!searchQuery.value) return props.availableMembers
    
    const query = searchQuery.value.toLowerCase()
    return props.availableMembers.filter(member => 
        member.name.toLowerCase().includes(query) || 
        member.email.toLowerCase().includes(query)
    )
})

const toggleMember = (memberId: number) => {
    const index = selectedMembers.value.indexOf(memberId)
    if (index > -1) {
        selectedMembers.value.splice(index, 1)
    } else {
        selectedMembers.value.push(memberId)
    }
}

const addMembers = () => {
    if (selectedMembers.value.length === 0) {
        alert('Please select at least one member to add')
        return
    }
    
    router.post(`/admin/groups/${props.group.id}/members`, {
        member_ids: selectedMembers.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            selectedMembers.value = []
            showAddMembers.value = false
            searchQuery.value = ''
        }
    })
}
</script>

<template>
    <Head :title="`Edit ${group.name} - Group Management`" />
    
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent">
                        Edit Group
                    </h1>
                    <p class="text-muted-foreground">
                        Update group information and settings
                    </p>
                </div>
                <Button variant="outline" as-child>
                    <Link :href="`/admin/groups/${group.id}`">
                        Cancel
                    </Link>
                </Button>
            </div>

            <!-- Edit Form -->
            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <BookOpen class="h-5 w-5" />
                        Group Information
                    </CardTitle>
                    <CardDescription>
                        Update the group's details and settings
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
                                <option v-for="leader in leaders" :key="leader.id" :value="leader.id.toString()">
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

                        <!-- Active Status -->
                        <div class="flex items-center justify-between p-4 border rounded-lg">
                            <div class="space-y-0.5">
                                <Label for="is_active">Group Status</Label>
                                <p class="text-sm text-muted-foreground">
                                    Enable or disable this group
                                </p>
                            </div>
                            <label class="flex items-center space-x-2">
                                <input
                                    id="is_active"
                                    type="checkbox"
                                    v-model="form.is_active"
                                    class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50"
                                />
                                <span class="text-sm">{{ form.is_active ? 'Active' : 'Inactive' }}</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-2 pt-4">
                            <Button 
                                type="submit" 
                                :disabled="form.processing"
                                class="bg-purple-600 hover:bg-purple-700"
                            >
                                {{ form.processing ? 'Saving...' : 'Save Changes' }}
                            </Button>
                            <Button type="button" variant="outline" as-child>
                                <Link :href="`/admin/groups/${group.id}`">
                                    Cancel
                                </Link>
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

            <!-- Add Members Section -->
            <Card v-if="availableMembers && availableMembers.length > 0" class="max-w-2xl">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle class="flex items-center gap-2">
                                <UserPlus class="h-5 w-5" />
                                Add Members
                            </CardTitle>
                            <CardDescription>
                                Add new members to this group
                            </CardDescription>
                        </div>
                        <Button
                            variant="outline"
                            size="sm"
                            @click="showAddMembers = !showAddMembers"
                        >
                            {{ showAddMembers ? 'Hide' : 'Show Available Members' }}
                        </Button>
                    </div>
                </CardHeader>
                <CardContent v-if="showAddMembers">
                    <div class="space-y-4">
                        <!-- Search Bar -->
                        <div class="relative">
                            <Search class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                            <Input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search members by name or email..."
                                class="pl-10"
                            />
                        </div>

                        <!-- Selected Count Badge -->
                        <div v-if="selectedMembers.length > 0" class="flex items-center gap-2">
                            <Badge variant="default">
                                {{ selectedMembers.length }} member(s) selected
                            </Badge>
                        </div>

                        <!-- Available Members List -->
                        <div class="border rounded-lg max-h-60 overflow-y-auto">
                            <div v-if="filteredAvailableMembers.length === 0" class="p-4 text-center text-muted-foreground">
                                No members found
                            </div>
                            <div
                                v-for="member in filteredAvailableMembers"
                                :key="member.id"
                                @click="toggleMember(member.id)"
                                class="flex items-center gap-3 p-3 hover:bg-muted/50 cursor-pointer transition-colors border-b last:border-b-0"
                                :class="{ 'bg-purple-50 dark:bg-purple-900/20': selectedMembers.includes(member.id) }"
                            >
                                <input
                                    type="checkbox"
                                    :checked="selectedMembers.includes(member.id)"
                                    @click.stop="toggleMember(member.id)"
                                    class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50"
                                />
                                <div class="flex-1">
                                    <p class="font-medium">{{ member.name }}</p>
                                    <p class="text-sm text-muted-foreground">{{ member.email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Add Button -->
                        <Button
                            @click="addMembers"
                            :disabled="selectedMembers.length === 0"
                            class="w-full bg-purple-600 hover:bg-purple-700"
                        >
                            <UserPlus class="h-4 w-4 mr-2" />
                            Add {{ selectedMembers.length }} Member(s)
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Group Members List -->
            <Card v-if="group.members && group.members.length > 0" class="max-w-2xl">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Users class="h-5 w-5" />
                        Group Members ({{ group.members.length }})
                    </CardTitle>
                    <CardDescription>
                        Current members of this group
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-3">
                        <div 
                            v-for="member in group.members" 
                            :key="member.id"
                            class="flex items-center justify-between p-3 border rounded-lg hover:bg-muted/50 transition-colors"
                        >
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                    <User class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                                </div>
                                <div>
                                    <p class="font-medium">{{ member.name }}</p>
                                    <p class="text-sm text-muted-foreground">{{ member.email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <Badge :variant="member.pivot.status === 'approved' ? 'default' : member.pivot.status === 'pending' ? 'secondary' : 'outline'">
                                    {{ member.pivot.status }}
                                </Badge>
                                <span class="text-xs text-muted-foreground">
                                    {{ new Date(member.pivot.joined_at).toLocaleDateString() }}
                                </span>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    @click="removeMember(member.id, member.name)"
                                    class="text-red-600 hover:text-red-700 hover:bg-red-50"
                                >
                                    <UserMinus class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- No Members Message -->
            <Card v-else class="max-w-2xl">
                <CardContent class="py-8">
                    <div class="text-center text-muted-foreground">
                        <Users class="h-12 w-12 mx-auto mb-4 opacity-50" />
                        <p>No members in this group yet</p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
