<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Church } from 'lucide-vue-next';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="min-h-screen relative overflow-hidden">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0">
            <!-- Bible Study Background -->
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" 
                 style="background-image: url('https://images.unsplash.com/photo-1504052434569-70ad5836ab65?q=80&w=2070&auto=format&fit=crop');">
            </div>
            
            <!-- Dark Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900/80 via-blue-900/70 to-slate-900/80"></div>
        </div>

        <div class="relative flex min-h-screen flex-col items-center justify-center p-6">
            <!-- Back to Home Link -->
            <Link
                href="/"
                class="absolute top-6 left-6 group flex items-center gap-2 text-sm font-medium text-white/70 hover:text-white transition-colors"
            >
                <svg class="h-4 w-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Home
            </Link>

            <div class="w-full max-w-md">
                <!-- Minimalist Auth Card -->
                <div class="relative overflow-hidden rounded-2xl bg-slate-900/40 backdrop-blur-2xl shadow-2xl border border-white/10 p-10">
                    <!-- Content -->
                    <div class="relative">
                        <!-- Logo -->
                        <div class="flex justify-center mb-6">
                            <div class="bg-white rounded-lg p-2 shadow-lg">
                                <img src="/images/liam255.png" alt="Church Logo" class="h-28 w-auto object-contain" style="image-rendering: -webkit-optimize-contrast; image-rendering: crisp-edges;" />
                            </div>
                        </div>
                        
                        <div class="text-center mb-8">
                            <h1 class="text-4xl font-bold text-white mb-2">
                                {{ title }}
                            </h1>
                        </div>
                        
                        <slot />
                    </div>
                </div>

                <!-- Footer Text -->
                <p class="mt-6 text-center text-sm text-white/50">
                    © 2025 Victory Fellowship. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</template>
