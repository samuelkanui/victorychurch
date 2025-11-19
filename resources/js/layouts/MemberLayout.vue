<script setup lang="ts">
import MemberSidebarLayout from '@/layouts/member/MemberSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <MemberSidebarLayout :breadcrumbs="breadcrumbs">
        <slot />
    </MemberSidebarLayout>
</template>
