<script setup lang="ts">
import { Globe, Laptop, LogOut, Smartphone, Tablet } from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import type { SessionDevice } from '@/interfaces';

const props = defineProps<{
    session: SessionDevice;
}>();

const emit = defineEmits<{
    revoke: [session: SessionDevice];
}>();

const DeviceIcon = computed(() => {
    if (props.session.agent.is_desktop) {
        return Laptop;
    }

    if (props.session.agent.is_mobile) {
        return Smartphone;
    }

    if (props.session.agent.is_tablet) {
        return Tablet;
    }

    return Globe;
});

const deviceTitle = computed(() => {
    const { platform, browser } = props.session.agent;

    if (platform && browser) {
        return `${platform} — ${browser}`;
    }

    return platform || browser || 'Unknown device';
});
</script>

<template>
    <div class="flex items-center justify-between border-b p-4 last:border-b-0">
        <div class="flex items-center gap-4">
            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-muted"
            >
                <component
                    :is="DeviceIcon"
                    class="h-5 w-5 text-muted-foreground"
                />
            </div>

            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                    <p class="font-medium tracking-tight">
                        {{ deviceTitle }}
                    </p>

                    <span
                        v-if="session.is_current_device"
                        class="inline-flex items-center gap-1.5 rounded-md bg-emerald-50 px-2 py-0.5 text-[11px] font-medium text-emerald-700 ring-1 ring-emerald-600/20 ring-inset dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20"
                    >
                        <span
                            class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"
                        ></span>
                        This device
                    </span>
                </div>

                <p class="text-sm text-muted-foreground">
                    <span>{{ session.ip_address }}</span>
                    <span class="mx-1.5 text-muted-foreground/50">•</span>
                    <span>
                        {{
                            session.is_current_device
                                ? 'Active now'
                                : session.last_active
                        }}
                    </span>
                </p>
            </div>
        </div>

        <Button
            v-if="!session.is_current_device"
            variant="ghost"
            size="sm"
            class="text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
            title="Revoke session"
            @click="emit('revoke', session)"
        >
            <LogOut class="h-4 w-4" />
            <span class="sr-only">Revoke session</span>
        </Button>
    </div>
</template>
