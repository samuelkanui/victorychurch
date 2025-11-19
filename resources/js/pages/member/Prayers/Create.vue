<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import MemberLayout from '@/layouts/MemberLayout.vue'
import { type BreadcrumbItemType } from '@/types'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import { 
    MessageSquare, 
    Save, 
    X,
    Globe,
    Users,
    Lock,
    AlertCircle,
    Heart
} from 'lucide-vue-next'

interface Props {
    groups?: Array<{
        id: number
        name: string
    }>
}

defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Prayer Requests', href: '/member/prayers' },
    { title: 'Create Prayer Request' }
]

const form = useForm({
    title: '',
    description: '',
    privacy: 'group',
    is_anonymous: false,
    is_urgent: false,
})

const privacyOptions = [
    { 
        value: 'public', 
        label: 'Public', 
        description: 'Visible to all church members',
        icon: Globe,
        color: 'text-green-600'
    },
    { 
        value: 'group', 
        label: 'Group Only', 
        description: 'Visible only to your group members',
        icon: Users,
        color: 'text-blue-600'
    },
    { 
        value: 'private', 
        label: 'Private', 
        description: 'Visible only to leaders and administrators',
        icon: Lock,
        color: 'text-gray-600'
    },
]

const submit = () => {
    form.post('/member/prayers', {
        onSuccess: () => {
            // Handle success - redirect happens automatically
        }
    })
}
</script>

<template>
    <Head title="Create Prayer Request - Member Dashboard" />
    
    <MemberLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-green-600 to-green-800 bg-clip-text text-transparent">
                        Create Prayer Request
                    </h1>
                    <p class="text-muted-foreground">
                        Submit a prayer request to your church community
                    </p>
                </div>
            </div>

            <!-- Create Form -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <MessageSquare class="h-5 w-5" />
                        Prayer Request Details
                    </CardTitle>
                    <CardDescription>
                        Share your prayer needs with your church community
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Title -->
                        <div class="space-y-2">
                            <Label for="title">Prayer Request Title *</Label>
                            <Input 
                                id="title"
                                v-model="form.title"
                                placeholder="Brief title for your prayer request"
                                :class="{ 'border-red-500': form.errors.title }"
                            />
                            <p v-if="form.errors.title" class="text-sm text-red-600">{{ form.errors.title }}</p>
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <Label for="description">Prayer Request Details *</Label>
                            <textarea 
                                id="description"
                                v-model="form.description"
                                placeholder="Share the details of your prayer request. Be as specific as you're comfortable with."
                                rows="6"
                                class="flex min-h-[120px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 resize-none"
                                :class="{ 'border-red-500': form.errors.description }"
                            ></textarea>
                            <p v-if="form.errors.description" class="text-sm text-red-600">{{ form.errors.description }}</p>
                            <p class="text-xs text-muted-foreground">
                                Share your prayer needs, concerns, or thanksgiving. Your church community wants to support you in prayer.
                            </p>
                        </div>

                        <!-- Privacy Settings -->
                        <div class="space-y-4">
                            <div>
                                <Label class="text-base font-medium">Privacy Settings</Label>
                                <p class="text-sm text-muted-foreground">Choose who can see your prayer request</p>
                            </div>
                            
                            <div class="grid gap-4 md:grid-cols-3">
                                <div v-for="option in privacyOptions" :key="option.value" 
                                     class="relative">
                                    <input 
                                        :id="`privacy-${option.value}`"
                                        v-model="form.privacy"
                                        :value="option.value"
                                        type="radio"
                                        name="privacy"
                                        class="peer sr-only"
                                    />
                                    <label 
                                        :for="`privacy-${option.value}`"
                                        class="flex flex-col items-center justify-center p-4 border-2 rounded-lg cursor-pointer transition-colors hover:bg-gray-50 peer-checked:border-green-500 peer-checked:bg-green-50"
                                    >
                                        <component :is="option.icon" :class="['h-6 w-6 mb-2', option.color]" />
                                        <span class="font-medium text-sm">{{ option.label }}</span>
                                        <span class="text-xs text-muted-foreground text-center mt-1">{{ option.description }}</span>
                                    </label>
                                </div>
                            </div>
                            <p v-if="form.errors.privacy" class="text-sm text-red-600">{{ form.errors.privacy }}</p>
                        </div>

                        <!-- Additional Options -->
                        <div class="space-y-4">
                            <div>
                                <Label class="text-base font-medium">Additional Options</Label>
                                <p class="text-sm text-muted-foreground">Optional settings for your prayer request</p>
                            </div>
                            
                            <div class="space-y-4">
                                <!-- Anonymous Option -->
                                <div class="flex items-start space-x-3">
                                    <input 
                                        id="is_anonymous"
                                        v-model="form.is_anonymous"
                                        type="checkbox"
                                        class="mt-1 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500"
                                    />
                                    <div class="flex-1">
                                        <Label for="is_anonymous" class="font-medium">Submit Anonymously</Label>
                                        <p class="text-sm text-muted-foreground">
                                            Your name will not be shown with this prayer request. Only leaders and administrators will know who submitted it.
                                        </p>
                                    </div>
                                </div>

                                <!-- Urgent Option -->
                                <div class="flex items-start space-x-3">
                                    <input 
                                        id="is_urgent"
                                        v-model="form.is_urgent"
                                        type="checkbox"
                                        class="mt-1 h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500"
                                    />
                                    <div class="flex-1">
                                        <Label for="is_urgent" class="font-medium flex items-center gap-2">
                                            <AlertCircle class="h-4 w-4 text-red-600" />
                                            Mark as Urgent
                                        </Label>
                                        <p class="text-sm text-muted-foreground">
                                            This prayer request needs immediate attention and will be highlighted to church leaders.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Prayer Guidelines -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <Heart class="h-5 w-5 text-blue-600 mt-0.5" />
                                <div>
                                    <h4 class="font-medium text-blue-900 mb-2">Prayer Request Guidelines</h4>
                                    <ul class="text-sm text-blue-800 space-y-1">
                                        <li>• Be specific about your prayer needs while respecting privacy</li>
                                        <li>• Include thanksgiving and praise along with requests</li>
                                        <li>• Remember that your church family wants to support you</li>
                                        <li>• Consider following up with updates on answered prayers</li>
                                        <li>• Respect others' privacy when mentioning family or friends</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center gap-4 pt-4">
                            <Button 
                                type="submit" 
                                :disabled="form.processing"
                                class="bg-green-600 hover:bg-green-700"
                            >
                                <Save class="h-4 w-4 mr-2" />
                                {{ form.processing ? 'Submitting...' : 'Submit Prayer Request' }}
                            </Button>
                            
                            <Button 
                                type="button" 
                                variant="outline"
                                @click="$inertia.visit('/member/prayers')"
                            >
                                <X class="h-4 w-4 mr-2" />
                                Cancel
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </MemberLayout>
</template>
