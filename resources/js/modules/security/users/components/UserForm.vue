<template>
    <form class="space-y-6" @submit.prevent="$emit('submit')">
        <Card>
            <CardHeader>
                <CardTitle class="text-lg">Datos de la Cuenta</CardTitle>
                <CardDescription>
                    {{
                        isEditing
                            ? 'Actualiza el nombre, correo o contraseña.'
                            : 'Información básica y credenciales de acceso.'
                    }}
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
                <FormField
                    for-id="name"
                    label="Nombre Completo"
                    required
                    :error="form.errors.name"
                >
                    <Input
                        id="name"
                        v-model="form.name"
                        autocomplete="name"
                        placeholder="Nombre del usuario"
                        required
                    />
                </FormField>

                <FormField
                    for-id="email"
                    label="Correo Electrónico"
                    required
                    :error="form.errors.email"
                >
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                        autocomplete="email"
                        placeholder="usuario@ejemplo.com"
                        required
                    />
                </FormField>

                <div
                    v-if="!isEditing"
                    class="flex items-start space-x-3 rounded-lg border p-4 bg-muted/30"
                >
                    <Checkbox
                        id="send_invitation"
                        :model-value="form.send_invitation ?? true"
                        @update:model-value="(val) => { form.send_invitation = val === true; }"
                    />
                    <div class="space-y-1 leading-none">
                        <label
                            for="send_invitation"
                            class="cursor-pointer text-sm font-medium leading-none"
                        >
                            Enviar invitación por correo electrónico
                        </label>
                        <p class="text-xs text-muted-foreground">
                            El usuario recibirá un correo con un enlace para activar su cuenta y configurar su contraseña.
                        </p>
                    </div>
                </div>

                <FormField
                    v-if="isEditing || !form.send_invitation"
                    for-id="password"
                    :label="
                        isEditing ? 'Nueva Contraseña (opcional)' : 'Contraseña'
                    "
                    :required="!isEditing && !form.send_invitation"
                    :description="passwordDescription"
                    :error="form.errors.password"
                >
                    <Input
                        id="password"
                        v-model="form.password"
                        type="password"
                        autocomplete="new-password"
                        :placeholder="
                            isEditing
                                ? 'Dejar en blanco para mantener la actual'
                                : '••••••••'
                        "
                        :required="!isEditing && !form.send_invitation"
                    />
                </FormField>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle class="text-lg">Roles Asignados</CardTitle>
                <CardDescription>
                    Selecciona uno o más roles para este usuario.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <FormField :error="form.errors.roles">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div
                            v-for="role in roles"
                            :key="role.id"
                            class="flex cursor-pointer items-start space-x-3 rounded-lg border p-3 hover:bg-muted/50"
                            @click="toggleRole(role.name)"
                        >
                            <Checkbox
                                :id="`role-${role.id}`"
                                :model-value="isRoleSelected(role.name)"
                                @click.stop
                                @update:model-value="toggleRole(role.name)"
                            />
                            <div class="space-y-1 leading-none">
                                <span
                                    class="cursor-pointer text-sm font-medium capitalize"
                                >
                                    {{ role.name }}
                                </span>
                                <p
                                    v-if="role.description"
                                    class="text-xs text-muted-foreground"
                                >
                                    {{ role.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </FormField>
            </CardContent>

            <CardFooter class="border-t p-6">
                <slot name="actions">
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
                            <span>{{ resolvedSubmitLabel }}</span>
                        </Button>
                    </ActionGroup>
                </slot>
            </CardFooter>
        </Card>
    </form>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { InertiaForm } from '@inertiajs/vue3';
import { Save } from '@lucide/vue';
import { computed } from 'vue';
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
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import type { RoleOption, UserFormData } from '../interfaces';

interface Props {
    routeName: string;
    form: InertiaForm<UserFormData>;
    roles: RoleOption[];
    isEditing?: boolean;
    isRoleSelected: (name: string) => boolean;
    toggleRole: (name: string) => void;
    submitLabel?: string;
}

const props = withDefaults(defineProps<Props>(), {
    isEditing: false,
});

const resolvedSubmitLabel = computed(
    () =>
        props.submitLabel ??
        (props.isEditing ? 'Guardar cambios' : 'Crear Usuario'),
);

defineEmits<{
    submit: [];
}>();

const passwordDescription = computed(() =>
    props.isEditing
        ? 'Solo llena este campo si deseas cambiar la contraseña del usuario.'
        : undefined,
);
</script>
