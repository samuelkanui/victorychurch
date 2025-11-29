<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

defineProps<{
    title?: string;
    description?: string;
}>();

const backgroundImages = [
    '/images/church_worship.png',
    '/images/community_gathering.png',
    '/images/bible_study.png',
    '/images/prayer_moment.png',
    '/images/youth_ministry.png'
];

const currentImageIndex = ref(0);
let intervalId: number | undefined;

onMounted(() => {
    intervalId = setInterval(() => {
        currentImageIndex.value = (currentImageIndex.value + 1) % backgroundImages.length;
    }, 5000);
});

onUnmounted(() => {
    if (intervalId) {
        clearInterval(intervalId);
    }
});
</script>

<template>
    <div class="min-h-[100dvh] relative overflow-hidden text-[#595959]">
        <!-- Background Image Slider -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div 
                v-for="(image, index) in backgroundImages" 
                :key="image"
                class="absolute inset-0 bg-cover bg-center bg-no-repeat transition-opacity duration-1000 ease-in-out"
                :style="{ 
                    backgroundImage: `url('${image}')`,
                    opacity: currentImageIndex === index ? '1' : '0'
                }"
            >
                <!-- Overlay to ensure text readability -->
                <div class="absolute inset-0 bg-white/30 dark:bg-gray-900/70"></div>
            </div>
        </div>

        <div class="relative flex min-h-[100dvh] flex-col items-center justify-center p-6 z-10">
            <!-- Back to Home Link -->
            <Link
                href="/"
                class="absolute top-6 left-6 group flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-white hover:text-gray-900 dark:hover:text-gray-200 transition-colors"
            >
                <svg class="h-4 w-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Home
            </Link>

            <div class="w-full max-w-md">
                <!-- Minimalist Auth Card -->
                <div class="relative overflow-hidden rounded-2xl bg-white/60 dark:bg-slate-900/40 backdrop-blur-md shadow-2xl border border-gray-200/50 dark:border-white/10 p-10">
                    <!-- Content -->
                    <div class="relative">
                        <!-- Logo -->
                        <div class="flex justify-center mb-6">
                            <div class="bg-white rounded-lg p-2 shadow-lg">
                                <img src="/images/liam255.png" alt="Church Logo" class="h-28 w-auto object-contain" style="image-rendering: -webkit-optimize-contrast; image-rendering: crisp-edges;" />
                            </div>
                        </div>
                        
                        <div class="text-center mb-8">
                            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                                {{ title }}
                            </h1>
                        </div>
                        
                        <slot />
                    </div>
                </div>

                <!-- Footer Text -->
                <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
                    © 2025 Victory Fellowship. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</template>
