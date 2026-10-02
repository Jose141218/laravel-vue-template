import { computed, ref } from 'vue';

export interface MultiSelectOption {
    [key: string]: any;
}

export interface UseMultiSelectProps {
    modelValue?: Array<string | number>;
    options: Array<string | number | MultiSelectOption>;
    by?: string;
    valueKey?: string;
    labelKey?: string;
    capitalize?: boolean;
}

export interface NormalizedOption {
    value: string | number;
    label: string;
    raw: string | number | MultiSelectOption;
}

export function useMultiSelect(
    props: UseMultiSelectProps,
    emit: (e: 'update:modelValue', value: Array<string | number>) => void,
) {
    const isOpen = ref(false);
    const searchQuery = ref('');

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
                const rawLabel = String(opt);
                const label =
                    props.capitalize && rawLabel
                        ? rawLabel.charAt(0).toUpperCase() + rawLabel.slice(1)
                        : rawLabel;

                return { value: opt, label, raw: opt };
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

    const filteredOptions = computed(() => {
        if (!searchQuery.value.trim()) {
            return normalizedOptions.value;
        }

        const query = searchQuery.value.toLowerCase().trim();

        return normalizedOptions.value.filter((opt) =>
            opt.label.toLowerCase().includes(query),
        );
    });

    const selectedOptions = computed(() => {
        const currentValues = new Set(
            (props.modelValue || []).map((v) => String(v)),
        );

        return normalizedOptions.value.filter((opt) =>
            currentValues.has(String(opt.value)),
        );
    });

    const isAllSelected = computed(() => {
        if (normalizedOptions.value.length === 0) {
            return false;
        }

        return selectedOptions.value.length === normalizedOptions.value.length;
    });

    function isSelected(val: string | number): boolean {
        return (props.modelValue || []).some((v) => String(v) === String(val));
    }

    function toggleOption(val: string | number) {
        const current = [...(props.modelValue || [])];
        const index = current.findIndex((v) => String(v) === String(val));

        if (index > -1) {
            current.splice(index, 1);
        } else {
            const match = normalizedOptions.value.find(
                (opt) => String(opt.value) === String(val),
            );
            current.push(match ? match.value : val);
        }

        emit('update:modelValue', current);
    }

    function removeOption(val: string | number, e?: Event) {
        e?.stopPropagation();
        const current = (props.modelValue || []).filter(
            (v) => String(v) !== String(val),
        );
        emit('update:modelValue', current);
    }

    function clearAll(e?: Event) {
        e?.stopPropagation();
        emit('update:modelValue', []);
    }

    function toggleSelectAll() {
        if (isAllSelected.value) {
            emit('update:modelValue', []);
        } else {
            emit(
                'update:modelValue',
                normalizedOptions.value.map((opt) => opt.value),
            );
        }
    }

    return {
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
    };
}
