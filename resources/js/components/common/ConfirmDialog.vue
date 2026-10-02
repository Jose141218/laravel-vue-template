<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="sm:max-w-106.25">
            <DialogHeader class="gap-2">
                <div class="flex items-center gap-3">
                    <div
                        v-if="variant === 'destructive'"
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-destructive/10 text-destructive"
                    >
                        <AlertTriangle class="h-5 w-5" />
                    </div>
                    <DialogTitle>{{ title }}</DialogTitle>
                </div>
                <DialogDescription class="pt-1 text-sm text-muted-foreground">
                    {{ description }}
                </DialogDescription>
            </DialogHeader>

            <DialogFooter class="gap-2 pt-4 sm:gap-0">
                <Button
                    type="button"
                    variant="outline"
                    :disabled="loading"
                    @click="emit('cancel-delete')"
                >
                    {{ cancelText }}
                </Button>
                <Button
                    type="button"
                    :variant="variant"
                    :disabled="loading"
                    @click="emit('confirm')"
                >
                    <span v-if="loading">Procesando...</span>
                    <span v-else>{{ confirmText }}</span>
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { AlertTriangle } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface Props {
    open: boolean;
    title?: string;
    description?: string;
    confirmText?: string;
    cancelText?: string;
    variant?: 'destructive' | 'default';
    loading?: boolean;
}

withDefaults(defineProps<Props>(), {
    title: '¿Estás seguro?',
    description: 'Esta acción no se puede deshacer.',
    confirmText: 'Confirmar',
    cancelText: 'Cancelar',
    variant: 'destructive',
    loading: false,
});

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
    (e: 'confirm'): void;
    (e: 'cancel-delete'): void;
}>();
</script>
