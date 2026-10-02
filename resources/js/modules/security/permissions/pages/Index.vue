<template>
    <Head :title="title" />

    <ModuleLayout variant="index">
        <PageHeader
            :title="title"
            description="Administra los permisos atómicos del sistema clasificados por módulo."
        >
            <template #actions>
                <Button v-if="useCan('permissions.create')" as-child class="gap-2">
                    <Link :href="route(`${routeName}create`)">
                        <Plus class="h-4 w-4" />
                        <span>Nuevo Permiso</span>
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <Card>
            <CardHeader class="p-4 pb-2 sm:p-6 sm:pb-2">
                <TableToolbar
                    v-model="filters"
                    :sort-options="sortOptions"
                    :total="permissions.meta.total"
                    placeholder="Buscar permisos por nombre o descripción..."
                    @clear="clearFilters"
                >
                    <TableFilterItem label="Módulo" for-id="filter-module">
                        <Select
                            :model-value="filters.module_key || 'all'"
                            @update:model-value="
                                filters.module_key =
                                    $event === 'all' ? '' : String($event)
                            "
                        >
                            <SelectTrigger id="filter-module" class="h-9 w-full">
                                <SelectValue placeholder="Todos los módulos" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">
                                    Todos los módulos
                                </SelectItem>
                                <SelectItem
                                    v-for="module in modules"
                                    :key="module.key"
                                    :value="module.key"
                                >
                                    {{ module.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </TableFilterItem>
                </TableToolbar>
            </CardHeader>

            <CardContent class="p-0 sm:p-6 sm:pt-0">
                <PermissionsTable
                    :permissions="permissions.data"
                    :route-name="routeName"
                    :loading="isLoading"
                    @confirm-delete="confirmDelete"
                />
                <Pagination
                    v-bind="permissions.meta"
                    v-model:loading="isLoading"
                />
            </CardContent>
        </Card>

        <ConfirmDialog
            v-model:open="isDeleteDialogOpen"
            title="¿Eliminar permiso?"
            :description="`Se eliminará el permiso '${itemToDelete?.name}'. Esta acción desvinculará a los roles asociados.`"
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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { TableFilterItem, TableToolbar } from '@/components/ui/table';
import { useFilters } from '@/composables/useFilters';
import { useCan } from '@/composables/usePermissions';
import { useResourceDelete } from '@/composables/useResourceDelete';
import type { ResourceCollection, SortOption } from '@/interfaces';
import ModuleLayout from '@/layouts/ModuleLayout.vue';
import PermissionsTable from '../components/PermissionsTable.vue';
import type {
    ModuleOption,
    Permission,
    PermissionFilters,
} from '../interfaces';

const sortOptions: SortOption[] = [
    { label: 'Permiso', value: 'name' },
    { label: 'Descripción', value: 'description' },
    { label: 'Módulo', value: 'module_key' },
    { label: 'Fecha de registro', value: 'created_at' },
];

interface Props {
    title: string;
    routeName: string;
    permissions: ResourceCollection<Permission>;
    modules: ModuleOption[];
    filters: PermissionFilters;
}

const props = defineProps<Props>();

const {
    itemToDelete,
    isDeleteDialogOpen,
    isDeleting,
    confirmDelete,
    cancelDelete,
    handleDelete,
} = useResourceDelete<Permission>(props.routeName);

const { filters, isLoading, clearFilters } = useFilters(
    props.filters,
    route(`${props.routeName}index`),
);
</script>
