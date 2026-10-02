<template>
    <Head :title="title" />

    <ModuleLayout variant="form" size="md">
        <PageHeader :title="title" :back-href="route(`${routeName}index`)">
            <template #description>
                <p class="mt-1 text-sm text-muted-foreground">
                    Ficha de información y perfil del usuario
                    <span class="font-medium text-foreground">{{
                        user.name
                    }}</span>.
                </p>
            </template>

            <template v-if="canEdit" #actions>
                <Button as-child class="cursor-pointer">
                    <Link :href="route(`${routeName}edit`, user.id)">
                        <Pencil class="mr-2 h-4 w-4" />
                        <span>Editar Usuario</span>
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <div class="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle class="text-lg">Datos de la Cuenta</CardTitle>
                    <CardDescription>
                        Información general del usuario y estado de acceso a la plataforma.
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 rounded-lg border bg-muted/20 p-4">
                        <Avatar class="h-16 w-16 shrink-0 rounded-full border-2 border-background shadow-xs">
                            <AvatarImage
                                v-if="(user as any).avatar"
                                :src="(user as any).avatar"
                                :alt="user.name"
                            />
                            <AvatarFallback class="bg-primary/10 text-base font-semibold text-primary">
                                {{ getInitials(user.name) }}
                            </AvatarFallback>
                        </Avatar>

                        <div class="min-w-0 flex-1 space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-lg font-bold text-foreground">
                                    {{ user.name }}
                                </h2>
                                <Badge
                                    v-if="user.email_verified_at"
                                    variant="outline"
                                    class="text-xs font-normal border-emerald-500/30 text-emerald-700 dark:text-emerald-400 bg-emerald-500/10"
                                >
                                    Activo
                                </Badge>
                                <Badge
                                    v-else
                                    variant="outline"
                                    class="text-xs font-normal border-amber-500/30 text-amber-700 dark:text-amber-400 bg-amber-500/10"
                                >
                                    Invitación pendiente
                                </Badge>
                            </div>
                            <p class="text-sm text-muted-foreground truncate">
                                {{ user.email }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <DetailItem
                            label="Nombre Completo"
                            :value="user.name"
                        />

                        <DetailItem
                            label="Correo Electrónico"
                            :value="user.email"
                            :icon="Mail"
                            copyable
                        />

                        <DetailItem
                            label="ID de Usuario"
                            :value="String(user.id)"
                            :icon="Fingerprint"
                            copyable
                        />

                        <DetailItem
                            label="Fecha de Registro"
                            :icon="Calendar"
                        >
                            <span :title="user.created_at.formatted">
                                {{ user.created_at.formatted }} ({{ user.created_at.human }})
                            </span>
                        </DetailItem>

                        <DetailItem
                            label="Estado de Verificación"
                            :icon="UserCheck"
                            class="sm:col-span-2"
                        >
                            <span v-if="user.email_verified_at" class="text-foreground">
                                Verificado el {{ user.email_verified_at.formatted }}
                            </span>
                            <span v-else class="text-amber-600 dark:text-amber-400">
                                Pendiente de confirmación por el usuario
                            </span>
                        </DetailItem>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="text-lg">Roles Asignados</CardTitle>
                    <CardDescription>
                        Roles y facultades asignadas a este usuario.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="user.roles.length > 0"
                        class="grid grid-cols-1 gap-3 sm:grid-cols-2"
                    >
                        <div
                            v-for="role in user.roles"
                            :key="role.id"
                            class="rounded-lg border p-3.5 bg-card flex flex-col justify-between space-y-1.5"
                        >
                            <div class="flex items-center gap-2">
                                <Shield class="h-4 w-4 text-primary" />
                                <Badge
                                    :variant="role.name === 'admin' ? 'default' : 'secondary'"
                                    class="text-xs font-medium capitalize"
                                >
                                    {{ role.name }}
                                </Badge>
                            </div>
                            <p class="text-xs text-muted-foreground">
                                {{ role.description || 'Sin descripción asignada para este rol.' }}
                            </p>
                        </div>
                    </div>

                    <div
                        v-else
                        class="rounded-lg border border-dashed p-6 text-center text-sm text-muted-foreground"
                    >
                        Este usuario no tiene ningún rol asignado actualmente.
                    </div>
                </CardContent>
            </Card>
        </div>
    </ModuleLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    Calendar,
    Fingerprint,
    Mail,
    Pencil,
    Shield,
    UserCheck,
} from '@lucide/vue';
import { computed } from 'vue';
import DetailItem from '@/components/common/DetailItem.vue';
import PageHeader from '@/components/common/PageHeader.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { useInitials } from '@/composables/useInitials';
import { useCan } from '@/composables/usePermissions';
import ModuleLayout from '@/layouts/ModuleLayout.vue';
import type { User } from '../interfaces';

interface Props {
    title: string;
    routeName: string;
    user: User;
}

defineProps<Props>();

const { getInitials } = useInitials();
const canEdit = computed(() => useCan('users.edit'));
</script>
