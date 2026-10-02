<template>
    <Head :title="title" />

    <ModuleLayout variant="form" size="sm">
        <PageHeader :title="title" :back-href="route(`${routeName}index`)">
            <template #description>
                <p class="mt-1 text-sm text-muted-foreground">
                    Modifica el permiso
                    <span class="font-mono font-medium text-foreground">{{
                        permission.name
                    }}</span
                    >.
                </p>
            </template>
        </PageHeader>

        <PermissionForm
            :form="form"
            :route-name="routeName"
            :modules="modules"
            @submit="submit"
        />
    </ModuleLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PageHeader from '@/components/common/PageHeader.vue';
import ModuleLayout from '@/layouts/ModuleLayout.vue';
import PermissionForm from '../components/PermissionForm.vue';
import { usePermission } from '../composables/usePermission';
import type { ModuleOption, Permission } from '../interfaces';

interface Props {
    title: string;
    routeName: string;
    permission: Permission;
    modules: ModuleOption[];
}

const props = defineProps<Props>();

const { form, submit } = usePermission(props);
</script>
