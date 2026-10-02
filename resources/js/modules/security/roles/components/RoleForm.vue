<template>
    <form class="space-y-6" @submit.prevent="$emit('submit')">
        <Card>
            <CardHeader>
                <CardTitle class="text-lg">Información General</CardTitle>
                <CardDescription>
                    Datos identificadores del rol.
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
                <FormField
                    for-id="name"
                    label="Nombre del Rol"
                    required
                    :error="form.errors.name"
                >
                    <Input
                        id="name"
                        v-model="form.name"
                        autocomplete="off"
                        placeholder="Ej: coordinador, supervisor, auditor"
                        required
                    />
                </FormField>

                <FormField
                    for-id="description"
                    label="Descripción"
                    :error="form.errors.description"
                >
                    <Input
                        id="description"
                        v-model="form.description"
                        autocomplete="off"
                        placeholder="Descripción de las responsabilidades de este rol"
                    />
                </FormField>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle class="text-lg">Permisos del Sistema</CardTitle>
                <CardDescription>
                    Selecciona los permisos que tendrán los usuarios con este
                    rol.
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <RoleModulePermissions
                    v-for="module in modules"
                    :key="module.key"
                    :module="module"
                    :permissions="groupedPermissions[module.key] || []"
                    :selected-permissions="form.permissions"
                    @toggle-module="toggleModulePermissions(module.key)"
                    @toggle-permission="togglePermission"
                />
            </CardContent>

            <CardFooter class="border-t p-6">
                <ActionGroup
                    gap="xl"
                    class="w-full"
                    aria-label="Acciones del formulario"
                >
                    <Button
                        variant="outline"
                        as-child
                        :disabled="form.processing"
                    >
                        <Link :href="route(`${routeName}index`)">Cancelar</Link>
                    </Button>
                    <Button
                        type="submit"
                        class="gap-2"
                        :loading="form.processing"
                    >
                        <Save v-if="!form.processing" class="h-4 w-4" />
                        <span>
                            {{
                                form.processing ? 'Guardando...' : 'Guardar Rol'
                            }}
                        </span>
                    </Button>
                </ActionGroup>
            </CardFooter>
        </Card>
    </form>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { InertiaForm } from '@inertiajs/vue3';
import { Save } from '@lucide/vue';
import ActionGroup from '@/components/common/ActionGroup.vue';
import FormField from '@/components/common/FormField.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { useRolePermissions } from '../composables/useRolePermissions';
import type { ModuleItem, PermissionItem, RoleFormData } from '../interfaces';
import RoleModulePermissions from './RoleModulePermissions.vue';

interface Props {
    routeName: string;
    form: InertiaForm<RoleFormData>;
    modules: ModuleItem[];
    groupedPermissions: Record<string, PermissionItem[]>;
}

const props = defineProps<Props>();

defineEmits<{
    submit: [];
}>();

const { togglePermission, toggleModulePermissions } = useRolePermissions(
    props.form,
    props.groupedPermissions,
);
</script>
