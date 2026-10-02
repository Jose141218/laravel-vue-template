<template>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Correo Electrónico</th>
                    <th>Roles</th>
                    <th>Estado</th>
                    <th>Fecha de Registro</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>

            <TableSkeleton v-if="loading" :columns="6" :rows="5" />

            <tbody v-else-if="users.length > 0">
                <tr v-for="item in users" :key="item.id">
                    <td data-label="Usuario" class="font-medium text-foreground">
                        <div class="flex items-center gap-2.5">
                            <Avatar class="h-7 w-7 shrink-0 rounded-full">
                                <AvatarImage v-if="(item as any).avatar" :src="(item as any).avatar" :alt="item.name" />
                                <AvatarFallback class="bg-primary/10 text-[11px] font-medium text-primary">
                                    {{ getInitials(item.name) }}
                                </AvatarFallback>
                            </Avatar>
                            <span>{{ item.name }}</span>
                        </div>
                    </td>

                    <td data-label="Correo" class="text-muted-foreground">
                        {{ item.email }}
                    </td>

                    <td data-label="Roles">
                        <div class="flex flex-wrap gap-1">
                            <Badge v-for="role in item.roles" :key="role.id" :variant="role.name === 'admin'
                                ? 'default'
                                : 'secondary'
                                " class="text-xs font-normal capitalize">
                                {{ role.name }}
                            </Badge>
                            <span v-if="item.roles.length === 0" class="text-xs text-muted-foreground">
                                Sin roles
                            </span>
                        </div>
                    </td>

                    <td data-label="Estado">
                        <Badge v-if="item.status === 'active'" variant="outline"
                            class="text-xs font-normal border-emerald-500/30 text-emerald-700 dark:text-emerald-400 bg-emerald-500/10">
                            Activo
                        </Badge>
                        <Badge v-else-if="item.status === 'pending'" variant="outline"
                            class="text-xs font-normal border-amber-500/30 text-amber-700 dark:text-amber-400 bg-amber-500/10">
                            Invitación pendiente
                        </Badge>
                        <Badge v-else variant="outline"
                            class="text-xs font-normal border-rose-500/30 text-rose-700 dark:text-rose-400 bg-rose-500/10">
                            Invitación expirada
                        </Badge>
                    </td>

                    <td data-label="Registro" class="text-xs text-muted-foreground">
                        <span :title="item.created_at.formatted">
                            {{ item.created_at.human }}
                        </span>
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
                                    <Link :href="route(`${routeName}show`, item.id)">
                                        <Eye class="mr-2 h-4 w-4" />
                                        <span>Ver detalles</span>
                                    </Link>
                                </DropdownMenuItem>

                                <DropdownMenuItem
                                    v-if="canEdit"
                                    as-child
                                    class="cursor-pointer"
                                >
                                    <Link :href="route(`${routeName}edit`, item.id)">
                                        <Pencil class="mr-2 h-4 w-4" />
                                        <span>Editar usuario</span>
                                    </Link>
                                </DropdownMenuItem>

                                <DropdownMenuItem
                                    v-if="item.status !== 'active' && canEdit"
                                    class="cursor-pointer"
                                    @click="$emit('resendInvitation', item)"
                                >
                                    <Mail class="mr-2 h-4 w-4" />
                                    <span>Reenviar invitación</span>
                                </DropdownMenuItem>

                                <DropdownMenuSeparator
                                    v-if="canDelete && item.id !== $page.props.auth.user.id"
                                />

                                <DropdownMenuItem
                                    v-if="canDelete && item.id !== $page.props.auth.user.id"
                                    class="cursor-pointer text-destructive focus:text-destructive"
                                    @click="$emit('confirmDelete', item)"
                                >
                                    <Trash2 class="mr-2 h-4 w-4" />
                                    <span>Eliminar usuario</span>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </td>
                </tr>
            </tbody>

            <TableEmpty v-else :colspan="6" title="No se encontraron usuarios"
                description="No hay usuarios registrados o ninguno coincide con los criterios de búsqueda actuales." />
        </table>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Eye, Mail, MoreHorizontal, Pencil, Trash2 } from '@lucide/vue';
import { computed } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
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
import { useInitials } from '@/composables/useInitials';
import { useCan } from '@/composables/usePermissions';
import type { User } from '../interfaces';

const { getInitials } = useInitials();
const canEdit = computed(() => useCan('users.edit'));
const canDelete = computed(() => useCan('users.delete'));

interface Props {
    routeName: string;
    users: User[];
    loading?: boolean;
}

withDefaults(defineProps<Props>(), {
    loading: false,
});

defineEmits<{
    confirmDelete: [item: User];
    resendInvitation: [item: User];
}>();
</script>
