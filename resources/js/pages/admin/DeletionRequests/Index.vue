<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Clock, CheckCircle, XCircle, User, Calendar, FileText } from 'lucide-vue-next';

interface DeletionRequest {
    id: number;
    user: {
        id: number;
        name: string;
        email: string;
        role: string;
    };
    reason: string;
    status: string;
    reviewed_by: number | null;
    reviewer: {
        name: string;
    } | null;
    reviewed_at: string | null;
    admin_notes: string | null;
    created_at: string;
}

interface Props {
    requests: {
        data: DeletionRequest[];
        links: any[];
        current_page: number;
        last_page: number;
    };
    stats: {
        pending: number;
        approved: number;
        rejected: number;
        total: number;
    };
    currentStatus: string;
}

defineProps<Props>();

const showApproveDialog = ref(false);
const showRejectDialog = ref(false);
const selectedRequest = ref<DeletionRequest | null>(null);
const adminNotes = ref('');

const filterByStatus = (status: string) => {
    router.get('/admin/deletion-requests', { status }, { preserveState: true });
};

const openApproveDialog = (request: DeletionRequest) => {
    selectedRequest.value = request;
    adminNotes.value = '';
    showApproveDialog.value = true;
};

const openRejectDialog = (request: DeletionRequest) => {
    selectedRequest.value = request;
    adminNotes.value = '';
    showRejectDialog.value = true;
};

const approveRequest = () => {
    if (!selectedRequest.value) return;
    
    router.post(`/admin/deletion-requests/${selectedRequest.value.id}/approve`, {
        admin_notes: adminNotes.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showApproveDialog.value = false;
            selectedRequest.value = null;
            adminNotes.value = '';
        }
    });
};

const rejectRequest = () => {
    if (!selectedRequest.value || !adminNotes.value || adminNotes.value.length < 10) return;
    
    router.post(`/admin/deletion-requests/${selectedRequest.value.id}/reject`, {
        admin_notes: adminNotes.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showRejectDialog.value = false;
            selectedRequest.value = null;
            adminNotes.value = '';
        }
    });
};

