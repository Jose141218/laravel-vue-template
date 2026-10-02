<template>
    <Head :title="title" />

    <ModuleLayout variant="form" size="lg">
        <PageHeader :title="title" :back-href="route(`${routeName}index`)">
            <template #description>
                <p class="mt-1 text-sm text-muted-foreground">
                    Consulta la configuración y permisos asignados al rol
                    <span class="font-medium text-foreground capitalize">{{
                        role.name
                    }}</span>.
                </p>
            </template>

            <template v-if="canEdit" #actions>
                <Button as-child class="cursor-pointer">
                    <Link :href="route(`${routeName}edit`, role.id)">
                        <Pencil class="mr-2 h-4 w-4" />
                        <span>Editar Rol</span>
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <div class="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle class="text-lg">Información General</CardTitle>
                    <CardDescription>
                        Datos identificadores y métricas de asociación del rol.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <DetailItem label="Nombre del Rol" :icon="Shield">
                            <Badge variant="default" class="text-xs font-medium capitalize">
                                {{ role.name }}
                            </Badge>
                        </DetailItem>

                        <DetailItem
                            label="Descripción"
                            :value="role.description"
                            class="sm:col-span-2 lg:col-span-1"
                        />

                        <DetailItem label="Permisos Concedidos" :icon="Key">
                            <Badge variant="secondary" class="text-xs font-normal">
                                {{ role.permissions_count }} permisos
                            </Badge>
                        </DetailItem>

                        <DetailItem label="Usuarios Asignados" :icon="Users">
                            <Badge variant="outline" class="text-xs font-normal">
                                {{ role.users_count }} usuarios
                            </Badge>
                        </DetailItem>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="text-lg">Permisos del Sistema</CardTitle>
                    <CardDescription>
                        Inspección detallada de los permisos concedidos por módulo.
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <RolePermissionsViewer
                        v-for="module in modules"
                        :key="module.key"
                        :module="module"
                        :permissions="groupedPermissions[module.key] || []"
                        :assigned-permissions="role.permissions || []"
                    />
                </CardContent>
            </Card>
        </div>
    </ModuleLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Key, Pencil, Shield, Users } from '@lucide/vue';
import { computed } from 'vue';
import DetailItem from '@/components/common/DetailItem.vue';
import PageHeader from '@/components/common/PageHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { useCan } from '@/composables/usePermissions';
import ModuleLayout from '@/layouts/ModuleLayout.vue';
import RolePermissionsViewer from '../components/RolePermissionsViewer.vue';
import type { ModuleItem, PermissionItem, Role } from '../interfaces';

interface Props {
    title: string;
    routeName: string;
    role: Role;
    modules: ModuleItem[];
    groupedPermissions: Record<string, PermissionItem[]>;
}

defineProps<Props>();

const canEdit = computed(() => useCan('roles.edit'));
</script>
