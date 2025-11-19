<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { type BreadcrumbItem } from '@/types';

import AdminLayout from '@/layouts/AdminLayout.vue';
import LeaderLayout from '@/layouts/LeaderLayout.vue';
import MemberLayout from '@/layouts/MemberLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/appearance';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Appearance settings',
        href: edit().url,
    },
];

const page = usePage();
const user = page.props.auth.user;

const RoleLayout = computed(() => {
    if (user.role === 'admin') return AdminLayout;
    if (user.role === 'leader') return LeaderLayout;
    return MemberLayout;
});
</script>

<template>
    <component :is="RoleLayout" :breadcrumbs="breadcrumbItems">
        <Head title="Appearance settings" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    title="Appearance settings"
                    description="Update your account's appearance settings"
                />
                <AppearanceTabs />
            </div>
        </SettingsLayout>
    </component>
</template>