const getStatusBadgeVariant = (status: string) => {
    if (status === 'pending') return 'secondary';
    if (status === 'approved') return 'default';
    return 'destructive';
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <AdminLayout>
        <Head title="Account Deletion Requests" />

        <div class="px-4 py-6">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent">
                    Account Deletion Requests
                </h1>
                <p class="text-muted-foreground mt-2">
                    Review and manage account deletion requests from leaders and members
                </p>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-card rounded-lg border p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Pending</p>
                            <p class="text-2xl font-bold">{{ stats.pending }}</p>
                        </div>
                        <Clock class="h-8 w-8 text-yellow-500" />
                    </div>
                </div>

                <div class="bg-card rounded-lg border p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Approved</p>
                            <p class="text-2xl font-bold">{{ stats.approved }}</p>
                        </div>
                        <CheckCircle class="h-8 w-8 text-green-500" />
                    </div>
                </div>

                <div class="bg-card rounded-lg border p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Rejected</p>
                            <p class="text-2xl font-bold">{{ stats.rejected }}</p>
                        </div>
                        <XCircle class="h-8 w-8 text-red-500" />
                    </div>
                </div>

                <div class="bg-card rounded-lg border p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Total</p>
                            <p class="text-2xl font-bold">{{ stats.total }}</p>
                        </div>
                        <FileText class="h-8 w-8 text-blue-500" />
                    </div>
                </div>
            </div>

            <!-- Status Filter -->
            <div class="flex flex-wrap gap-2 mb-6">
                <Button
                    :variant="currentStatus === 'pending' ? 'default' : 'outline'"
                    @click="filterByStatus('pending')"
                    size="sm"
                >
                    Pending
                </Button>
                <Button
                    :variant="currentStatus === 'approved' ? 'default' : 'outline'"
                    @click="filterByStatus('approved')"
                    size="sm"
                >
                    Approved
                </Button>
                <Button
                    :variant="currentStatus === 'rejected' ? 'default' : 'outline'"
                    @click="filterByStatus('rejected')"
                    size="sm"
                >
                    Rejected
                </Button>
                <Button
                    :variant="currentStatus === 'all' ? 'default' : 'outline'"
                    @click="filterByStatus('all')"
                    size="sm"
                >
                    All
                </Button>
            </div>

            <!-- Requests List -->
            <div class="space-y-4">
                <div
                    v-for="request in requests.data"
                    :key="request.id"
                    class="bg-card rounded-lg border p-4 md:p-6 hover:shadow-md transition-shadow"
                >
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-3 mb-3">
                                <div class="h-10 w-10 flex-shrink-0 rounded-full bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center">
                                    <User class="h-5 w-5 text-white" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-semibold text-lg truncate">{{ request.user.name }}</h3>
                                    <p class="text-sm text-muted-foreground truncate">{{ request.user.email }}</p>
                                </div>
                                <div class="flex gap-2 flex-wrap">
                                    <Badge :variant="getStatusBadgeVariant(request.status)">
                                        {{ request.status }}
                                    </Badge>
                                    <Badge variant="outline" class="capitalize">
                                        {{ request.user.role }}
                                    </Badge>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-start gap-2">
                                    <FileText class="h-4 w-4 text-muted-foreground mt-0.5 flex-shrink-0" />
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium">Reason:</p>
                                        <p class="text-sm text-muted-foreground break-words">{{ request.reason }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 text-sm text-muted-foreground flex-wrap">
                                    <Calendar class="h-4 w-4 flex-shrink-0" />
                                    <span class="break-words">Submitted: {{ formatDate(request.created_at) }}</span>
                                </div>

                                <div v-if="request.reviewed_at" class="flex items-center gap-2 text-sm text-muted-foreground flex-wrap">
                                    <CheckCircle class="h-4 w-4 flex-shrink-0" />
                                    <span class="break-words">Reviewed by {{ request.reviewer?.name }} on {{ formatDate(request.reviewed_at) }}</span>
                                </div>

                                <div v-if="request.admin_notes" class="mt-2 p-3 bg-muted rounded-md">
                                    <p class="text-sm font-medium mb-1">Admin Notes:</p>
                                    <p class="text-sm text-muted-foreground break-words">{{ request.admin_notes }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-if="request.status === 'pending'" class="flex flex-col sm:flex-row gap-2 lg:ml-4">
                            <Button
                                variant="default"
                                size="sm"
                                @click="openApproveDialog(request)"
                                class="w-full sm:w-auto"
                            >
                                <CheckCircle class="h-4 w-4 mr-1" />
                                Approve
                            </Button>
                            <Button
                                variant="destructive"
                                size="sm"
                                @click="openRejectDialog(request)"
                                class="w-full sm:w-auto"
                            >
                                <XCircle class="h-4 w-4 mr-1" />
                                Reject
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="requests.data.length === 0" class="text-center py-12">
                    <FileText class="h-12 w-12 text-muted-foreground mx-auto mb-4" />
                    <p class="text-muted-foreground">No deletion requests found</p>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="requests.last_page > 1" class="mt-6 flex justify-center gap-2">
                <Button
                    v-for="link in requests.links"
                    :key="link.label"
                    :variant="link.active ? 'default' : 'outline'"
                    :disabled="!link.url"
                    @click="link.url && router.visit(link.url)"
                    size="sm"
                >
                    <span v-html="link.label"></span>
                </Button>
            </div>
        </div>

        <!-- Approve Dialog -->
        <Dialog v-model:open="showApproveDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Approve Deletion Request</DialogTitle>
                    <DialogDescription>
                        Approve {{ selectedRequest?.user.name }}'s account deletion request. They will be able to delete their account after approval.
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-4">
                    <div>
                        <Label for="approve-notes">Admin Notes (Optional)</Label>
                        <Textarea
                            id="approve-notes"
                            v-model="adminNotes"
                            placeholder="Add any notes for the user..."
                            rows="3"
                        />
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="showApproveDialog = false">
                        Cancel
                    </Button>
                    <Button @click="approveRequest">
                        Approve Request
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Reject Dialog -->
        <Dialog v-model:open="showRejectDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Reject Deletion Request</DialogTitle>
                    <DialogDescription>
                        Reject {{ selectedRequest?.user.name }}'s account deletion request. Please provide a reason for rejection.
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-4">
                    <div>
                        <Label for="reject-notes">Reason for Rejection (Required, min 10 characters)</Label>
                        <Textarea
                            id="reject-notes"
                            v-model="adminNotes"
                            placeholder="Explain why this request is being rejected..."
                            rows="4"
                            class="resize-none"
                        />
                        <p class="text-xs text-muted-foreground mt-1">
                            {{ adminNotes.length }}/500 characters
                        </p>
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="showRejectDialog = false">
                        Cancel
                    </Button>
                    <Button
                        variant="destructive"
                        @click="rejectRequest"
                        :disabled="!adminNotes || adminNotes.length < 10"
                    >
                        Reject Request
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AdminLayout>
</template>
