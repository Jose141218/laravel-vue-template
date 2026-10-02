<template>
    <div
        v-if="links && links.length > 3"
        class="flex flex-col items-center justify-between gap-4 px-2 py-4 transition-opacity duration-200 sm:flex-row"
        :class="{ 'pointer-events-none opacity-60': loading }"
    >
        <div class="text-xs text-muted-foreground">
            Mostrando
            <span class="font-medium text-foreground">{{ from ?? 0 }}</span> a
            <span class="font-medium text-foreground">{{ to ?? 0 }}</span> de
            <span class="font-medium text-foreground">{{ total }}</span>
            resultados
        </div>

        <nav
            class="flex flex-wrap items-center justify-center gap-1"
            aria-label="Pagination"
        >
            <template v-for="(link, index) in links" :key="index">
                <!-- Previous Button -->
                <Component
                    :is="link.url ? Link : 'span'"
                    v-if="index === 0"
                    :href="link.url ?? undefined"
                    preserve-scroll
                    preserve-state
                    @start="loading = true"
                    @finish="loading = false"
                    @cancel="loading = false"
                    :class="[
                        'inline-flex h-8 items-center justify-center rounded-md px-2.5 text-xs font-medium transition-colors',
                        link.url
                            ? 'text-foreground hover:bg-muted'
                            : 'pointer-events-none text-muted-foreground/50',
                    ]"
                >
                    <ChevronLeft class="h-4 w-4" />
                    <span class="sr-only">Anterior</span>
                </Component>

                <!-- Next Button -->
                <Component
                    :is="link.url ? Link : 'span'"
                    v-else-if="index === links.length - 1"
                    :href="link.url ?? undefined"
                    preserve-scroll
                    preserve-state
                    @start="loading = true"
                    @finish="loading = false"
                    @cancel="loading = false"
                    :class="[
                        'inline-flex h-8 items-center justify-center rounded-md px-2.5 text-xs font-medium transition-colors',
                        link.url
                            ? 'text-foreground hover:bg-muted'
                            : 'pointer-events-none text-muted-foreground/50',
                    ]"
                >
                    <ChevronRight class="h-4 w-4" />
                    <span class="sr-only">Siguiente</span>
                </Component>

                <!-- Page Number or Ellipsis -->
                <Component
                    :is="link.url && !link.active ? Link : 'span'"
                    v-else
                    :href="link.url ?? undefined"
                    preserve-scroll
                    preserve-state
                    @start="loading = true"
                    @finish="loading = false"
                    @cancel="loading = false"
                    :class="[
                        'inline-flex h-8 min-w-8 items-center justify-center rounded-md px-2 text-xs font-medium transition-colors',
                        link.active
                            ? 'pointer-events-none bg-primary text-primary-foreground shadow-sm'
                            : link.url
                              ? 'text-foreground hover:bg-muted'
                              : 'pointer-events-none text-muted-foreground/50',
                    ]"
                >
                    <span v-html="link.label" />
                </Component>
            </template>
        </nav>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from '@lucide/vue';
import type { PaginationProps } from '@/interfaces';

const loading = defineModel<boolean>('loading', { default: false });

withDefaults(defineProps<PaginationProps>(), {
    links: () => [],
    from: 0,
    to: 0,
    total: 0,
});
</script>
