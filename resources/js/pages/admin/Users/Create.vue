<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { UserPlus, ArrowLeft } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'User Management',
        href: '/admin/users',
    },
    {
        title: 'Create User',
        href: '/admin/users/create',
    },
];

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'member',
});

const submit = () => {
    form.post('/admin/users', {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Create User" />

    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent">
                        Create New User
                    </h1>
                    <p class="text-muted-foreground">
                        Add a new member, leader, or administrator to the system
                    </p>
                </div>
                <Button variant="outline" as-child>
                    <a href="/admin/users">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Back to Users
                    </a>
                </Button>
            </div>

            <!-- Create User Form -->
            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <UserPlus class="h-5 w-5" />
                        User Information
                    </CardTitle>
                    <CardDescription>
                        Enter the details for the new user account
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
                                required
                            />
                            <p v-if="form.errors.name" class="text-sm text-red-600">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <Label for="email">Email Address</Label>
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                                placeholder="Enter email address"
                                :class="{ 'border-red-500': form.errors.email }"
                                required
                            />
                            <p v-if="form.errors.email" class="text-sm text-red-600">
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- Role -->
                        <div class="space-y-2">
                            <Label for="role">Role</Label>
                            <select
                                id="role"
                                v-model="form.role"
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                :class="{ 'border-red-500': form.errors.role }"
                                required
                            >
                                <option value="member">Member</option>
                                <option value="leader">Leader</option>
                                <option value="admin">Administrator</option>
                            </select>
                            <p v-if="form.errors.role" class="text-sm text-red-600">
                                {{ form.errors.role }}
                            </p>
                            <p class="text-sm text-muted-foreground">
                                Choose the appropriate role for this user
                            </p>
                        </div>

                        <!-- Password -->
                        <div class="space-y-2">
                            <Label for="password">Password</Label>
                            <Input
                                id="password"
                                v-model="form.password"
                                type="password"
                                placeholder="Enter password"
                                :class="{ 'border-red-500': form.errors.password }"
                                required
                            />
                            <p v-if="form.errors.password" class="text-sm text-red-600">
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <!-- Confirm Password -->
                        <div class="space-y-2">
                            <Label for="password_confirmation">Confirm Password</Label>
                            <Input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                placeholder="Confirm password"
                                :class="{ 'border-red-500': form.errors.password_confirmation }"
                                required
                            />
                            <p v-if="form.errors.password_confirmation" class="text-sm text-red-600">
                                {{ form.errors.password_confirmation }}
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end space-x-4 pt-4">
                            <Button type="button" variant="outline" as-child>
                                <a href="/admin/users">Cancel</a>
                            </Button>
                            <Button 
                                type="submit" 
                                class="bg-purple-600 hover:bg-purple-700"
                                :disabled="form.processing"
                            >
                                <UserPlus class="mr-2 h-4 w-4" />
                                {{ form.processing ? 'Creating...' : 'Create User' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
