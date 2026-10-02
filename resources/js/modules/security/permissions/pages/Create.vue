<template>
    <Head :title="title" />

    <ModuleLayout variant="form" size="sm">
        <PageHeader :title="title" :back-href="route(`${routeName}index`)">
            <template #description>
                <p class="mt-1 text-sm text-muted-foreground">
                    Registra un permiso individual (formato recomendado:
                    <code class="font-mono text-foreground">modulo.accion</code
                    >).
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
import type { ModuleOption } from '../interfaces';

interface Props {
    title: string;
    routeName: string;
    modules: ModuleOption[];
}

const props = defineProps<Props>();

const { form, submit } = usePermission(props);
</script>
