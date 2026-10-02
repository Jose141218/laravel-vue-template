import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { User, UserFormData } from '../interfaces';

export interface UseUserProps {
    routeName: string;
    user?: User | null;
}

export const useUser = (props: UseUserProps) => {
    const isEditing = computed(() => Boolean(props.user?.id));

    const form = useForm<UserFormData>({
        name: props.user?.name ?? '',
        email: props.user?.email ?? '',
        password: '',
        send_invitation: !props.user?.id,
        roles: [...(props.user?.roles?.map((r) => r.name) ?? [])],
    });

    const isRoleSelected = (name: string) => form.roles.includes(name);

    const toggleRole = (name: string) => {
        const index = form.roles.indexOf(name);

        if (index > -1) {
            form.roles.splice(index, 1);
        } else {
            form.roles.push(name);
        }
    };

    const storeForm = () => {
        form.post(route(`${props.routeName}store`), {
            preserveScroll: true,
        });
    };

    const updateForm = () => {
        if (!props.user?.id) {
            return;
        }

        form.put(route(`${props.routeName}update`, props.user.id));
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
        isRoleSelected,
        toggleRole,
        submit,
        storeForm,
        updateForm,
    };
};
