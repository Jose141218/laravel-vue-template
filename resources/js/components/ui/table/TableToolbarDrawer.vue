<template>
    <Sheet :modal="!isDesktop" v-model:open="isOpen">
        <SheetTrigger as-child>
            <Button variant="outline" size="sm" class="h-9 gap-1.5" aria-label="Abrir filtros">
                <SlidersHorizontal class="h-4 w-4" />
                <span>Filtros</span>
                <Badge v-if="activeFiltersCount > 0" variant="secondary" class="ml-1 h-5 px-1.5 text-[10px]">
                    {{ activeFiltersCount }}
                </Badge>
            </Button>
        </SheetTrigger>

        <SheetContent side="right" class="w-80 sm:w-96 md:shadow-2xl md:border-l" overlay-class="md:hidden">
            <SheetHeader>
                <SheetTitle>Filtros</SheetTitle>
                <SheetDescription>
                    Ajusta los parámetros de búsqueda y visualización.
                </SheetDescription>
            </SheetHeader>

            <div class="mx-4 flex flex-1 flex-col gap-4 overflow-y-auto">
                <div v-if="$slots.default" class="flex flex-col gap-4">
                    <slot />
                </div>

                <TableFilterItem v-if="sortOptions.length > 0" label="Ordenar por" :for-id="drawerSortId" class="md:hidden">
                    <ButtonGroup class="w-full">
                        <Select v-model="filters.order">
                            <SelectTrigger :id="drawerSortId" class="h-9 flex-1" aria-label="Ordenar por campo">
                                <SelectValue placeholder="Seleccionar campo" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="opt in sortOptions" :key="opt.value" :value="opt.value">
                                    {{ opt.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>

                        <Button type="button" variant="outline" size="icon" class="h-9 w-9 shrink-0" :title="filters.direction === 'asc'
                            ? 'Ascendente'
                            : 'Descendente'
                            " @click="toggleSortDirection">
                            <ArrowDownAZ v-if="filters.direction === 'asc'" class="h-4 w-4" />
                            <ArrowUpZA v-else class="h-4 w-4" />
                        </Button>
                    </ButtonGroup>
                </TableFilterItem>

                <TableFilterItem label="Registros por página" :for-id="drawerRowsId" class="md:hidden">
                    <Select :model-value="String(filters.rows)" @update:model-value="filters.rows = Number($event)">
                        <SelectTrigger :id="drawerRowsId" class="h-9 w-full" aria-label="Registros por página">
                            <SelectValue :placeholder="String(filters.rows)" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="opt in rowOptions" :key="opt" :value="String(opt)">
                                {{ opt }} registros
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </TableFilterItem>
            </div>

            <SheetFooter class="mt-auto flex flex-col gap-2">
                <Button variant="outline" class="w-full gap-2" @click="handleClear">
                    <RotateCcw class="h-4 w-4" />
                    Limpiar todos los filtros
                </Button>
            </SheetFooter>
        </SheetContent>
    </Sheet>
</template>

<script setup lang="ts">
import {
    ArrowDownAZ,
    ArrowUpZA,
    RotateCcw,
    SlidersHorizontal,
} from '@lucide/vue';
import { useMediaQuery } from '@vueuse/core';
import { ref, useId, watch } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import TableFilterItem from './TableFilterItem.vue';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import type { SortOption } from '@/interfaces';

interface Props {
    sortOptions?: SortOption[];
    rowOptions?: number[];
    activeFiltersCount?: number;
}

withDefaults(defineProps<Props>(), {
    sortOptions: () => [],
    rowOptions: () => [5, 10, 25, 50, 100],
    activeFiltersCount: 0,
});

const emit = defineEmits<{
    (e: 'clear'): void;
}>();

const filters = defineModel<Record<string, any>>({
    default: () => ({}),
});

const drawerSortId = useId();
const drawerRowsId = useId();

const isOpen = ref(false);
const isDesktop = useMediaQuery('(min-width: 768px)');

// Cerrar el drawer automáticamente si se cambia entre móvil y desktop
// para evitar parpadeos en el overlay negro y saltos en la distribución de controles.
watch(isDesktop, () => {
    if (isOpen.value) {
        isOpen.value = false;
    }
});

const toggleSortDirection = () => {
    filters.value.direction =
        filters.value.direction === 'asc' ? 'desc' : 'asc';
};

const handleClear = () => {
    isOpen.value = false;
    emit('clear');
};
</script>
