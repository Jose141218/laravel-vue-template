<script setup lang="ts">
import type { BreadcrumbItem } from '@/interfaces';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';

const { breadcrumbs = [] } = defineProps<{
    breadcrumbs?: BreadcrumbItem[];
}>();
</script>

<template>
    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppSidebarLayout>
</template>
