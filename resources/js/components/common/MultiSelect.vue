<template>
    <Popover v-model:open="isOpen">
        <PopoverTrigger as-child :disabled="disabled">
            <button
                type="button"
                v-bind="$attrs"
                :id="id"
                role="combobox"
                :aria-expanded="isOpen"
                :disabled="disabled"
                :class="
                    cn(
                        'border-input bg-transparent shadow-xs transition-[color,box-shadow] outline-none',
                        'focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50',
                        'disabled:cursor-not-allowed disabled:opacity-50 dark:bg-input/30 dark:hover:bg-input/50',
                        'flex h-9 w-full items-center justify-between gap-2 overflow-hidden rounded-md border px-3 text-left text-sm whitespace-nowrap',
                        size === 'sm' && 'h-8 text-xs',
                        props.class,
                        triggerClass,
                    )
                "
            >
                <div class="flex flex-1 items-center gap-1.5 overflow-hidden">
                    <span
                        v-if="selectedOptions.length === 0"
                        class="truncate text-muted-foreground select-none"
                    >
                        {{ placeholder }}
                    </span>

                    <template v-else-if="selectedOptions.length === 1">
                        <Badge
                            variant="secondary"
                            class="h-5.5 max-w-full shrink-0 gap-1 truncate pr-1 text-xs font-normal"
                        >
                            <span
                                :class="{ capitalize: capitalize }"
                                class="max-w-36 truncate"
                            >
                                {{ selectedOptions[0].label }}
                            </span>
                            <span
                                role="button"
                                tabindex="0"
                                aria-label="Eliminar opción"
                                class="rounded-full p-0.5 text-muted-foreground transition-colors hover:bg-muted-foreground/20 hover:text-foreground"
                                @click="
                                    removeOption(
                                        selectedOptions[0].value,
                                        $event,
                                    )
                                "
                            >
                                <X class="size-2.5" />
                            </span>
                        </Badge>
                    </template>

                    <template v-else>
                        <Badge
                            variant="secondary"
                            class="h-5.5 shrink-0 px-2 text-xs font-normal"
                        >
                            {{ selectedOptions.length }} seleccionados
                        </Badge>
                    </template>
                </div>

                <div
                    class="flex shrink-0 items-center gap-1.5 text-muted-foreground"
                >
                    <span
                        v-if="
                            clearable && selectedOptions.length > 0 && !disabled
                        "
                        role="button"
                        tabindex="0"
                        aria-label="Limpiar selección"
                        class="rounded-full p-0.5 transition-colors hover:bg-muted"
                        @click="clearAll($event)"
                        @keydown.enter.stop="clearAll($event)"
                        @keydown.space.stop="clearAll($event)"
                    >
                        <X class="size-3.5" />
                    </span>
                    <ChevronDown
                        :class="
                            cn(
                                'size-4 opacity-50 transition-transform duration-200',
                                isOpen && 'rotate-180',
                            )
                        "
                    />
                </div>
            </button>
        </PopoverTrigger>

        <PopoverContent
            align="start"
            class="w-(--reka-popover-trigger-width) min-w-67.5 rounded-xl p-2.5 shadow-lg"
        >
            <div v-if="searchable" class="mb-2">
                <div
                    class="flex items-center gap-2 rounded-md border border-input bg-background px-2.5 py-1.5 focus-within:ring-1 focus-within:ring-ring"
                >
                    <Search class="size-4 shrink-0 text-muted-foreground" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        :placeholder="searchPlaceholder"
                        class="flex-1 bg-transparent text-sm outline-none placeholder:text-muted-foreground"
                    />
                </div>
            </div>

            <div
                v-if="selectAll && normalizedOptions.length > 1"
                class="flex items-center justify-between px-1 pb-2 text-xs text-muted-foreground"
            >
                <button
                    type="button"
                    class="cursor-pointer font-normal transition-colors hover:text-foreground"
                    @click="toggleSelectAll"
                >
                    {{
                        isAllSelected
                            ? 'Deseleccionar todos'
                            : 'Seleccionar todos'
                    }}
                </button>
                <button
                    v-if="selectedOptions.length > 0"
                    type="button"
                    class="cursor-pointer font-normal transition-colors hover:text-foreground"
                    @click="clearAll"
                >
                    Limpiar ({{ selectedOptions.length }})
                </button>
            </div>

            <div class="max-h-60 space-y-0.5 overflow-y-auto px-0.5 py-0.5">
                <div
                    v-if="filteredOptions.length === 0"
                    class="py-6 text-center text-xs text-muted-foreground"
                >
                    No se encontraron resultados
                </div>

                <div
                    v-for="opt in filteredOptions"
                    :key="String(opt.value)"
                    :class="
                        cn(
                            'flex cursor-pointer items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm transition-colors select-none',
                            isSelected(opt.value)
                                ? 'bg-primary/5 font-medium text-foreground'
                                : 'text-foreground/90 hover:bg-muted/70',
                        )
                    "
                    @click="toggleOption(opt.value)"
                >
                    <Checkbox
                        :model-value="isSelected(opt.value)"
                        class="pointer-events-none"
                    />

                    <slot
                        name="item"
                        :option="opt.raw"
                        :selected="isSelected(opt.value)"
                    >
                        <span
                            :class="{ capitalize: capitalize }"
                            class="flex-1 truncate"
                        >
                            {{ opt.label }}
                        </span>
                    </slot>
                </div>
            </div>

            <div
                class="mt-2 flex items-center justify-between border-t border-border px-1 pt-2.5 text-xs"
            >
                <span class="text-muted-foreground">
                    {{ selectedOptions.length }} de
                    {{ normalizedOptions.length }} seleccionados
                </span>
                <button
                    type="button"
                    class="cursor-pointer font-medium text-primary transition-colors hover:text-primary/80"
                    @click="isOpen = false"
                >
                    Listo
                </button>
            </div>
        </PopoverContent>
    </Popover>
