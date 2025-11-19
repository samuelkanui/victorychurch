<script setup lang="ts">
import { Head } from '@inertiajs/vue3'

interface Props {
    stats: {
        my_groups: number
        pending_applications: number
        my_prayer_requests: number
        upcoming_meetings: number
        pending_assignments: number
    }
}

defineProps<Props>()
</script>

<template>
    <Head title="Member Dashboard Test" />
    
    <div class="p-8">
        <h1 class="text-2xl font-bold mb-4">Member Dashboard - Test Version</h1>
        
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4">Statistics</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p><strong>My Groups:</strong> {{ stats.my_groups }}</p>
                    <p><strong>Pending Applications:</strong> {{ stats.pending_applications }}</p>
                    <p><strong>Prayer Requests:</strong> {{ stats.my_prayer_requests }}</p>
                </div>
                <div>
                    <p><strong>Upcoming Meetings:</strong> {{ stats.upcoming_meetings }}</p>
                    <p><strong>Pending Assignments:</strong> {{ stats.pending_assignments }}</p>
                </div>
            </div>
        </div>
        
        <div class="mt-6 bg-green-100 p-4 rounded">
            <p class="text-green-800">✅ Member dashboard is working! The issue was likely with the complex layout components.</p>
        </div>
    </div>
</template>
