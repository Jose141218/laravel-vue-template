<template>

    <Head :title="title" />

    <ModuleLayout variant="index">
        <PageHeader :title="title"
            description="Administra los módulos para agrupar permisos y funcionalidades del sistema.">
            <template #actions>
                <Button as-child class="gap-2">
                    <Link :href="route(`${routeName}create`)">
                        <Plus class="h-4 w-4" />
                        <span>Nuevo Módulo</span>
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <Card>
            <CardHeader class="p-4 pb-2 sm:p-6 sm:pb-2">
                <TableToolbar v-model="filters" :sort-options="sortOptions" :total="modules.meta.total"
                    placeholder="Buscar por nombre o clave de módulo..." @clear="clearFilters" />
            </CardHeader>

            <CardContent class="p-0 sm:p-6 sm:pt-0">
                <ModulesTable :modules="modules.data" :route-name="routeName" :loading="isLoading"
                    @confirm-delete="confirmDelete" />
                <Pagination v-bind="modules.meta" v-model:loading="isLoading" />
            </CardContent>
        </Card>

        <ConfirmDialog v-model:open="isDeleteDialogOpen" title="¿Eliminar módulo?"
            :description="`Se eliminará el módulo '${itemToDelete?.name}'. Esta acción no se puede deshacer.`"
            :loading="isDeleting" @confirm="handleDelete" @cancel-delete="cancelDelete" />
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
import { useResourceDelete } from '@/composables/useResourceDelete';
import type { ResourceCollection, SortOption } from '@/interfaces';
import ModuleLayout from '@/layouts/ModuleLayout.vue';
import ModulesTable from '../components/ModulesTable.vue';
import type { Module, ModuleFilters } from '../interfaces';

const sortOptions: SortOption[] = [
    { label: 'Nombre', value: 'name' },
    { label: 'Clave', value: 'key' },
    { label: 'Fecha de registro', value: 'created_at' },
];

interface Props {
    title: string;
    routeName: string;
    modules: ResourceCollection<Module>;
    filters: ModuleFilters;
}

const props = defineProps<Props>();

const {
    itemToDelete,
    isDeleteDialogOpen,
    isDeleting,
    confirmDelete,
    cancelDelete,
    handleDelete,
} = useResourceDelete<Module>(props.routeName);

const { filters, isLoading, clearFilters } = useFilters(
    props.filters,
    route(`${props.routeName}index`),
);
</script>
