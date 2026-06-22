<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { 
    Users, 
    BookOpen, 
    Calendar, 
    Heart,
    Shield,
    Crown,
    ArrowRight,
    Sparkles,
    Target,
    TrendingUp,
    Award,
    Zap,
    Globe,
    Sun,
    Moon
} from 'lucide-vue-next';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

// Dark mode toggle
const isDark = ref(false);

onMounted(() => {
    // Check if user has a theme preference
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    isDark.value = savedTheme === 'dark' || (!savedTheme && prefersDark);
    
    if (isDark.value) {
        document.documentElement.classList.add('dark');
    }

    // Start background slider
    setInterval(() => {
        currentImageIndex.value = (currentImageIndex.value + 1) % backgroundImages.length;
    }, 5000);
});

const backgroundImages = [
    '/images/church_worship.png',
    '/images/community_gathering.png',
    '/images/bible_study.png',
    '/images/prayer_moment.png',
    '/images/youth_ministry.png'
];

const currentImageIndex = ref(0);
const showMobileMenu = ref(false);

const toggleDarkMode = () => {
    isDark.value = !isDark.value;
    
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

const features = [
    {
        icon: Users,
        title: 'Smart Community Management',
        description: 'Effortlessly organize members, leaders, and administrators with intelligent role-based access control and automated workflows.',
        color: 'from-blue-500 to-cyan-500',
        iconColor: 'text-blue-600'
    },
    {
        icon: BookOpen,
        title: 'Interactive Bible Studies',
        description: 'Create engaging Bible study groups with real-time assignments, progress tracking, and collaborative learning tools.',
        color: 'from-green-500 to-emerald-500',
        iconColor: 'text-green-600'
    },
    {
        icon: Heart,
        title: 'Prayer & Support Network',
        description: 'Build a compassionate community with our prayer wall, enabling members to share requests and offer support instantly.',
        color: 'from-purple-500 to-pink-500',
        iconColor: 'text-purple-600'
    },
    {
        icon: Calendar,
        title: 'Advanced Event Management',
        description: 'Schedule meetings, track attendance, and send automated reminders for both virtual and in-person gatherings.',
        color: 'from-orange-500 to-red-500',
        iconColor: 'text-orange-600'
    },
    {
        icon: Target,
        title: 'Goal Tracking & Analytics',
        description: 'Monitor spiritual growth, track engagement metrics, and make data-driven decisions for your ministry.',
        color: 'from-indigo-500 to-purple-500',
        iconColor: 'text-indigo-600'
    },
    {
        icon: Shield,
        title: 'Enterprise-Grade Security',
        description: 'Protect sensitive church data with bank-level encryption, secure authentication, and comprehensive privacy controls.',
        color: 'from-gray-500 to-slate-500',
        iconColor: 'text-gray-600'
    }
];

const benefits = [
    {
        icon: Zap,
        title: 'Lightning Fast',
        description: 'Built with modern technology for instant load times'
    },
    {
        icon: Globe,
        title: 'Access Anywhere',
        description: 'Cloud-based platform accessible from any device'
    },
    {
        icon: Award,
        title: 'Award Winning',
        description: 'Recognized by church leaders worldwide'
    },
    {
        icon: TrendingUp,
        title: 'Proven Growth',
        description: 'Churches see 40% increase in engagement'
    }
];

const testimonials = [
    {
        name: 'Pastor John Smith',
        role: 'Senior Pastor, Grace Community Church',
        content: 'This platform has revolutionized how we connect with our congregation. The intuitive interface and powerful features have increased our member engagement by 60% in just three months.',
        avatar: '👨‍💼',
        rating: 5
    },
    {
        name: 'Sister Mary Davis',
        role: 'Ministry Leader, Faith Baptist Church',
        content: 'The prayer wall feature has transformed our church community. Members feel more connected than ever, and we\'ve seen a remarkable increase in spiritual support and fellowship.',
        avatar: '👩‍🏫',
        rating: 5
    },
    {
        name: 'Elder Michael Chen',
        role: 'Elder, New Life Fellowship',
        content: 'As someone who manages multiple Bible study groups, this system has been a game-changer. The assignment tracking and progress monitoring tools are absolutely phenomenal.',
        avatar: '👨‍💻',
        rating: 5
    }
];
</script>

<template>
    <Head title="Church Management System - Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    
    <div class="min-h-[100dvh] relative overflow-hidden">
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

        <!-- Navigation -->
        <nav class="relative z-50 backdrop-blur-sm bg-white/70 dark:bg-white/10 border-b border-gray-200/20 dark:border-white/10 sticky top-0">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16 sm:h-20">
                    <div class="flex items-center space-x-4 group cursor-pointer">
                        <div class="relative bg-white rounded-lg p-1.5 shadow-sm">
                            <img src="/images/liam255.png" alt="Church Logo" class="h-10 sm:h-14 w-auto object-contain transform group-hover:scale-110 transition-transform" style="image-rendering: -webkit-optimize-contrast; image-rendering: crisp-edges;" />
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <!-- Dark Mode Toggle -->
                        <button
                            @click="toggleDarkMode"
                            class="group relative flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-xl bg-gray-200/50 dark:bg-white/10 text-gray-700 dark:text-white transition-all hover:bg-gray-200 dark:hover:bg-white/20 hover:scale-110"
                            :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                        >
                            <Sun v-if="isDark" class="h-4 w-4 sm:h-5 sm:w-5 transition-transform group-hover:rotate-180" />
                            <Moon v-else class="h-4 w-4 sm:h-5 sm:w-5 transition-transform group-hover:-rotate-12" />
                        </button>
                        
                        <!-- Desktop View - Show buttons directly -->
                        <template v-if="$page.props.auth.user">
                            <Link
                                href="/dashboard"
                                class="group relative inline-flex items-center rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 sm:px-6 sm:py-3 text-xs sm:text-sm font-semibold text-white shadow-lg transition-all hover:shadow-2xl hover:scale-105"
                            >
                                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-700 to-purple-700 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <Crown class="relative mr-1.5 sm:mr-2 h-3.5 w-3.5 sm:h-4 sm:w-4" />
                                <span class="relative">Dashboard</span>
                            </Link>
                        </template>
                        <template v-else>
                            <!-- Desktop: Show both buttons -->
                            <div class="hidden sm:flex items-center space-x-3">
                                <Link
                                    href="/login"
                                    class="rounded-xl px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-white transition-all hover:bg-gray-100 dark:hover:bg-white/10 hover:scale-105"
                                >
                                    Sign In
                                </Link>
                                <Link
                                    v-if="canRegister"
                                    href="/register"
                                    class="group relative inline-flex items-center rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-2xl hover:scale-105"
                                >
                                    <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-700 to-purple-700 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    <span class="relative">Join Us</span>
                                    <ArrowRight class="relative ml-2 h-4 w-4 group-hover:translate-x-1 transition-transform" />
                                </Link>
                            </div>
                            
                            <!-- Mobile: Show dropdown menu -->
                            <div class="relative sm:hidden">
                                <button
                                    @click="showMobileMenu = !showMobileMenu"
                                    class="flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg transition-all hover:shadow-xl"
                                    :aria-label="showMobileMenu ? 'Close menu' : 'Open menu'"
                                >
                                    <svg v-if="!showMobileMenu" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                    <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <div
                                    v-show="showMobileMenu"
                                    class="absolute right-0 mt-2 w-48 rounded-xl bg-white dark:bg-gray-800 shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden z-50"
                                >
                                    <Link
                                        href="/login"
                                        class="block px-4 py-3 text-sm font-medium text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                        @click="showMobileMenu = false"
                                    >
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 mr-3 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                            </svg>
                                            Sign In
                                        </div>
                                    </Link>
                                    <Link
                                        v-if="canRegister"
                                        href="/register"
                                        class="block px-4 py-3 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 transition-colors"
                                        @click="showMobileMenu = false"
                                    >
                                        <div class="flex items-center justify-between">
                                            <span>Join Us</span>
                                            <ArrowRight class="h-4 w-4" />
                                        </div>
                                    </Link>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative z-10 overflow-hidden min-h-[100dvh] flex flex-col justify-center py-12 sm:py-20 lg:py-32">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-4xl text-center">

                    
                    <!-- Main Heading -->
                    <h1 class="mb-6 sm:mb-8 text-4xl sm:text-5xl lg:text-7xl xl:text-8xl font-extrabold tracking-tight text-gray-900 dark:text-white animate-fade-in-up">
                        Transform Your
                        <span class="relative inline-block">
                            <!-- Shadow layer for better visibility in light mode -->
                            <span class="absolute inset-0 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 dark:from-blue-400 dark:via-purple-400 dark:to-pink-400 bg-clip-text text-transparent blur-sm opacity-40 dark:opacity-0 animate-gradient">
                                Church Community
                            </span>
                            <!-- Main gradient text -->
                            <span class="relative bg-gradient-to-r from-blue-700 via-purple-700 to-pink-700 dark:from-blue-400 dark:via-purple-400 dark:to-pink-400 bg-clip-text text-transparent animate-gradient">
                                Church Community
                            </span>
                            <svg class="absolute -bottom-1 sm:-bottom-2 left-0 w-full" height="12" viewBox="0 0 300 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2 10C50 5 100 2 150 5C200 8 250 5 298 10" stroke="url(#gradient)" stroke-width="3" stroke-linecap="round"/>
                                <defs>
                                    <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" style="stop-color:#60A5FA;stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:#C084FC;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#F472B6;stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                            </svg>
                        </span>
                    </h1>
                    
                    <p class="mb-8 sm:mb-12 text-lg sm:text-xl leading-relaxed text-gray-700 dark:text-gray-200 sm:text-2xl animate-fade-in-up" style="animation-delay: 0.2s;">
                        The all-in-one platform to <strong class="text-gray-900 dark:text-white">connect, engage, and grow</strong> your congregation with powerful tools designed for modern ministry.
                    </p>
                    

                </div>
            </div>
        </div>

        <!-- Benefits Bar -->
        <div class="relative z-10 bg-white/50 dark:bg-gray-900/50 backdrop-blur-md py-12 border-y border-gray-200/50 dark:border-gray-700/50">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
                    <div 
                        v-for="benefit in benefits" 
                        :key="benefit.title"
                        class="flex flex-col items-center text-center group"
                    >
                        <component :is="benefit.icon" class="h-8 w-8 text-blue-600 dark:text-blue-400 mb-3 group-hover:scale-110 transition-transform" />
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">{{ benefit.title }}</h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ benefit.description }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="relative z-10 bg-gradient-to-b from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 py-24 lg:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-3xl text-center mb-20">
                    <h2 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl dark:text-white mb-6">
                        Powerful Features for
                        <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Modern Ministry</span>
                    </h2>
                    <p class="text-xl leading-8 text-gray-600 dark:text-gray-300">
                        Everything you need to build, manage, and grow a thriving church community in one comprehensive platform.
                    </p>
                </div>
                
                <div class="mx-auto max-w-7xl">
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                        <div 
                            v-for="feature in features" 
                            :key="feature.title"
                            class="group relative overflow-hidden rounded-3xl bg-white dark:bg-gray-800 p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100 dark:border-gray-700"
                        >
                            <!-- Gradient Background -->
                            <div :class="['absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-5 transition-opacity', feature.color]"></div>
                            
                            <!-- Icon -->
                            <div class="relative mb-6">
                                <div :class="['inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br shadow-lg', feature.color]">
                                    <component :is="feature.icon" class="h-8 w-8 text-white" />
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="relative">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {{ feature.title }}
                                </h3>
                                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                                    {{ feature.description }}
                                </p>
                            </div>
                            
                            <!-- Hover Arrow -->
                            <div class="relative mt-6 flex items-center text-blue-600 dark:text-blue-400 font-semibold text-sm opacity-0 group-hover:opacity-100 transition-opacity">
                                <span>Learn more</span>
                                <ArrowRight class="ml-2 h-4 w-4 group-hover:translate-x-1 transition-transform" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Testimonials Section -->
        <div class="relative bg-gradient-to-b from-gray-50 to-white dark:from-gray-800 dark:to-gray-900 py-24 lg:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-3xl text-center mb-20">
                    <h2 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl dark:text-white mb-6">
                        Loved by
                        <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Church Leaders</span>
                    </h2>
                    <p class="text-xl leading-8 text-gray-600 dark:text-gray-300">
                        Discover why pastors and ministry leaders trust our platform to transform their communities.
                    </p>
                </div>
                
                <div class="mx-auto max-w-7xl">
                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                        <div 
                            v-for="testimonial in testimonials" 
                            :key="testimonial.name"
                            class="group relative overflow-hidden rounded-3xl bg-white dark:bg-gray-800 p-8 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100 dark:border-gray-700"
                        >
                            <!-- Quote Icon -->
                            <div class="absolute top-6 right-6 opacity-10 group-hover:opacity-20 transition-opacity">
                                <svg class="h-16 w-16 text-blue-600" fill="currentColor" viewBox="0 0 32 32">
                                    <path d="M9.352 4C4.456 7.456 1 13.12 1 19.36c0 5.088 3.072 8.064 6.624 8.064 3.36 0 5.856-2.688 5.856-5.856 0-3.168-2.208-5.472-5.088-5.472-.576 0-1.344.096-1.536.192.48-3.264 3.552-7.104 6.624-9.024L9.352 4zm16.512 0c-4.8 3.456-8.256 9.12-8.256 15.36 0 5.088 3.072 8.064 6.624 8.064 3.264 0 5.856-2.688 5.856-5.856 0-3.168-2.304-5.472-5.184-5.472-.576 0-1.248.096-1.44.192.48-3.264 3.456-7.104 6.528-9.024L25.864 4z"/>
                                </svg>
                            </div>
                            
                            <!-- Stars -->
                            <div class="flex gap-1 mb-4">
                                <svg v-for="i in testimonial.rating" :key="i" class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                            
                            <figure>
                                <blockquote class="relative text-lg leading-8 text-gray-700 dark:text-gray-300 mb-6">
                                    <p>"{{ testimonial.content }}"</p>
                                </blockquote>
                                <figcaption class="flex items-center gap-x-4">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-purple-500 text-2xl shadow-lg">
                                        {{ testimonial.avatar }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white">{{ testimonial.name }}</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ testimonial.role }}</div>
                                    </div>
                                </figcaption>
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="relative bg-gray-900">
            <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                    <!-- Brand -->
                    <div class="md:col-span-2">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="bg-white rounded-lg p-1.5 shadow-sm">
                                <img src="/images/liam255.png" alt="Church Logo" class="h-12 w-auto object-contain" style="image-rendering: -webkit-optimize-contrast; image-rendering: crisp-edges;" />
                            </div>
                        </div>
                        <p class="text-gray-400 text-sm max-w-md">
                            Empowering churches worldwide with innovative tools to connect, engage, and grow their communities.
                        </p>
                    </div>
                    
                    <!-- Quick Links -->
                    <div>
                        <h3 class="text-white font-semibold mb-4">Platform</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Features</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Pricing</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Security</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Updates</a></li>
                        </ul>
                    </div>
                    
                    <!-- Support -->
                    <div>
                        <h3 class="text-white font-semibold mb-4">Support</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Documentation</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Help Center</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact Us</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Status</a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Bottom Bar -->
                <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-gray-400">
                        &copy; {{ new Date().getFullYear() }} Victory Fellowship Church Management. All rights reserved.
                    </p>
                    <div class="flex items-center gap-6 text-sm text-gray-400">
                        <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                        <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                        <a href="#" class="hover:text-white transition-colors">Cookies</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes gradient {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out;
}

.animate-gradient {
    background-size: 200% 200%;
    animation: gradient 3s ease infinite;
}
</style>
