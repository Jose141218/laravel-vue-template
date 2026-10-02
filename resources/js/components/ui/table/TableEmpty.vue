<script setup lang="ts">
import type { Component, HTMLAttributes } from 'vue';
import Empty from '@/components/common/Empty.vue';

interface Props {
    title?: string;
    description?: string;
    colspan?: number;
    icon?: Component;
    class?: HTMLAttributes['class'];
}

const props = withDefaults(defineProps<Props>(), {
    title: 'No se encontraron registros',
    description:
        'Prueba ajustando los términos de búsqueda o filtros aplicados.',
    colspan: 1,
    icon: undefined,
    class: undefined,
});
</script>

<template>
    <tbody class="table-empty-body">
        <tr>
            <td :colspan="colspan">
                <Empty
                    :title="title"
                    :description="description"
                    :icon="icon"
                    :class="props.class"
                >
                    <template v-if="$slots.icon" #icon>
                        <slot name="icon" />
                    </template>
                    <template v-if="$slots.description" #description>
                        <slot name="description" />
                    </template>
                    <template v-if="$slots.action" #action>
                        <slot name="action" />
                    </template>
                </Empty>
            </td>
        </tr>
    </tbody>
</template>
