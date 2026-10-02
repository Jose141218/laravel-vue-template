<template>
    <Head :title="title" />

    <ModuleLayout variant="form" size="sm">
        <PageHeader
            :title="title"
            description="Define un nuevo grupo funcional para organizar permisos."
            :back-href="route(`${routeName}index`)"
        />

        <ModuleForm :form="form" :route-name="routeName" @submit="submit" />
    </ModuleLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PageHeader from '@/components/common/PageHeader.vue';
import ModuleLayout from '@/layouts/ModuleLayout.vue';
import ModuleForm from '../components/ModuleForm.vue';
import { useModule } from '../composables/useModule';

interface Props {
    title: string;
    routeName: string;
}

const props = defineProps<Props>();

const { form, submit } = useModule(props);
</script>
