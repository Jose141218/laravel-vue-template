<script setup lang="ts">
import { Copy, Check } from '@lucide/vue';
import type { Component, HTMLAttributes } from 'vue';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import { cn } from '@/lib/utils';

interface Props {
    label: string;
    value?: string | number | null;
    icon?: Component;
    copyable?: boolean;
    class?: HTMLAttributes['class'];
}

const props = withDefaults(defineProps<Props>(), {
    value: null,
    copyable: false,
});

const copied = ref(false);

const copyValue = async () => {
    if (props.value === null || props.value === undefined || props.value === '') {
return;
}

    try {
        await navigator.clipboard.writeText(String(props.value));
        copied.value = true;
        toast.success('Copiado al portapapeles');
        setTimeout(() => {
            copied.value = false;
        }, 2000);
    } catch {
        toast.error('No se pudo copiar el texto');
    }
};
</script>

<template>
    <div :class="cn('space-y-1.5', props.class)">
        <dt class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
            <component :is="icon" v-if="icon" class="h-3.5 w-3.5 shrink-0" />
            <span>{{ label }}</span>
        </dt>
        <dd class="flex items-center gap-2 text-sm font-medium text-foreground">
            <slot>
                <span>{{ (value !== null && value !== undefined && value !== '') ? value : '—' }}</span>
            </slot>
            <button
                v-if="copyable && (value !== null && value !== undefined && value !== '')"
                type="button"
                class="cursor-pointer text-muted-foreground hover:text-foreground transition-colors p-0.5 rounded focus:outline-none focus:ring-1 focus:ring-ring"
                :title="`Copiar ${label.toLowerCase()}`"
                @click="copyValue"
            >
                <Check v-if="copied" class="h-3.5 w-3.5 text-emerald-500" />
                <Copy v-else class="h-3.5 w-3.5" />
                <span class="sr-only">Copiar</span>
            </button>
        </dd>
    </div>
</template>