</template>

<script setup lang="ts">
import { ChevronDown, Search, X } from '@lucide/vue';
import type { HTMLAttributes } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import {
    useMultiSelect
    
} from '@/composables/useMultiSelect';
import type {MultiSelectOption} from '@/composables/useMultiSelect';
import { cn } from '@/lib/utils';

defineOptions({
    inheritAttrs: false,
});

export interface MultiSelectProps {
    modelValue?: Array<string | number>;
    options: Array<string | number | MultiSelectOption>;
    by?: string;
    valueKey?: string;
    labelKey?: string;
    placeholder?: string;
    searchPlaceholder?: string;
    searchable?: boolean;
    clearable?: boolean;
    selectAll?: boolean;
    maxDisplay?: number;
    capitalize?: boolean;
    disabled?: boolean;
    id?: string;
    size?: 'sm' | 'default';
    class?: HTMLAttributes['class'];
    triggerClass?: HTMLAttributes['class'];
}

const props = withDefaults(defineProps<MultiSelectProps>(), {
    modelValue: () => [],
    options: () => [],
    by: undefined,
    valueKey: undefined,
    labelKey: undefined,
    placeholder: 'Seleccionar opciones...',
    searchPlaceholder: 'Buscar opción...',
    searchable: true,
    clearable: true,
    selectAll: true,
    maxDisplay: 2,
    capitalize: false,
    disabled: false,
    id: undefined,
    size: 'default',
    class: undefined,
    triggerClass: undefined,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: Array<string | number>): void;
}>();

const {
    isOpen,
    searchQuery,
    normalizedOptions,
    filteredOptions,
    selectedOptions,
    isAllSelected,
    isSelected,
    toggleOption,
    removeOption,
    clearAll,
    toggleSelectAll,
} = useMultiSelect(props, emit);
</script>
