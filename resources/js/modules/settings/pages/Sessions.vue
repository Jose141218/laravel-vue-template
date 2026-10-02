<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { AlertTriangle } from '@lucide/vue';
import type { SessionDevice } from '@/interfaces';
import ManageSessions from '../components/ManageSessions.vue';

interface Props {
    sessions: SessionDevice[];
    isDatabaseDriver: boolean;
}

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Session settings',
                href: route('sessions.index'),
            },
        ],
    },
});
</script>

<template>
    <Head title="Session settings" />

    <h1 class="sr-only">Session settings</h1>

    <div class="space-y-6">
        <div
            v-if="!isDatabaseDriver"
            class="flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4 text-amber-800 dark:border-amber-900/30 dark:bg-amber-950/20 dark:text-amber-300"
        >
            <AlertTriangle
                class="h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400"
            />
            <div class="space-y-1 text-sm">
                <p class="font-medium">Database session driver required</p>
                <p class="text-amber-700 dark:text-amber-400">
                    To manage and log out active browser sessions on other
                    devices, your application session driver must be configured
                    to
                    <code class="font-mono font-semibold">database</code>.
                </p>
            </div>
        </div>

        <ManageSessions :sessions="sessions" />
    </div>
</template>
