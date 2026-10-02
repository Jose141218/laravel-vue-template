<template>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Rol</th>
                    <th>Descripción</th>
                    <th>Permisos</th>
                    <th>Usuarios</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>

            <TableSkeleton v-if="loading" :columns="5" :rows="5" />

            <tbody v-else-if="roles.length > 0">
                <tr v-for="item in roles" :key="item.id">
                    <td
                        data-label="Rol"
                        class="font-medium text-foreground capitalize"
                    >
                        {{ item.name }}
                    </td>

                    <td data-label="Descripción" class="text-muted-foreground">
                        {{ item.description || '—' }}
                    </td>

                    <td data-label="Permisos">
                        <Badge variant="secondary" class="text-xs font-normal">
                            {{ item.permissions_count }} permisos
                        </Badge>
                    </td>

                    <td data-label="Usuarios">
                        <Badge variant="outline" class="text-xs font-normal">
                            {{ item.users_count }} usuarios
                        </Badge>
                    </td>

                    <td data-label="Acciones" class="text-right">
                        <ActionGroup>
                            <Button
                                variant="ghost"
                                size="icon"
                                class="cursor-pointer h-8 w-8 text-muted-foreground hover:text-foreground"
                                as-child
                                title="Ver detalles del rol"
                            >
                                <Link
                                    :href="route(`${routeName}show`, item.id)"
                                >
                                    <Eye class="h-4 w-4" />
                                </Link>
                            </Button>

                            <Button
                                v-if="canEdit"
                                variant="ghost"
                                size="icon"
                                class="cursor-pointer h-8 w-8 text-muted-foreground hover:text-foreground"
                                as-child
                                title="Editar rol"
                            >
                                <Link
                                    :href="route(`${routeName}edit`, item.id)"
                                >
                                    <Pencil class="h-4 w-4" />
                                </Link>
                            </Button>

                            <Button
                                v-if="item.name !== 'admin' && canDelete"
                                variant="ghost"
                                size="icon"
                                class="cursor-pointer h-8 w-8 text-muted-foreground hover:text-destructive"
                                title="Eliminar rol"
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
                :colspan="5"
                title="No se encontraron roles"
                description="No hay roles registrados o ninguno coincide con los criterios de búsqueda actuales."
            />
        </table>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Eye, Pencil, Trash2 } from '@lucide/vue';
import { computed } from 'vue';
import ActionGroup from '@/components/common/ActionGroup.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { TableEmpty, TableSkeleton } from '@/components/ui/table';
import { useCan } from '@/composables/usePermissions';
import type { Role } from '../interfaces';

const canEdit = computed(() => useCan('roles.edit'));
const canDelete = computed(() => useCan('roles.delete'));

interface Props {
    routeName: string;
    roles: Role[];
    loading?: boolean;
}

withDefaults(defineProps<Props>(), {
    loading: false,
});

defineEmits<{
    confirmDelete: [item: Role];
}>();
</script>
