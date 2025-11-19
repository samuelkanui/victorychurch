<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Checkbox } from '@/components/ui/checkbox'
import { User, Mail, Shield, Crown } from 'lucide-vue-next'

interface Props {
    user: {
        id: number
        name: string
        email: string
        role: string
        is_active: boolean
    }
}

const props = defineProps<Props>()

const breadcrumbs = [
    { title: 'User Management', href: '/admin/users' },
    { title: props.user.name, href: `/admin/users/${props.user.id}` },
    { title: 'Edit' }
]

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role: props.user.role,
    is_active: props.user.is_active,
})

const submit = () => {
    form.put(`/admin/users/${props.user.id}`, {
        preserveScroll: true,
    })
}

const getRoleIcon = (role: string) => {
    switch (role) {
        case 'admin': return Crown
        case 'leader': return Shield
        case 'member': return User
        default: return User
    }
}
</script>

<template>
    <Head :title="`Edit ${user.name} - User Management`" />
    
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent">
                        Edit User
                    </h1>
                    <p class="text-muted-foreground">
                        Update user information and settings
                    </p>
                </div>
                <Button variant="outline" as-child>
                    <Link :href="`/admin/users/${user.id}`">
                        Cancel
                    </Link>
                </Button>
            </div>

            <!-- Edit Form -->
            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <component :is="getRoleIcon(user.role)" class="h-5 w-5" />
                        User Information
                    </CardTitle>
                    <CardDescription>
                        Update the user's basic information and account settings
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Name -->
                        <div class="space-y-2">
                            <Label for="name">Full Name</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Enter full name"
                                :class="{ 'border-red-500': form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="text-sm text-red-600">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <Label for="email">Email Address</Label>
                            <div class="relative">
                                <Mail class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                                <Input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    placeholder="Enter email address"
                                    class="pl-10"
                                    :class="{ 'border-red-500': form.errors.email }"
                                />
                            </div>
                            <p v-if="form.errors.email" class="text-sm text-red-600">
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- Role -->
                        <div class="space-y-2">
                            <Label for="role">User Role</Label>
                            <select
                                id="role"
                                v-model="form.role"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                :class="{ 'border-red-500': form.errors.role }"
                            >
                                <option value="">Select a role</option>
                                <option value="member">Member</option>
                                <option value="leader">Leader</option>
                                <option value="admin">Administrator</option>
                            </select>
                            <p v-if="form.errors.role" class="text-sm text-red-600">
                                {{ form.errors.role }}
                            </p>
                        </div>

                        <!-- Active Status -->
                        <div class="flex items-center justify-between p-4 border rounded-lg">
                            <div class="space-y-0.5">
                                <Label for="is_active">Account Status</Label>
                                <p class="text-sm text-muted-foreground">
                                    Enable or disable this user account
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
                                <Link :href="`/admin/users/${user.id}`">
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
