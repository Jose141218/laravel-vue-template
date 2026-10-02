<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/common/Heading.vue';
import AppearanceTabs from '../components/AppearanceTabs.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Appearance settings',
                href: route('appearance.edit'),
            },
        ],
    },
});
</script>

<template>
    <Head title="Appearance settings" />

    <h1 class="sr-only">Appearance settings</h1>

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Appearance settings"
            description="Update the appearance settings for your account"
        />
        <AppearanceTabs />
    </div>
</template>
