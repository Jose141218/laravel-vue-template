<template>
    <Card>
        <form @submit.prevent="$emit('submit')">
            <CardContent class="space-y-4 p-6">
                <FormField for-id="name" label="Nombre del Módulo" required :error="form.errors.name">
                    <Input id="name" v-model="form.name" autocomplete="off"
                        placeholder="Ej: Catálogos, Seguridad, Finanzas" required />
                </FormField>

                <FormField for-id="key" label="Clave Única (Key)" required
                    description="Identificador corto para asociar permisos (solo letras, números y guiones)."
                    :error="form.errors.key">
                    <Input id="key" v-model="form.key" autocomplete="off" placeholder="Ej: cat, seg, fin" required />
                </FormField>

                <FormField for-id="description" label="Descripción" :error="form.errors.description">
                    <Input id="description" v-model="form.description" autocomplete="off"
                        placeholder="Descripción de la función de este módulo" />
                </FormField>
            </CardContent>

            <CardFooter class="border-t p-6">
                <ActionGroup gap="xl" class="w-full" aria-label="Acciones del formulario">
                    <Button variant="outline" as-child :disabled="form.processing">
                        <Link :href="route(`${routeName}index`)">Cancelar</Link>
                    </Button>
                    <Button type="submit" class="gap-2" :loading="form.processing">
                        <Save v-if="!form.processing" class="h-4 w-4" />
                        <span>
                            {{
                                form.processing
                                    ? 'Guardando...'
                                    : 'Guardar Módulo'
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
import type { ModuleFormData } from '../interfaces';

interface Props {
    routeName: string;
    form: InertiaForm<ModuleFormData>;
}

defineProps<Props>();

defineEmits<{
    submit: [];
}>();
</script>
