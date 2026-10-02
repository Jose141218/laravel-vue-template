<template>
    <Head :title="title" />
    <ModuleLayout variant="form" size="sm">
        <PageHeader :title="title" :back-href="route(`${routeName}index`)">
            <template #description>
                <p class="mt-1 text-sm text-muted-foreground">
                    Modifica los datos del módulo
                    <span class="font-medium text-foreground">{{
                        module.name
                    }}</span
                    >.
                </p>
            </template>
        </PageHeader>

        <ModuleForm :form="form" :route-name="routeName" @submit="submit" />
    </ModuleLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PageHeader from '@/components/common/PageHeader.vue';
import ModuleLayout from '@/layouts/ModuleLayout.vue';
import ModuleForm from '../components/ModuleForm.vue';
import { useModule } from '../composables/useModule';
import type { Module } from '../interfaces';

interface Props {
    title: string;
    module: Module;
    routeName: string;
}

const props = defineProps<Props>();

const { form, submit } = useModule(props);
</script>
