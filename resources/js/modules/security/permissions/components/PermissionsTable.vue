<template>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Permiso (Name)</th>
                    <th>Descripción</th>
                    <th>Módulo</th>
                    <th v-if="hasActions" class="text-right">Acciones</th>
                </tr>
            </thead>

            <TableSkeleton v-if="loading" :columns="hasActions ? 4 : 3" :rows="5" />

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

                    <td v-if="hasActions" data-label="Acciones" class="text-right">
                        <ActionGroup>
                            <Button
                                v-if="canEdit"
                                variant="ghost"
                                size="icon"
                                class="cursor-pointer h-8 w-8 text-muted-foreground hover:text-foreground"
                                as-child
                                title="Editar permiso"
                            >
                                <Link
                                    :href="route(`${routeName}edit`, item.id)"
                                >
                                    <Pencil class="h-4 w-4" />
                                </Link>
                            </Button>

                            <Button
                                v-if="canDelete"
                                variant="ghost"
                                size="icon"
                                class="cursor-pointer h-8 w-8 text-muted-foreground hover:text-destructive"
                                title="Eliminar permiso"
                                @click="$emit('confirmDelete', item)"
                            >
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </ActionGroup>
                    </td>
                </tr>
            </tbody>

            <TableEmpty
                v-else
                :colspan="hasActions ? 4 : 3"
                title="No se encontraron permisos"
                description="No hay permisos registrados o ninguno coincide con los criterios de búsqueda actuales."
            />
        </table>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Pencil, Trash2 } from '@lucide/vue';
import { computed } from 'vue';
import ActionGroup from '@/components/common/ActionGroup.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { TableEmpty, TableSkeleton } from '@/components/ui/table';
import { useCan } from '@/composables/usePermissions';
import type { Permission } from '../interfaces';

interface Props {
    routeName: string;
    permissions: Permission[];
    loading?: boolean;
}

withDefaults(defineProps<Props>(), {
    loading: false,
});

defineEmits<{
    confirmDelete: [item: Permission];
}>();

const canEdit = computed(() => useCan('permissions.edit'));
const canDelete = computed(() => useCan('permissions.delete'));
const hasActions = computed(() => canEdit.value || canDelete.value);
</script>
