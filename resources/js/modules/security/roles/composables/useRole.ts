import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { Role, RoleFormData } from '../interfaces';

export interface UseRoleProps {
    routeName: string;
    role?: Role | null;
}

export const useRole = (props: UseRoleProps) => {
    const isEditing = computed(() => Boolean(props.role?.id));

    const form = useForm<RoleFormData>({
        name: props.role?.name ?? '',
        description: props.role?.description ?? '',
        permissions: [...(props.role?.permissions ?? [])],
    });

    const storeForm = () => {
        form.post(route(`${props.routeName}store`), {
            preserveScroll: true,
        });
    };

    const updateForm = () => {
        if (!props.role?.id) {
            return;
        }

        form.put(route(`${props.routeName}update`, props.role.id), {
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
