<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import { Button } from '@/components/ui/button';

interface Props {
    title: string;
    description?: string;
    backHref?: string;
    backTitle?: string;
}

withDefaults(defineProps<Props>(), {
    backTitle: 'Volver',
});
</script>

<template>
    <div
        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
    >
        <div class="flex items-center gap-3.5">
            <Button
                v-if="backHref"
                variant="ghost"
                size="icon"
                class="h-9 w-9 shrink-0 text-muted-foreground hover:text-foreground"
                as-child
            >
                <Link
                    :href="backHref"
                    :title="backTitle"
                    :aria-label="backTitle"
                >
                    <ArrowLeft class="h-4 w-4" />
                </Link>
            </Button>

            <div class="min-w-0">
                <slot name="title">
                    <h1
                        class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl"
                    >
                        {{ title }}
                    </h1>
                </slot>
                <slot name="description">
                    <p
                        v-if="description"
                        class="mt-1 text-sm text-muted-foreground"
                    >
                        {{ description }}
                    </p>
                </slot>
            </div>
        </div>

        <div v-if="$slots.actions" class="flex flex-wrap items-center gap-2">
            <slot name="actions" />
        </div>
    </div>
</template>
