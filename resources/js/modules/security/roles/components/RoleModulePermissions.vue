<template>
    <div class="space-y-3 rounded-lg border p-4">
        <div class="flex items-center justify-between border-b pb-2">
            <div>
                <h3 class="text-sm font-semibold text-foreground">
                    {{ module.name }}
                </h3>
                <p
                    v-if="module.description"
                    class="text-xs text-muted-foreground"
                >
                    {{ module.description }}
                </p>
            </div>
            <Button
                type="button"
                variant="ghost"
                size="sm"
                class="h-8 gap-1.5 text-xs"
                @click="emit('toggleModule')"
            >
                <CheckSquare
                    v-if="isFullySelected"
                    class="h-3.5 w-3.5 text-primary"
                />
                <Square v-else class="h-3.5 w-3.5 text-muted-foreground" />
                <span>
                    {{
                        isFullySelected
                            ? 'Deseleccionar todos'
                            : 'Seleccionar todos'
                    }}
                </span>
            </Button>
        </div>

        <div
            class="grid max-h-96 grid-cols-1 gap-3 overflow-y-auto pt-1 sm:grid-cols-2"
        >
            <div
                v-for="permission in permissions"
                :key="permission.id"
                class="flex cursor-pointer items-start space-x-2.5 rounded-md p-2 hover:bg-muted/50"
                @click="emit('togglePermission', permission.name)"
            >
                <Checkbox
                    :id="`perm-${permission.id}`"
                    :model-value="isPermissionSelected(permission.name)"
                    @click.stop
                    @update:model-value="
                        emit('togglePermission', permission.name)
                    "
                />
                <div class="space-y-0.5 leading-none">
                    <span class="cursor-pointer text-xs font-medium">
                        {{ permission.description || permission.name }}
                    </span>
                    <p class="font-mono text-[11px] text-muted-foreground">
                        {{ permission.name }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { CheckSquare, Square } from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import type { ModuleItem, PermissionItem } from '../interfaces';

interface Props {
    module: ModuleItem;
    permissions?: PermissionItem[];
    selectedPermissions: string[];
}

const props = withDefaults(defineProps<Props>(), {
    permissions: () => [],
});

const emit = defineEmits<{
    toggleModule: [];
    togglePermission: [name: string];
}>();

const isPermissionSelected = (name: string) =>
    props.selectedPermissions.includes(name);

const isFullySelected = computed(
    () =>
        props.permissions.length > 0 &&
        props.permissions.every((p) =>
            props.selectedPermissions.includes(p.name),
        ),
);
</script>
