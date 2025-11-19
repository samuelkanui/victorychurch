<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { 
    Table, 
    TableBody, 
    TableCell, 
    TableHead, 
    TableHeader, 
    TableRow 
} from '@/components/ui/table';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Users, 
    UserPlus, 
    Search, 
    MoreHorizontal,
    Edit,
    Trash2,
    Shield,
    Crown,
    User,
    Filter
} from 'lucide-vue-next';
import { ref, computed } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
    role: 'admin' | 'leader' | 'member';
    email_verified_at: string | null;
    created_at: string;
    groups_count: number;
    last_login_at: string | null;
    is_active: boolean;
}

interface Props {
    users: {
        data: User[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    filters: {
        search?: string;
        role?: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'User Management',
        href: '/admin/users',
    },
];

const searchQuery = ref(props.filters.search || '');
const selectedRole = ref(props.filters.role || '');

const getRoleIcon = (role: string) => {
    switch (role) {
        case 'admin': return Crown;
        case 'leader': return Shield;
        case 'member': return User;
        default: return User;
    }
};

const getRoleBadgeVariant = (role: string) => {
    switch (role) {
        case 'admin': return 'default';
        case 'leader': return 'secondary';
        case 'member': return 'outline';
        default: return 'outline';
    }
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const search = () => {
    router.get('/admin/users', {
        search: searchQuery.value,
        role: selectedRole.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedRole.value = '';
    router.get('/admin/users', {}, {
        preserveState: true,
        replace: true,
    });
};

const deleteUser = (userId: number) => {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(`/admin/users/${userId}`, {
            preserveScroll: true,
        });
    }
};

const totalStats = computed(() => ({
    total: props.users.total,
    admins: props.users.data.filter(u => u.role === 'admin').length,
    leaders: props.users.data.filter(u => u.role === 'leader').length,
    members: props.users.data.filter(u => u.role === 'member').length,
}));
</script>

<template>
    <Head title="User Management" />

    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent">
                        User Management
                    </h1>
                    <p class="text-muted-foreground">
                        Manage church members, leaders, and administrators
                    </p>
                </div>
                <Button as-child class="bg-purple-600 hover:bg-purple-700">
                    <Link href="/admin/users/create">
                        <UserPlus class="mr-2 h-4 w-4" />
                        Add User
                    </Link>
                </Button>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Users</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ totalStats.total }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Administrators</CardTitle>
                        <Crown class="h-4 w-4 text-purple-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-purple-600">{{ totalStats.admins }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Leaders</CardTitle>
                        <Shield class="h-4 w-4 text-blue-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">{{ totalStats.leaders }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Members</CardTitle>
                        <User class="h-4 w-4 text-green-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">{{ totalStats.members }}</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Filters -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Filter class="h-5 w-5" />
                        Filters
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-col gap-4 md:flex-row md:items-end">
                        <div class="flex-1">
                            <label class="text-sm font-medium">Search Users</label>
                            <div class="relative mt-1">
                                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    v-model="searchQuery"
                                    placeholder="Search by name or email..."
                                    class="pl-9"
                                    @keyup.enter="search"
                                />
                            </div>
                        </div>
                        <div class="w-full md:w-48">
                            <label class="text-sm font-medium">Role</label>
                            <select 
                                v-model="selectedRole"
                                class="mt-1 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                            >
                                <option value="">All Roles</option>
                                <option value="admin">Administrator</option>
                                <option value="leader">Leader</option>
                                <option value="member">Member</option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <Button @click="search" class="bg-purple-600 hover:bg-purple-700">
                                Apply Filters
                            </Button>
                            <Button variant="outline" @click="clearFilters">
                                Clear
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Users Table -->
            <Card>
                <CardHeader>
                    <CardTitle>Users</CardTitle>
                    <CardDescription>
                        Showing {{ users.data.length }} of {{ users.total }} users
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>User</TableHead>
                                <TableHead>Role</TableHead>
                                <TableHead>Groups</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Joined</TableHead>
                                <TableHead>Last Login</TableHead>
                                <TableHead class="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="user in users.data" :key="user.id">
                                <TableCell>
                                    <div class="flex items-center space-x-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 text-purple-600">
                                            <User class="h-4 w-4" />
                                        </div>
                                        <div>
                                            <div class="font-medium">{{ user.name }}</div>
                                            <div class="text-sm text-muted-foreground">{{ user.email }}</div>
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <Badge :variant="getRoleBadgeVariant(user.role)" class="flex w-fit items-center gap-1">
                                        <component :is="getRoleIcon(user.role)" class="h-3 w-3" />
                                        {{ user.role.charAt(0).toUpperCase() + user.role.slice(1) }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <span class="text-sm">{{ user.groups_count }} groups</span>
                                </TableCell>
                                <TableCell>
                                    <div class="flex items-center space-x-2">
                                        <div :class="[
                                            'h-2 w-2 rounded-full',
                                            user.is_active ? 'bg-green-500' : 'bg-gray-400'
                                        ]"></div>
                                        <span class="text-sm">
                                            {{ user.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <span class="text-sm">{{ formatDate(user.created_at) }}</span>
                                </TableCell>
                                <TableCell>
                                    <span class="text-sm">
                                        {{ user.last_login_at ? formatDate(user.last_login_at) : 'Never' }}
                                    </span>
                                </TableCell>
                                <TableCell class="text-right">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button variant="ghost" class="h-8 w-8 p-0">
                                                <MoreHorizontal class="h-4 w-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuLabel>Actions</DropdownMenuLabel>
                                            <DropdownMenuItem as-child>
                                                <Link :href="`/admin/users/${user.id}`">
                                                    <User class="mr-2 h-4 w-4" />
                                                    View Profile
                                                </Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem as-child>
                                                <Link :href="`/admin/users/${user.id}/edit`">
                                                    <Edit class="mr-2 h-4 w-4" />
                                                    Edit User
                                                </Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuSeparator />
                                            <DropdownMenuItem 
                                                class="text-red-600"
                                                @click="deleteUser(user.id)"
                                            >
                                                <Trash2 class="mr-2 h-4 w-4" />
                                                Delete User
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <!-- Pagination -->
                    <div v-if="users.last_page > 1" class="mt-4 flex items-center justify-between">
                        <div class="text-sm text-muted-foreground">
                            Showing {{ ((users.current_page - 1) * users.per_page) + 1 }} to 
                            {{ Math.min(users.current_page * users.per_page, users.total) }} of 
                            {{ users.total }} results
                        </div>
                        <div class="flex items-center space-x-2">
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="users.current_page === 1"
                                as-child
                            >
                                <Link :href="`/admin/users?page=${users.current_page - 1}`">
                                    Previous
                                </Link>
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="users.current_page === users.last_page"
                                as-child
                            >
                                <Link :href="`/admin/users?page=${users.current_page + 1}`">
                                    Next
                                </Link>
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
