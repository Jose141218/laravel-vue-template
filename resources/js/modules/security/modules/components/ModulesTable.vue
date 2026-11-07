<template>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Clave (Key)</th>
                    <th>Descripción</th>
                </tr>
            </thead>

            <TableSkeleton v-if="loading" :columns="3" :rows="5" />

            <tbody v-else-if="modules.length > 0">
                <tr v-for="item in modules" :key="item.id">
                    <td data-label="Nombre" class="font-medium text-foreground">
                        {{ item.name }}
                    </td>

                    <td data-label="Clave">
                        <code class="rounded bg-muted px-2 py-0.5 font-mono text-xs">
                            {{ item.key }}
                        </code>
                    </td>

                    <td data-label="Descripción" class="text-muted-foreground">
                        {{ item.description || '—' }}
                    </td>
                </tr>
            </tbody>

            <TableEmpty
                v-else
                :colspan="3"
                title="No se encontraron módulos"
                description="No hay módulos registrados o ninguno coincide con los criterios de búsqueda actuales."
            />
        </table>
    </div>
</template>

<script setup lang="ts">
import { TableEmpty, TableSkeleton } from '@/components/ui/table';
import type { Module } from '../interfaces';

interface Props {
    modules: Module[];
    routeName?: string;
    loading?: boolean;
}

withDefaults(defineProps<Props>(), {
    loading: false,
});
</script>
