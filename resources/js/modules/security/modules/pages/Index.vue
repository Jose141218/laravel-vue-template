<template>
    <Head :title="title" />

    <ModuleLayout variant="index">
        <PageHeader
            :title="title"
            description="Consulta los módulos para agrupar permisos y funcionalidades del sistema."
        />

        <Card>
            <CardHeader class="p-4 pb-2 sm:p-6 sm:pb-2">
                <TableToolbar
                    v-model="filters"
                    :sort-options="sortOptions"
                    :total="modules.meta.total"
                    placeholder="Buscar por nombre o clave de módulo..."
                    @clear="clearFilters"
                />
            </CardHeader>

            <CardContent class="p-0 sm:p-6 sm:pt-0">
                <ModulesTable
                    :modules="modules.data"
                    :route-name="routeName"
                    :loading="isLoading"
                />
                <Pagination
                    v-bind="modules.meta"
                    v-model:loading="isLoading"
                />
            </CardContent>
        </Card>
    </ModuleLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PageHeader from '@/components/common/PageHeader.vue';
import Pagination from '@/components/common/Pagination.vue';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { TableToolbar } from '@/components/ui/table';
import { useFilters } from '@/composables/useFilters';
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

const { filters, isLoading, clearFilters } = useFilters(
    props.filters,
    route(`${props.routeName}index`),
);
</script>
