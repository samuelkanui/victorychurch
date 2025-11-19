<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { Separator } from '@/components/ui/separator';
import { Badge } from '@/components/ui/badge';
import type { BreadcrumbItemType } from '@/types';
import { Shield } from 'lucide-vue-next';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <header class="flex h-16 shrink-0 items-center gap-2 border-b border-blue-200 bg-gradient-to-r from-blue-50 to-blue-100 px-4 dark:border-blue-800 dark:from-blue-950 dark:to-blue-900">
        <SidebarTrigger class="-ml-1 text-blue-700 hover:bg-blue-200 dark:text-blue-300 dark:hover:bg-blue-800" />
        <Separator orientation="vertical" class="mr-2 h-4 bg-blue-300 dark:bg-blue-700" />
        
        <div class="flex flex-1 items-center justify-between">
            <Breadcrumbs :breadcrumbs="breadcrumbs" class="text-blue-700 dark:text-blue-300" />
            
            <div class="flex items-center space-x-2">
                <Badge variant="outline" class="border-blue-300 text-blue-700 dark:border-blue-700 dark:text-blue-300">
                    <Shield class="mr-1 h-3 w-3" />
                    Leader
                </Badge>
            </div>
        </div>
    </header>
</template>
