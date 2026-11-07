<template>
    <Head :title="title" />

    <ModuleLayout variant="index">
        <PageHeader
            :title="title"
            description="Consulta los permisos atómicos del sistema clasificados por módulo."
        />

        <Card>
            <CardHeader class="p-4 pb-2 sm:p-6 sm:pb-2">
                <TableToolbar
                    v-model="filters"
                    :sort-options="sortOptions"
                    :total="permissions.meta.total"
                    placeholder="Buscar por nombre o descripción de permiso..."
                    @clear="clearFilters"
                >
                    <template #filters>
                        <TableFilterItem
                            label="Módulo"
                            :has-value="Boolean(filters.module_key)"
                            @clear="filters.module_key = undefined"
                        >
                            <Select
                                v-model="filters.module_key"
                                class="w-full sm:w-[200px]"
                            >
                                <SelectTrigger class="w-full sm:w-[200px]">
                                    <SelectValue
                                        placeholder="Filtrar por módulo"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectItem
                                            v-for="item in modules"
                                            :key="item.key"
                                            :value="item.key"
                                        >
                                            {{ item.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </TableFilterItem>
                    </template>
                </TableToolbar>
            </CardHeader>

            <CardContent class="p-0 sm:p-6 sm:pt-0">
                <PermissionsTable
                    :permissions="permissions.data"
                    :route-name="routeName"
                    :loading="isLoading"
                />
                <Pagination
                    v-bind="permissions.meta"
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
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { TableFilterItem, TableToolbar } from '@/components/ui/table';
import { useFilters } from '@/composables/useFilters';
import type { ResourceCollection, SortOption } from '@/interfaces';
import ModuleLayout from '@/layouts/ModuleLayout.vue';
import PermissionsTable from '../components/PermissionsTable.vue';
import type {
    ModuleOption,
    Permission,
    PermissionFilters,
} from '../interfaces';

const sortOptions: SortOption[] = [
    { label: 'Nombre', value: 'name' },
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

const { filters, isLoading, clearFilters } = useFilters(
    props.filters,
    route(`${props.routeName}index`),
);
</script>
