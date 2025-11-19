<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { Separator } from '@/components/ui/separator';
import { Badge } from '@/components/ui/badge';
import type { BreadcrumbItemType } from '@/types';
import { Crown } from 'lucide-vue-next';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <header class="flex h-16 shrink-0 items-center gap-2 border-b border-purple-200 bg-gradient-to-r from-purple-50 to-purple-100 px-4 dark:border-purple-800 dark:from-purple-950 dark:to-purple-900">
        <SidebarTrigger class="-ml-1 text-purple-700 hover:bg-purple-200 dark:text-purple-300 dark:hover:bg-purple-800" />
        <Separator orientation="vertical" class="mr-2 h-4 bg-purple-300 dark:bg-purple-700" />
        
        <div class="flex flex-1 items-center justify-between">
            <Breadcrumbs :breadcrumbs="breadcrumbs" class="text-purple-700 dark:text-purple-300" />
            
            <div class="flex items-center space-x-2">
                <Badge variant="outline" class="border-purple-300 text-purple-700 dark:border-purple-700 dark:text-purple-300">
                    <Crown class="mr-1 h-3 w-3" />
                    Administrator
                </Badge>
            </div>
        </div>
    </header>
</template>
