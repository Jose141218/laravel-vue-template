import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { Permission, PermissionFormData } from '../interfaces';

export interface UsePermissionProps {
    routeName: string;
    permission?: Permission | null;
}

export const usePermission = (props: UsePermissionProps) => {
    const isEditing = computed(() => Boolean(props.permission?.id));

    const form = useForm<PermissionFormData>({
        name: props.permission?.name ?? '',
        description: props.permission?.description ?? '',
        module_key: props.permission?.module_key ?? '',
    });

    const storeForm = () => {
        form.post(route(`${props.routeName}store`), {
            preserveScroll: true,
        });
    };

    const updateForm = () => {
        if (!props.permission?.id) {
            return;
        }

        form.put(route(`${props.routeName}update`, props.permission.id), {
            preserveScroll: true,
        });
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
