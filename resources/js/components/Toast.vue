<script setup lang="ts">
import { ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { CheckCircle, XCircle, AlertCircle, X } from 'lucide-vue-next'

interface Toast {
    id: number
    type: 'success' | 'error' | 'info'
    message: string
}

const toasts = ref<Toast[]>([])
let toastId = 0

const addToast = (type: 'success' | 'error' | 'info', message: string) => {
    const id = toastId++
    toasts.value.push({ id, type, message })
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        removeToast(id)
    }, 5000)
}

const removeToast = (id: number) => {
    const index = toasts.value.findIndex(t => t.id === id)
    if (index > -1) {
        toasts.value.splice(index, 1)
    }
}

const getIcon = (type: string) => {
    switch (type) {
        case 'success': return CheckCircle
        case 'error': return XCircle
        case 'info': return AlertCircle
        default: return AlertCircle
    }
}

const getColorClasses = (type: string) => {
    switch (type) {
        case 'success': return 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200'
        case 'error': return 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200'
        case 'info': return 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200'
        default: return 'bg-gray-50 dark:bg-gray-900/20 border-gray-200 dark:border-gray-800 text-gray-800 dark:text-gray-200'
    }
}

const getIconColorClasses = (type: string) => {
    switch (type) {
        case 'success': return 'text-green-600 dark:text-green-400'
        case 'error': return 'text-red-600 dark:text-red-400'
        case 'info': return 'text-blue-600 dark:text-blue-400'
        default: return 'text-gray-600 dark:text-gray-400'
    }
}

// Watch for flash messages from Inertia
const page = usePage()
watch(() => page.props, (props: any) => {
    if (props.flash?.success) {
        addToast('success', props.flash.success)
    }
    if (props.flash?.error) {
        addToast('error', props.flash.error)
    }
    if (props.flash?.info) {
        addToast('info', props.flash.info)
    }
}, { deep: true, immediate: true })
</script>

<template>
    <div class="fixed top-4 right-4 z-50 space-y-2 max-w-md">
        <TransitionGroup
            enter-active-class="transition ease-out duration-300"
            enter-from-class="transform translate-x-full opacity-0"
            enter-to-class="transform translate-x-0 opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="transform translate-x-0 opacity-100"
            leave-to-class="transform translate-x-full opacity-0"
        >
            <div
                v-for="toast in toasts"
                :key="toast.id"
                :class="[
                    'flex items-center gap-3 p-4 rounded-lg border shadow-lg',
                    getColorClasses(toast.type)
                ]"
            >
                <component 
                    :is="getIcon(toast.type)" 
                    :class="['h-5 w-5 flex-shrink-0', getIconColorClasses(toast.type)]"
                />
                <p class="flex-1 text-sm font-medium">{{ toast.message }}</p>
                <button
                    @click="removeToast(toast.id)"
                    class="flex-shrink-0 hover:opacity-70 transition-opacity"
                >
                    <X class="h-4 w-4" />
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>
