<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { Separator } from '@/components/ui/separator';
import { Badge } from '@/components/ui/badge';
import type { BreadcrumbItemType } from '@/types';
import { User } from 'lucide-vue-next';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <header class="flex h-16 shrink-0 items-center gap-2 border-b border-green-200 bg-gradient-to-r from-green-50 to-green-100 px-4 dark:border-green-800 dark:from-green-950 dark:to-green-900">
        <SidebarTrigger class="-ml-1 text-green-700 hover:bg-green-200 dark:text-green-300 dark:hover:bg-green-800" />
        <Separator orientation="vertical" class="mr-2 h-4 bg-green-300 dark:bg-green-700" />
        
        <div class="flex flex-1 items-center justify-between">
            <Breadcrumbs :breadcrumbs="breadcrumbs" class="text-green-700 dark:text-green-300" />
            
            <div class="flex items-center space-x-2">
                <Badge variant="outline" class="border-green-300 text-green-700 dark:border-green-700 dark:text-green-300">
                    <User class="mr-1 h-3 w-3" />
                    Member
                </Badge>
            </div>
        </div>
    </header>
</template>
