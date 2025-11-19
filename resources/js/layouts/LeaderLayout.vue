<script setup lang="ts">
import LeaderSidebarLayout from '@/layouts/leader/LeaderSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <LeaderSidebarLayout :breadcrumbs="breadcrumbs">
        <slot />
    </LeaderSidebarLayout>
</template>
