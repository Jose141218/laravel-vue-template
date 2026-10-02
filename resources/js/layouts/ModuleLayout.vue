<script setup lang="ts">
import { computed } from 'vue';
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

export type ModuleLayoutVariant = 'index' | 'form' | 'detail' | 'full';
export type FormSize = 'sm' | 'md' | 'lg' | 'full';

interface Props {
    variant?: ModuleLayoutVariant;
    size?: FormSize;
    class?: HTMLAttributes['class'];
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'index',
    size: 'sm',
});

const maxWidthClass = computed(() => {
    if (props.variant === 'form') {
        switch (props.size) {
            case 'sm':
                return 'max-w-2xl';
            case 'md':
                return 'max-w-3xl';
            case 'lg':
                return 'max-w-5xl';
            case 'full':
                return 'max-w-full';
            default:
                return 'max-w-2xl';
        }
    }

    switch (props.variant) {
        case 'detail':
            return 'max-w-5xl';
        case 'full':
            return 'max-w-full';
        case 'index':
        default:
            return 'max-w-7xl';
    }
});
</script>

<template>
    <div
        data-slot="module-layout"
        :class="
            cn(
                'mx-auto w-full min-w-0 space-y-6 px-4 py-6 sm:px-6 lg:px-8',
                maxWidthClass,
                props.class,
            )
        "
    >
        <slot name="header" />

        <slot />
    </div>
</template>
