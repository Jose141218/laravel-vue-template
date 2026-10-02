<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { computed } from 'vue';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { cn } from '@/lib/utils';

defineOptions({
    inheritAttrs: false,
});

export interface SelectInputOption {
    [key: string]: any;
}

export interface SelectInputProps {
    modelValue?: string | number | null;
    options: Array<string | number | SelectInputOption>;
    by?: string;
    valueKey?: string;
    labelKey?: string;
    placeholder?: string;
    allLabel?: string;
    capitalize?: boolean;
    disabled?: boolean;
    id?: string;
    size?: 'sm' | 'default';
    class?: HTMLAttributes['class'];
    triggerClass?: HTMLAttributes['class'];
}

const props = withDefaults(defineProps<SelectInputProps>(), {
    modelValue: '',
    options: () => [],
    by: undefined,
    valueKey: undefined,
    labelKey: undefined,
    placeholder: undefined,
    allLabel: undefined,
    capitalize: false,
    disabled: false,
    id: undefined,
    size: 'default',
    class: undefined,
    triggerClass: undefined,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | number): void;
}>();

interface NormalizedOption {
    value: string | number;
    label: string;
    raw: string | number | SelectInputOption;
}

const valKey = computed(() => props.by || props.valueKey || 'id');
const lblKey = computed(() => props.by || props.labelKey || 'name');

const normalizedOptions = computed<NormalizedOption[]>(() => {
    if (!props.options) {
return [];
}

    const valueProp = valKey.value;
    const labelProp = lblKey.value;

    return props.options.map((opt) => {
        if (typeof opt !== 'object' || opt === null) {
            return { value: opt, label: String(opt), raw: opt };
        }

        const value = opt[valueProp] ?? opt.value ?? '';
        const rawLabel = String(
            opt[labelProp] ?? opt.label ?? opt[valueProp] ?? '',
        );
        const label =
            props.capitalize && rawLabel
                ? rawLabel.charAt(0).toUpperCase() + rawLabel.slice(1)
                : rawLabel;

        return {
            value,
            label,
            raw: opt,
        };
    });
});

const internalValue = computed(() => {
    if (props.allLabel) {
        return props.modelValue ? String(props.modelValue) : 'all';
    }

    return props.modelValue !== null &&
        props.modelValue !== undefined &&
        props.modelValue !== ''
        ? String(props.modelValue)
        : undefined;
});

function handleUpdate(val: any) {
    if (props.allLabel && val === 'all') {
        emit('update:modelValue', '');

        return;
    }

    const match = normalizedOptions.value.find(
        (opt) => String(opt.value) === String(val),
    );
    const finalVal = match ? match.value : val;
    emit('update:modelValue', finalVal);
}
</script>

<template>
    <Select
        :model-value="internalValue"
        :disabled="disabled"
        @update:model-value="handleUpdate"
    >
        <SelectTrigger
            :id="id"
            :size="size"
            :class="
                cn(
                    'h-9',
                    capitalize && '*:data-[slot=select-value]:capitalize',
                    props.class,
                    triggerClass,
                )
            "
            v-bind="$attrs"
        >
            <SelectValue
                :placeholder="placeholder || allLabel"
                :class="{ capitalize }"
            />
        </SelectTrigger>
        <SelectContent>
            <SelectItem v-if="allLabel" value="all">
                {{ allLabel }}
            </SelectItem>
            <SelectItem
                v-for="opt in normalizedOptions"
                :key="String(opt.value)"
                :value="String(opt.value)"
            >
                <slot name="item" :option="opt.raw">
                    <span :class="{ capitalize: capitalize }">{{
                        opt.label
                    }}</span>
                </slot>
            </SelectItem>
        </SelectContent>
    </Select>
</template>
