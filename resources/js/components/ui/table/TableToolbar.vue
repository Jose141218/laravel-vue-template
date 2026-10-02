<template>
    <div class="flex flex-col gap-3">
        <!-- Desktop / Tablet layout (>= 768px) -->
        <div class="hidden md:flex md:flex-col xl:flex-row xl:items-center xl:justify-between gap-3">
            <div class="relative w-full xl:w-64 2xl:w-80 shrink-0">
                <Search
                    class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input :id="desktopSearchId" name="search" v-model="filters.search" type="search" autocomplete="off"
                    :placeholder="placeholder" :aria-label="placeholder" class="h-9 pr-4 pl-9 text-sm w-full" />
            </div>

            <div class="flex flex-wrap items-center gap-2 2xl:gap-2.5 flex-1 xl:justify-end">
                <TableFilterItem v-if="sortOptions.length > 0" label="Ordenar" :for-id="sortSelectId"
                    orientation="horizontal">
                    <ButtonGroup>
                        <SelectInput :id="sortSelectId" v-model="filters.order" :options="sortOptions"
                            placeholder="Columna" class="w-48" />

                        <Button type="button" variant="outline" size="icon" class="h-9 w-9 shrink-0" :title="filters.direction === 'asc'
                            ? 'Ascendente (click para Descendente)'
                            : 'Descendente (click para Ascendente)'
                            " @click="toggleSortDirection">
                            <ArrowDownAZ v-if="filters.direction === 'asc'" class="h-4 w-4" />
                            <ArrowUpZA v-else class="h-4 w-4" />
                        </Button>
                    </ButtonGroup>
                </TableFilterItem>

                <TableFilterItem label="Filas" :for-id="rowsSelectId" orientation="horizontal">
                    <Select :model-value="String(filters.rows)" @update:model-value="filters.rows = Number($event)">
                        <SelectTrigger :id="rowsSelectId" class="h-9 w-16 px-2.5" aria-label="Filas por página">
                            <SelectValue :placeholder="String(filters.rows)" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="opt in rowOptions" :key="opt" :value="String(opt)">
                                {{ opt }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </TableFilterItem>

                <TableToolbarDrawer v-if="hasFilters" v-model="filters" :sort-options="sortOptions"
                    :row-options="rowOptions" :active-filters-count="customActiveFiltersCount" @clear="emit('clear')">
                    <slot />
                </TableToolbarDrawer>

                <Button variant="outline" size="sm" class="h-9 gap-1.5" title="Limpiar filtros" @click="emit('clear')">
                    <RotateCcw class="h-3.5 w-3.5" />
                    <span>Limpiar</span>
                </Button>

                <span v-if="total !== undefined"
                    class="text-xs whitespace-nowrap text-muted-foreground ml-auto xl:ml-0">
                    Total: <strong class="text-foreground">{{ total }}</strong>
                </span>
            </div>
        </div>

        <!-- Mobile layout (< 768px) -->
        <div class="flex flex-col gap-2.5 md:hidden">
            <div class="relative w-full">
                <Search
                    class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input :id="mobileSearchId" name="search_mobile" v-model="filters.search" type="search"
                    autocomplete="off" :placeholder="placeholder" :aria-label="placeholder"
                    class="h-9 pr-4 pl-9 text-sm w-full" />
            </div>

            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <TableToolbarDrawer v-model="filters" :sort-options="sortOptions" :row-options="rowOptions"
                        :active-filters-count="mobileActiveFiltersCount" @clear="emit('clear')">
                        <template v-if="hasFilters" #default>
                            <slot />
                        </template>
                    </TableToolbarDrawer>

                    <Button variant="outline" size="sm" class="h-9 gap-1.5" title="Limpiar filtros"
                        @click="emit('clear')">
                        <RotateCcw class="h-3.5 w-3.5" />
                        <span>Limpiar</span>
                    </Button>
                </div>

                <span v-if="total !== undefined" class="text-xs whitespace-nowrap text-muted-foreground">
                    Total: <strong class="text-foreground">{{ total }}</strong>
                </span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import {
    ArrowDownAZ,
    ArrowUpZA,
    RotateCcw,
    Search,
} from '@lucide/vue';
import { computed, useId, useSlots } from 'vue';
import SelectInput from '@/components/common/SelectInput.vue';
import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import TableFilterItem from './TableFilterItem.vue';
import TableToolbarDrawer from './TableToolbarDrawer.vue';
import type { SortOption } from '@/interfaces';

interface Props {
    sortOptions?: SortOption[];
    rowOptions?: number[];
    total?: number;
    placeholder?: string;
}

const baseId = useId();
const desktopSearchId = `${baseId}-search-desktop`;
const mobileSearchId = `${baseId}-search-mobile`;
const sortSelectId = `${baseId}-sort-select`;
const rowsSelectId = `${baseId}-rows-select`;

const props = withDefaults(defineProps<Props>(), {
    sortOptions: () => [],
    rowOptions: () => [5, 10, 25, 50, 100],
    total: undefined,
    placeholder: 'Buscar registros...',
});

const emit = defineEmits<{
    (e: 'clear'): void;
}>();

const slots = useSlots();
const hasFilters = computed(() => Boolean(slots.default));

const filters = defineModel<Record<string, any>>({
    default: () => ({
        search: '',
        rows: 5,
        order: '',
        direction: 'asc',
    }),
});

const toggleSortDirection = () => {
    filters.value.direction =
        filters.value.direction === 'asc' ? 'desc' : 'asc';
};

const customActiveFiltersCount = computed(() => {
    let count = 0;

    Object.entries(filters.value).forEach(([key, value]) => {
        if (!['search', 'rows', 'order', 'direction', 'page'].includes(key)) {
            if (
                value !== null &&
                value !== undefined &&
                value !== '' &&
                value !== 'all'
            ) {
                count++;
            }
        }
    });

    return count;
});

const mobileActiveFiltersCount = computed(() => {
    let count = customActiveFiltersCount.value;
    if (filters.value.order) count++;
    return count;
});
</script>
