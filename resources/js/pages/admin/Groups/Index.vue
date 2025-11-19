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
    BookOpen, 
    Plus, 
    Search, 
    MoreHorizontal,
    Edit,
    Trash2,
    Eye,
    Users,
    UserCheck,
    Calendar,
    Filter,
    Crown
} from 'lucide-vue-next';
import { ref, computed } from 'vue';

interface Group {
    id: number;
    name: string;
    description: string;
    leader: {
        id: number;
        name: string;
        email: string;
    };
    is_active: boolean;
    max_members: number;
    meeting_schedule: string | null;
    members_count: number;
    approved_members_count: number;
    created_at: string;
}

interface Props {
    groups: {
        data: Group[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    filters: {
        search?: string;
        status?: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'Group Management',
        href: '/admin/groups',
    },
];

const searchQuery = ref(props.filters.search || '');
const selectedStatus = ref(props.filters.status || '');

const getStatusBadgeVariant = (isActive: boolean) => {
    return isActive ? 'default' : 'secondary';
};


const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const search = () => {
    router.get('/admin/groups', {
        search: searchQuery.value,
        status: selectedStatus.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedStatus.value = '';
    router.get('/admin/groups', {}, {
        preserveState: true,
        replace: true,
    });
};

const deleteGroup = (groupId: number) => {
    if (confirm('Are you sure you want to delete this group? This action cannot be undone.')) {
        router.delete(`/admin/groups/${groupId}`, {
            preserveScroll: true,
        });
    }
};

const totalStats = computed(() => ({
    total: props.groups.total,
    active: props.groups.data.filter(g => g.is_active).length,
    inactive: props.groups.data.filter(g => !g.is_active).length,
    totalMembers: props.groups.data.reduce((sum, g) => sum + g.approved_members_count, 0),
}));
</script>

<template>
    <Head title="Group Management" />

    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent">
                        Group Management
                    </h1>
                    <p class="text-muted-foreground">
                        Manage Bible study groups, leaders, and member assignments
                    </p>
                </div>
                <Button as-child class="bg-purple-600 hover:bg-purple-700">
                    <Link href="/admin/groups/create">
                        <Plus class="mr-2 h-4 w-4" />
                        Create Group
                    </Link>
                </Button>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Groups</CardTitle>
                        <BookOpen class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ totalStats.total }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Active Groups</CardTitle>
                        <UserCheck class="h-4 w-4 text-green-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">{{ totalStats.active }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Inactive Groups</CardTitle>
                        <Users class="h-4 w-4 text-gray-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-gray-600">{{ totalStats.inactive }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Members</CardTitle>
                        <Users class="h-4 w-4 text-blue-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">{{ totalStats.totalMembers }}</div>
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
                            <label class="text-sm font-medium">Search Groups</label>
                            <div class="relative mt-1">
                                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    v-model="searchQuery"
                                    placeholder="Search by name, description, or leader..."
                                    class="pl-9"
                                    @keyup.enter="search"
                                />
                            </div>
                        </div>
                        <div class="w-full md:w-48">
                            <label class="text-sm font-medium">Status</label>
                            <select 
                                v-model="selectedStatus"
                                class="mt-1 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                            >
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
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

            <!-- Groups Table -->
            <Card>
                <CardHeader>
                    <CardTitle>Groups</CardTitle>
                    <CardDescription>
                        Showing {{ groups.data.length }} of {{ groups.total }} groups
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Group</TableHead>
                                <TableHead>Leader</TableHead>
                                <TableHead>Members</TableHead>
                                <TableHead>Schedule</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Created</TableHead>
                                <TableHead class="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="group in groups.data" :key="group.id">
                                <TableCell>
                                    <div class="flex items-center space-x-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 text-purple-600">
                                            <BookOpen class="h-4 w-4" />
                                        </div>
                                        <div>
                                            <div class="font-medium">{{ group.name }}</div>
                                            <div class="text-sm text-muted-foreground line-clamp-1">
                                                {{ group.description || 'No description' }}
                                            </div>
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div class="flex items-center space-x-2">
                                        <Crown class="h-4 w-4 text-blue-600" />
                                        <div>
                                            <div class="font-medium">{{ group.leader.name }}</div>
                                            <div class="text-sm text-muted-foreground">{{ group.leader.email }}</div>
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div class="text-sm">
                                        <div class="font-medium">{{ group.approved_members_count }}/{{ group.max_members }}</div>
                                        <div class="text-muted-foreground">
                                            {{ group.members_count - group.approved_members_count }} pending
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div class="flex items-center space-x-2">
                                        <Calendar class="h-4 w-4 text-muted-foreground" />
                                        <span class="text-sm">
                                            {{ group.meeting_schedule || 'Not scheduled' }}
                                        </span>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <Badge :variant="getStatusBadgeVariant(group.is_active)">
                                        {{ group.is_active ? 'Active' : 'Inactive' }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <span class="text-sm">{{ formatDate(group.created_at) }}</span>
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
                                                <Link :href="`/admin/groups/${group.id}`">
                                                    <Eye class="mr-2 h-4 w-4" />
                                                    View Details
                                                </Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem as-child>
                                                <Link :href="`/admin/groups/${group.id}/edit`">
                                                    <Edit class="mr-2 h-4 w-4" />
                                                    Edit Group
                                                </Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuSeparator />
                                            <DropdownMenuItem 
                                                class="text-red-600"
                                                @click="deleteGroup(group.id)"
                                            >
                                                <Trash2 class="mr-2 h-4 w-4" />
                                                Delete Group
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <!-- Pagination -->
                    <div v-if="groups.last_page > 1" class="mt-4 flex items-center justify-between">
                        <div class="text-sm text-muted-foreground">
                            Showing {{ ((groups.current_page - 1) * groups.per_page) + 1 }} to 
                            {{ Math.min(groups.current_page * groups.per_page, groups.total) }} of 
                            {{ groups.total }} results
                        </div>
                        <div class="flex items-center space-x-2">
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="groups.current_page === 1"
                                as-child
                            >
                                <Link :href="`/admin/groups?page=${groups.current_page - 1}`">
                                    Previous
                                </Link>
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="groups.current_page === groups.last_page"
                                as-child
                            >
                                <Link :href="`/admin/groups?page=${groups.current_page + 1}`">
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
