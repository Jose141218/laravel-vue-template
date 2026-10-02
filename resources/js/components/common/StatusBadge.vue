<script setup lang="ts">
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';

interface Props {
    status?: string | boolean | null;
    activeText?: string;
    inactiveText?: string;
    variant?: 'default' | 'secondary' | 'destructive' | 'outline';
}

const props = withDefaults(defineProps<Props>(), {
    status: true,
    activeText: 'Activo',
    inactiveText: 'Inactivo',
    variant: undefined,
});

const isPositive = computed(() => {
    if (typeof props.status === 'boolean') {
        return props.status;
    }

    if (typeof props.status === 'string') {
        return ['active', 'activo', 'enabled', '1', 'true'].includes(
            props.status.toLowerCase(),
        );
    }

    return false;
});

const computedVariant = computed(() => {
    if (props.variant) {
        return props.variant;
    }

    return isPositive.value ? 'default' : 'secondary';
});

const computedLabel = computed(() => {
    if (
        typeof props.status === 'string' &&
        props.status &&
        ![
            'active',
            'activo',
            'enabled',
            'inactive',
            'inactivo',
            'disabled',
        ].includes(props.status.toLowerCase())
    ) {
        return props.status;
    }

    return isPositive.value ? props.activeText : props.inactiveText;
});
</script>

<template>
    <Badge :variant="computedVariant" class="text-xs font-normal capitalize">
        {{ computedLabel }}
    </Badge>
</template>
