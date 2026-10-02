<template>

    <Head :title="title" />

    <ModuleLayout variant="index">
        <PageHeader :title="title" description="Administra las cuentas de usuario y sus roles asignados.">
            <template #actions>
                <Button as-child class="gap-2">
                    <Link :href="route(`${routeName}create`)">
                        <Plus class="h-4 w-4" />
                        <span>Nuevo Usuario</span>
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <Card>
            <CardHeader class="p-4 pb-2 sm:p-6 sm:pb-2">
                <TableToolbar v-model="filters" :sort-options="sortOptions" :total="users.meta.total"
                    placeholder="Buscar por nombre o correo electrónico..." @clear="clearFilters">
                    <TableFilterItem label="Rol" for-id="filter-role">
                        <SelectInput id="filter-role" v-model="filters.role" :options="roles" by="name" all-label="Todos los roles"
                            capitalize />
                    </TableFilterItem>
                </TableToolbar>
            </CardHeader>

            <CardContent class="p-0 sm:p-6 sm:pt-0">
                <UsersTable :users="users.data" :route-name="routeName" :loading="isLoading"
                    @confirm-delete="confirmDelete" @resend-invitation="confirmResend" />
                <Pagination v-bind="users.meta" v-model:loading="isLoading" />
            </CardContent>
        </Card>

        <ConfirmDialog v-model:open="isDeleteDialogOpen" title="¿Eliminar usuario?"
            :description="`Se eliminará el usuario '${itemToDelete?.name}' (${itemToDelete?.email}). Esta acción no se puede deshacer.`"
            :loading="isDeleting" @confirm="handleDelete" @cancel-delete="cancelDelete" />

        <ConfirmDialog v-model:open="isResendDialogOpen" title="¿Reenviar invitación?"
            :description="`Se enviará un nuevo enlace de activación a '${itemToResend?.email}'. El enlace anterior quedará invalidado.`"
            confirm-text="Reenviar" variant="default" :loading="isResending" @confirm="handleResend"
            @cancel-delete="cancelResend" />
    </ModuleLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import ConfirmDialog from '@/components/common/ConfirmDialog.vue';
import PageHeader from '@/components/common/PageHeader.vue';
import Pagination from '@/components/common/Pagination.vue';
import SelectInput from '@/components/common/SelectInput.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { TableFilterItem, TableToolbar } from '@/components/ui/table';
import { useFilters } from '@/composables/useFilters';
import { useResourceDelete } from '@/composables/useResourceDelete';
import type { ResourceCollection, SortOption } from '@/interfaces';
import ModuleLayout from '@/layouts/ModuleLayout.vue';
import UsersTable from '../components/UsersTable.vue';
import { useUserInvitation } from '../composables/useUserInvitation';
import type { RoleData, User, UserFilters } from '../interfaces';

const sortOptions: SortOption[] = [
    { label: 'Usuario', value: 'name' },
    { label: 'Correo electrónico', value: 'email' },
    { label: 'Fecha de registro', value: 'created_at' },
];

interface Props {
    title: string;
    routeName: string;
    users: ResourceCollection<User>;
    roles: RoleData[];
    filters: UserFilters;
}

const props = defineProps<Props>();

const {
    itemToDelete,
    isDeleteDialogOpen,
    isDeleting,
    confirmDelete,
    cancelDelete,
    handleDelete,
} = useResourceDelete<User>(props.routeName);

const {
    itemToResend,
    isResendDialogOpen,
    isResending,
    confirmResend,
    cancelResend,
    handleResend,
} = useUserInvitation(props.routeName);

const { filters, isLoading, clearFilters } = useFilters(
    props.filters,
    route(`${props.routeName}index`),
);
</script>
