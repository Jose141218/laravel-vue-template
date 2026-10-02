<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { computed } from 'vue';
import { cn } from '@/lib/utils';

export interface ActionGroupProps {
    align?: 'start' | 'center' | 'end' | 'between';
    gap?: 'none' | 'xs' | 'sm' | 'md' | 'lg' | 'xl';
    direction?: 'horizontal' | 'vertical';
    wrap?: boolean;
    class?: HTMLAttributes['class'];
    ariaLabel?: string;
}

const props = withDefaults(defineProps<ActionGroupProps>(), {
    align: 'end',
    gap: 'sm',
    direction: 'horizontal',
    wrap: false,
    ariaLabel: 'Acciones',
});

const alignClasses = {
    start: 'justify-start',
    center: 'justify-center',
    end: 'justify-end',
    between: 'justify-between',
};

const gapClasses = {
    none: 'gap-0',
    xs: 'gap-0.5',
    sm: 'gap-1',
    md: 'gap-1.5',
    lg: 'gap-2',
    xl: 'gap-3',
};

const directionClasses = {
    horizontal: 'flex-row items-center',
    vertical: 'flex-col items-stretch',
};

const classes = computed(() =>
    cn(
        'inline-flex [&>*]:shrink-0',
        directionClasses[props.direction],
        alignClasses[props.align],
        gapClasses[props.gap],
        props.wrap && 'flex-wrap',
        props.class,
    ),
);
</script>

<template>
    <div
        role="group"
        :aria-label="ariaLabel"
        data-slot="action-group"
        :class="classes"
    >
        <slot />
    </div>
</template>
