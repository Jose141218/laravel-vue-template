<template>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Permiso (Name)</th>
                    <th>Descripción</th>
                    <th>Módulo</th>
                </tr>
            </thead>

            <TableSkeleton v-if="loading" :columns="3" :rows="5" />

            <tbody v-else-if="permissions.length > 0">
                <tr v-for="item in permissions" :key="item.id">
                    <td
                        data-label="Permiso"
                        class="font-medium text-foreground"
                    >
                        <code
                            class="rounded bg-muted px-2 py-0.5 font-mono text-xs"
                        >
                            {{ item.name }}
                        </code>
                    </td>

                    <td data-label="Descripción" class="text-muted-foreground">
                        {{ item.description || '—' }}
                    </td>

                    <td data-label="Módulo">
                        <Badge
                            variant="outline"
                            class="text-xs font-normal uppercase"
                        >
                            {{ item.module_key || 'General' }}
                        </Badge>
                    </td>
                </tr>
            </tbody>

            <TableEmpty
                v-else
                :colspan="3"
                title="No se encontraron permisos"
                description="No hay permisos registrados o ninguno coincide con los criterios de búsqueda actuales."
            />
        </table>
    </div>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { TableEmpty, TableSkeleton } from '@/components/ui/table';
import type { Permission } from '../interfaces';

interface Props {
    permissions: Permission[];
    routeName?: string;
    loading?: boolean;
}

withDefaults(defineProps<Props>(), {
    loading: false,
});
</script>
