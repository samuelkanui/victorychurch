<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { toUrl, urlIsActive } from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { show } from '@/routes/two-factor';
import { edit as editPassword } from '@/routes/user-password';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

// Determine theme colors based on user role
const themeColors = computed(() => {
    const role = user.value?.role;
    
    if (role === 'admin') {
        return {
            gradient: 'from-red-500 to-orange-500',
            hover: 'hover:bg-red-50 dark:hover:bg-red-950/20',
            active: 'bg-red-100 dark:bg-red-950/40',
            text: 'text-red-600 dark:text-red-400',
            border: 'border-red-200 dark:border-red-800'
        };
    } else if (role === 'leader') {
        return {
            gradient: 'from-blue-500 to-purple-500',
            hover: 'hover:bg-blue-50 dark:hover:bg-blue-950/20',
            active: 'bg-blue-100 dark:bg-blue-950/40',
            text: 'text-blue-600 dark:text-blue-400',
            border: 'border-blue-200 dark:border-blue-800'
        };
    } else {
        // member
        return {
            gradient: 'from-green-500 to-teal-500',
            hover: 'hover:bg-green-50 dark:hover:bg-green-950/20',
            active: 'bg-green-100 dark:bg-green-950/40',
            text: 'text-green-600 dark:text-green-400',
            border: 'border-green-200 dark:border-green-800'
        };
    }
});

const sidebarNavItems: NavItem[] = [
    {
        title: 'Profile',
        href: editProfile(),
    },
    {
        title: 'Password',
        href: editPassword(),
    },
    {
        title: 'Two-Factor Auth',
        href: show(),
    },
    {
        title: 'Appearance',
        href: editAppearance(),
    },
];

const currentPath = typeof window !== undefined ? window.location.pathname : '';
</script>

<template>
    <div class="px-4 py-6">
        <Heading
            title="Settings"
            description="Manage your profile and account settings"
        />

        <div class="flex flex-col lg:flex-row lg:space-x-12">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-y-1 space-x-0">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="toUrl(item.href)"
                        variant="ghost"
                        :class="[
                            'w-full justify-start transition-colors',
                            urlIsActive(item.href, currentPath) 
                                ? themeColors.active 
                                : themeColors.hover
                        ]"
                        as-child
                    >
                        <Link :href="item.href">
                            <component :is="item.icon" class="h-4 w-4" />
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="my-6 lg:hidden" />

            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
