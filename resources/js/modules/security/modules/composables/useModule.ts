import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { Module, ModuleFormData } from '../interfaces';

export interface UseModuleProps {
    routeName: string;
    module?: Module | null;
}

export const useModule = (props: UseModuleProps) => {
    const isEditing = computed(() => Boolean(props.module?.id));

    const form = useForm<ModuleFormData>({
        name: props.module?.name ?? '',
        key: props.module?.key ?? '',
        description: props.module?.description ?? '',
    });

    const storeForm = () => {
        form.post(route(`${props.routeName}store`));
    };

    const updateForm = () => {
        if (!props.module?.id) {
            return;
        }

        form.put(route(`${props.routeName}update`, props.module.id));
    };

    const submit = () => {
        if (isEditing.value) {
            updateForm();
        } else {
            storeForm();
        }
    };

    return {
        form,
        isEditing,
        submit,
        storeForm,
        updateForm,
    };
};
