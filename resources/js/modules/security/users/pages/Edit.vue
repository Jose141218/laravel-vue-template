<template>
    <Head :title="title" />

    <ModuleLayout variant="form" size="sm">
        <PageHeader :title="title" :back-href="route(`${routeName}index`)">
            <template #description>
                <p class="mt-1 text-sm text-muted-foreground">
                    Modifica los datos y roles del usuario
                    <span class="font-medium text-foreground">{{
                        user.name
                    }}</span
                    >.
                </p>
            </template>
        </PageHeader>

        <UserForm
            :form="form"
            :route-name="routeName"
            :roles="roles"
            :is-editing="isEditing"
            :is-role-selected="isRoleSelected"
            :toggle-role="toggleRole"
            @submit="submit"
        />
    </ModuleLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PageHeader from '@/components/common/PageHeader.vue';
import ModuleLayout from '@/layouts/ModuleLayout.vue';
import UserForm from '../components/UserForm.vue';
import { useUser } from '../composables/useUser';
import type { RoleOption, User } from '../interfaces';

interface Props {
    title: string;
    routeName: string;
    user: User;
    roles: RoleOption[];
}

const props = defineProps<Props>();

const { form, isEditing, isRoleSelected, toggleRole, submit } = useUser(props);
</script>
