<template>
    <div
        :class="
            cn(
                'flex w-full flex-col items-center justify-center px-4 py-12 text-center',
                props.class,
            )
        "
    >
        <slot name="icon">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-full bg-muted/60 text-muted-foreground"
            >
                <component :is="icon" v-if="icon" class="h-6 w-6" />
                <FolderSearch v-else class="h-6 w-6" />
            </div>
        </slot>

        <h3 v-if="title" class="mt-3 text-sm font-semibold text-foreground">
            {{ title }}
        </h3>

        <p
            v-if="description"
            class="mt-1 max-w-sm text-xs text-muted-foreground"
        >
            <slot name="description">
                {{ description }}
            </slot>
        </p>

        <div
            v-if="$slots.action"
            class="mt-4 flex items-center justify-center gap-2"
        >
            <slot name="action" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { FolderSearch } from '@lucide/vue';
import type { Component, HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

export interface EmptyProps {
    title?: string;
    description?: string;
    icon?: Component;
    class?: HTMLAttributes['class'];
}

const props = withDefaults(defineProps<EmptyProps>(), {
    title: 'No se encontraron registros',
    description:
        'Prueba ajustando los términos de búsqueda o filtros aplicados.',
    icon: undefined,
    class: undefined,
});
</script>
