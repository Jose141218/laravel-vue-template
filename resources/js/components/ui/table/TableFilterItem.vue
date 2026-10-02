<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { computed, useId } from 'vue';
import { cn } from '@/lib/utils';

export interface TableFilterItemProps {
    label?: string;
    for?: string;
    forId?: string;
    orientation?: 'vertical' | 'horizontal';
    class?: HTMLAttributes['class'];
    labelClass?: HTMLAttributes['class'];
}

const props = withDefaults(defineProps<TableFilterItemProps>(), {
    label: undefined,
    for: undefined,
    forId: undefined,
    orientation: 'vertical',
    class: undefined,
    labelClass: undefined,
});

const autoId = useId();
const resolvedFor = computed(() => props.forId || props.for || autoId);
</script>

<template>
    <div
        :class="cn(
            orientation === 'horizontal'
                ? 'flex items-center gap-1.5'
                : 'flex flex-col gap-2 w-full',
            props.class
        )"
    >
        <label
            v-if="label"
            :for="resolvedFor"
            :class="cn(
                orientation === 'horizontal'
                    ? 'text-xs text-muted-foreground whitespace-nowrap select-none font-normal'
                    : 'text-xs font-semibold uppercase tracking-wider text-muted-foreground select-none',
                props.labelClass
            )"
        >
            {{ label }}<template v-if="orientation === 'horizontal'">:</template>
        </label>
        <div :class="orientation === 'horizontal' ? 'w-auto' : 'w-full [&>*]:w-full'">
            <slot :id="resolvedFor" :for-id="resolvedFor" />
        </div>
    </div>
</template>
