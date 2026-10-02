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
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="cursor-pointer h-8 w-8 text-muted-foreground hover:text-foreground"
                                    title="Acciones"
                                >
                                    <MoreHorizontal class="h-4 w-4" />
                                    <span class="sr-only">Acciones</span>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="w-48">
                                <DropdownMenuItem as-child class="cursor-pointer">
                                    <Link
                                        :href="route(`${routeName}show`, item.id)"
                                    >
                                        <Eye class="mr-2 h-4 w-4" />
                                        <span>Ver detalles</span>
                                    </Link>
                                </DropdownMenuItem>

                                <DropdownMenuItem
                                    v-if="canEdit"
                                    as-child
                                    class="cursor-pointer"
                                >
                                    <Link
                                        :href="route(`${routeName}edit`, item.id)"
                                    >
                                        <Pencil class="mr-2 h-4 w-4" />
                                        <span>Editar rol</span>
                                    </Link>
                                </DropdownMenuItem>

                                <DropdownMenuSeparator
                                    v-if="item.name !== 'admin' && canDelete"
                                />

                                <DropdownMenuItem
                                    v-if="item.name !== 'admin' && canDelete"
                                    class="cursor-pointer text-destructive focus:text-destructive"
                                    @click="$emit('confirmDelete', item)"
                                >
                                    <Trash2 class="mr-2 h-4 w-4" />
                                    <span>Eliminar rol</span>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
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
import { Eye, MoreHorizontal, Pencil, Trash2 } from '@lucide/vue';
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
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
