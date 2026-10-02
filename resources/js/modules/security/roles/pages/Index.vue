<template>
    <Head :title="title" />

    <ModuleLayout variant="index">
        <PageHeader
            :title="title"
            description="Administra los roles del sistema y los permisos asignados a cada uno."
        >
            <template #actions>
                <Button v-if="useCan('roles.create')" as-child class="gap-2">
                    <Link :href="route(`${routeName}create`)">
                        <Plus class="h-4 w-4" />
                        <span>Nuevo Rol</span>
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <Card>
            <CardHeader class="p-4 pb-2 sm:p-6 sm:pb-2">
                <TableToolbar
                    v-model="filters"
                    :sort-options="sortOptions"
                    :total="roles.meta.total"
                    placeholder="Buscar roles por nombre o descripción..."
                    @clear="clearFilters"
                />
            </CardHeader>

            <CardContent class="p-0 sm:p-6 sm:pt-0">
                <RolesTable
                    :roles="roles.data"
                    :route-name="routeName"
                    :loading="isLoading"
                    @confirm-delete="confirmDelete"
                />
                <Pagination v-bind="roles.meta" v-model:loading="isLoading" />
            </CardContent>
        </Card>

        <ConfirmDialog
            v-model:open="isDeleteDialogOpen"
            title="¿Eliminar rol?"
            :description="`Se eliminará el rol '${itemToDelete?.name}'. Esta acción desvinculará a los usuarios asignados.`"
            :loading="isDeleting"
            @confirm="handleDelete"
            @cancel-delete="cancelDelete"
        />
    </ModuleLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import ConfirmDialog from '@/components/common/ConfirmDialog.vue';
import PageHeader from '@/components/common/PageHeader.vue';
import Pagination from '@/components/common/Pagination.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { TableToolbar } from '@/components/ui/table';
import { useFilters } from '@/composables/useFilters';
import { useCan } from '@/composables/usePermissions';
import { useResourceDelete } from '@/composables/useResourceDelete';
import type { ResourceCollection, SortOption } from '@/interfaces';
import ModuleLayout from '@/layouts/ModuleLayout.vue';
import RolesTable from '../components/RolesTable.vue';
import type { Role, RoleFilters } from '../interfaces';

const sortOptions: SortOption[] = [
    { label: 'Rol', value: 'name' },
    { label: 'Descripción', value: 'description' },
    { label: 'Fecha de registro', value: 'created_at' },
];

interface Props {
    title: string;
    routeName: string;
    roles: ResourceCollection<Role>;
    filters: RoleFilters;
}

const props = defineProps<Props>();

const {
    itemToDelete,
    isDeleteDialogOpen,
    isDeleting,
    confirmDelete,
    cancelDelete,
    handleDelete,
} = useResourceDelete<Role>(props.routeName);

const { filters, isLoading, clearFilters } = useFilters(
    props.filters,
    route(`${props.routeName}index`),
);
</script>
