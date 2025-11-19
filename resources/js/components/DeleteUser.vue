<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { Form, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

// Components
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { AlertCircle, CheckCircle, Clock, XCircle } from 'lucide-vue-next';

const passwordInput = ref<InstanceType<typeof Input> | null>(null);
const reason = ref('');
const page = usePage();
const user = computed(() => page.props.auth.user);
const deletionRequest = computed(() => user.value?.deletion_request);

const isAdmin = computed(() => user.value?.role === 'admin');
const hasPendingRequest = computed(() => deletionRequest.value?.status === 'pending');
const hasApprovedRequest = computed(() => deletionRequest.value?.status === 'approved');
const hasRejectedRequest = computed(() => deletionRequest.value?.status === 'rejected');

const submitDeletionRequest = () => {
    if (!reason.value || reason.value.length < 10) {
        return;
    }
    
    router.post('/deletion-requests', {
        reason: reason.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            reason.value = '';
        }
    });
};

const cancelDeletionRequest = () => {
    if (confirm('Are you sure you want to cancel your deletion request?')) {
        router.delete('/deletion-requests/cancel', {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <div class="space-y-6">
        <HeadingSmall
            title="Delete account"
            description="Delete your account and all of its resources"
        />

        <!-- Pending Request Status -->
        <div v-if="hasPendingRequest" class="space-y-4 rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-800 dark:bg-yellow-950/20">
            <div class="flex items-start gap-3">
                <Clock class="h-5 w-5 text-yellow-600 dark:text-yellow-400 mt-0.5" />
                <div class="flex-1 space-y-2">
                    <p class="font-medium text-yellow-900 dark:text-yellow-100">Deletion Request Pending</p>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                        Your account deletion request is pending admin approval. You will be notified once it has been reviewed.
                    </p>
                    <div class="text-sm text-yellow-600 dark:text-yellow-400">
                        <p><strong>Reason:</strong> {{ deletionRequest.reason }}</p>
                        <p><strong>Submitted:</strong> {{ new Date(deletionRequest.created_at).toLocaleDateString() }}</p>
                    </div>
                    <Button variant="outline" size="sm" @click="cancelDeletionRequest" class="mt-2">
                        Cancel Request
                    </Button>
                </div>
            </div>
        </div>

        <!-- Approved Request Status -->
        <div v-else-if="hasApprovedRequest" class="space-y-4 rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-950/20">
            <div class="flex items-start gap-3">
                <CheckCircle class="h-5 w-5 text-green-600 dark:text-green-400 mt-0.5" />
                <div class="flex-1 space-y-2">
                    <p class="font-medium text-green-900 dark:text-green-100">Deletion Request Approved</p>
                    <p class="text-sm text-green-700 dark:text-green-300">
                        Your account deletion request has been approved. You can now proceed to delete your account.
                    </p>
                    <div v-if="deletionRequest.admin_notes" class="text-sm text-green-600 dark:text-green-400">
                        <p><strong>Admin Notes:</strong> {{ deletionRequest.admin_notes }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rejected Request Status -->
        <div v-else-if="hasRejectedRequest" class="space-y-4 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-950/20">
            <div class="flex items-start gap-3">
                <XCircle class="h-5 w-5 text-red-600 dark:text-red-400 mt-0.5" />
                <div class="flex-1 space-y-2">
                    <p class="font-medium text-red-900 dark:text-red-100">Deletion Request Rejected</p>
                    <p class="text-sm text-red-700 dark:text-red-300">
                        Your account deletion request was not approved. Please contact an administrator if you have questions.
                    </p>
                    <div v-if="deletionRequest.admin_notes" class="text-sm text-red-600 dark:text-red-400">
                        <p><strong>Admin Notes:</strong> {{ deletionRequest.admin_notes }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Deletion Form (for Leaders/Members without pending request) -->
        <div v-else-if="!isAdmin" class="space-y-4 rounded-lg border border-orange-200 bg-orange-50 p-4 dark:border-orange-800 dark:bg-orange-950/20">
            <div class="flex items-start gap-3">
                <AlertCircle class="h-5 w-5 text-orange-600 dark:text-orange-400 mt-0.5" />
                <div class="flex-1 space-y-3">
                    <div>
                        <p class="font-medium text-orange-900 dark:text-orange-100">Admin Approval Required</p>
                        <p class="text-sm text-orange-700 dark:text-orange-300">
                            As a {{ user.role }}, you must request approval from an administrator before deleting your account.
                        </p>
                    </div>
                    <div class="space-y-2">
                        <Label for="reason" class="text-orange-900 dark:text-orange-100">Reason for deletion (minimum 10 characters)</Label>
                        <Textarea
                            id="reason"
                            v-model="reason"
                            placeholder="Please explain why you want to delete your account..."
                            rows="4"
                            class="resize-none"
                        />
                        <p class="text-xs text-orange-600 dark:text-orange-400">
                            {{ reason.length }}/1000 characters
                        </p>
                    </div>
                    <Button 
                        @click="submitDeletionRequest" 
                        :disabled="!reason || reason.length < 10"
                        variant="destructive"
                    >
                        Submit Deletion Request
                    </Button>
                </div>
            </div>
        </div>

        <!-- Admin Direct Deletion -->
        <div v-else
            class="space-y-4 rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10"
        >
            <div class="relative space-y-0.5 text-red-600 dark:text-red-100">
                <p class="font-medium">Warning</p>
                <p class="text-sm">
                    Please proceed with caution, this cannot be undone.
                </p>
            </div>
            <Dialog>
                <DialogTrigger as-child>
                    <Button variant="destructive" data-test="delete-user-button"
                        >Delete account</Button
                    >
                </DialogTrigger>
                <DialogContent>
                    <Form
                        v-bind="ProfileController.destroy.form()"
                        reset-on-success
                        @error="() => passwordInput?.$el?.focus()"
                        :options="{
                            preserveScroll: true,
                        }"
                        class="space-y-6"
                        v-slot="{ errors, processing, reset, clearErrors }"
                    >
                        <DialogHeader class="space-y-3">
                            <DialogTitle
                                >Are you sure you want to delete your
                                account?</DialogTitle
                            >
                            <DialogDescription>
                                Once your account is deleted, all of its
                                resources and data will also be permanently
                                deleted. Please enter your password to confirm
                                you would like to permanently delete your
                                account.
                            </DialogDescription>
                        </DialogHeader>

                        <div class="grid gap-2">
                            <Label for="password" class="sr-only"
                                >Password</Label
                            >
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                ref="passwordInput"
                                placeholder="Password"
                            />
                            <InputError :message="errors.password" />
                        </div>

                        <DialogFooter class="gap-2">
                            <DialogClose as-child>
                                <Button
                                    variant="secondary"
                                    @click="
                                        () => {
                                            clearErrors();
                                            reset();
                                        }
                                    "
                                >
                                    Cancel
                                </Button>
                            </DialogClose>

                            <Button
                                type="submit"
                                variant="destructive"
                                :disabled="processing"
                                data-test="confirm-delete-user-button"
                            >
                                Delete account
                            </Button>
                        </DialogFooter>
                    </Form>
                </DialogContent>
            </Dialog>
        </div>
    </div>
</template>
