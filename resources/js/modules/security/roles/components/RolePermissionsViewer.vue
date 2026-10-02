<script setup lang="ts">
import { CheckCircle2, XCircle } from '@lucide/vue';
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import type { ModuleItem, PermissionItem } from '../interfaces';

interface Props {
    module: ModuleItem;
    permissions: PermissionItem[];
    assignedPermissions: string[];
}

const props = defineProps<Props>();

const isGranted = (permissionName: string): boolean => {
    return props.assignedPermissions.includes(permissionName);
};

const grantedCount = computed(() => {
    return props.permissions.filter((p) => props.assignedPermissions.includes(p.name)).length;
});

const isFullyGranted = computed(() => {
    return props.permissions.length > 0 && grantedCount.value === props.permissions.length;
});
</script>

<template>
    <div class="space-y-3 rounded-lg border p-4 bg-card">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b pb-3">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-semibold text-foreground">
                        {{ module.name }}
                    </h3>
                    <code class="rounded bg-muted px-1.5 py-0.5 font-mono text-[10px] text-muted-foreground uppercase">
                        {{ module.key }}
                    </code>
                </div>
                <p v-if="module.description" class="mt-0.5 text-xs text-muted-foreground">
                    {{ module.description }}
                </p>
            </div>

            <Badge
                :variant="isFullyGranted ? 'default' : grantedCount > 0 ? 'secondary' : 'outline'"
                class="text-xs font-normal"
            >
                {{ grantedCount }} de {{ permissions.length }} concedidos
            </Badge>
        </div>

        <div
            v-if="permissions.length > 0"
            class="grid grid-cols-1 gap-2.5 sm:grid-cols-2"
        >
            <div
                v-for="permission in permissions"
                :key="permission.id"
                :class="[
                    'flex items-start gap-2.5 rounded-md p-2.5 transition-colors',
                    isGranted(permission.name)
                        ? 'border border-emerald-500/20 bg-emerald-500/5 text-foreground'
                        : 'border border-dashed border-border/60 bg-muted/20 opacity-55 text-muted-foreground'
                ]"
            >
                <CheckCircle2
                    v-if="isGranted(permission.name)"
                    class="h-4 w-4 shrink-0 text-emerald-600 dark:text-emerald-400 mt-0.5"
                />
                <XCircle
                    v-else
                    class="h-4 w-4 shrink-0 text-muted-foreground/40 mt-0.5"
                />

                <div class="min-w-0 flex-1 space-y-0.5">
                    <p class="text-xs font-medium leading-tight">
                        {{ permission.description || permission.name }}
                    </p>
                    <p class="font-mono text-[10px] text-muted-foreground truncate">
                        {{ permission.name }}
                    </p>
                </div>
            </div>
        </div>

        <p v-else class="text-xs text-muted-foreground italic py-1">
            No hay permisos registrados en este módulo.
        </p>
    </div>
</template>
