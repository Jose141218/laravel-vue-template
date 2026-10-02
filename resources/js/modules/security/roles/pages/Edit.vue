<template>
    <Head :title="title" />

    <ModuleLayout variant="form" size="lg">
        <PageHeader :title="title" :back-href="route(`${routeName}index`)">
            <template #description>
                <p class="mt-1 text-sm text-muted-foreground">
                    Modifica el rol
                    <span class="font-medium text-foreground capitalize">{{
                        role.name
                    }}</span>
                    y sus permisos.
                </p>
            </template>
        </PageHeader>

        <RoleForm
            :form="form"
            :route-name="routeName"
            :modules="modules"
            :grouped-permissions="groupedPermissions"
            submit-label="Guardar cambios"
            @submit="submit"
        />
    </ModuleLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PageHeader from '@/components/common/PageHeader.vue';
import ModuleLayout from '@/layouts/ModuleLayout.vue';
import RoleForm from '../components/RoleForm.vue';
import { useRole } from '../composables/useRole';
import type { ModuleItem, PermissionItem, Role } from '../interfaces';

interface Props {
    title: string;
    routeName: string;
    role: Role;
    modules: ModuleItem[];
    groupedPermissions: Record<string, PermissionItem[]>;
}

const props = defineProps<Props>();

const { form, submit } = useRole(props);
</script>
