<template>
    <Head :title="title" />

    <ModuleLayout variant="form" size="lg">
        <PageHeader
            :title="title"
            description="Crea un rol y asigna los permisos correspondientes."
            :back-href="route(`${routeName}index`)"
        />

        <RoleForm
            :form="form"
            :route-name="routeName"
            :modules="modules"
            :grouped-permissions="groupedPermissions"
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
import type { ModuleItem, PermissionItem } from '../interfaces';

interface Props {
    title: string;
    routeName: string;
    modules: ModuleItem[];
    groupedPermissions: Record<string, PermissionItem[]>;
}

const props = defineProps<Props>();

const { form, submit } = useRole(props);
</script>
