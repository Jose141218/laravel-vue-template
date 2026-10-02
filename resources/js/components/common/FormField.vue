<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import {
    Field,
    FieldDescription,
    FieldError,
    FieldLabel,
} from '@/components/ui/field';

interface Props {
    label?: string;
    required?: boolean;
    error?: string;
    description?: string;
    forId?: string;
    class?: HTMLAttributes['class'];
}

const props = defineProps<Props>();
</script>

<template>
    <Field :class="props.class">
        <FieldLabel v-if="label" :for="forId" :required="required">
            {{ label }}
        </FieldLabel>
        <slot />
        <FieldDescription v-if="description">
            {{ description }}
        </FieldDescription>
        <FieldError v-if="error">
            {{ error }}
        </FieldError>
    </Field>
</template>
