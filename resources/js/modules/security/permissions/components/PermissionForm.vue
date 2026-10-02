<template>
    <Card>
        <form @submit.prevent="$emit('submit')">
            <CardContent class="space-y-4 p-6">
                <FormField
                    for-id="name"
                    label="Nombre del Permiso"
                    required
                    description="Identificador único del permiso utilizado en policies, middleware y directivas."
                    :error="form.errors.name"
                >
                    <Input
                        id="name"
                        v-model="form.name"
                        autocomplete="off"
                        placeholder="Ej: users.index, posts.create, reports.export"
                        required
                    />
                </FormField>

                <FormField
                    for-id="module_key"
                    label="Módulo Asociado"
                    :error="form.errors.module_key"
                >
                    <Select
                        :model-value="form.module_key"
                        @update:model-value="form.module_key = String($event)"
                    >
                        <SelectTrigger id="module_key">
                            <SelectValue
                                placeholder="Selecciona un módulo..."
                            />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="module in modules"
                                :key="module.key"
                                :value="module.key"
                            >
                                {{ module.name }} ({{ module.key }})
                            </SelectItem>
                        </SelectContent>
                    </Select>
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
                        placeholder="Ej: Ver lista de usuarios en el sistema"
                    />
                </FormField>
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
                                form.processing
                                    ? 'Guardando...'
                                    : 'Guardar Permiso'
                            }}
                        </span>
                    </Button>
                </ActionGroup>
            </CardFooter>
        </form>
    </Card>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { InertiaForm } from '@inertiajs/vue3';
import { Save } from '@lucide/vue';
import ActionGroup from '@/components/common/ActionGroup.vue';
import FormField from '@/components/common/FormField.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { ModuleOption, PermissionFormData } from '../interfaces';

interface Props {
    routeName: string;
    form: InertiaForm<PermissionFormData>;
    modules: ModuleOption[];
}

defineProps<Props>();

defineEmits<{
    submit: [];
}>();
</script>
