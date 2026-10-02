<template>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Clave (Key)</th>
                    <th>Descripción</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>

            <TableSkeleton v-if="loading" :columns="4" :rows="5" />

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

                    <td data-label="Acciones" class="text-right">
                        <ActionGroup>
                            <Button variant="ghost" size="icon"
                                class="cursor-pointer h-8 w-8 text-muted-foreground hover:text-foreground" as-child
                                title="Editar módulo">
                                <Link :href="route(`${routeName}edit`, item.id)">
                                    <Pencil class="h-4 w-4" />
                                </Link>
                            </Button>

                            <Button variant="ghost" size="icon"
                                class="cursor-pointer h-8 w-8 text-muted-foreground hover:text-destructive"
                                title="Eliminar módulo" @click="$emit('confirmDelete', item)">
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </ActionGroup>
                    </td>
                </tr>
            </tbody>

            <TableEmpty v-else :colspan="4" title="No se encontraron módulos"
                description="No hay módulos registrados o ninguno coincide con los criterios de búsqueda actuales." />
        </table>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Pencil, Trash2 } from '@lucide/vue';
import ActionGroup from '@/components/common/ActionGroup.vue';
import { Button } from '@/components/ui/button';
import { TableEmpty, TableSkeleton } from '@/components/ui/table';
import type { Module } from '../interfaces';

interface Props {
    routeName: string;
    modules: Module[];
    loading?: boolean;
}

withDefaults(defineProps<Props>(), {
    loading: false,
});

defineEmits<{
    confirmDelete: [item: Module];
}>();
</script>
